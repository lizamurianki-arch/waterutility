<?php
session_start();
require_once __DIR__ .'/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle="Assigned Complaints-AquaTrack";
include __DIR__ . '/../includes/header.php';
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Technician'){
    header("Location:/waterutility/technician/login.php");
    exit();
}
$sid = $_SESSION['staff_id'];
$filter = isset($_GET['status'])?mysqli_real_escape_string($conn,$_GET['status']):"";
$where = $filter ?"AND c.status='$filter'":"";
$complaints = mysqli_query($conn,"SELECT c.*,cu.first_name,cu.last_name FROM complaints c LEFT JOIN customers cu ON c.customer_id=cu.customer_id WHERE c.assigned_to=$sid $where ORDER BY c.date_submitted DESC");
?>
<div class="dash-body">
    <div class="container-fluid py-4 px-4">
        <div class="row g-4">
            <div class="col-md-2">
            <div class="sidebar">
                <p clas="sidebar-title">Tech Menu</p>
                <a href="/waterutility/technician/dashboard.php"class="sidebar-link sidebar-link-dark">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
</a>
<a href="/waterutility/technician/view_assigned.php"class="sidebar-link active sidebar-link-dark">
<i class="fas fa-wrench"></i>Assigned
</a>
<a href="/waterutility/technician/logout.php"class="sidebar-link logout">
    <i class="fas fa-sign-out-alt"></i>
</a>
</div>
            </div>
            <div class="col-md-10">
                <div class="content-card">
                    <div class="card-header-bar">
                        <h5><i class="fas fa-wrench me-2"></i>My Assigned Complaints</h5>
                        <div>
                            <a href="view_assigned.php"class="btn-action btn-dark">All</a>
                            <a href="view_assigned.php?status=Pending"class="btn-action"style="background:#856404;color:white;">Pending</a>
<a href="view_assigned.php?status=In Progress"class="btn-action"style="background:#055160;color:white;">In Progress</a>
<a href="view_assigned.php?status=Resolved"class="btn-action btn-green">Resolved</a>
</div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Issue Type</th>
                                    <th>Location</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
</tr>
</thead>
</tbody>
<?php if(mysqli_num_rows($complaints)>0):?>
    <?php while($row=mysqli_fetch_assoc($complaints)):?>
        <tr>
            <td><strong style="color:#0077b6;"><?php echo$row['reference_number'];?></strong></td>
            <td><?php echo $row['issue_type'];?></td>
            <td><?php echo $row['location'];?></td>
            <td><?php echo substr($row['description'],0,40).'...';?></td>
            <td><?php
            if($row['status']=='Pending')echo'<span class="badge-pending">Pending</span>';
            elseif($row['status']=='In Progress')echo'<span class="badge-progress">In Progress</span>';
            else echo'<span class="badge-resolved">Resolved</span>';
            ?></td>
            <td><?php echo date('d/m/Y',strtotime($row['date_submitted']));?></td>
            <td>
                <a href="/waterutility/technician/update_status.php?id=<?php echo $row['complaint_id'];?>"class="btn-action btn-dark">
                    <i class="fas fa-edit me-1"></i>Update
    </a>
    </td>
    </tr>
    <?php endwhile;?>
    <?php else:?>
        <tr>
            <td colspan="7"class="text-center py-4 text-muted">
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
    <?php require_once __DIR__.'/../includes/footer.php';?>