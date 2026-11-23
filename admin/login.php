<?php
session_start();
require_once __DIR__ . '/../config/db.php';

/** @var mysqli $conn */
global $conn;
$error = "";
$pageTitle = "Admin Login - AquaTrack";
$hideNav = true;
include __DIR__ . '/../includes/header.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $em = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pw = $_POST['password'];
    $r  = mysqli_query($conn, "SELECT * FROM staff WHERE email='$em'");
    if(mysqli_num_rows($r) == 1){
        $u = mysqli_fetch_assoc($r);
        if(password_verify($pw, $u['password'])){
            $_SESSION['staff_id']   = $u['staff_id'];
            $_SESSION['staff_name'] = $u['first_name'];
            $_SESSION['role']       = $u['role'];
            if($u['role'] == 'Admin'){
                header("Location: /waterutility/admin/dashboard.php");
            } else {
                header("Location: /waterutility/technician/dashboard.php");
            }
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-icon auth-icon-dark"><i class="fas fa-user-shield"></i></div>
        <h2 class="auth-title auth-title-dark">Staff Portal</h2>
        <p class="auth-sub">Sign in with your staff credentials</p>
        <?php if($error): ?><div class="alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <label class="field-label">Email</label>
            <div class="input-group">
                <div class="input-icon"><i class="fas fa-envelope"></i></div>
                <input type="email" name="email" placeholder="Staff email" required>
            </div>
            <label class="field-label">Password</label>
            <div class="input-group">
                <div class="input-icon"><i class="fas fa-lock"></i></div>
                <input type="password" name="password" placeholder="Staff password" required>
            </div>
            <button type="submit" class="btn-submit btn-submit-dark">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>
        <div class="auth-divider">or</div>
        <a href="/waterutility/customer/login.php" class="btn-portal">
            <i class="fas fa-user me-2"></i>Customer Login
        </a>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/waterutility/includes/footer.php'; ?>