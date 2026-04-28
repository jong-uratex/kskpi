<?php
require_once 'functions.php';
protect_page();

// Restrict access to Admins only
if ($_SESSION['role'] !== 'Admin') {
    header("Location: my_evaluation.php");
    exit();
}

// Fetch all evaluations with employee names
$query = "SELECT e.*, u.fullname, u.department 
          FROM evaluations e 
          JOIN users u ON e.employee_id = u.id 
          ORDER BY e.evaluation_date DESC";
$result = $conn->query($query);
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0 text-navy font-weight-bold">Evaluation Records</h1></div>
                <div class="col-sm-6 text-right">
                    <a href="evaluate.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus mr-1"></i> New Evaluation</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="align-middle"><?php echo date('M d, Y', strtotime($row['evaluation_date'])); ?></td>
                                    <td class="align-middle font-weight-bold text-navy"><?php echo $row['fullname']; ?></td>
                                    <td class="align-middle"><span class="badge badge-secondary"><?php echo $row['department']; ?></span></td>
                                    <td class="align-middle text-center font-weight-bold"><?php echo number_format($row['weighted_score'], 2); ?></td>
                                    <td class="align-middle text-center">
                                        <span class="badge <?php echo ($row['status'] == 'Approved') ? 'badge-success' : 'badge-warning'; ?> px-3">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="align-middle text-right">
                                        <button type="button" class="btn btn-info btn-sm view-details" 
                                                data-name="<?php echo $row['fullname']; ?>"
                                                data-dept="<?php echo $row['department']; ?>"
                                                data-total="<?php echo $row['weighted_score']; ?>"
                                                data-prod="<?php echo $row['score_productivity']; ?>"
                                                data-qual="<?php echo $row['score_quality']; ?>"
                                                data-atti="<?php echo $row['score_attitude']; ?>"
                                                data-team="<?php echo $row['score_teamwork']; ?>"
                                                data-kpi="<?php echo $row['score_kpi']; ?>"
                                                data-admin-rem="<?php echo htmlspecialchars($row['admin_remarks']); ?>"
                                                data-emp-rem="<?php echo htmlspecialchars($row['employee_remarks']); ?>"
                                                data-sig="<?php echo $row['signature_data']; ?>">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title">Employee Performance Report</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-6">
                        <label class="small text-uppercase mb-0">Employee Name</label>
                        <h4 id="m-name" class="text-navy font-weight-bold"></h4>
                    </div>
                    <div class="col-6 text-right">
                        <label class="small text-uppercase mb-0">Weighted Final Score</label>
                        <h2 id="m-total" class="text-primary font-weight-bold"></h2>
                    </div>
                </div>
                
                <table class="table table-bordered">
                    <thead class="bg-light text-center">
                        <tr><th>Category</th><th>Score (1-5)</th></tr>
                    </thead>
                    <tbody class="text-center" id="m-score-table">
                        </tbody>
                </table>

                <div class="row mt-4">
                    <div class="col-md-6 border-right">
                        <label class="font-weight-bold">Admin Remarks</label>
                        <p id="m-admin-rem" class="text-muted"></p>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">Employee Remarks</label>
                        <p id="m-emp-rem" class="text-muted"></p>
                    </div>
                </div>

                <div class="text-center mt-4 border-top pt-3" id="sig-section">
                    <label class="d-block">Digital Signature Confirmation</label>
                    <img id="m-sig-img" src="" style="max-height: 120px; border: 1px solid #ddd; padding: 5px;">
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>