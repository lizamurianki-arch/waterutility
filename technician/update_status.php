<?php
session_start();
require_once __DIR__ . '/../config/db.php';
/** @var mysqli $conn */
global $conn;

$pageTitle = "Update Status - AquaTrack";

// Security check: Ensure technician is logged in
if (!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Technician') {
    header("Location: login.php");
    exit();
}

$sid = $_SESSION['staff_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$error = "";
$success = "";

// Fetch complaint details
$query = mysqli_query($conn, "SELECT c.*, cu.first_name, cu.last_name FROM complaints c LEFT JOIN customers cu ON c.customer_id = cu.customer_id WHERE c.complaint_id = $id");
$complaint = mysqli_fetch_assoc($query);

if (!$complaint) {
    header("Location: view_assigned.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $details = mysqli_real_escape_string($conn, trim($_POST['resolution_details']));
    
    $date_sql = ($status == 'Resolved') ? "'" . date('Y-m-d H:i:s') . "'" : "NULL";
    
    // Update complaint status
    if (mysqli_query($conn, "UPDATE complaints SET status='$status', date_resolved=$date_sql WHERE complaint_id=$id")) {
        if ($status == 'Resolved' && !empty($details)) {
            mysqli_query($conn, "INSERT INTO resolutions(resolution_details, complaint_id, staff_id) VALUES('$details', $id, $sid)");
        }
        $success = "Status updated successfully!";
        // Refresh complaint data
        $query = mysqli_query($conn, "SELECT c.*, cu.first_name, cu.last_name FROM complaints c LEFT JOIN customers cu ON c.customer_id = cu.customer_id WHERE c.complaint_id = $id");
        $complaint = mysqli_fetch_assoc($query);
    } else {
        $error = "Error updating status: " . mysqli_error($conn);
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="dash-body">
    <div class="container-fluid py-4 px-4" style="max-width: 800px;">
        <div class="card shadow-sm border-0 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-edit me-2"></i> Update Complaint #<?php echo htmlspecialchars($complaint['reference_number'] ?? $id); ?></h3>
                <a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Back</a>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="bg-light p-3 rounded mb-4">
                <p class="mb-1"><strong>Customer:</strong> <?php echo htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']); ?></p>
                <p class="mb-1"><strong>Issue Type:</strong> <?php echo htmlspecialchars($complaint['issue_type']); ?></p>
                <p class="mb-0"><strong>Location:</strong> <?php echo htmlspecialchars($complaint['location']); ?></p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="Pending" <?php echo ($complaint['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?php echo ($complaint['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Resolved" <?php echo ($complaint['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="resolution_details" class="form-label fw-bold">Resolution Details / Remarks</label>
                    <textarea name="resolution_details" id="resolution_details" rows="4" class="form-control" placeholder="Enter notes about the repair..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>