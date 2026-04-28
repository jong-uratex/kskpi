<?php
// Display errors for debugging (Disable this once the site is live!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost'; // Usually 'localhost' on Hostinger/cPanel
$db   = 'u390249810_kskpidb';
$user = 'u390249810_kskpiu';
$pass = 'KS*kpi*2026';

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // If connection fails, this will show you why
    die("Database Connection Failed: " . $e->getMessage());
}

if(!isset($_SESSION)) {
    session_start();
}
?>