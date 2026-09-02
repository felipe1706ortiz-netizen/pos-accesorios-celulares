<?php
namespace App\Config;

use PDO;
use PDOException;

/**
 * ==============================================================================
 * CLASE DATABASE (Patrón Singleton con PDO)
 * ==============================================================================
 * Gestiona la conexión única, segura y optimizada a MySQL.
 * Configura atributos de seguridad contra SQL Injection y manejo de excepciones.
 * ==============================================================================
 */
class Database
{
    /**
     * Instancia única de la clase Database (Singleton)
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * Objeto de conexión PDO
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * Constructor privado para evitar instanciación directa
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Evitar la clonación del objeto Singleton
     */
    private function __clone() {}

    /**
     * Evitar la deserialización del objeto Singleton
     */
    public function __wakeup()
    {
        throw new \Exception("No se puede deserializar una instancia de Singleton.");
    }

    /**
     * Obtiene la instancia única de la clase Database
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establece la conexión PDO con MySQL utilizando configuraciones de seguridad estrictas
     * @throws PDOException
     */
    private function connect(): void
    {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=%s",
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            // Lanzar excepciones en caso de errores SQL
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            
            // Retornar registros como arreglos asociativos por defecto
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            
            // Desactivar emulación de sentencias preparadas para máxima seguridad real contra inyecciones SQL
            PDO::ATTR_EMULATE_PREPARES   => false,
            
            // Forzar nombres de columnas a minúsculas para portabilidad
            PDO::ATTR_CASE               => PDO::CASE_NATURAL,
            
            // Timeout de conexión en segundos
            PDO::ATTR_TIMEOUT            => 5,
            
            // Configurar juego de caracteres inicial y zona horaria en MySQL
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_unicode_ci"
        ];

        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Retorna la conexión activa de PDO
     * @return PDO
     */
    public function getConnection(): PDO
    {
        // Si por alguna razón la conexión se perdió, reconectar
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Inicia una transacción de base de datos
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    /**
     * Confirma una transacción activa
     * @return bool
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    /**
     * Revierte una transacción activa
     * @return bool
     */
    public function rollBack(): bool
    {
        if ($this->getConnection()->inTransaction()) {
            return $this->getConnection()->rollBack();
        }
        return false;
    }

    /**
     * Verifica si existe una transacción activa
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->getConnection()->inTransaction();
    }

    /**
     * Manejador centralizado de errores de conexión PDO
     * @param PDOException $e
     */
    private function handleError(PDOException $e): void
    {
        if (ENVIRONMENT === 'development') {
            $errorHtml = '<div style="font-family: Arial, sans-serif; padding: 25px; margin: 30px auto; max-width: 700px; background: #fff5f5; border: 1px solid #feb2b2; border-radius: 8px; color: #9b2c2c; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
            $errorHtml .= '<h2 style="margin-top: 0; color: #c53030; display: flex; align-items: center; gap: 8px;">⚠️ Error de Conexión a Base de Datos</h2>';
            $errorHtml .= '<p><strong>Mensaje:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            $errorHtml .= '<p><strong>Código de Error:</strong> ' . htmlspecialchars((string)$e->getCode()) . '</p>';
            $errorHtml .= '<hr style="border: 0; border-top: 1px solid #feb2b2; margin: 15px 0;">';
            $errorHtml .= '<p style="font-size: 13px; color: #742a2a;"><strong>Verifique:</strong></p>';
            $errorHtml .= '<ul style="font-size: 13px; color: #742a2a; padding-left: 20px; line-height: 1.6;">';
            $errorHtml .= '<li>Que el servicio MySQL esté iniciado en el Panel de XAMPP.</li>';
            $errorHtml .= '<li>Que la base de datos <code>' . htmlspecialchars(DB_NAME) . '</code> haya sido creada importando <code>database/database.sql</code>.</li>';
            $errorHtml .= '<li>Que el usuario (<code>' . htmlspecialchars(DB_USER) . '</code>) y la contraseña en <code>app/Config/config.php</code> sean correctos.</li>';
            $errorHtml .= '</ul>';
            $errorHtml .= '</div>';
            die($errorHtml);
        } else {
            error_log("Error PDO: " . $e->getMessage());
            die("Lo sentimos, ocurrió un problema al conectar con el servidor de datos. Por favor contacte al administrador.");
        }
    }
}
