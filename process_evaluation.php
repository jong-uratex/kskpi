<?php
require_once 'functions.php';
checkLogin();

if ($_SESSION['role'] !== 'Admin') {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: evaluate.php');
    exit();
}

$employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
$evaluation_date = isset($_POST['evaluation_date']) ? sanitize($_POST['evaluation_date']) : '';
$admin_remarks = isset($_POST['admin_remarks']) ? sanitize($_POST['admin_remarks']) : '';

$score_fields = [
    'productivity' => 'score_productivity',
    'quality' => 'score_quality',
    'attitude' => 'score_attitude',
    'teamwork' => 'score_teamwork',
    'kpi' => 'score_kpi'
];

$scores = [];
$error = '';

foreach ($score_fields as $post_key => $field_name) {
    if (!isset($_POST['score_' . $post_key])) {
        $error = 'All score fields are required.';
        break;
    }

    $score = intval($_POST['score_' . $post_key]);
    if ($score < 1 || $score > 5) {
        $error = 'Each score must be between 1 and 5.';
        break;
    }

    $scores[$field_name] = $score;
}

if ($employee_id <= 0) {
    $error = 'Please select a valid employee for evaluation.';
}

if (empty($evaluation_date) || !DateTime::createFromFormat('Y-m-d', $evaluation_date)) {
    $error = 'Please provide a valid evaluation date.';
}

if ($error) {
    header('Location: evaluate.php?error=' . urlencode($error));
    exit();
}

$weightQuery = "SELECT u.department, d.w_productivity, d.w_quality, d.w_attitude, d.w_teamwork, d.w_kpi 
                FROM users u 
                JOIN department_weights d ON u.department = d.department 
                WHERE u.id = ? LIMIT 1";

$stmt = $conn->prepare($weightQuery);
$stmt->bind_param('i', $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$weightRow = $result->fetch_assoc();

if (!$weightRow) {
    header('Location: evaluate.php?error=' . urlencode('Unable to determine department weights for this employee.'));
    exit();
}

$weighted_score = (
    $scores['score_productivity'] * $weightRow['w_productivity'] +
    $scores['score_quality'] * $weightRow['w_quality'] +
    $scores['score_attitude'] * $weightRow['w_attitude'] +
    $scores['score_teamwork'] * $weightRow['w_teamwork'] +
    $scores['score_kpi'] * $weightRow['w_kpi']
);

$insertQuery = "INSERT INTO evaluations (
                    employee_id,
                    evaluation_date,
                    score_productivity,
                    score_quality,
                    score_attitude,
                    score_teamwork,
                    score_kpi,
                    weighted_score,
                    admin_remarks,
                    employee_remarks,
                    signature_data,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, 'Pending')";

$insert = $conn->prepare($insertQuery);
$insert->bind_param(
    'isiiiiids',
    $employee_id,
    $evaluation_date,
    $scores['score_productivity'],
    $scores['score_quality'],
    $scores['score_attitude'],
    $scores['score_teamwork'],
    $scores['score_kpi'],
    $weighted_score,
    $admin_remarks
);

if ($insert->execute()) {
    header('Location: evaluation_list.php?success=' . urlencode('Evaluation saved successfully.'));
    exit();
}

header('Location: evaluate.php?error=' . urlencode('Failed to save evaluation. Please try again.'));
exit();
