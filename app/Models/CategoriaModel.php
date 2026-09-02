<?php
namespace App\Models;

use App\Core\Model;

/**
 * ==============================================================================
 * MODELO CATEGORIAMODEL
 * ==============================================================================
 * Gestión de categorías para accesorios de celulares.
 * ==============================================================================
 */
class CategoriaModel extends Model
{
    protected string $table = 'categorias';

    /**
     * Obtiene todas las categorías activas ordenadas por nombre
     * @return array
     */
    public function getActivas(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 1 ORDER BY nombre ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Obtiene todas las categorías con el conteo de productos asociados
     * @return array
     */
    public function getCategoriasConConteo(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) AS total_productos
                FROM {$this->table} c
                LEFT JOIN productos p ON p.categoria_id = c.id AND p.estado = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Verifica si un nombre de categoría ya existe (excluyendo un ID opcional)
     * @param string $nombre
     * @param int|null $excludeId
     * @return bool
     */
    public function existsNombre(string $nombre, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE nombre = :nombre";
        $params = [':nombre' => trim($nombre)];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        return (int)$this->fetchColumn($sql, $params) > 0;
    }
}
