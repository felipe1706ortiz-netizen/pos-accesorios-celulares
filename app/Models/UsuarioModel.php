<?php
namespace App\Models;

use App\Core\Model;

/**
 * ==============================================================================
 * MODELO USUARIOMODEL
 * ==============================================================================
 * Gestión de usuarios, autenticación, registro y verificación por correo.
 * ==============================================================================
 */
class UsuarioModel extends Model
{
    protected string $table = 'usuarios';

    /**
     * Busca un usuario activo por nombre de usuario o email
     * 
     * @param string $login Identificador (usuario o email)
     * @return array|null
     */
    public function findByLogin(string $login): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (usuario = :login_user OR email = :login_email) 
                  AND estado = 1 
                LIMIT 1";

        return $this->fetchOne($sql, [
            ':login_user'  => trim($login),
            ':login_email' => trim($login)
        ]);
    }

    /**
     * Busca un usuario por su dirección de correo electrónico
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        return $this->fetchOne($sql, [':email' => trim($email)]);
    }

    /**
     * Busca un usuario por su nombre de usuario
     * 
     * @param string $usuario
     * @return array|null
     */
    public function findByUsername(string $usuario): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE usuario = :usuario LIMIT 1";
        return $this->fetchOne($sql, [':usuario' => trim($usuario)]);
    }

    /**
     * Busca un usuario mediante su token de verificación
     * 
     * @param string $token
     * @return array|null
     */
    public function findByToken(string $token): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE token_verificacion = :token LIMIT 1";
        return $this->fetchOne($sql, [':token' => trim($token)]);
    }

    /**
     * Marca la cuenta de correo como verificada y elimina el token
     * 
     * @param int $userId
     * @return bool
     */
    public function markEmailVerified(int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET email_verificado = 1, 
                    token_verificacion = NULL, 
                    token_expira = NULL,
                    estado = 1
                WHERE id = :id";
        
        $this->query($sql, [':id' => $userId]);
        return true;
    }

    /**
     * Asigna un nuevo token de verificación y fecha de expiración
     * 
     * @param int $userId
     * @param string $token
     * @param string $expiresAt
     * @return bool
     */
    public function setVerificationToken(int $userId, string $token, string $expiresAt): bool
    {
        $sql = "UPDATE {$this->table} 
                SET token_verificacion = :token, 
                    token_expira = :expires 
                WHERE id = :id";
        
        $this->query($sql, [
            ':token'   => $token,
            ':expires' => $expiresAt,
            ':id'      => $userId
        ]);
        return true;
    }

    /**
     * Asigna un token de recuperación de contraseña y fecha de expiración
     * 
     * @param int $userId
     * @param string $token
     * @param string $expiresAt
     * @return bool
     */
    public function setPasswordResetToken(int $userId, string $token, string $expiresAt): bool
    {
        $sql = "UPDATE {$this->table} 
                SET token_recuperacion = :token, 
                    token_recuperacion_expira = :expires 
                WHERE id = :id";
        
        $this->query($sql, [
            ':token'   => $token,
            ':expires' => $expiresAt,
            ':id'      => $userId
        ]);
        return true;
    }

    /**
     * Busca un usuario activo mediante su token de recuperación de contraseña
     * 
     * @param string $token
     * @return array|null
     */
    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE token_recuperacion = :token 
                  AND estado = 1 
                LIMIT 1";
        return $this->fetchOne($sql, [':token' => trim($token)]);
    }

    /**
     * Actualiza la contraseña del usuario y limpia los tokens de recuperación
     * 
     * @param int $userId
     * @param string $newPasswordHash
     * @return bool
     */
    public function updatePasswordAndClearResetToken(int $userId, string $newPasswordHash): bool
    {
        $sql = "UPDATE {$this->table} 
                SET password = :password, 
                    token_recuperacion = NULL, 
                    token_recuperacion_expira = NULL,
                    updated_at = NOW() 
                WHERE id = :id";
        
        $this->query($sql, [
            ':password' => $newPasswordHash,
            ':id'       => $userId
        ]);
        return true;
    }

    /**
     * Actualiza la fecha y hora del último login
     * 
     * @param int $userId
     */
    public function updateLastLogin(int $userId): void
    {
        $sql = "UPDATE {$this->table} SET ultimo_login = NOW() WHERE id = :id";
        $this->query($sql, [':id' => $userId]);
    }
}
