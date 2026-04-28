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
    $signature = $_POST['signature'] ?? '';
    $eval_id = $_POST['eval_id'];

    // Validate signature is not empty
    if (empty($signature)) {
        $error_msg = "Please provide a digital signature before approving.";
    } else {
        $update = $conn->prepare("UPDATE evaluations SET employee_remarks = ?, signature_data = ?, status = 'Approved' WHERE id = ? AND employee_id = ?");
        $update->bind_param("ssii", $remarks, $signature, $eval_id, $user_id);
        
        if ($update->execute()) {
            $success_msg = "Evaluation approved successfully!";
            header("Refresh:2");
        } else {
            $error_msg = "Failed to save approval. Please try again.";
        }
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?> <div class="content-wrapper"> <section class="content pt-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    
                    <?php if(isset($success_msg)): ?>
                        <div class="alert alert-success shadow alert-dismissible fade show" role="alert">
                            <?php echo $success_msg; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($error_msg)): ?>
                        <div class="alert alert-warning shadow alert-dismissible fade show" role="alert">
                            <?php echo $error_msg; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    <?php endif; ?>

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
                            <div class="col-md-7">
                                <div class="card card-primary card-outline shadow">
                                    <div class="card-header"><h3 class="card-title font-weight-bold">Score Breakdown</h3></div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped m-0">
                                            <thead class="text-navy">
                                                <tr><th>Category</th><th>Score</th><th>Weight</th><th>Weighted</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr><td>Productivity</td><td><?php echo $eval['score_productivity']; ?></td><td><?php echo ($eval['w_productivity']*100); ?>%</td><td><?php echo number_format($eval['score_productivity'] * $eval['w_productivity'], 2); ?></td></tr>
                                                <tr><td>Quality of Work</td><td><?php echo $eval['score_quality']; ?></td><td><?php echo ($eval['w_quality']*100); ?>%</td><td><?php echo number_format($eval['score_quality'] * $eval['w_quality'], 2); ?></td></tr>
                                                <tr><td>Work Attitude</td><td><?php echo $eval['score_attitude']; ?></td><td><?php echo ($eval['w_attitude']*100); ?>%</td><td><?php echo number_format($eval['score_attitude'] * $eval['w_attitude'], 2); ?></td></tr>
                                                <tr><td>Teamwork</td><td><?php echo $eval['score_teamwork']; ?></td><td><?php echo ($eval['w_teamwork']*100); ?>%</td><td><?php echo number_format($eval['score_teamwork'] * $eval['w_teamwork'], 2); ?></td></tr>
                                                <tr><td>Role-Specific KPI</td><td><?php echo $eval['score_kpi']; ?></td><td><?php echo ($eval['w_kpi']*100); ?>%</td><td><?php echo number_format($eval['score_kpi'] * $eval['w_kpi'], 2); ?></td></tr>
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th colspan="3" class="text-right">FINAL RATING:</th>
                                                    <th class="text-navy h4 font-weight-bold"><?php echo $eval['weighted_score']; ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="card shadow-sm mt-3">
                                    <div class="card-body">
                                        <h5 class="text-navy font-weight-bold"><i class="fas fa-comment-dots mr-2"></i> Admin Feedback:</h5>
                                        <p class="text-muted italic">"<?php echo nl2br($eval['admin_remarks']); ?>."</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <?php if($eval['status'] == 'Pending'): ?>
                                    <div class="card card-warning card-outline shadow">
                                        <div class="card-header"><h3 class="card-title font-weight-bold">Acknowledge & Sign</h3></div>
                                        <div class="card-body">
                                            <form action="my_evaluation.php" method="POST" id="sig-form">
                                                <input type="hidden" name="eval_id" value="<?php echo $eval['id']; ?>">
                                                
                                                <div class="form-group">
                                                    <label>Your Clarifications (Optional)</label>
                                                    <textarea name="employee_remarks" class="form-control" rows="3" placeholder="Enter remarks..."></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label>Digital Signature</label>
                                                    <canvas id="sig-canvas" width="400" height="160" style="border: 2px dashed #cbd5e0; width: 100%; background: #fff; cursor: crosshair; display: block;"></canvas>
                                                    <textarea id="sig-data" name="signature" class="d-none"></textarea>
                                                </div>

                                                <button type="button" class="btn btn-sm btn-default" onclick="clearSignature()">Clear</button>
                                                <button type="submit" name="submit_approval" class="btn btn-success btn-block mt-3 shadow" onclick="return validateSignature()">
                                                    Confirm Approval
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="card bg-success shadow text-center py-5">
                                        <div class="card-body">
                                            <i class="fas fa-check-double fa-4x mb-3"></i>
                                            <h4>Evaluation Approved</h4>
                                            <p>Signed on <?php echo date('M d, Y', strtotime($eval['evaluation_date'])); ?></p>
                                            <?php if($eval['signature_data']): ?>
                                                <img src="<?php echo htmlspecialchars($eval['signature_data'], ENT_QUOTES, 'UTF-8'); ?>" alt="Signature" style="max-width: 180px; background: white; border-radius: 5px; padding: 5px;">
                                            <?php else: ?>
                                                <p class="text-muted mt-3">Signature data not available</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>