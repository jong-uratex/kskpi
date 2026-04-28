<?php
require_once 'functions.php';
protect_page(); 

$user_id = $_SESSION['user_id'];

// Fetch the latest evaluation
$query = "SELECT e.*, d.w_productivity, d.w_quality, d.w_attitude, d.w_teamwork, d.w_kpi 
          FROM evaluations e 
          JOIN users u ON e.employee_id = u.id 
          JOIN department_weights d ON u.department = d.department
          WHERE e.employee_id = ? ORDER BY e.evaluation_date DESC LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$eval = $stmt->get_result()->fetch_assoc();

// Handle Signature Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_approval'])) {
    $remarks = sanitize($_POST['employee_remarks']);
    $signature = $_POST['signature']; 
    $eval_id = $_POST['eval_id'];

    $update = $conn->prepare("UPDATE evaluations SET employee_remarks = ?, signature_data = ?, status = 'Approved' WHERE id = ? AND employee_id = ?");
    $update->bind_param("ssii", $remarks, $signature, $eval_id, $user_id);
    
    if ($update->execute()) {
        header("Refresh:0");
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?> <div class="content-wrapper"> <section class="content pt-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    
                    <?php if(!$eval): ?>
                        <div class="card shadow-lg text-center p-5">
                            <i class="fas fa-file-invoice fa-4x text-gray-200 mb-3"></i>
                            <h3 class="text-muted">No evaluation record found.</h3>
                        </div>
                    <?php else: ?>

                        <div class="card bg-navy shadow-lg mb-4 overflow-hidden position-relative" style="border-radius: 15px; border:none;">
                            <div class="card-body p-4" style="z-index: 5; position: relative;">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <img src="https://impro.usercontent.one/appid/oneComWsb/domain/kscat-asia.com/media/kscat-asia.com/onewebmedia/kscat%20logo.png" 
                                             alt="KSCAT Logo" class="floating-logo" style="max-height: 80px; background: white; padding: 10px; border-radius: 50%;">
                                    </div>
                                    <div class="col-md-10">
                                        <h2 class="display-5 mb-1">Hello, <strong><?php echo $_SESSION['fullname']; ?></strong></h2>
                                        <p class="lead mb-0">KS Cross Asia Technology | Performance Evaluation Portal 2026</p>
                                    </div>
                                </div>
                            </div>
                            <div class="dashboard-bg-anim"></div>
                        </div>

                        <div class="row">
                            </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>