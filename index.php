<?php
require_once __DIR__ . '/../config.php';

if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'Admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: emp_dashboard.php"); // Redirects employees here
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>