<?php
require_once 'functions.php';
protect_page();

// Restrict access to Admins only
if ($_SESSION['role'] !== 'Admin') {
    header("Location: my_evaluation.php");
    exit();
}

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $fullname = sanitize($_POST['fullname'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $department = sanitize($_POST['department'] ?? '');

    if ($fullname === '' || $username === '' || $password === '' || $department === '') {
        $error_msg = 'Please provide fullname, username, password, and department for the new employee.';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $check->bind_param('s', $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error_msg = 'That username is already taken. Please choose a different one.';
        } else {
            $insert = $conn->prepare("INSERT INTO users (fullname, username, password, department, role) VALUES (?, ?, ?, ?, 'Employee')");
            $insert->bind_param('ssss', $fullname, $username, $password, $department);

            if ($insert->execute()) {
                $success_msg = 'New employee has been added successfully.';
            } else {
                $error_msg = 'Unable to add employee at this time. Please try again later.';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_evaluation'], $_POST['evaluation_id'])) {
    $evaluation_id = intval($_POST['evaluation_id']);

    if ($evaluation_id > 0) {
        $update = $conn->prepare(
            "UPDATE evaluations SET 
                score_productivity = 0,
                score_quality = 0,
                score_attitude = 0,
                score_teamwork = 0,
                score_kpi = 0,
                weighted_score = 0,
                status = 'Pending',
                signature_data = NULL,
                employee_remarks = NULL
             WHERE id = ?"
        );
        $update->bind_param('i', $evaluation_id);

        if ($update->execute()) {
            $success_msg = 'Evaluation score has been reset and approval status has been updated.';
        } else {
            $error_msg = 'Unable to reset the evaluation at this time. Please try again later.';
        }
    } else {
        $error_msg = 'Invalid evaluation selected for reset.';
    }
}

if (isset($_GET['success']) && !$success_msg) {
    $success_msg = htmlspecialchars($_GET['success']);
}
if (isset($_GET['error']) && !$error_msg) {
    $error_msg = htmlspecialchars($_GET['error']);
}

$departments = [];
$deptResult = $conn->query("SELECT department FROM department_weights ORDER BY department");
while ($deptRow = $deptResult->fetch_assoc()) {
    $departments[] = $deptRow['department'];
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
                    <button type="button" class="btn btn-success shadow-sm mr-2" data-toggle="modal" data-target="#addEmployeeModal">
                        <i class="fas fa-user-plus mr-1"></i> New Employee
                    </button>
                    <a href="evaluate.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus mr-1"></i> New Evaluation</a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($success_msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_msg; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php elseif ($error_msg): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_msg; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
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
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Reset this employee evaluation score and unapprove it?');">
                                            <input type="hidden" name="evaluation_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="reset_evaluation" value="1">
                                            <button type="submit" class="btn btn-danger btn-sm ml-1">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        </form>
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

<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add New Employee</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="evaluation_list.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department" class="form-control" required>
                            <option value="">-- Select Department --</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="text-muted small">The new employee will be created with the role <strong>Employee</strong>.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="add_employee" value="1">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Employee</button>
                </div>
            </form>
        </div>
    </div>
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