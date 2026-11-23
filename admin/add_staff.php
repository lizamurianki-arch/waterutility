<?php
session_start();
require_once __DIR__.'/../config/db.php';
/** @var mysqli $conn */
global $conn;
$pageTitle = "Add Staff - AquaTrack";
include __DIR__ . '/../includes/header.php';
if(!isset($_SESSION['staff_id']) || $_SESSION['role'] != 'Admin'){
    header("Location:/waterutility/admin/login.php");
    exit();
}

$error = "";
$success = "";
$first_name = $last_name = $email = $phone = $role = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name  = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email      = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone      = trim($_POST['phone']);
    $role       = isset($_POST['role']) ? $_POST['role'] : "";
    $password   = $_POST['password'];

    // --- VALIDATION ---
    if(empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($role) || empty($password)){
        $error = "All fields are required!";
    }
    // Role: only Technician allowed
    elseif($role != 'Technician'){
        $error = "Invalid role selected. Only Technician is allowed!";
    }
    // Phone: only numbers, exactly 10 digits
    elseif(!preg_match('/^[0-9]{10}$/', $phone)){
        $error = "Phone number must be exactly 10 digits and numbers only! Example: 0712345678";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address!";
    }
    else{
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM staff WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "Email already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $department_id = 2; // 2 = Technical - Technician always goes to Technical department
            $sql = "INSERT INTO staff (first_name, last_name, email, phone, role, password, department_id) VALUES ('$first_name','$last_name','$email','$phone','$role','$hashed','$department_id')";
            if(mysqli_query($conn, $sql)){
                $success = "Staff added successfully!";
                $first_name = $last_name = $email = $phone = $role = "";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<div class="dash-body">
    <div class="container-fluid py-4 px-4">
        <div class="row g-4">
            <div class="col-md-2">
                <div class="sidebar">
                    <p class="sidebar-title">Admin Menu</p>
                    <a href="/waterutility/admin/dashboard.php" class="sidebar-link sidebar-link-dark">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="/waterutility/admin/view_complaints.php" class="sidebar-link sidebar-link-dark">
                        <i class="fas fa-list"></i>All Complaints
                    </a>
                    <a href="/waterutility/admin/manage_complaints.php" class="sidebar-link sidebar-link-dark">
                        <i class="fas fa-tasks"></i>Manage Complaint
                    </a>
                    <a href="/waterutility/admin/add_staff.php" class="sidebar-link active sidebar-link-dark">
                        <i class="fas fa-user-plus"></i>Add Staff
                    </a>
                    <a href="/waterutility/admin/logout.php" class="sidebar-link logout">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                </div>
            </div>
            <div class="col-md-10">
                <div class="content-card">
                    <div class="card-header-bar">
                        <h5><i class="fas fa-user-plus me-2"></i>Add New Staff</h5>
                    </div>
                    <div class="p-4">
                        <?php if($error):?>
                            <div class="alert alert-danger"><?php echo $error;?></div>
                        <?php endif;?>
                        <?php if($success):?>
                            <div class="alert alert-success"><?php echo $success;?></div>
                        <?php endif;?>

                        <form method="POST" id="addStaffForm" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">First Name</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="First name" value="<?php echo $first_name;?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="Last name" value="<?php echo $last_name;?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Staff email" value="<?php echo $email;?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <input type="tel" name="phone" id="phone" class="form-control" placeholder="Phone number e.g. 0712345678"
                                    value="<?php echo $phone;?>"
                                    pattern="[0-9]{10}" maxlength="10" minlength="10"
                                    inputmode="numeric" autocomplete="off" required
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)">
                            
                                <div id="phoneError" class="text-danger mt-1" style="display:none;">Phone number must be exactly 10 digits and numbers only!</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="Technician" <?php if($role=='Technician') echo 'selected';?>>Technician</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>

                            <button type="submit" class="btn-action btn-dark w-100">Add Staff</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('addStaffForm').addEventListener('submit', function(e){
    var phone = document.getElementById('phone').value.trim();
    var phoneError = document.getElementById('phoneError');
    // Must be exactly 10 digits, numbers only
    if(!/^[0-9]{10}$/.test(phone)){
        e.preventDefault();
        phoneError.style.display = 'block';
        document.getElementById('phone').focus();
        return false;
    } else {
        phoneError.style.display = 'none';
    }
});
// Block typing letters in phone box
document.getElementById('phone').addEventListener('keypress', function(e){
    if(e.key < '0' || e.key > '9'){
        e.preventDefault();
    }
});
</script>

<?php require_once __DIR__.'/../includes/footer.php';?>