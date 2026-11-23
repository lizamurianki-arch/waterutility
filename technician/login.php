<?php
session_start();
require_once __DIR__ . '/../config/db.php';
/** @var mysqli $conn */
global $conn;

$pageTitle = "Technician Login - AquaTrack";
include __DIR__ . '/../includes/header.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $em = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pw = $_POST['password'];
    
    // Query to find the technician by email and role
    $r = mysqli_query($conn, "SELECT * FROM staff WHERE email='$em' AND role='Technician'");
    
    if ($r && mysqli_num_rows($r) == 1) {
        $u = mysqli_fetch_assoc($r); // Fetch user record from database
        
        // Verify the password against the secure database hash
        if (password_verify($pw, $u['password'])) {
            $_SESSION['staff_id'] = $u['staff_id'];
            $_SESSION['staff_name'] = $u['first_name'];
            $_SESSION['role'] = $u['role'];
            
            // Redirect using JavaScript for reliable client-side navigation
            echo "<script>window.location.href='dashboard.php';</script>";
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No technician account found with that email.";
    }
}
?>

<div class="auth-page" style="background: linear-gradient(135deg,#1a1a2e,#16213e,#0f3460);">
    <div class="auth-card">
        <div class="auth-icon auth-icon-dark">
            <i class="fas fa-wrench"></i>
        </div>
        
        <h2 class="auth-title auth-title-dark">Technician Portal</h2>
        <p class="auth-sub">Sign in to view your assigned complaints</p>
        
        <!-- Display error message if login fails -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="color: #ff6b6b; text-align: center; margin-bottom: 15px; font-weight: bold;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <div class="input-icon"><i class="fas fa-envelope"></i></div>
                <input type="email" name="email" placeholder="Your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="input-group">
                <div class="input-icon"><i class="fas fa-lock"></i></div>
                <input type="password" name="password" placeholder="Your password" required>
            </div>
            
            <button type="submit" class="btn btn-auth btn-dark">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>