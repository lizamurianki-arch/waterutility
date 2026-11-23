<?php
session_start();
require_once __DIR__ . '/../config/db.php';
/** @var mysqli $conn */
global $conn;

$pageTitle = "Login - AquaTrack";
$hideNav = true; // REMOVE Dashboard / Submit / My Complaints, LEAVE only Logout
include __DIR__ . '/../includes/header.php';
$error = $success = "";
if(isset($_GET['registered'])) $success = "Account created! Please login.";
if(isset($_GET['logout'])) $success = "Logged out successfully.";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $em = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pw = $_POST['password'];
    $r  = mysqli_query($conn, "SELECT * FROM customers WHERE email='$em'");
    if(mysqli_num_rows($r) == 1){
        $u = mysqli_fetch_assoc($r);
        if(password_verify($pw, $u['password'])){
            $_SESSION['customer_id']   = $u['customer_id'];
            $_SESSION['customer_name'] = $u['first_name'];
            header("Location: /waterutility/customer/dashboard.php");
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
        <div class="auth-icon"><i class="fas fa-user"></i></div>
        <h2 class="auth-title">Welcome Back!</h2>
        <p class="auth-sub">Sign in to your customer account</p>
        <?php if($error): ?><div class="alert-danger"><?php echo $error; ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert-success"><?php echo $success; ?></div><?php endif; ?>
        <form method="POST">
            <label class="field-label">Email</label>
            <div class="input-group">
                <div class="input-icon"><i class="fas fa-envelope"></i></div>
                <input type="email" name="email" placeholder="Your email" required>
            </div>
            <label class="field-label">Password</label>
            <div class="input-group">
                <div class="input-icon"><i class="fas fa-lock"></i></div>
                <input type="password" name="password" placeholder="Your password" required>
            </div>
            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>
        <div class="auth-divider">or</div>
        <div class="auth-link">No account? <a href="/waterutility/customer/register.php">Register here</a></div>
        <a href="/waterutility/admin/login.php" class="btn-portal">
            <i class="fas fa-user-shield me-2"></i>Staff / Admin Login
        </a>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/waterutility/includes/footer.php'; ?>