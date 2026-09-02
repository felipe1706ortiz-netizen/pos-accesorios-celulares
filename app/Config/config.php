<?php
/**
 * ==============================================================================
 * ARCHIVO DE CONFIGURACIÓN GLOBAL
 * ==============================================================================
 * Sistema POS e Inventario - Tienda de Accesorios para Celulares
 * Define constantes del entorno, conexión a MySQL y rutas del sistema.
 * ==============================================================================
 */

// Evitar acceso directo
defined('APP_PATH') or define('APP_PATH', dirname(__DIR__));
defined('ROOT_PATH') or define('ROOT_PATH', dirname(APP_PATH));

// Configuración de visualización de errores (Cambiar a false en producción o vía ENV)
define('ENVIRONMENT', getenv('APP_ENV') ?: 'development'); // 'development' o 'production'

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', APP_PATH . '/logs/error.log');
}

// Zona horaria predeterminada
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'America/Bogota');

// ------------------------------------------------------------------------------
// PARÁMETROS DE CONEXIÓN A BASE DE DATOS (MySQL o Supabase / PostgreSQL)
// ------------------------------------------------------------------------------
// Soporte para URL de conexión directa (ej: Supabase Connection String)
$dbUrl = getenv('DATABASE_URL') ?: getenv('SUPABASE_DB_URL');
$dbConfig = [
    'driver'  => getenv('DB_DRIVER') ?: 'mysql',
    'host'    => getenv('DB_HOST') ?: 'localhost',
    'port'    => getenv('DB_PORT') ?: '3306',
    'name'    => getenv('DB_NAME') ?: 'pos_accesorios',
    'user'    => getenv('DB_USER') ?: 'root',
    'pass'    => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    'sslmode' => getenv('DB_SSLMODE') ?: 'require'
];

if ($dbUrl) {
    $parsedUrl = parse_url($dbUrl);
    if ($parsedUrl) {
        $scheme = $parsedUrl['scheme'] ?? 'mysql';
        $dbConfig['driver'] = (str_starts_with($scheme, 'postgres') || $scheme === 'pgsql') ? 'pgsql' : 'mysql';
        $dbConfig['host']   = $parsedUrl['host'] ?? $dbConfig['host'];
        $dbConfig['port']   = (string)($parsedUrl['port'] ?? ($dbConfig['driver'] === 'pgsql' ? '5432' : '3306'));
        $dbConfig['user']   = isset($parsedUrl['user']) ? urldecode($parsedUrl['user']) : $dbConfig['user'];
        $dbConfig['pass']   = isset($parsedUrl['pass']) ? urldecode($parsedUrl['pass']) : $dbConfig['pass'];
        $dbConfig['name']   = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : $dbConfig['name'];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            if (isset($queryParams['sslmode'])) {
                $dbConfig['sslmode'] = $queryParams['sslmode'];
            }
        }
    }
}

define('DB_DRIVER',  $dbConfig['driver']);
define('DB_HOST',    $dbConfig['host']);
define('DB_PORT',    $dbConfig['port']);
define('DB_NAME',    $dbConfig['name']);
define('DB_USER',    $dbConfig['user']);
define('DB_PASS',    $dbConfig['pass']);
define('DB_CHARSET', $dbConfig['charset']);
define('DB_SSLMODE', $dbConfig['sslmode']);

// ------------------------------------------------------------------------------
// URL BASE Y RUTAS DE DIRECTORIOS
// ------------------------------------------------------------------------------
// Detección automática de protocolo y URL base para XAMPP
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptName, '/');

// Si se ejecuta desde public/, normalizar la URL
if (substr($baseUrl, -7) === '/public') {
    $baseUrl = substr($baseUrl, 0, -7);
}

define('APP_URL', $baseUrl);
define('PUBLIC_URL', $baseUrl . '/public');
define('ASSETS_URL', PUBLIC_URL . '/assets');
define('CSS_URL', PUBLIC_URL . '/css');
define('JS_URL', PUBLIC_URL . '/js');

// ------------------------------------------------------------------------------
// PARÁMETROS DE LA APLICACIÓN
// ------------------------------------------------------------------------------
define('APP_NAME', 'POS Celulares & Accesorios');
define('APP_VERSION', '1.0.0');
define('CURRENCY_SYMBOL', '$');
define('DEFAULT_PAGE_LIMIT', 15);

// Parámetros de Seguridad y Sesión
define('SESSION_NAME', 'POS_ACCESSORIES_SESSION');
define('SESSION_LIFETIME', 28800); // 8 horas en segundos
define('CSRF_TOKEN_KEY', 'csrf_token_pos');
