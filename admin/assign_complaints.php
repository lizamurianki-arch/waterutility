<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/** @var mysqli $conn */
global $conn;

$pageTitle = "Dashboard - AquaTrack";
include __DIR__ . '/../includes/header.php';

if(!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Admin'){
    header("Location: /waterutility/admin/login.php");
    exit();
}

$id = intval($_GET['id']);
$complaint = mysqli_fetch_assoc(mysqli_query($conn,"SELECT c.*,cu.first_name,cu.last_name FROM complaints c LEFT JOIN customers cu ON c.customer_id=cu.customer_id WHERE c.complaint_id=$id"));
$technicians = mysqli_query($conn,"SELECT * FROM staff WHERE role='Technician' ORDER BY first_name");
$error = $success = "";

if(!$complaint){
    header("Location: /waterutility/admin/view_complaints.php");
    exit();
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $tech_input = trim($_POST['technician_name'] ?? '');
    
    if(empty($tech_input)){
        $error = "Please select a technician.";
    } else {
        $safe = mysqli_real_escape_string($conn, $tech_input);
        // Find technician by name you typed
        $find = mysqli_query($conn, "SELECT staff_id FROM staff WHERE CONCAT(first_name,' ',last_name) LIKE '%$safe%' OR first_name LIKE '%$safe%' OR last_name LIKE '%$safe%' LIMIT 1");
        
        if(mysqli_num_rows($find) > 0){
            $t_row = mysqli_fetch_assoc($find);
            $tech_id = intval($t_row['staff_id']);
            
            // Admin only assigns - status auto becomes In Progress, Admin does NOT pick Resolved
            if(mysqli_query($conn,"UPDATE complaints SET assigned_to=$tech_id, status='In Progress' WHERE complaint_id=$id")){
                $success = "Complaint assigned successfully to $tech_input!";
                $complaint = mysqli_fetch_assoc(mysqli_query($conn,"SELECT c.*,cu.first_name,cu.last_name FROM complaints c LEFT JOIN customers cu ON c.customer_id=cu.customer_id WHERE c.complaint_id=$id"));
            } else {
                $error = "Failed: ".mysqli_error($conn);
            }
        } else {
            $error = "Technician '$tech_input' not found in staff table.";
        }
    }

}
?>
<div class="dash-body">
<div class="container-fluid py-4 px-4">
<div class="row g-4">
    <div class="col-md-2">
        <div class="sidebar">
            <p class="sidebar-title">Admin Menu</p>
            <a href="/waterutility/admin/dashboard.php" class="sidebar-link sidebar-link-dark"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="/waterutility/admin/view_complaints.php" class="sidebar-link active sidebar-link-dark"><i class="fas fa-list"></i> All Complaints</a>
            <a href="/waterutility/admin/manage_complaint.php" class="sidebar-link sidebar-link-dark"><i class="fas fa-users"></i> Manage complaint</a>
            <a href="/waterutility/admin/add_staff.php" class="sidebar-link sidebar-link-dark"><i class="fas fa-user-plus"></i> Add Staff</a>
            <a href="/waterutility/admin/logout.php" class="sidebar-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="col-md-10">
        <div class="content-card">
            <div class="card-header-bar">
                <h5><i class="fas fa-user-check me-2"></i>Assign Technician</h5>
                <a href="/waterutility/admin/view_complaints.php" class="btn-action btn-dark"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
            <?php if($error): ?><div class="alert-danger"><?php echo $error; ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert-success"><?php echo $success; ?></div><?php endif; ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div style="background:#f8fbff;border-radius:12px;padding:20px;border:1px solid #e0eef8;">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-file-alt me-2"></i>Complaint Details</h6>
                        <p class="mb-2"><strong>Reference:</strong> <span style="color:#0077b6;"><?php echo $complaint['reference_number']; ?></span></p>
                        <p class="mb-2"><strong>Customer:</strong> <?php echo $complaint['first_name'].' '.$complaint['last_name']; ?></p>
                        <p class="mb-2"><strong>Issue:</strong> <?php echo $complaint['issue_type']; ?></p>
                        <p class="mb-2"><strong>Location:</strong> <?php echo $complaint['location']; ?></p>
                        <p class="mb-2"><strong>Description:</strong> <?php echo $complaint['description']; ?></p>
                        <p class="mb-0"><strong>Status:</strong>
                            <?php
                            if($complaint['status']=='Pending') echo '<span class="badge-pending ms-1">Pending</span>';
                            elseif($complaint['status']=='In Progress') echo '<span class="badge-progress ms-1">In Progress</span>';
                            else echo '<span class="badge-resolved ms-1">Resolved</span>';
                            ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="POST">
                        
                        <input type="text" name="technician_name" list="techList" class="form-field" placeholder="Type technician name e.g. John..." required value="<?php 
                        if(!empty($complaint['assigned_to'])){
                            $ass = mysqli_fetch_assoc(mysqli_query($conn,"SELECT CONCAT(first_name,' ',last_name) as fullname FROM staff WHERE staff_id=".intval($complaint['assigned_to'])));
                            echo $ass['fullname'] ?? '';
                        }
                        ?>">
                        <datalist id="techList">
                            <?php 
                            // Reset pointer and list all staff as suggestions
                            mysqli_data_seek($technicians, 0);
                            while($t = mysqli_fetch_assoc($technicians)): 
                            ?>
                            <option value="<?php echo $t['first_name'].' '.$t['last_name']; ?>">
                            <?php endwhile; ?>
                        </datalist>
                        
                        <button type="submit" class="btn-form-submit" style="background:linear-gradient(135deg,#1a1a2e,#16213e);">
                            <i class="fas fa-user-check me-2"></i>Assign Complaint
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/waterutility/includes/footer.php'; ?>