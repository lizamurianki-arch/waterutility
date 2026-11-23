<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'AquaTrack'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.0/css/all.css">
    <link href="/waterutility/css/style.css?v=2" rel="stylesheet">
</head>
<body>
<nav class="navbar">
    <a class="nav-brand" href="/waterutility/index.php">
        <div class="nav-logo"><i class="fas fa-tint"></i></div>
        <div>
            <span class="nav-brand-name">AquaTrack</span>
            <span class="nav-brand-sub">Water Utility System</span>
        </div>
    </a>
    <div class="nav-links">
        <?php if(isset($hideNav) && $hideNav == true): ?>
            <!-- LOGIN PAGE: SHOW NOTHING -->
        <?php elseif(isset($_SESSION['staff_id'])): ?>
            <!-- STAFF / ADMIN: SHOW NOTHING ON TOP - left sidebar already has it -->
        <?php elseif(isset($_SESSION['customer_id'])): ?>
            <a href="/waterutility/customer/dashboard.php" class="nav-link"><i class="fas fa-home me-1"></i>Dashboard</a>
            <a href="/waterutility/customer/submit_complaint.php" class="nav-link"><i class="fas fa-paper-plane me-1"></i>Submit</a>
            <a href="/waterutility/customer/view_complaint.php" class="nav-link"><i class="fas fa-list me-1"></i>My Complaints</a>
            <a href="/waterutility/customer/logout.php" class="nav-link nav-btn"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        <?php else: ?>
            <a href="/waterutility/index.php" class="nav-link">Home</a>
            <a href="/waterutility/customer/login.php" class="nav-link">Track</a>
            <a href="/waterutility/customer/login.php" class="nav-link nav-btn"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
        <?php endif; ?>
    </div>
</nav>