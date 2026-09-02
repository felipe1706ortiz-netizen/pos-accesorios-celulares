<?php
namespace App\Core;

use App\Config\Database;
use PDO;
use PDOStatement;
use PDOException;

/**
 * ==============================================================================
 * CLASE BASE MODEL
 * ==============================================================================
 * Proporciona métodos reutilizables de acceso a datos, ejecución de sentencias
 * preparadas (prepared statements), transacciones y operaciones CRUD básicas.
 * ==============================================================================
 */
abstract class Model
{
    /**
     * Instancia de PDO para el modelo
     * @var PDO
     */
    protected PDO $db;

    /**
     * Nombre de la tabla asociada al modelo
     * @var string
     */
    protected string $table = '';

    /**
     * Clave primaria de la tabla
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Constructor: Asigna la conexión PDO singleton
     */
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Ejecuta una consulta SQL con parámetros preparados de forma segura
     * 
     * @param string $sql Consulta SQL con placeholders (:param o ?)
     * @param array $params Arreglo asociativo o indexado de parámetros
     * @return PDOStatement
     * @throws PDOException
     */
    protected function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error SQL en Modelo [" . get_called_class() . "]: " . $e->getMessage() . " | Query: " . $sql);
            throw $e;
        }
    }

    /**
     * Obtiene todos los registros coincidentes con la consulta
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Obtiene un único registro (o false si no existe)
     * 
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Obtiene un valor escalar único (ej: COUNT(*), SUM(total))
     * 
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    protected function fetchColumn(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * Obtiene todos los registros activos de la tabla del modelo
     * 
     * @param string $orderBy Campo de ordenación (ej: 'id DESC')
     * @return array
     */
    public function getAll(string $orderBy = 'id DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        return $this->fetchAll($sql);
    }

    /**
     * Busca un registro por su clave primaria
     * 
     * @param int|string $id
     * @return array|null
     */
    public function findById($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Inserta un nuevo registro en la tabla
     * 
     * @param array $data Datos en formato ['columna' => 'valor']
     * @return int ID generado por AUTO_INCREMENT
     */
    public function insert(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, $data);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualiza un registro por su clave primaria
     * 
     * @param int|string $id
     * @param array $data Datos a actualizar ['columna' => 'valor']
     * @return bool
     */
    public function update($id, array $data): bool
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');

        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :primary_id";
        $data['primary_id'] = $id;

        $stmt = $this->query($sql, $data);
        return $stmt->rowCount() > 0;
    }

    /**
     * Elimina un registro por su clave primaria (o soft delete si se prefiere)
     * 
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Inicia una transacción PDO
     */
    public function beginTransaction(): bool
    {
        return Database::getInstance()->beginTransaction();
    }

    /**
     * Confirma la transacción PDO activa
     */
    public function commit(): bool
    {
        return Database::getInstance()->commit();
    }

    /**
     * Revierte la transacción PDO activa
     */
    public function rollBack(): bool
    {
        return Database::getInstance()->rollBack();
    }
}
