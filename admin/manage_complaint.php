<?php
session_start();
require_once __DIR__ .'/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "Manage Staff - AquaTrack";
include_once __DIR__ .'/../includes/header.php';
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Admin'){
    header("Location: /waterutility/admin/login.php");
    exit();
}

$staff = mysqli_query($conn,"SELECT s.*,d.department_name FROM staff s LEFT JOIN departments d ON s.department_id=d.department_id ORDER BY s.role,s.first_name");
?>
<div class="dash-body">
<div class="container-fluid py-4 px-4">
<div class="row g-4">
    <div class="col-md-2">
        <div class="sidebar">
            <p class="sidebar-title">Admin Menu</p>
            <a href="/waterutility/admin/dashboard.php" class="sidebar-link sidebar-link-dark"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="/waterutility/admin/view_complaints.php" class="sidebar-link sidebar-link-dark"><i class="fas fa-list"></i> All Complaints</a>
            <a href="/waterutility/admin/manage_complaint.php" class="sidebar-link active sidebar-link-dark"><i class="fas fa-users"></i> Manage complaint</a>
            <a href="/waterutility/admin/add_staff.php" class="sidebar-link sidebar-link-dark"><i class="fas fa-user-plus"></i> Add Staff</a>
            <a href="/waterutility/admin/logout.php" class="sidebar-link logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="col-md-10">
        <div class="content-card">
            <div class="card-header-bar">
                <h5><i class="fas fa-users me-2"></i>All Staff Members</h5>
                <a href="/waterutility/admin/add_staff.php" class="btn-action btn-dark"><i class="fas fa-user-plus me-1"></i>Add New Staff</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Department</th><th>Joined</th></tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($staff) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($staff)): ?>
                            <tr>
                                <td><strong><?php echo $row['first_name'].' '.$row['last_name']; ?></strong></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['role']=='Admin' ? '<span class="badge-admin">Admin</span>' : '<span class="badge-tech">Technician</span>'; ?></td>
                                <td><?php echo $row['department_name']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No staff found</td></tr>
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