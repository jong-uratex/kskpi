<?php
require_once __DIR__ . '/../config.php';

// Helper to secure inputs
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Redirect if not logged in
function protect_page() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function getEvaluationSummary($conn, $id) {
    $stmt = $conn->prepare("SELECT e.*, u.fullname, u.department FROM evaluations e JOIN users u ON e.employee_id = u.id WHERE e.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>