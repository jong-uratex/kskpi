<?php
require_once 'functions.php';
protect_page();

// Restrict access to Admins only
if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}

$message = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['weights'] as $dept => $values) {
        $w_prod = $values['productivity'];
        $w_qual = $values['quality'];
        $w_atti = $values['attitude'];
        $w_team = $values['teamwork'];
        $w_kpi  = $values['kpi'];

        $stmt = $conn->prepare("UPDATE department_weights SET 
            w_productivity = ?, w_quality = ?, w_attitude = ?, w_teamwork = ?, w_kpi = ? 
            WHERE department = ?");
        $stmt->bind_param("ddddds", $w_prod, $w_qual, $w_atti, $w_team, $w_kpi, $dept);
        $stmt->execute();
    }
    $message = "System weights updated successfully!";
}

// Fetch current weights
$weights_res = $conn->query("SELECT * FROM department_weights");
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-navy font-weight-bold">System Settings</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if($message): ?>
                <div class="alert alert-success alert-dismissible shadow">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="card card-primary card-outline shadow">
                <div class="card-header">
                    <h3 class="card-title">Department Evaluation Weights (2026)</h3>
                </div>
                <form method="POST" action="manage_weights.php">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped m-0">
                                <thead class="bg-navy">
                                    <tr>
                                        <th>Department</th>
                                        <th>Productivity</th>
                                        <th>Quality</th>
                                        <th>Attitude</th>
                                        <th>Teamwork</th>
                                        <th>Role KPI</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $weights_res->fetch_assoc()): ?>
                                    <tr>
                                        <td class="align-middle font-weight-bold"><?php echo $row['department']; ?></td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="1" class="form-control weight-input" 
                                                   name="weights[<?php echo $row['department']; ?>][productivity]" 
                                                   value="<?php echo $row['w_productivity']; ?>" oninput="validateSum(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="1" class="form-control weight-input" 
                                                   name="weights[<?php echo $row['department']; ?>][quality]" 
                                                   value="<?php echo $row['w_quality']; ?>" oninput="validateSum(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="1" class="form-control weight-input" 
                                                   name="weights[<?php echo $row['department']; ?>][attitude]" 
                                                   value="<?php echo $row['w_attitude']; ?>" oninput="validateSum(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="1" class="form-control weight-input" 
                                                   name="weights[<?php echo $row['department']; ?>][teamwork]" 
                                                   value="<?php echo $row['w_teamwork']; ?>" oninput="validateSum(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" max="1" class="form-control weight-input" 
                                                   name="weights[<?php echo $row['department']; ?>][kpi]" 
                                                   value="<?php echo $row['w_kpi']; ?>" oninput="validateSum(this)">
                                        </td>
                                        <td class="align-middle text-center font-weight-bold total-cell">
                                            1.00
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <p class="text-muted float-left mt-2">
                            <i class="fas fa-info-circle"></i> Note: The sum for each row must equal <strong>1.00</strong> (100%).
                        </p>
                        <button type="submit" class="btn btn-primary float-right shadow px-4">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card bg-light shadow-sm">
                <div class="card-body overflow-hidden position-relative">
                    <h5 class="font-weight-bold text-navy">How these weights work:</h5>
                    <p>Changing these values will affect all <strong>future</strong> evaluations. The system multiplies the raw score (1-5) by these weights to get the weighted total.</p>
                    <div class="dashboard-bg-anim" style="opacity: 0.05;"></div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
// Real-time validation to ensure weights sum to 1.00
function validateSum(input) {
    let row = input.closest('tr');
    let inputs = row.querySelectorAll('.weight-input');
    let total = 0;
    
    inputs.forEach(i => {
        total += parseFloat(i.value) || 0;
    });
    
    let totalCell = row.querySelector('.total-cell');
    totalCell.innerText = total.toFixed(2);
    
    if (total.toFixed(2) != "1.00") {
        totalCell.classList.add('text-danger');
        totalCell.classList.remove('text-success');
    } else {
        totalCell.classList.add('text-success');
        totalCell.classList.remove('text-danger');
    }
}

// Run validation on load
document.querySelectorAll('tr').forEach(tr => {
    let firstInput = tr.querySelector('.weight-input');
    if(firstInput) validateSum(firstInput);
});
</script>

<?php include 'footer.php'; ?>