<?php
session_start();
require_once __DIR__ . '/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "My Complaints - AquaTrack";
include __DIR__ . '/../includes/header.php';
if(!isset($_SESSION['customer_id'])){
    header("Location: /waterutility/customer/login.php");
    exit();
}

$cid = $_SESSION['customer_id'];
$complaints = mysqli_query($conn,"SELECT * FROM complaints WHERE customer_id=$cid ORDER BY date_submitted DESC");
?>
<div class="page-header">
    <div class="container">
        <h2><i class="fas fa-list me-2"></i>My Complaints</h2>
        <p>Track the status of all your submitted complaints</p>
    </div>
</div>
<div class="dash-body">
<div class="container-fluid py-4 px-4">
<div class="row g-4">
    <div class="col-md-2">
        <div class="sidebar">
            <p class="sidebar-title">Menu</p>
            <a href="/waterutility/customer/dashboard.php" class="sidebar-link"><i class="fas fa-home"></i> Dashboard</a>
            <a href="/waterutility/customer/submit_complaint.php" class="sidebar-link"><i class="fas fa-paper-plane"></i> Submit</a>
            <a href="/waterutility/customer/view_complaint.php" class="sidebar-link active"><i class="fas fa-list"></i> My Complaints</a>
            <a href="/waterutility/customer/logout.php" class="sidebar-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="col-md-10">
        <div class="content-card">
            <div class="card-header-bar">
                <h5><i class="fas fa-list me-2"></i>All My Complaints</h5>
                <a href="/waterutility/customer/submit_complaint.php" class="btn-action btn-blue"><i class="fas fa-plus me-1"></i>New Complaint</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr><th>Reference</th><th>Issue Type</th><th>Location</th><th>Description</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($complaints) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($complaints)): ?>
                            <tr>
                                <td><strong style="color:#0077b6;"><?php echo $row['reference_number']; ?></strong></td>
                                <td><?php echo $row['issue_type']; ?></td>
                                <td><?php echo $row['location']; ?></td>
                                <td><?php echo substr($row['description'],0,50).'...'; ?></td>
                                <td><?php
                                    if($row['status']=='Pending') echo '<span class="badge-pending">Pending</span>';
                                    elseif($row['status']=='In Progress') echo '<span class="badge-progress">In Progress</span>';
                                    else echo '<span class="badge-resolved">✓ Resolved</span>';
                                ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['date_submitted'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x d-block mb-3"></i>
                                No complaints yet.<br>
                                <a href="/waterutility/customer/submit_complaint.php" class="btn-action btn-blue mt-3 d-inline-block">Submit Your First Complaint</a>
                            </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/waterutility/includes/footer.php'; ?>