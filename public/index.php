<?php
/**
 * ==============================================================================
 * FRONT CONTROLLER (Punto Único de Entrada de la Aplicación)
 * ==============================================================================
 * Carga la configuración, inicializa el Autoloader PSR-4, arranca la sesión
 * y delega el enrutamiento al Router central.
 * ==============================================================================
 */

// Definir constante base de la aplicación
define('APP_PATH', dirname(__DIR__) . '/app');
define('ROOT_PATH', dirname(__DIR__));

// Cargar configuración global
require_once APP_PATH . '/Config/config.php';

// ------------------------------------------------------------------------------
// AUTOLOADER PSR-4 (Carga automática de clases en el namespace 'App\' y 'PHPMailer\')
// ------------------------------------------------------------------------------
spl_autoload_register(function ($class) {
    // Carga de PHPMailer
    if (str_starts_with($class, 'PHPMailer\\PHPMailer\\')) {
        $className = str_replace('PHPMailer\\PHPMailer\\', '', $class);
        $file = APP_PATH . '/Lib/PHPMailer/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Inicializar sesión segura
use App\Core\Session;
use App\Core\Router;
use App\Core\Auth;

Session::start();

// ------------------------------------------------------------------------------
// REGISTRO DE RUTAS DEL SISTEMA POS
// ------------------------------------------------------------------------------
$router = new Router();

// Ruta Raíz
$router->get('/', function() {
    if (Auth::check()) {
        header('Location: ' . (Auth::isAdmin() ? APP_URL . '/dashboard' : APP_URL . '/pos'));
    } else {
        header('Location: ' . APP_URL . '/login');
    }
    exit;
});

// 1. MÓDULO DE AUTENTICACIÓN Y REGISTRO
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/registro', 'AuthController@showRegistro');
$router->post('/registro', 'AuthController@registro');
$router->get('/verificar-email', 'AuthController@verificarEmail');
$router->post('/reenviar-verificacion', 'AuthController@reenviarVerificacion');

// 2. DASHBOARD / RESUMEN GENERAL (ADMIN)
$router->get('/dashboard', 'DashboardController@index');

// 3. MÓDULO POS (CORE FACTURACIÓN Y VENTA RÁPIDA)
$router->get('/pos', 'PosController@index');
$router->get('/pos/buscar-producto', 'PosController@buscarProducto'); // API AJAX para escáner / modal F2
$router->post('/pos/procesar-venta', 'PosController@procesarVenta');  // API AJAX transaccional
$router->get('/pos/imprimir/{id}', 'PosController@imprimirTicket');

// 4. MÓDULO DE INVENTARIO Y MOVIMIENTOS
$router->get('/inventario', 'InventarioController@index');
$router->post('/inventario/guardar', 'InventarioController@guardar');
$router->post('/inventario/actualizar/{id}', 'InventarioController@actualizar');
$router->post('/inventario/ajuste-rapido', 'InventarioController@ajusteRapido');
$router->get('/inventario/eliminar/{id}', 'InventarioController@eliminar');
$router->get('/inventario/kardex', 'InventarioController@kardex');
$router->get('/inventario/kardex/{id}', 'InventarioController@kardex');
$router->get('/inventario/categorias', 'InventarioController@categorias');
$router->post('/inventario/categorias/guardar', 'InventarioController@guardarCategoria');
$router->get('/inventario/buscar-ajax', 'InventarioController@buscarAjax');

// 5. MÓDULO DE HISTORIAL DE FACTURAS
$router->get('/facturas', 'FacturaController@index');
$router->get('/facturas/detalle/{id}', 'FacturaController@detalle');
$router->post('/facturas/anular/{id}', 'FacturaController@anular');

// 6. MÓDULO DE MOVIMIENTOS DE CAJA Y ARQUEO / CIERRE
$router->get('/caja', 'CajaController@index');
$router->get('/caja/apertura', 'CajaController@apertura');
$router->post('/caja/abrir', 'CajaController@abrir');
$router->get('/caja/movimientos', 'CajaController@movimientos');
$router->post('/caja/guardar-movimiento', 'CajaController@guardarMovimiento');
$router->get('/caja/cierre', 'CajaController@cierre');
$router->post('/caja/cerrar', 'CajaController@cerrar');
$router->get('/caja/ticket/{id}', 'CajaController@ticketCierre');
$router->get('/caja/historial', 'CajaController@historial');
$router->get('/caja/pulso-gaveta', 'CajaController@pulsoGaveta');
$router->get('/caja/estado-ajax', 'CajaController@estadoGavetaAjax');

// Despachar la petición actual
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
