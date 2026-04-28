<?php
include __DIR__ . '/functions.php';
checkLogin();

if ($_SESSION['role'] !== 'Admin') {
    header("Location: dashboard.php");
    exit();
}

// Fetch employees and weights for the form
$employees = $conn->query("SELECT id, fullname, department FROM users WHERE role = 'Employee'");
$weights_res = $conn->query("SELECT * FROM department_weights");
$weights = [];
while($w = $weights_res->fetch_assoc()) { $weights[$w['department']] = $w; }
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Employee Performance Evaluation</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>
            <div class="card card-primary card-outline">
                <form action="process_evaluation.php" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Select Employee</label>
                                <select name="employee_id" id="employee_select" class="form-control" required onchange="updateWeights()">
                                    <option value="">-- Choose Employee --</option>
                                    <?php while($emp = $employees->fetch_assoc()): ?>
                                        <option value="<?= $emp['id'] ?>" data-dept="<?= $emp['department'] ?>">
                                            <?= $emp['fullname'] ?> (<?= $emp['department'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Evaluation Date</label>
                                <input type="date" name="evaluation_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <hr>

                        <table class="table table-bordered mt-3">
                            <thead class="bg-navy">
                                <tr>
                                    <th>Category</th>
                                    <th>Weight</th>
                                    <th>Score (1-5)</th>
                                    <th>Weighted Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $categories = [
                                    'productivity' => 'Productivity',
                                    'quality' => 'Quality of Work',
                                    'attitude' => 'Work Attitude',
                                    'teamwork' => 'Teamwork',
                                    'kpi' => 'Role-Specific KPI'
                                ];
                                foreach($categories as $key => $label): ?>
                                <tr>
                                    <td><?= $label ?></td>
                                    <td id="w_<?= $key ?>_display">0.00</td>
                                    <td>
                                        <input type="number" name="score_<?= $key ?>" class="form-control score-input" 
                                               min="1" max="5" step="1" required oninput="calculateTotal()">
                                    </td>
                                    <td id="weighted_<?= $key ?>">0.00</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="3" class="text-right">Final Score:</th>
                                    <th id="final_score">0.00</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="form-group">
                            <label>Admin Remarks</label>
                            <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Enter performance feedback..."></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary shadow">Submit Evaluation</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
const weightData = <?= json_encode($weights) ?>;

function updateWeights() {
    const select = document.getElementById('employee_select');
    const dept = select.options[select.selectedIndex].getAttribute('data-dept');
    if(weightData[dept]) {
        document.getElementById('w_productivity_display').innerText = weightData[dept].w_productivity;
        document.getElementById('w_quality_display').innerText = weightData[dept].w_quality;
        document.getElementById('w_attitude_display').innerText = weightData[dept].w_attitude;
        document.getElementById('w_teamwork_display').innerText = weightData[dept].w_teamwork;
        document.getElementById('w_kpi_display').innerText = weightData[dept].w_kpi;
        calculateTotal();
    }
}

function calculateTotal() {
    const select = document.getElementById('employee_select');
    const dept = select.options[select.selectedIndex].getAttribute('data-dept');
    if(!dept) return;

    let final = 0;
    const cats = ['productivity', 'quality', 'attitude', 'teamwork', 'kpi'];
    
    cats.forEach(cat => {
        const score = document.getElementsByName('score_' + cat)[0].value || 0;
        const weight = weightData[dept]['w_' + cat];
        const weighted = (score * weight).toFixed(2);
        document.getElementById('weighted_' + cat).innerText = weighted;
        final += parseFloat(weighted);
    });
    
    document.getElementById('final_score').innerText = final.toFixed(2);
}
</script>

<?php include 'footer.php'; ?>