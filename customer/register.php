<?php
session_start();
require_once __DIR__.'/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "Register - AquaTrack";
include __DIR__ . '/../includes/header.php';

$error = "";
$success = "";
$first_name = $last_name = $email = $phone = $address = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name  = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email      = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone      = trim($_POST['phone']);
    $address    = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $password   = $_POST['password'];

    if(empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($address) || empty($password)){
        $error = "All fields are required!";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $phone)){
        $error = "Phone number must be exactly 10 digits and numbers only! Example: 0712345678";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address!";
    }
    else{
        $check = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "Email already exists! Please login.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO customers (first_name, last_name, email, phone, address, password) VALUES ('$first_name','$last_name','$email','$phone','$address','$hashed')";
            if(mysqli_query($conn, $sql)){
                $success = "Account created successfully! You can now login.";
                $first_name = $last_name = $email = $phone = $address = "";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<style>
/* FORCE CENTER - overrides your old auth-wrapper */
.register-page{
    min-height: 85vh;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: linear-gradient(135deg, #0077b6, #00b4d8) !important;
    padding: 40px 15px;
    margin: 0 !important;
    width: 100% !important;
}
.register-card{
    background: #fff !important;
    width: 100% !important;
    max-width: 560px !important;
    margin: 0 auto !important;
    padding: 35px 30px !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
</style>

<div class="register-page">
    <div class="register-card">
        <div class="text-center mb-3">
            <div style="width:80px;height:80px;background:#0096c7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                <i class="fas fa-user-plus fa-2x text-white"></i>
            </div>
        </div>
        <h2 class="text-center fw-bold" style="color:#0077b6;">Create Account</h2>
        <p class="text-center text-muted mb-4">Register to start reporting water issues</p>

        <?php if($error):?>
            <div class="alert alert-danger"><?php echo $error;?></div>
        <?php endif;?>
        <?php if($success):?>
            <div class="alert alert-success"><?php echo $success;?></div>
        <?php endif;?>

        <form method="POST" id="registerForm" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">First Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="first_name" class="form-control" placeholder="First name" value="<?php echo $first_name;?>" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Last Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="last_name" class="form-control" placeholder="Last name" value="<?php echo $last_name;?>" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" 
                    value="<?php echo $email;?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Phone</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="e.g. 0712345678"
                        value="<?php echo $phone;?>"
                        pattern="[0-9]{10}" maxlength="10" minlength="10"
                        inputmode="numeric" autocomplete="off" required
                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                </div>
            
                <div id="phoneError" class="text-danger mt-1" style="display:none;">Phone number must be exactly 10 digits and numbers only!</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="address" class="form-control" placeholder="Your area" value="<?php echo $address;?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>

            <button type="submit" class="btn-action btn-dark w-100">Create Account</button>
            <p class="text-center mt-3">Already have an account? <a href="/waterutility/customer/login.php">Login</a></p>
        </form>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e){
    var phone = document.getElementById('phone').value.trim();
    var phoneError = document.getElementById('phoneError');
    if(!/^[0-9]{10}$/.test(phone)){
        e.preventDefault();
        phoneError.style.display = 'block';
        document.getElementById('phone').focus();
        return false;
    } else {
        phoneError.style.display = 'none';
    }
});
document.getElementById('phone').addEventListener('keypress', function(e){
    if(e.key < '0' || e.key > '9'){
        e.preventDefault();
    }
});
</script>

<?php require_once __DIR__.'/../includes/footer.php';?>