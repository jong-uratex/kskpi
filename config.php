<?php
// Prevent direct access to the config file when loaded through a web request.
if (php_sapi_name() !== 'cli' && basename($_SERVER['SCRIPT_NAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit('Forbidden');
}

// Use environment variables in production and default values for local development.
define('APP_DEBUG', getenv('APP_DEBUG') === '1');

ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('display_startup_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Database credentials and connection settings.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'u390249810_kskpidb');
define('DB_USER', getenv('DB_USER') ?: 'u390249810_kskpiu');
define('DB_PASS', getenv('DB_PASS') ?: 'KS*kpi*2026');
define('DB_CONNECT_TIMEOUT', (int) (getenv('DB_CONNECT_TIMEOUT') ?: 5));
define('DB_CHARSET', 'utf8mb4');

define('DB_DEBUG', APP_DEBUG);

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

if (!function_exists('mysqli_init')) {
    exit('MySQLi extension is not enabled. Please install/enable the PHP mysqli extension.');
}

$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, DB_CONNECT_TIMEOUT);

try {
    $conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset(DB_CHARSET);
} catch (mysqli_sql_exception $e) {
    if (APP_DEBUG) {
        exit('Database Connection Failed: ' . $e->getMessage());
    }

    exit('Database connection failed. Please contact the administrator.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>