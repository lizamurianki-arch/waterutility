<?php
session_start();
require_once __DIR__ .'/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "Manage Technicians - AquaTrack";
include_once __DIR__ .'/../includes/header.php';
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Admin'){
    header("Location: /waterutility/admin/login.php");
    exit();
}

// DELETE technician
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ? AND role = 'Technician'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: /waterutility/admin/manage_technicians.php?deleted=1");
    exit();
}

// GET all technicians
$result = $conn->query("SELECT * FROM staff WHERE role = 'Technician' ORDER BY staff_id DESC");
$count = $result->num_rows;
?>

<div style="padding:20px; max-width:900px; margin:auto;">
    <h2>Manage Technicians (<?php echo $count; ?>)</h2>
    <p>
        <a href="dashboard.php">← Back to Dashboard</a> |
        <a href="add_staff.php">+ Add New Technician</a>
    </p>

    <?php if(isset($_GET['deleted'])): ?>
        <p style="color:green;">Technician deleted successfully!</p>
    <?php endif; ?>

    <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden;">
        <tr style="background:#eaf6fb; color:#0097b2;">
            <th style="padding:12px; text-align:left;">ID</th>
            <th style="padding:12px; text-align:left;">Full Name</th>
            <th style="padding:12px; text-align:left;">Email</th>
            <th style="padding:12px; text-align:left;">Phone</th>
            <th style="padding:12px; text-align:left;">Action</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:12px;"><?php echo $row['staff_id']; ?></td>
            <td style="padding:12px;"><?php echo $row['full_name']; ?></td>
            <td style="padding:12px;"><?php echo $row['email']; ?></td>
            <td style="padding:12px;"><?php echo $row['phone'] ?? '-'; ?></td>
            <td style="padding:12px;">
                <a href="manage_technicians.php?delete=<?php echo $row['staff_id']; ?>"
                   onclick="return confirm('Are you sure you want to delete <?php echo $row['full_name']; ?>?');"
                   style="background:#ff4757; color:#fff; padding:6px 14px; border-radius:6px; text-decoration:none; font-size:13px;">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>

        <?php if($count == 0): ?>
        <tr><td colspan="5" style="padding:12px; text-align:center;">No technicians yet. <a href="add_staff.php">Add one</a></td></tr>
        <?php endif; ?>
    </table>
</div>

<?php include_once __DIR__ .'/../includes/footer.php'; ?>