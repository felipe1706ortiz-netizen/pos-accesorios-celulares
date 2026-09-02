<?php
namespace App\Models;

use App\Core\Model;

/**
 * ==============================================================================
 * MODELO CONFIGURACIONMODEL
 * ==============================================================================
 * Parámetros del negocio, encabezado y pie de ticket para impresora térmica.
 * ==============================================================================
 */
class ConfiguracionModel extends Model
{
    protected string $table = 'configuracion';

    /**
     * Obtiene todos los parámetros de configuración como un array asociativo ['clave' => 'valor']
     * @return array
     */
    public function getMapaConfiguracion(): array
    {
        $sql = "SELECT clave, valor FROM {$this->table}";
        $registros = $this->fetchAll($sql);

        $config = [];
        foreach ($registros as $row) {
            $config[$row['clave']] = $row['valor'];
        }

        return $config;
    }

    /**
     * Obtiene un parámetro individual
     * @param string $clave
     * @param string $default
     * @return string
     */
    public function getValor(string $clave, string $default = ''): string
    {
        $sql = "SELECT valor FROM {$this->table} WHERE clave = :clave LIMIT 1";
        $val = $this->fetchColumn($sql, [':clave' => $clave]);
        return $val !== false ? (string)$val : $default;
    }
}
