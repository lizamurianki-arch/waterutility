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

$filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where  = $filter ? "WHERE c.status='$filter'" : "";
$complaints = mysqli_query($conn,"SELECT c.*,cu.first_name,cu.last_name,s.first_name AS tech_first,s.last_name AS tech_last FROM complaints c LEFT JOIN customers cu ON c.customer_id=cu.customer_id LEFT JOIN staff s ON c.assigned_to=s.staff_id $where ORDER BY c.date_submitted DESC");
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
                <h5><i class="fas fa-list me-2"></i>All Complaints</h5>
                <div>
                    <a href="view_complaints.php" class="btn-action btn-dark">All</a>
                    <a href="view_complaints.php?status=Pending" class="btn-action btn-action" style="background:#856404;color:white;">Pending</a>
                    <a href="view_complaints.php?status=In Progress" class="btn-action btn-action" style="background:#055160;color:white;">In Progress</a>
                    <a href="view_complaints.php?status=Resolved" class="btn-action btn-green">Resolved</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr><th>Reference</th><th>Customer</th><th>Issue</th><th>Location</th><th>Assigned To</th><th>Status</th><th>Date</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($complaints) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($complaints)): ?>
                            <tr>
                                <td><strong style="color:#0077b6;"><?php echo $row['reference_number']; ?></strong></td>
                                <td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>
                                <td><?php echo $row['issue_type']; ?></td>
                                <td><?php echo $row['location']; ?></td>
                                <td><?php echo $row['tech_first'] ? $row['tech_first'].' '.$row['tech_last'] : '<span style="color:#999;">Not Assigned</span>'; ?></td>
                                <td><?php
                                    if($row['status']=='Pending') echo '<span class="badge-pending">Pending</span>';
                                    elseif($row['status']=='In Progress') echo '<span class="badge-progress">In Progress</span>';
                                    else echo '<span class="badge-resolved">Resolved</span>';
                                ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['date_submitted'])); ?></td>
                                <td><a href="/waterutility/admin/assign_complaints.php?id=<?php echo $row['complaint_id']; ?>" class="btn-action btn-dark">Assign</a></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No complaints found</td></tr>
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