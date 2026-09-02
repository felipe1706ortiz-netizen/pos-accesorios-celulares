<?php
namespace App\Core;

/**
 * ==============================================================================
 * CLASE SESSION
 * ==============================================================================
 * Manejo seguro de sesiones PHP, tokens CSRF y mensajes Flash temporales.
 * ==============================================================================
 */
class Session
{
    /**
     * Inicia la sesión PHP con directivas seguras de cookies
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_lifetime', SESSION_LIFETIME);
            ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
            
            session_name(SESSION_NAME);
            session_start();
        }
    }

    /**
     * Establece un valor en la sesión
     * @param string $key
     * @param mixed $value
     */
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de la sesión
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verifica si una clave existe en la sesión
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Elimina una clave de la sesión
     * @param string $key
     */
    public static function remove(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destruye la sesión completa
     */
    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Establece un mensaje flash (disponible solo en la siguiente petición)
     * @param string $type 'success', 'danger', 'warning', 'info'
     * @param string $message
     */
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash_messages'][$type][] = $message;
    }

    /**
     * Obtiene y limpia todos los mensajes flash
     * @return array
     */
    public static function getFlash(): array
    {
        self::start();
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }

    /**
     * Genera o retorna el token CSRF actual
     * @return string
     */
    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION[CSRF_TOKEN_KEY])) {
            $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_KEY];
    }

    /**
     * Valida si el token CSRF provisto es válido
     * @param string|null $token
     * @return bool
     */
    public static function validateCsrf(?string $token): bool
    {
        self::start();
        if (empty($token) || empty($_SESSION[CSRF_TOKEN_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
    }
}
