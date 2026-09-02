<?php
namespace App\Core;

/**
 * ==============================================================================
 * CLASE AUTH (Control de Acceso y Roles)
 * ==============================================================================
 * Centraliza la verificación de autenticación de usuarios y permisos según rol.
 * ==============================================================================
 */
class Auth
{
    /**
     * Inicia la sesión de un usuario autenticado
     * @param array $user Datos del usuario provenientes de la BD
     */
    public static function login(array $user): void
    {
        Session::start();
        // Regenerar ID de sesión para prevenir Session Fixation
        session_regenerate_id(true);

        Session::set('user_id', (int)$user['id']);
        Session::set('user_name', $user['nombre']);
        Session::set('user_username', $user['usuario']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['rol']);
        Session::set('logged_in_time', time());
    }

    /**
     * Cierra la sesión activa
     */
    public static function logout(): void
    {
        Session::destroy();
    }

    /**
     * Verifica si hay un usuario autenticado
     * @return bool
     */
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    /**
     * Retorna los datos del usuario logueado en sesión
     * @return array|null
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'id'       => Session::get('user_id'),
            'nombre'   => Session::get('user_name'),
            'usuario'  => Session::get('user_username'),
            'email'    => Session::get('user_email'),
            'rol'      => Session::get('user_role')
        ];
    }

    /**
     * Retorna el ID del usuario en sesión
     * @return int|null
     */
    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    /**
     * Retorna el rol del usuario en sesión ('admin' o 'cajero')
     * @return string|null
     */
    public static function role(): ?string
    {
        return Session::get('user_role');
    }

    /**
     * Verifica si el usuario actual es Administrador
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /**
     * Verifica si el usuario actual es Cajero
     * @return bool
     */
    public static function isCajero(): bool
    {
        return self::role() === 'cajero';
    }

    /**
     * Middleware de protección: Requiere autenticación
     * Si no está autenticado, redirige al login.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            Session::setFlash('warning', 'Debe iniciar sesión para acceder al sistema.');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    /**
     * Middleware de protección: Requiere rol de Administrador
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            Session::setFlash('danger', 'Acceso denegado: Se requieren permisos de administrador.');
            header('Location: ' . APP_URL . '/pos');
            exit;
        }
    }
}
