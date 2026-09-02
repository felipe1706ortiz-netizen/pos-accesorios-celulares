<?php
namespace App\Models;

use App\Core\Model;

/**
 * ==============================================================================
 * MODELO USUARIOMODEL
 * ==============================================================================
 * Gestión de usuarios, autenticación y consulta por nombre de usuario o email.
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
