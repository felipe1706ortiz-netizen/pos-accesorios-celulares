<?php
namespace App\Core;

/**
 * ==============================================================================
 * CLASE ROUTER (Enrutador de Peticiones HTTP)
 * ==============================================================================
 * Registra rutas GET/POST, procesa parámetros dinámicos en la URL y despacha
 * la ejecución al Controlador y Método correspondiente.
 * ==============================================================================
 */
class Router
{
    /**
     * Arreglo de rutas registradas
     * @var array
     */
    private array $routes = [];

    /**
     * Registra una ruta HTTP GET
     * 
     * @param string $uri Ruta relativa (ej: '/', '/pos', '/inventario/editar/{id}')
     * @param string|callable $action 'Controlador@metodo' o función anónima
     */
    public function get(string $uri, $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Registra una ruta HTTP POST
     * 
     * @param string $uri
     * @param string|callable $action
     */
    public function post(string $uri, $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Agrega la ruta a la tabla interna de enrutamiento
     */
    private function addRoute(string $method, string $uri, $action): void
    {
        $uri = '/' . trim($uri, '/');
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri'    => $uri,
            'action' => $action
        ];
    }

    /**
     * Resuelve la petición HTTP actual y despacha al controlador
     * 
     * @param string $requestUri URI solicitada
     * @param string $requestMethod Método HTTP ('GET', 'POST', etc.)
     */
    public function dispatch(string $requestUri, string $requestMethod): void
    {
        // 1. Limpiar query strings (?foo=bar) de la URI
        $parsedUrl = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        
        // 2. Detectar directorio del script (ej: /poss/public o /poss)
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        
        // Directorio raíz del proyecto si está en /public
        $projectDir = $scriptDir;
        if (substr($projectDir, -7) === '/public') {
            $projectDir = substr($projectDir, 0, -7);
        }

        // 3. Eliminar prefijos de subcarpetas si existen en la URL
        if (!empty($scriptDir) && $scriptDir !== '/' && strpos($parsedUrl, $scriptDir) === 0) {
            $parsedUrl = substr($parsedUrl, strlen($scriptDir));
        } elseif (!empty($projectDir) && $projectDir !== '/' && strpos($parsedUrl, $projectDir) === 0) {
            $parsedUrl = substr($parsedUrl, strlen($projectDir));
        }

        // 4. Limpiar /public residual si el usuario accedió explícitamente a /public
        if (strpos($parsedUrl, '/public') === 0) {
            $parsedUrl = substr($parsedUrl, 7);
        }

        $path = '/' . trim($parsedUrl, '/');
        $method = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Convertir patrón de ruta con parámetros {id} a expresión regular
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Eliminar match completo

                $action = $route['action'];

                // Si es una función anónima (closure)
                if (is_callable($action)) {
                    call_user_func_array($action, $matches);
                    return;
                }

                // Si es formato 'Controller@method'
                if (is_string($action) && strpos($action, '@') !== false) {
                    [$controllerName, $methodName] = explode('@', $action);
                    $fullControllerClass = "App\\Controllers\\{$controllerName}";

                    if (!class_exists($fullControllerClass)) {
                        $this->handleNotFound("Controlador [{$fullControllerClass}] no encontrado.");
                        return;
                    }

                    $controllerInstance = new $fullControllerClass();

                    if (!method_exists($controllerInstance, $methodName)) {
                        $this->handleNotFound("El método [{$methodName}] no existe en el controlador [{$fullControllerClass}].");
                        return;
                    }

                    // Llamar al método pasando los parámetros extraídos de la URL
                    call_user_func_array([$controllerInstance, $methodName], $matches);
                    return;
                }
            }
        }

        // Si no hubo coincidencia de ruta, lanzar 404
        $this->handleNotFound("La ruta [{$method} {$path}] no existe en el sistema.");
    }

    /**
     * Manejador de error 404 No Encontrado
     */
    private function handleNotFound(string $message): void
    {
        http_response_code(404);
        $errorView = APP_PATH . '/Views/errors/404.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo "<div style='font-family: Arial, sans-serif; padding: 40px; text-align: center;'>";
            echo "<h1 style='color: #e53e3e;'>404 - Página no encontrada</h1>";
            echo "<p style='color: #718096;'>" . htmlspecialchars($message) . "</p>";
            echo "<a href='" . APP_URL . "' style='padding: 10px 20px; background: #3182ce; color: #fff; text-decoration: none; border-radius: 6px;'>Ir a Página Principal</a>";
            echo "</div>";
        }
        exit;
    }
}
