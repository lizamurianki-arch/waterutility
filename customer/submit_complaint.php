<?php
session_start();
require_once __DIR__ . '/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "Submit Complaint - AquaTrack";
include __DIR__ . '/../includes/header.php';
if(!isset($_SESSION['customer_id'])){
    header("Location: /waterutility/customer/login.php");
    exit();
}

$cid = $_SESSION['customer_id'];
$error = $success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $type = mysqli_real_escape_string($conn, $_POST['issue_type']);
    $loc  = mysqli_real_escape_string($conn, trim($_POST['location']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
    $ref  = 'WU-'.strtoupper(substr(uniqid(),7));
    $q = "INSERT INTO complaints (reference_number,issue_type,location,description,customer_id) VALUES ('$ref','$type','$loc','$desc',$cid)";
    if(mysqli_query($conn, $q)){
        $success = "Complaint submitted! Your reference number is: <strong>$ref</strong> — Save this to track your complaint.";
    } else {
        $error = "Failed: ".mysqli_error($conn);
    }
}
?>
<div class="page-header">
    <div class="container">
        <h2><i class="fas fa-paper-plane me-2"></i>Submit a Complaint</h2>
        <p>Describe your water issue and we'll get it resolved</p>
    </div>
</div>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-8">
            <div class="form-card">
                <h5 class="fw-bold text-primary mb-4"><i class="fas fa-edit me-2"></i>Complaint Details</h5>
                <?php if($error): ?><div class="alert-danger"><?php echo $error; ?></div><?php endif; ?>
                <?php if($success): ?><div class="alert-success"><?php echo $success; ?></div><?php endif; ?>
                <form method="POST">
                    <label class="field-label">Issue Type</label>
                    <select name="issue_type" class="form-field" required>
                        <option value="">-- Select Issue Type --</option>
                        <option value="Pipe Burst">🔧 Pipe Burst</option>
                        <option value="Water Shortage">💧 Water Shortage</option>
                        <option value="Contamination">⚠️ Contamination</option>
                        <option value="Billing Dispute">📄 Billing Dispute</option>
                        <option value="Leakage">🚰 Leakage</option>
                        <option value="Low Pressure">📉 Low Pressure</option>
                        <option value="No Water Supply">🚫 No Water Supply</option>
                        <option value="Other">❓ Other</option>
                    </select>
                    <label class="field-label">Location / Area</label>
                    <input type="text" name="location" class="form-field" placeholder="e.g. Chuka Town" required>
                    <label class="field-label">Description</label>
                    <textarea name="description" class="form-field" rows="5" placeholder="Describe the issue in detail..." required></textarea>
                    <button type="submit" class="btn-form-submit">
                        <i class="fas fa-paper-plane me-2"></i>Submit Complaint
                    </button>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="help-card mb-3">
                <h5><i class="fas fa-headset me-2"></i>Need Help?</h5>
                <div class="help-item">
                    <div class="help-icon"><i class="fas fa-phone"></i></div>
                    <div><h6>Call Us</h6><p>+254 700 000 000</p></div>
                </div>
                <div class="help-item">
                    <div class="help-icon"><i class="fas fa-envelope"></i></div>
                    <div><h6>Email Us</h6><p>support@aquatrack.co.ke</p></div>
                </div>
                <div class="help-item">
                    <div class="help-icon"><i class="fas fa-clock"></i></div>
                    <div><h6>Working Hours</h6><p>Mon–Fri: 8AM–5PM</p></div>
                </div>
            </div>
            <div class="help-card">
                <h5><i class="fas fa-info-circle me-2"></i>Quick Links</h5>
                <a href="/waterutility/customer/dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/waterutility/customer/view_complaint.php" class="sidebar-link"><i class="fas fa-list"></i> My Complaints</a>
            </div>
        </div>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/waterutility/includes/footer.php'; ?>