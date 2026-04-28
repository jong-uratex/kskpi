<?php
require_once 'functions.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT id, password, fullname, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Check password (Plain text for now as per your sample data)
            if ($password === $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['role'] = $row['role'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Username not found.";
        }
    } catch (Exception $e) {
        $error = "System Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KSCAT | Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo"><img src="https://impro.usercontent.one/appid/oneComWsb/domain/kscat-asia.com/media/kscat-asia.com/onewebmedia/kscat%20logo.png" class="floating-logo" width="200"></div>
    <div class="card card-outline card-primary">
        <div class="card-body login-card-body">
            <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form action="login.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>