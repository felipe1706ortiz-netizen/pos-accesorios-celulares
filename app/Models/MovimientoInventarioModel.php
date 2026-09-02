<?php
namespace App\Models;

use App\Core\Model;

/**
 * ==============================================================================
 * MODELO MOVIMIENTOINVENTARIOMODEL (Kárdex y Trazabilidad de Stock)
 * ==============================================================================
 * Registra cada entrada, salida, ajuste físico o venta de inventario con auditoría.
 * ==============================================================================
 */
class MovimientoInventarioModel extends Model
{
    protected string $table = 'movimientos_inventario';

    /**
     * Registra un movimiento en el kárdex de inventario
     * 
     * @param int $productoId
     * @param int $usuarioId
     * @param string $tipo 'ENTRADA' | 'SALIDA' | 'AJUSTE' | 'VENTA' | 'DEVOLUCION'
     * @param int $cantidad Unidades que se mueven (positiva)
     * @param int $stockAnterior
     * @param int $stockNuevo
     * @param float $precioUnitario
     * @param string $motivo Justificación del movimiento
     * @return int ID del movimiento creado
     */
    public function registrar(
        int $productoId,
        int $usuarioId,
        string $tipo,
        int $cantidad,
        int $stockAnterior,
        int $stockNuevo,
        float $precioUnitario,
        string $motivo
    ): int {
        return $this->insert([
            'producto_id'     => $productoId,
            'usuario_id'      => $usuarioId,
            'tipo_movimiento' => strtoupper($tipo),
            'cantidad'        => abs($cantidad),
            'stock_anterior'  => $stockAnterior,
            'stock_nuevo'     => $stockNuevo,
            'precio_unitario' => $precioUnitario,
            'motivo'          => trim($motivo)
        ]);
    }

    /**
     * Obtiene los últimos movimientos de inventario con datos del producto y usuario
     * 
     * @param int $limit
     * @param int|null $productoId
     * @return array
     */
    public function getHistorial(int $limit = 100, ?int $productoId = null): array
    {
        $sql = "SELECT m.*, 
                       p.nombre AS producto_nombre, 
                       p.codigo_barras, 
                       c.nombre AS categoria_nombre,
                       u.nombre AS usuario_nombre
                FROM {$this->table} m
                INNER JOIN productos p ON p.id = m.producto_id
                LEFT JOIN categorias c ON c.id = p.categoria_id
                INNER JOIN usuarios u ON u.id = m.usuario_id";

        $params = [];
        if ($productoId !== null) {
            $sql .= " WHERE m.producto_id = :producto_id";
            $params[':producto_id'] = $productoId;
        }

        $sql .= " ORDER BY m.created_at DESC LIMIT {$limit}";

        return $this->fetchAll($sql, $params);
    }
}
