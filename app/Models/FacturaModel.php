<?php
namespace App\Models;

use App\Core\Model;
use App\Models\ProductoModel;
use App\Models\MovimientoInventarioModel;
use App\Models\SesionCajaModel;
use Exception;

/**
 * ==============================================================================
 * MODELO FACTURAMODEL (Core Transaccional de Ventas e Historial)
 * ==============================================================================
 * Manejo de facturas, numeración correlativa, procesamiento atómico de ventas,
 * reportes, auditoría y anulación de transacciones.
 * ==============================================================================
 */
class FacturaModel extends Model
{
    protected string $table = 'facturas';

    /**
     * Genera el siguiente número correlativo de factura (ej: FAC-2026-00001)
     * @return string
     */
    public function generarNumeroFactura(): string
    {
        $year = date('Y');
        $prefix = "FAC-{$year}-";

        $sql = "SELECT numero_factura FROM {$this->table} 
                WHERE numero_factura LIKE :prefix 
                ORDER BY id DESC LIMIT 1";

        $ultimoNumero = $this->fetchColumn($sql, [':prefix' => "{$prefix}%"]);

        if ($ultimoNumero) {
            $consecutivo = (int)substr($ultimoNumero, -5) + 1;
        } else {
            $consecutivo = 1;
        }

        return $prefix . str_pad((string)$consecutivo, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Procesa una venta POS completa de forma atómica
     * 
     * @param array $ventaData Datos de cabecera (cliente, subtotal, descuento, total, metodo_pago, monto_recibido, cambio)
     * @param array $items Lista de productos en el carrito
     * @param int $usuarioId ID del cajero/vendedor
     * @return array Datos de la factura creada
     * @throws Exception
     */
    public function procesarVenta(array $ventaData, array $items, int $usuarioId): array
    {
        if (empty($items)) {
            throw new Exception("El carrito de compras está vacío.");
        }

        // 1. Obtener o inicializar sesión de caja activa
        $sesionCajaModel = new SesionCajaModel();
        $sesion = $sesionCajaModel->obtenerOCrearSesion($usuarioId);
        $sesionId = (int)$sesion['id'];

        $this->beginTransaction();

        try {
            $productoModel = new ProductoModel();
            $movimientoModel = new MovimientoInventarioModel();

            // 2. Generar número de factura único
            $numeroFactura = $this->generarNumeroFactura();

            $subtotal = (float)($ventaData['subtotal'] ?? 0);
            $descuento = (float)($ventaData['descuento'] ?? 0);
            $impuesto = (float)($ventaData['impuesto'] ?? 0);
            $total = (float)($ventaData['total'] ?? 0);
            $metodoPago = strtoupper($ventaData['metodo_pago'] ?? 'EFECTIVO');
            $montoRecibido = (float)($ventaData['monto_recibido'] ?? $total);
            $cambio = (float)($ventaData['cambio'] ?? 0);

            // 3. Insertar cabecera de la factura
            $facturaId = $this->insert([
                'numero_factura'    => $numeroFactura,
                'sesion_caja_id'    => $sesionId,
                'usuario_id'        => $usuarioId,
                'cliente_nombre'    => trim($ventaData['cliente_nombre'] ?? 'Cliente General'),
                'cliente_documento' => trim($ventaData['cliente_documento'] ?? '222222222222'),
                'subtotal'          => $subtotal,
                'descuento'         => $descuento,
                'impuesto'          => $impuesto,
                'total'             => $total,
                'metodo_pago'       => $metodoPago,
                'monto_recibido'    => $montoRecibido,
                'cambio'            => $cambio,
                'estado'            => 'COMPLETADA',
                'notas'             => trim($ventaData['notas'] ?? '')
            ]);

            // 4. Procesar cada ítem del carrito (Detalle, Descuento de Stock y Kárdex)
            $detalleModel = new DetalleFacturaModel();

            foreach ($items as $item) {
                $prodId = (int)$item['id'];
                $cantidad = (int)$item['cantidad'];
                $precioUnitario = (float)$item['precio'];
                $descuentoItem = (float)($item['descuento'] ?? 0);
                $subtotalItem = ($cantidad * $precioUnitario) - $descuentoItem;

                if ($cantidad <= 0) {
                    throw new Exception("Cantidad inválida para el producto ID {$prodId}.");
                }

                // Verificar producto y stock disponible
                $producto = $productoModel->findById($prodId);
                if (!$producto) {
                    throw new Exception("El producto con ID {$prodId} no existe.");
                }

                $stockActual = (int)$producto['stock'];
                if ($stockActual < $cantidad) {
                    throw new Exception("Stock insuficiente para '{$producto['nombre']}'. Disponible: {$stockActual}, Solicitado: {$cantidad}.");
                }

                $nuevoStock = $stockActual - $cantidad;

                // Insertar en detalle_facturas
                $detalleModel->insert([
                    'factura_id'      => $facturaId,
                    'producto_id'     => $prodId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'precio_compra'   => (float)$producto['precio_compra'],
                    'descuento'       => $descuentoItem,
                    'subtotal'        => $subtotalItem
                ]);

                // Actualizar stock de producto
                $productoModel->update($prodId, [
                    'stock' => $nuevoStock
                ]);

                // Registrar salida en Kárdex
                $movimientoModel->registrar(
                    $prodId,
                    $usuarioId,
                    'VENTA',
                    $cantidad,
                    $stockActual,
                    $nuevoStock,
                    $precioUnitario,
                    "Venta POS Factura #{$numeroFactura}"
                );
            }

            // 5. Acumular venta en la sesión de caja activa
            $sesionCajaModel->acumularVenta($sesionId, $total, $metodoPago);

            $this->commit();

            return [
                'id'             => $facturaId,
                'numero_factura' => $numeroFactura,
                'total'          => $total,
                'cambio'         => $cambio,
                'metodo_pago'    => $metodoPago
            ];

        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Anula una factura de venta de forma atómica:
     * - Devuelve el stock vendido a cada producto.
     * - Registra el movimiento 'DEVOLUCION' en el Kárdex.
     * - Descuenta el monto de la sesión de caja asociada.
     * - Marca la factura como 'ANULADA'.
     * 
     * @param int $facturaId
     * @param int $usuarioId
     * @param string $motivo
     * @return bool
     * @throws Exception
     */
    public function anularFactura(int $facturaId, int $usuarioId, string $motivo = 'Anulación de venta'): bool
    {
        $factura = $this->findById($facturaId);
        if (!$factura) {
            throw new Exception("Factura no encontrada.");
        }

        if ($factura['estado'] === 'ANULADA') {
            throw new Exception("La factura #{$factura['numero_factura']} ya se encuentra anulada.");
        }

        $this->beginTransaction();

        try {
            $detalleModel = new DetalleFacturaModel();
            $productoModel = new ProductoModel();
            $movimientoModel = new MovimientoInventarioModel();

            $items = $detalleModel->getItemsByFactura($facturaId);

            // 1. Revertir stock de cada producto
            foreach ($items as $item) {
                $prodId = (int)$item['producto_id'];
                $cantidad = (int)$item['cantidad'];

                $prod = $productoModel->findById($prodId);
                if ($prod) {
                    $stockAnterior = (int)$prod['stock'];
                    $stockNuevo = $stockAnterior + $cantidad;

                    $productoModel->update($prodId, ['stock' => $stockNuevo]);

                    $movimientoModel->registrar(
                        $prodId,
                        $usuarioId,
                        'DEVOLUCION',
                        $cantidad,
                        $stockAnterior,
                        $stockNuevo,
                        (float)$item['precio_unitario'],
                        "Anulación Factura #{$factura['numero_factura']}: " . ($motivo ?: 'Sin motivo especificado')
                    );
                }
            }

            // 2. Marcar la factura como ANULADA
            $this->update($facturaId, [
                'estado' => 'ANULADA',
                'notas'  => trim(($factura['notas'] ?? '') . " | Anulada: " . ($motivo ?: 'Sin motivo'))
            ]);

            // 3. Restar el monto de la sesión de caja si existe
            if (!empty($factura['sesion_caja_id'])) {
                $sesionId = (int)$factura['sesion_caja_id'];
                $total = (float)$factura['total'];
                $metodo = strtoupper($factura['metodo_pago']);

                $columna = 'total_ventas_efectivo';
                if ($metodo === 'TARJETA') $columna = 'total_ventas_tarjeta';
                elseif ($metodo === 'TRANSFERENCIA') $columna = 'total_ventas_transferencia';

                $decrEsperado = ($metodo === 'EFECTIVO') ? ", monto_esperado = GREATEST(0, monto_esperado - :monto_esp)" : "";

                $sqlSesion = "UPDATE sesiones_caja 
                              SET {$columna} = GREATEST(0, {$columna} - :monto)
                                  {$decrEsperado}
                              WHERE id = :id";
                $paramsSesion = [':monto' => $total, ':id' => $sesionId];
                if ($metodo === 'EFECTIVO') $paramsSesion[':monto_esp'] = $total;

                $this->query($sqlSesion, $paramsSesion);
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene la factura completa con datos de la tienda, cajero y productos
     * 
     * @param int|string $id
     * @return array|null
     */
    public function getFacturaCompleta($id): ?array
    {
        $sql = "SELECT f.*, u.nombre AS cajero_nombre, s.fecha_apertura 
                FROM {$this->table} f
                INNER JOIN usuarios u ON u.id = f.usuario_id
                LEFT JOIN sesiones_caja s ON s.id = f.sesion_caja_id
                WHERE f.id = :id LIMIT 1";

        $factura = $this->fetchOne($sql, [':id' => $id]);
        if (!$factura) {
            return null;
        }

        $detalleModel = new DetalleFacturaModel();
        $factura['items'] = $detalleModel->getItemsByFactura((int)$id);

        return $factura;
    }

    /**
     * Obtiene lista de facturas con filtros avanzados
     * 
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @param string|null $metodoPago
     * @param string|null $estado
     * @param string $search
     * @param int $limit
     * @return array
     */
    public function getHistorial(
        ?string $fechaInicio = null, 
        ?string $fechaFin = null, 
        ?string $metodoPago = null, 
        ?string $estado = null, 
        string $search = '', 
        int $limit = 200
    ): array {
        $sql = "SELECT f.*, u.nombre AS cajero_nombre 
                FROM {$this->table} f
                INNER JOIN usuarios u ON u.id = f.usuario_id
                WHERE 1=1";

        $params = [];

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(f.created_at) >= :fecha_ini";
            $params[':fecha_ini'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(f.created_at) <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        if (!empty($metodoPago)) {
            $sql .= " AND f.metodo_pago = :metodo_pago";
            $params[':metodo_pago'] = strtoupper($metodoPago);
        }

        if (!empty($estado)) {
            $sql .= " AND f.estado = :estado";
            $params[':estado'] = strtoupper($estado);
        }

        if (!empty($search)) {
            $sql .= " AND (f.numero_factura LIKE :search OR f.cliente_nombre LIKE :search2 OR f.cliente_documento LIKE :search3)";
            $wildcard = "%{$search}%";
            $params[':search'] = $wildcard;
            $params[':search2'] = $wildcard;
            $params[':search3'] = $wildcard;
        }

        $sql .= " ORDER BY f.id DESC LIMIT {$limit}";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Obtiene métricas y estadísticas consolidadas del historial de facturas
     * 
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function getMetricasHistorial(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_facturas,
                    COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' THEN total ELSE 0 END), 0) AS total_ventas,
                    COALESCE(AVG(CASE WHEN estado = 'COMPLETADA' THEN total ELSE NULL END), 0) AS ticket_promedio,
                    COALESCE(SUM(CASE WHEN estado = 'ANULADA' THEN 1 ELSE 0 END), 0) AS total_anuladas,
                    COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' AND metodo_pago = 'EFECTIVO' THEN total ELSE 0 END), 0) AS total_efectivo,
                    COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' AND metodo_pago = 'TARJETA' THEN total ELSE 0 END), 0) AS total_tarjeta,
                    COALESCE(SUM(CASE WHEN estado = 'COMPLETADA' AND metodo_pago = 'TRANSFERENCIA' THEN total ELSE 0 END), 0) AS total_transferencia
                FROM {$this->table}
                WHERE 1=1";

        $params = [];

        if (!empty($fechaInicio)) {
            $sql .= " AND DATE(created_at) >= :fecha_ini";
            $params[':fecha_ini'] = $fechaInicio;
        }

        if (!empty($fechaFin)) {
            $sql .= " AND DATE(created_at) <= :fecha_fin";
            $params[':fecha_fin'] = $fechaFin;
        }

        return $this->fetchOne($sql, $params) ?: [
            'total_facturas'      => 0,
            'total_ventas'        => 0,
            'ticket_promedio'     => 0,
            'total_anuladas'      => 0,
            'total_efectivo'      => 0,
            'total_tarjeta'       => 0,
            'total_transferencia' => 0
        ];
    }
}
