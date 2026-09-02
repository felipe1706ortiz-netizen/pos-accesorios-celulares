<?php
namespace App\Core;

/**
 * ==============================================================================
 * CLASE BASE CONTROLLER
 * ==============================================================================
 * Controlador principal del que heredan todos los controladores del sistema.
 * Gestiona el renderizado de vistas, respuestas JSON, sanitización y validación.
 * ==============================================================================
 */
abstract class Controller
{
    /**
     * Renderiza una vista PHP dentro de una plantilla de layout maestra
     * 
     * @param string $view Ruta relativa de la vista dentro de app/Views (ej: 'pos/index')
     * @param array $data Datos que serán extraídos como variables en la vista
     * @param string|null $layout Nombre del layout dentro de app/Views/layouts (por defecto 'main', null para sin layout)
     */
    protected function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        // Extraer datos para que estén disponibles como variables locales en la vista
        extract($data);

        // Variables globales de utilidad para las vistas
        $currentUser = Auth::user();
        $flashMessages = Session::getFlash();
        $csrfToken = Session::csrfToken();

        $viewFile = APP_PATH . '/Views/' . ltrim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            $this->renderError("La vista [{$view}] no fue encontrada en la ruta especificada: {$viewFile}");
            return;
        }

        // Si se especificó un layout, capturamos el buffer de la vista y lo inyectamos en el layout
        if ($layout !== null) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            $layoutFile = APP_PATH . '/Views/layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                // Fallback: imprimir el contenido directamente si el layout no existe
                echo $content;
            }
        } else {
            // Renderizado directo sin layout (para modales o fragmentos AJAX)
            require $viewFile;
        }
    }

    /**
     * Retorna una respuesta JSON estructurada y finaliza la ejecución
     * Ideal para llamadas AJAX / Fetch API del módulo POS o escáner
     * 
     * @param mixed $data Datos o respuesta
     * @param int $statusCode Código HTTP (200, 400, 404, 500, etc.)
     */
    protected function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirecciona a una ruta interna del sistema
     * 
     * @param string $path Ruta relativa (ej: '/pos' o '/inventario')
     */
    protected function redirect(string $path): void
    {
        $url = (strpos($path, 'http') === 0) ? $path : APP_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    /**
     * Obtiene y sanitiza datos del arreglo $_POST
     * 
     * @param string|null $key Clave a consultar (si es null retorna todo el array sanitizado)
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    protected function getPost(?string $key = null, $default = null)
    {
        if ($key === null) {
            $clean = [];
            foreach ($_POST as $k => $v) {
                $clean[$k] = is_string($v) ? trim($v) : $v;
            }
            return $clean;
        }

        if (isset($_POST[$key])) {
            return is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];
        }

        return $default;
    }

    /**
     * Obtiene y sanitiza datos del arreglo $_GET
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    protected function getQuery(?string $key = null, $default = null)
    {
        if ($key === null) {
            $clean = [];
            foreach ($_GET as $k => $v) {
                $clean[$k] = is_string($v) ? trim($v) : $v;
            }
            return $clean;
        }

        if (isset($_GET[$key])) {
            return is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key];
        }

        return $default;
    }

    /**
     * Lee y decodifica un payload JSON enviado en el cuerpo de la petición (php://input)
     * 
     * @return array
     */
    protected function getJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Validador simple de campos obligatorios
     * 
     * @param array $data Datos a validar
     * @param array $requiredFields Lista de campos que no deben estar vacíos
     * @return array Lista de errores (vacío si todo es válido)
     */
    protected function validateRequired(array $data, array $requiredFields): array
    {
        $errors = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $fieldName = ucfirst(str_replace('_', ' ', $field));
                $errors[$field] = "El campo '{$fieldName}' es obligatorio.";
            }
        }
        return $errors;
    }

    /**
     * Valida el token CSRF para peticiones POST de formularios
     * 
     * @return bool
     */
    protected function validateCsrf(): bool
    {
        $token = $this->getPost('csrf_token') ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        return Session::validateCsrf($token);
    }

    /**
     * Renderiza una pantalla de error amigable
     * 
     * @param string $message
     * @param int $code
     */
    protected function renderError(string $message, int $code = 500): void
    {
        http_response_code($code);
        $errorFile = APP_PATH . '/Views/errors/' . $code . '.php';
        if (file_exists($errorFile)) {
            require $errorFile;
        } else {
            echo "<div style='font-family:sans-serif; padding:40px; text-align:center;'>";
            echo "<h1 style='color:#e53e3e;'>Error {$code}</h1>";
            echo "<p style='color:#4a5568; font-size:16px;'>" . htmlspecialchars($message) . "</p>";
            echo "<a href='" . APP_URL . "' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#3182ce; color:#fff; text-decoration:none; border-radius:5px;'>Volver al Inicio</a>";
            echo "</div>";
        }
        exit;
    }
}
