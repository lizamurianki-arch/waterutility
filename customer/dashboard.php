<?php
session_start();
require_once __DIR__ . '/../config/db.php';
/**@var mysqli $conn */
global $conn;

$pageTitle = "Dashboard - AquaTrack";
include __DIR__ . '/../includes/header.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: /waterutility/customer/login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? 'Customer';

// COUNTS
$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM complaints WHERE customer_id='$customer_id'");
$total = mysqli_fetch_assoc($r)['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM complaints WHERE customer_id='$customer_id' AND status='Pending'");
$pending = mysqli_fetch_assoc($r)['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM complaints WHERE customer_id='$customer_id' AND status='In Progress'");
$in_progress = mysqli_fetch_assoc($r)['c'];

$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM complaints WHERE customer_id='$customer_id' AND status='Resolved'");
$resolved = mysqli_fetch_assoc($r)['c'];

// ASSIGNED TECH
$tech = null;
$tech_q = mysqli_query($conn, "SELECT c.complaint_id, c.description, c.issue_type, c.status, s.first_name, s.last_name, s.phone, s.email, s.role
    FROM complaints c
    LEFT JOIN staff s ON c.assigned_to = s.staff_id
    WHERE c.customer_id='$customer_id' AND c.assigned_to IS NOT NULL AND c.assigned_to != ''
    ORDER BY c.complaint_id DESC LIMIT 1");
if($tech_q && mysqli_num_rows($tech_q) > 0){
    $tech = mysqli_fetch_assoc($tech_q);
}
?>

<style>
.wrap{display:flex; gap:20px; padding:20px 30px;}
.sidebar{background:#fff; width:220px; border-radius:15px; padding:20px; height:fit-content;}
.sidebar small{color:#999; font-weight:700; letter-spacing:1px;}
.side-link{display:flex; align-items:center; gap:10px; padding:12px; border-radius:10px; text-decoration:none; color:#333; font-weight:600; margin-top:10px;}
.side-link.active{background:#00aeef; color:#fff;}
.side-link.logout{color:#e53e3e;}
.main{flex:1;}
.cards{display:grid; grid-template-columns:repeat(4,1fr); gap:15px; margin-top:20px;}
.card{background:#fff; border-radius:15px; padding:20px; display:flex; gap:15px; align-items:center;}
.card .icon{width:55px; height:55px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px;}
.card h2{margin:0; font-size:26px;}
.card p{margin:0; color:#777;}
.action-grid{display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:20px;}
.action{background:#fff; border-radius:15px; padding:30px; text-align:center;}
.action .icon{width:60px; height:60px; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:24px; margin:0 auto 15px;}
.btn-blue{background:#00aeef; color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none; font-weight:600; display:inline-block; border:none; cursor:pointer;}
.btn-green{background:#2e9e5b; color:#fff; padding:8px 16px; border-radius:8px; text-decoration:none; font-weight:600; display:inline-block;}
.btn-dark{background:#2d3748; color:#fff; padding:10px 18px; border-radius:8px; font-weight:600; border:none; cursor:pointer;}
.tech-box{background:#fff; border-radius:15px; padding:20px; margin-top:15px; display:none; align-items:center; gap:20px; border-left:5px solid #00aeef;}
.tech-box.show{display:flex;}
.tech-avatar{width:65px; height:65px; background:#2d3748; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:26px; flex-shrink:0;}
.tech-box h3{margin:0 0 5px;}
.tech-box p{margin:2px 0; color:#666; font-size:14px;}
.badge{padding:4px 10px; border-radius:20px; font-size:12px; font-weight:700; color:#fff;}
.badge-progress{background:#9b59b6;}
.badge-pending{background:#f39c12;}
</style>

<div class="wrap">
  <div class="sidebar">
    <small>MENU</small>
    <a class="side-link active" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a class="side-link" href="submit_complaint.php"><i class="fas fa-paper-plane"></i> Submit</a>
    <a class="side-link" href="view_complaint.php"><i class="fas fa-list"></i> My Complaints</a>
    <a class="side-link logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <div class="main">
    <h1 style="margin:0;">Welcome, <?php echo htmlspecialchars($customer_name); ?>! 👋</h1>

    <!-- BUTTON - ASSIGNED TECHNICIAN -->
    <div style="margin-top:20px;">
        <button class="btn-dark" onclick="document.getElementById('techDetails').classList.toggle('show')">
            <i class="fas fa-user-wrench"></i> Assigned Technician
        </button>
        <span style="color:#777; font-size:14px; margin-left:10px;">Click to view who is handling your complaint</span>
    </div>

    <!-- HIDDEN DETAILS - SHOWS ONLY AFTER CLICK -->
    <div id="techDetails" class="tech-box">
        <?php if($tech): ?>
            <div class="tech-avatar"><i class="fas fa-user-wrench"></i></div>
            <div style="flex:1;">
                <h3><i class="fas fa-wrench"></i> Your Assigned Technician</h3>
                <p><b><?php echo htmlspecialchars($tech['first_name'].' '.$tech['last_name']); ?></b> - <?php echo htmlspecialchars($tech['role']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($tech['phone']); ?> &nbsp; | &nbsp; <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($tech['email']); ?></p>
                <p>For Complaint #<?php echo $tech['complaint_id']; ?> - <?php echo htmlspecialchars($tech['issue_type'] ?? $tech['description']); ?></p>
            </div>
            <span class="badge <?php echo $tech['status']=='In Progress' ? 'badge-progress' : 'badge-pending'; ?>"><?php echo htmlspecialchars($tech['status']); ?></span>
        <?php else: ?>
            <div class="tech-avatar" style="background:#f39c12;"><i class="fas fa-user-clock"></i></div>
            <div>
                <h3>No Technician Assigned Yet</h3>
                <p>Your complaint is still Pending. A technician will be assigned soon and will appear here.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="cards">
        <div class="card"><div class="icon" style="background:#00aeef;"><i class="fas fa-file-alt"></i></div><div><h2><?php echo $total; ?></h2><p>Total</p></div></div>
        <div class="card"><div class="icon" style="background:#f39c12;"><i class="fas fa-clock"></i></div><div><h2><?php echo $pending; ?></h2><p>Pending</p></div></div>
        <div class="card"><div class="icon" style="background:#9b59b6;"><i class="fas fa-spinner"></i></div><div><h2><?php echo $in_progress; ?></h2><p>In Progress</p></div></div>
        <div class="card"><div class="icon" style="background:#2e9e5b;"><i class="fas fa-check-circle"></i></div><div><h2><?php echo $resolved; ?></h2><p>Resolved</p></div></div>
    </div>

    <div class="action-grid">
        <div class="action"><div class="icon" style="background:#00aeef;"><i class="fas fa-paper-plane"></i></div><h3>Submit New Complaint</h3><p style="color:#777;">Report a water supply issue</p><br><a class="btn-blue" href="submit_complaint.php">+ Submit Now</a></div>
        <div class="action"><div class="icon" style="background:#2e9e5b;"><i class="fas fa-search"></i></div><h3>Track My Complaints</h3><p style="color:#777;">View status of your complaints</p><br><a class="btn-green" href="view_complaint.php">View All</a></div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>