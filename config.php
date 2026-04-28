<?php
// Display errors for debugging (Disable this once the site is live!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost'; // Usually 'localhost' on Hostinger/cPanel
$db   = 'u390249810_kskpidb';
$user = 'u390249810_kskpiu';
$pass = 'KS*kpi*2026';
$connectTimeout = 5;

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

if (!function_exists('mysqli_init')) {
    die('MySQLi extension is not enabled. Please install/enable the PHP mysqli extension.');
}

$conn = mysqli_init();
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, $connectTimeout);

try {
    $conn->real_connect($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Database Connection Failed: ' . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>