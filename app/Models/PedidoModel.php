<?php
namespace App\Models;

use App\Core\Model;
use Exception;

/**
 * ==============================================================================
 * MODELO PEDIDOMODEL (Gestión y Control de Pedidos y Encargos de Clientes)
 * ==============================================================================
 * Control de pedidos personalizados o productos por encargo, abonos (anticipos),
 * saldos pendientes, estados de entrega y trazabilidad para el cliente.
 * ==============================================================================
 */
class PedidoModel extends Model
{
    protected string $table = 'pedidos';

    /**
     * Genera el siguiente código correlativo de pedido (ej: PED-2026-0001)
     * @return string
     */
    public function generarCodigo(): string
    {
        $year = date('Y');
        $prefix = "PED-{$year}-";

        $sql = "SELECT codigo FROM {$this->table} 
                WHERE codigo LIKE :prefix 
                ORDER BY id DESC LIMIT 1";

        $ultimo = $this->fetchColumn($sql, [':prefix' => "{$prefix}%"]);

        if ($ultimo) {
            $num = (int)substr($ultimo, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Registra un nuevo pedido o encargo de cliente
     *
     * @param array $data Datos del formulario
     * @param int $usuarioId Cajero o empleado responsable
     * @return array Datos del pedido insertado
     * @throws Exception
     */
    public function crear(array $data, int $usuarioId): array
    {
        $clienteNombre = trim($data['cliente_nombre'] ?? '');
        $clienteTelefono = trim($data['cliente_telefono'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');

        if (empty($clienteNombre)) {
            throw new Exception("El nombre del cliente es obligatorio.");
        }
        if (empty($clienteTelefono)) {
            throw new Exception("El número de teléfono o WhatsApp es obligatorio.");
        }
        if (empty($descripcion)) {
            throw new Exception("La descripción del producto o accesorio solicitado es obligatoria.");
        }

        $cantidad = max(1, (int)($data['cantidad'] ?? 1));
        $precioTotal = max(0.0, (float)($data['precio_total'] ?? 0));
        $abono = max(0.0, (float)($data['abono'] ?? 0));
        $saldoPendiente = max(0.0, $precioTotal - $abono);
        $metodoAbono = strtoupper($data['metodo_pago_abono'] ?? 'EFECTIVO');
        $fechaEntrega = !empty($data['fecha_entrega_estimada']) ? $data['fecha_entrega_estimada'] : null;
        $productoId = !empty($data['producto_id']) ? (int)$data['producto_id'] : null;

        $codigo = $this->generarCodigo();

        $id = $this->insert([
            'codigo'                 => $codigo,
            'cliente_nombre'         => $clienteNombre,
            'cliente_telefono'       => $clienteTelefono,
            'cliente_documento'      => trim($data['cliente_documento'] ?? ''),
            'producto_id'            => $productoId,
            'descripcion'            => $descripcion,
            'cantidad'               => $cantidad,
            'precio_total'           => $precioTotal,
            'abono'                  => $abono,
            'saldo_pendiente'        => $saldoPendiente,
            'metodo_pago_abono'      => $metodoAbono,
            'estado'                 => 'PENDIENTE',
            'fecha_entrega_estimada' => $fechaEntrega,
            'notas'                  => trim($data['notas'] ?? ''),
            'usuario_id'             => $usuarioId
        ]);

        return $this->findByIdWithDetails($id) ?: [
            'id'              => $id,
            'codigo'          => $codigo,
            'cliente_nombre'  => $clienteNombre,
            'precio_total'    => $precioTotal,
            'abono'           => $abono,
            'saldo_pendiente' => $saldoPendiente
        ];
    }

    /**
     * Obtiene los detalles completos de un pedido por ID
     *
     * @param int $id
     * @return array|null
     */
    public function findByIdWithDetails(int $id): ?array
    {
        $sql = "SELECT p.*, 
                       u.nombre AS cajero_nombre, 
                       pr.nombre AS producto_catalogo_nombre,
                       pr.codigo_barras AS producto_codigo_barras
                FROM {$this->table} p
                INNER JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN productos pr ON pr.id = p.producto_id
                WHERE p.id = :id LIMIT 1";

        return $this->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Obtiene el listado de pedidos con filtros por estado y buscador
     *
     * @param string|null $estado 'PENDIENTE' | 'EN_CAMINO' | 'LISTO' | 'ENTREGADO' | 'CANCELADO'
     * @param string $search
     * @param int $limit
     * @return array
     */
    public function getListado(?string $estado = null, string $search = '', int $limit = 100): array
    {
        $sql = "SELECT p.*, 
                       u.nombre AS cajero_nombre, 
                       pr.nombre AS producto_catalogo_nombre
                FROM {$this->table} p
                INNER JOIN usuarios u ON u.id = p.usuario_id
                LEFT JOIN productos pr ON pr.id = p.producto_id
                WHERE 1=1";

        $params = [];

        if (!empty($estado) && $estado !== 'TODOS') {
            $sql .= " AND p.estado = :estado";
            $params[':estado'] = strtoupper($estado);
        }

        if (!empty($search)) {
            $sql .= " AND (p.codigo LIKE :search OR p.cliente_nombre LIKE :search2 OR p.cliente_telefono LIKE :search3 OR p.descripcion LIKE :search4)";
            $wildcard = "%{$search}%";
            $params[':search'] = $wildcard;
            $params[':search2'] = $wildcard;
            $params[':search3'] = $wildcard;
            $params[':search4'] = $wildcard;
        }

        $sql .= " ORDER BY p.id DESC LIMIT {$limit}";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Actualiza el estado operativo de un pedido
     *
     * @param int $id
     * @param string $nuevoEstado 'PENDIENTE' | 'EN_CAMINO' | 'LISTO' | 'ENTREGADO' | 'CANCELADO'
     * @param string|null $notas
     * @return bool
     */
    public function actualizarEstado(int $id, string $nuevoEstado, ?string $notas = null): bool
    {
        $estadosValidos = ['PENDIENTE', 'EN_CAMINO', 'LISTO', 'ENTREGADO', 'CANCELADO'];
        $nuevoEstado = strtoupper(trim($nuevoEstado));

        if (!in_array($nuevoEstado, $estadosValidos)) {
            throw new Exception("Estado de pedido no válido.");
        }

        $data = ['estado' => $nuevoEstado];
        if ($notas !== null) {
            $pedido = $this->findById($id);
            $notasActuales = $pedido['notas'] ?? '';
            $data['notas'] = trim("{$notasActuales} | [{$nuevoEstado}]: {$notas}");
        }

        return $this->update($id, $data);
    }

    /**
     * Registra un abono adicional hacia el saldo pendiente del pedido
     *
     * @param int $id
     * @param float $montoAbono
     * @return bool
     * @throws Exception
     */
    public function registrarAbono(int $id, float $montoAbono): bool
    {
        $pedido = $this->findById($id);
        if (!$pedido) {
            throw new Exception("El pedido especificado no existe.");
        }

        if ($montoAbono <= 0) {
            throw new Exception("El monto de abono debe ser mayor a cero.");
        }

        $saldoActual = (float)$pedido['saldo_pendiente'];
        $abonoActual = (float)$pedido['abono'];

        $nuevoAbono = $abonoActual + $montoAbono;
        $nuevoSaldo = max(0.0, $saldoActual - $montoAbono);

        $updateData = [
            'abono'           => $nuevoAbono,
            'saldo_pendiente' => $nuevoSaldo
        ];

        // Si ya completó el 100% y estaba pendiente o listo, mantener o actualizar
        if ($nuevoSaldo <= 0 && $pedido['estado'] === 'PENDIENTE') {
            $updateData['estado'] = 'LISTO';
        }

        return $this->update($id, $updateData);
    }

    /**
     * Obtiene métricas consolidadas de pedidos para el panel de control
     * @return array
     */
    public function getMetricas(): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_pedidos,
                    COUNT(CASE WHEN estado IN ('PENDIENTE', 'EN_CAMINO') THEN 1 END) AS pedidos_activos,
                    COUNT(CASE WHEN estado = 'LISTO' THEN 1 END) AS pedidos_listos,
                    COUNT(CASE WHEN estado = 'ENTREGADO' THEN 1 END) AS pedidos_entregados,
                    COALESCE(SUM(abono), 0) AS total_abonos,
                    COALESCE(SUM(CASE WHEN estado != 'CANCELADO' THEN saldo_pendiente ELSE 0 END), 0) AS saldo_por_cobrar
                FROM {$this->table}";

        return $this->fetchOne($sql) ?: [
            'total_pedidos'     => 0,
            'pedidos_activos'   => 0,
            'pedidos_listos'    => 0,
            'pedidos_entregados'=> 0,
            'total_abonos'      => 0,
            'saldo_por_cobrar'  => 0
        ];
    }
}
