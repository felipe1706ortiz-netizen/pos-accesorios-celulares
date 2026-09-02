<?php
namespace App\Models;

use App\Core\Model;

/**
 * ==============================================================================
 * MODELO DETALLEFACTURAMODEL
 * ==============================================================================
 * Renglones y productos vendidos por factura.
 * ==============================================================================
 */
class DetalleFacturaModel extends Model
{
    protected string $table = 'detalle_facturas';

    /**
     * Obtiene todos los ítems de una factura con datos del producto
     * 
     * @param int|string $facturaId
     * @return array
     */
    public function getItemsByFactura(int $facturaId): array
    {
        $sql = "SELECT d.*, p.nombre AS producto_nombre, p.codigo_barras 
                FROM {$this->table} d
                INNER JOIN productos p ON p.id = d.producto_id
                WHERE d.factura_id = :factura_id
                ORDER BY d.id ASC";

        return $this->fetchAll($sql, [':factura_id' => $facturaId]);
    }
}
