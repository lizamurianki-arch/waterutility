<?php
session_start();
require_once __DIR__.'/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "Technician Dashboard - AquaTrack";
include __DIR__ . '/../includes/header.php';
$error = "";
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Technician'){
    header("Location:/waterutility/technician/login.php");
    exit();
}
$sid =$_SESSION['staff_id'];
$name =$_SESSION['staff_name'];
$total =mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*)c FROM complaints WHERE assigned_to=$sid"))['c'];
$pending =mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*)c FROM complaints WHERE assigned_to=$sid AND status='Pending'"))['c'];
$progress =mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*)c FROM complaints WHERE assigned_to=$sid AND status='In Progress'"))['c'];
$resolved =mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*)c FROM complaints WHERE assigned_to=$sid AND status='Resolved'"))['c'];
$recent = mysqli_query($conn, "SELECT c.*, cu.first_name, cu.last_name FROM complaints c LEFT JOIN customers cu ON c.customer_id = cu.customer_id WHERE c.assigned_to=$sid ORDER BY c.date_submitted DESC LIMIT 5");
?>
<div class="dash-body">
    <div class="container-fluid py-4 px-4">
        <div class="row g-4">
            <div class="col-md-2">
                <div class="sidebar">
                    <p class="sidebar-title">Tech Menu</p>
                    <a href="/waterutility/technician/dashboard.php" class="sidebar-link active sidebar-link-dark">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="/waterutility/technician/view_assigned.php" class="sidebar-link sidebar-link-dark">
                        <i class="fas fa-wrench"></i>Assigned
                    </a>
                    <a href="/waterutility/technician/logout.php" class="sidebar-link logout">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </div>
            </div>
            <div class="col-md-10">
                <h4 class="fw-bold mb-4">Welcome, <?php echo $name;?>!👋</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon si-blue"><i class="fas fa-file-alt"></i></div>
                            <div class="stat-info"><h3><?php echo $total;?></h3><p>Assigned</p></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon si-orange"><i class="fas fa-clock"></i></div>
                            <div class="stat-info"><h3><?php echo $pending;?></h3><p>Pending</p></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon si-blue"><i class="fas fa-spinner"></i></div>
                            <div class="stat-info"><h3><?php echo $progress;?></h3><p>In Progress</p></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <div class="stat-icon si-green"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-info"><h3><?php echo $resolved;?></h3><p>Resolved</p></div>
                        </div>
                    </div>
                </div>
                <div class="content-card">
                    <div class="card-header-bar">
                        <h5><i class="fas fa-wrench me-2"></i>My Assigned Complaint</h5>
                        <a href="/waterutility/technician/view_assigned.php" class="btn-action btn-dark">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Issue Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($recent)>0):?>
                                    <?php while($row = mysqli_fetch_assoc($recent)):?>
                                        <tr>
                                            <td><strong style="color:#0077b6;"><?php echo $row['reference_number'];?></strong></td>
                                            <td><?php echo $row['issue_type'];?></td>
                                            <td><?php echo $row['location'];?></td>
                                            <td><?php
                                            if($row['status']=='Pending')echo'<span class="badge-pending">Pending</span>';
                                            elseif($row['status']=='In Progress')echo'<span class="badge-progress">In Progress</span>';
                                            else echo'<span class="badge-resolved">Resolved</span>';
                                            ?></td>
                                            <td><?php echo date('d/m/Y',strtotime($row['date_submitted']));?></td>
                                            <td>
                                                <a href="/waterutility/technician/update_status.php?id=<?php echo $row['complaint_id'];?>" class="btn-action btn-dark">
                                                    <i class="fas fa-edit me-1"></i>Update
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile;?>
                                <?php else:?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                            No complaints assigned yet
                                        </td>
                                    </tr>
                                <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__.'/../includes/footer.php';?>