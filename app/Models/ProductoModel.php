<?php
namespace App\Models;

use App\Core\Model;
use Exception;

/**
 * ==============================================================================
 * MODELO PRODUCTOMODEL
 * ==============================================================================
 * Catálogo maestro de accesorios, control de stock, precios y códigos de barras.
 * ==============================================================================
 */
class ProductoModel extends Model
{
    protected string $table = 'productos';

    /**
     * Obtiene el listado completo de productos con su categoría asociada y filtros
     * 
     * @param string $search Término de búsqueda (código de barras o nombre)
     * @param int|null $categoriaId Filtrar por categoría
     * @param string|null $stockStatus 'ok' | 'low' | 'out'
     * @return array
     */
    public function getListado(string $search = '', ?int $categoriaId = null, ?string $stockStatus = null): array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM {$this->table} p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.estado = 1";
        
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.codigo_barras LIKE :search OR p.nombre LIKE :search2 OR p.descripcion LIKE :search3)";
            $searchWildcard = "%{$search}%";
            $params[':search'] = $searchWildcard;
            $params[':search2'] = $searchWildcard;
            $params[':search3'] = $searchWildcard;
        }

        if (!empty($categoriaId)) {
            $sql .= " AND p.categoria_id = :categoria_id";
            $params[':categoria_id'] = $categoriaId;
        }

        if ($stockStatus === 'low') {
            $sql .= " AND p.stock <= p.stock_minimo AND p.stock > 0";
        } elseif ($stockStatus === 'out') {
            $sql .= " AND p.stock = 0";
        } elseif ($stockStatus === 'ok') {
            $sql .= " AND p.stock > p.stock_minimo";
        }

        $sql .= " ORDER BY p.id DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Busca un producto por su código de barras exacto
     * 
     * @param string $codigo
     * @return array|null
     */
    public function findByCodigoBarras(string $codigo): ?array
    {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM {$this->table} p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.codigo_barras = :codigo AND p.estado = 1
                LIMIT 1";

        return $this->fetchOne($sql, [':codigo' => trim($codigo)]);
    }

    /**
     * Verifica si un código de barras ya existe (excluyendo un ID opcional para edición)
     * 
     * @param string $codigo
     * @param int|null $excludeId
     * @return bool
     */
    public function existsCodigo(string $codigo, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE codigo_barras = :codigo";
        $params = [':codigo' => trim($codigo)];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        return (int)$this->fetchColumn($sql, $params) > 0;
    }

    /**
     * Registra un nuevo producto y su movimiento inicial en Kárdex de forma transaccional
     * 
     * @param array $data
     * @param int $usuarioId
     * @return int ID del producto insertado
     * @throws Exception
     */
    public function crear(array $data, int $usuarioId): int
    {
        $this->beginTransaction();
        try {
            $stockInicial = (int)($data['stock'] ?? 0);

            $productoId = $this->insert([
                'codigo_barras' => trim($data['codigo_barras']),
                'nombre'        => trim($data['nombre']),
                'descripcion'   => trim($data['descripcion'] ?? ''),
                'categoria_id'  => (int)$data['categoria_id'],
                'precio_compra' => (float)$data['precio_compra'],
                'precio_venta'  => (float)$data['precio_venta'],
                'stock'         => $stockInicial,
                'stock_minimo'  => (int)($data['stock_minimo'] ?? 5),
                'estado'        => 1
            ]);

            // Si se ingresó con stock inicial > 0, registrar en Kárdex
            if ($stockInicial > 0) {
                $movimientoModel = new MovimientoInventarioModel();
                $movimientoModel->registrar(
                    $productoId,
                    $usuarioId,
                    'ENTRADA',
                    $stockInicial,
                    0,
                    $stockInicial,
                    (float)$data['precio_compra'],
                    'Inventario Inicial de Creación de Producto'
                );
            }

            $this->commit();
            return $productoId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Realiza un ajuste rápido de stock y/o precios con registro en el Kárdex
     * 
     * @param int $productoId
     * @param int $nuevoStock
     * @param float $nuevoPrecioVenta
     * @param float|null $nuevoPrecioCompra
     * @param string $motivo
     * @param int $usuarioId
     * @return bool
     * @throws Exception
     */
    public function ajusteRapido(
        int $productoId,
        int $nuevoStock,
        float $nuevoPrecioVenta,
        ?float $nuevoPrecioCompra,
        string $motivo,
        int $usuarioId
    ): bool {
        $producto = $this->findById($productoId);
        if (!$producto) {
            throw new Exception("Producto no encontrado.");
        }

        $this->beginTransaction();
        try {
            $stockAnterior = (int)$producto['stock'];
            $diferenciaStock = $nuevoStock - $stockAnterior;

            $updateData = [
                'stock'        => $nuevoStock,
                'precio_venta' => $nuevoPrecioVenta
            ];

            if ($nuevoPrecioCompra !== null && $nuevoPrecioCompra >= 0) {
                $updateData['precio_compra'] = $nuevoPrecioCompra;
            }

            $this->update($productoId, $updateData);

            // Si cambió la cantidad de stock, registrar en Kárdex
            if ($diferenciaStock !== 0) {
                $tipoMov = $diferenciaStock > 0 ? 'ENTRADA' : 'SALIDA';
                $movimientoModel = new MovimientoInventarioModel();
                $movimientoModel->registrar(
                    $productoId,
                    $usuarioId,
                    $tipoMov,
                    abs($diferenciaStock),
                    $stockAnterior,
                    $nuevoStock,
                    $updateData['precio_compra'] ?? (float)$producto['precio_compra'],
                    $motivo ?: 'Ajuste rápido de inventario'
                );
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene métricas consolidadas del inventario
     * @return array
     */
    public function getMetricas(): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_productos,
                    COALESCE(SUM(stock), 0) AS total_unidades,
                    COALESCE(SUM(stock * precio_compra), 0) AS valor_costo_total,
                    COALESCE(SUM(stock * precio_venta), 0) AS valor_venta_total,
                    COALESCE(SUM(CASE WHEN stock <= stock_minimo AND stock > 0 THEN 1 ELSE 0 END), 0) AS alertas_stock_bajo,
                    COALESCE(SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END), 0) AS productos_agotados
                FROM {$this->table}
                WHERE estado = 1";

        return $this->fetchOne($sql) ?: [
            'total_productos'     => 0,
            'total_unidades'      => 0,
            'valor_costo_total'   => 0,
            'valor_venta_total'   => 0,
            'alertas_stock_bajo'  => 0,
            'productos_agotados'  => 0
        ];
    }
}
