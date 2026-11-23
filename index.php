<?php
$pageTitle = "Home - AquaTrack";
include('includes/header.php');
?>

<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1>Report Water Issues.<br>Track Resolution.<br>Stay Informed.</h1>
                <p>AquaTrack makes it easy to report water supply problems, track complaint status in real time, and ensure every issue gets resolved efficiently.</p>
                <a href="/waterutility/customer/register.php" class="btn-hero">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
                <a href="/waterutility/customer/login.php" class="btn-hero-outline">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>
        </div>
    </div>
</section>
<!-- HOW IT WORKS -->
<section class="section section-gray" id="how-it-works">
    <div class="container">
        <h2 class="section-title">How It Works</h2>
        <p class="section-sub">Four simple steps from complaint to resolution</p>
        <div class="row">
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-num">1</div>
                    <h5>Create Account</h5>
                    <p>Register with your name, email and phone number to access the system.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-num">2</div>
                    <h5>Submit Complaint</h5>
                    <p>Describe your water issue, provide your location and submit your complaint.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-num">3</div>
                    <h5>Gets Assigned</h5>
                    <p>Admin receives your complaint and assigns it to the right technician immediately.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="step-card">
                    <div class="step-num">4</div>
                    <h5>Issue Resolved</h5>
                    <p>Technician fixes the issue and updates the status. You can track progress anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Report a Water Issue?</h2>
        <p>Create your free account and submit your first complaint in under 2 minutes.</p>
        <a href="/waterutility/customer/register.php" class="btn-cta">
            <i class="fas fa-user-plus me-2"></i>Get Started 
        </a>
        <a href="/waterutility/admin/login.php" class="btn-cta-outline">
            <i class="fas fa-user-shield me-2"></i>Staff Login
        </a>
    </div>
</section>

<?php include('includes/footer.php'); ?>