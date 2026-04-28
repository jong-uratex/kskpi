<?php
require_once 'functions.php';
protect_page(); // Redirects to login if session is invalid

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$fullname = $_SESSION['fullname'];

// Data Fetching Logic
$total_emps = 0;
$pending_evals = 0;
$completed_evals = 0;
$my_eval = null;
$error_message = '';

try {
    if ($role == 'Admin') {
        // Admin Stats
        $total_emps = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'Employee'")->fetch_assoc()['count'];
        $pending_evals = $conn->query("SELECT COUNT(*) as count FROM evaluations WHERE status = 'Pending'")->fetch_assoc()['count'];
        $completed_evals = $conn->query("SELECT COUNT(*) as count FROM evaluations WHERE status = 'Approved'")->fetch_assoc()['count'];
    } else {
        // Employee Stats
        $stmt = $conn->prepare("SELECT weighted_score, status FROM evaluations WHERE employee_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $my_eval = $stmt->get_result()->fetch_assoc();
    }
} catch (mysqli_sql_exception $e) {
    $error_message = 'Unable to load dashboard data. Please refresh the page or contact your administrator.';
}
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>

<div class="content-wrapper">
    
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-navy font-weight-bold">Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card bg-navy shadow-lg mb-4 overflow-hidden position-relative" style="border-radius: 15px; border:none;">
                <div class="card-body p-4" style="z-index: 5; position: relative;">
                    <h2 class="display-5">Welcome, <strong><?php echo $fullname; ?></strong></h2>
                    <p class="lead">KS Cross Asia Technology | Performance Evaluation System 2026</p>
                </div>
                <div class="dashboard-bg-anim"></div>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-warning shadow-sm mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php if ($role == 'Admin'): ?>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info shadow">
                            <div class="inner">
                                <h3><?php echo $total_emps; ?></h3>
                                <p>Total Employees</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-warning shadow">
                            <div class="inner">
                                <h3><?php echo $pending_evals; ?></h3>
                                <p>Pending Reviews</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <a href="evaluate.php" class="small-box-footer">View Pending <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success shadow">
                            <div class="inner">
                                <h3><?php echo $completed_evals; ?></h3>
                                <p>Completed</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <a href="#" class="small-box-footer">View Records <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="col-md-7">
                        <div class="card card-outline card-primary shadow">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i> My Performance Summary</h3>
                            </div>
                            <div class="card-body text-center py-5">
                                <?php if ($my_eval): ?>
                                    <h4 class="text-muted mb-1">Current Weighted Score</h4>
                                    <h1 class="display-2 text-navy font-weight-bold"><?php echo $my_eval['weighted_score']; ?></h1>
                                    <div class="mt-3">
                                        <span class="badge badge-pill <?php echo ($my_eval['status'] == 'Approved') ? 'badge-success' : 'badge-warning'; ?> p-2 px-3">
                                            Status: <?php echo $my_eval['status']; ?>
                                        </span>
                                    </div>
                                    <a href="my_evaluation.php" class="btn btn-navy mt-4 px-4 shadow">View Detailed Report</a>
                                <?php else: ?>
                                    <i class="fas fa-file-invoice fa-4x text-gray-300 mb-3"></i>
                                    <p class="text-muted">No evaluation record available for this period yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card shadow">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold">System Announcements</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <i class="fas fa-info-circle text-info mr-2"></i> <b>Q1 Evaluation</b> <span class="float-right text-muted">Active</span>
                                    </li>
                                    <li class="list-group-item">
                                        <i class="fas fa-signature text-success mr-2"></i> <b>E-Signature</b> <span class="float-right text-muted">Required</span>
                                    </li>
                                </ul>
                                <p class="text-sm text-muted mt-3">
                                    Please ensure you review your scores and provide your digital signature before the end of the month.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            </div></section>
    </div>
<?php include 'footer.php'; ?>