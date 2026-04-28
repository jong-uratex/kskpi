<?php
require_once 'functions.php';
protect_page();

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// Fetch latest evaluation for the specific employee
$stmt = $conn->prepare("SELECT weighted_score, status, evaluation_date FROM evaluations WHERE employee_id = ? ORDER BY evaluation_date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$my_eval = $stmt->get_result()->fetch_assoc();
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-navy font-weight-bold">Employee Dashboard</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card bg-navy shadow-lg mb-4 overflow-hidden position-relative" style="border-radius: 15px; border:none;">
                <div class="card-body p-4" style="z-index: 5; position: relative;">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <img src="https://impro.usercontent.one/appid/oneComWsb/domain/kscat-asia.com/media/kscat-asia.com/onewebmedia/kscat%20logo.png" 
                                 alt="KSCAT Logo" class="floating-logo" style="max-height: 80px;">
                        </div>
                        <div class="col-md-10">
                            <h2 class="display-5 mb-1">Welcome, <strong><?php echo $fullname; ?></strong></h2>
                            <p class="lead mb-0">KS Cross Asia Technology | Performance Tracking 2026</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-bg-anim"></div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-info shadow">
                        <div class="inner">
                            <h3><?php echo $my_eval ? number_format($my_eval['weighted_score'], 2) : 'N/A'; ?></h3>
                            <p>Latest Performance Score</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="my_evaluation.php" class="small-box-footer">View Detailed Report <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="small-box <?php echo (isset($my_eval['status']) && $my_eval['status'] == 'Approved') ? 'bg-success' : 'bg-warning'; ?> shadow">
                        <div class="inner">
                            <h3><?php echo $my_eval['status'] ?? 'No Record'; ?></h3>
                            <p>Approval Status</p>
                        </div>
                        <div class="icon">
                            <i class="fas <?php echo (isset($my_eval['status']) && $my_eval['status'] == 'Approved') ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                        </div>
                        <a href="my_evaluation.php" class="small-box-footer">Go to Evaluation <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h3 class="card-title font-weight-bold">Quick Tasks</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Review Scores</b> <span class="float-right"><i class="fas fa-eye text-primary"></i></span>
                                </li>
                                <li class="list-group-item">
                                    <b>Digital Signature</b> <span class="float-right"><i class="fas fa-pen-nib text-navy"></i></span>
                                </li>
                            </ul>
                            <p class="text-sm text-muted">Please ensure all pending evaluations are signed to finalize your record.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php include 'footer.php'; ?>