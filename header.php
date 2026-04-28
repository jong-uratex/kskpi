<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSCAT | Performance Evaluation</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link text-navy"><strong>2026 Evaluation Period</strong></span>
            </li>
        </ul>
    </nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="dashboard.php" class="brand-link">
        <img src="https://impro.usercontent.one/appid/oneComWsb/domain/kscat-asia.com/media/kscat-asia.com/onewebmedia/kscat%20logo.png" 
             alt="KSCAT Logo" class="floating-logo" width="180">
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION['fullname']; ?></a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php if($_SESSION['role'] == 'Admin'): ?>
                <li class="nav-header">ADMINISTRATION</li>
                <li class="nav-item">
                    <a href="evaluate.php" class="nav-link">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>Perform Evaluation</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="evaluation_list.php" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Evaluation List</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_weights.php" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Settings</p>
                    </a>
                </li>
                <?php else: ?>
                <li class="nav-header">EMPLOYEE</li>
                <li class="nav-item">
                    <a href="my_evaluation.php" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>My Scores</p>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item mt-5">
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>