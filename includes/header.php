<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/eproject';
$nav_script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COVID-19 Booking System | Premium Care</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
</head>

<body>
    <header class="main-header">
        <div class="container header-container">
            <div class="logo">
                <a href="<?php echo $base_url; ?>/index.php">
                    <span class="medical-cross">✚</span> VaxiCare
                </a>
            </div>

            <nav class="main-nav">
                <input type="checkbox" id="menu-toggle" class="menu-toggle-checkbox">
                <label for="menu-toggle" class="menu-toggle-label">
                    <span></span><span></span><span></span>
                </label>

                <ul class="nav-links">


                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'Admin'): ?>
                            <li><a href="<?php echo $base_url; ?>/admin/index.php" class="nav-item">Admin console</a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>/index.php" class="nav-item">Home</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] === 'Hospital'): ?>
                            <li><a href="<?php echo $base_url; ?>/hospital/index.php" class="nav-item">Dashboard</a></li>
                            <li><a href="<?php echo $base_url; ?>/hospital/appointments.php" class="nav-item">Appointments</a></li>
                            <li><a href="<?php echo $base_url; ?>/hospital/services.php" class="nav-item">Services</a></li>
                            <li><a href="<?php echo $base_url; ?>/faq.php" class="nav-item">FAQ</a></li>
                            <li><a href="<?php echo $base_url; ?>/privacy.php" class="nav-item">Privacy</a></li>
                            <li><a href="<?php echo $base_url; ?>/terms.php" class="nav-item">Terms</a></li>
                        <?php elseif ($_SESSION['role'] === 'Patient'): ?>
                            <li><a href="<?php echo $base_url; ?>/patient/index.php" class="nav-item">Dashboard</a></li>
                            <li><a href="<?php echo $base_url; ?>/patient/search.php" class="nav-item">Find hospitals</a></li>
                            <li><a href="<?php echo $base_url; ?>/patient/services.php" class="nav-item">Services</a></li>
                            <li><a href="<?php echo $base_url; ?>/patient/review.php" class="nav-item">Reviews</a></li>
                            <li><a href="<?php echo $base_url; ?>/faq.php" class="nav-item">FAQ</a></li>
                            <li><a href="<?php echo $base_url; ?>/privacy.php" class="nav-item">Privacy</a></li>
                            <li><a href="<?php echo $base_url; ?>/terms.php" class="nav-item">Terms</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo $base_url; ?>/auth/logout.php" class="btn-outline">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>/index.php#how-it-works" class="nav-item">How it Works</a></li>
                        <li><a href="<?php echo $base_url; ?>/faq.php" class="nav-item">FAQ</a></li>
                        <li><a href="<?php echo $base_url; ?>/privacy.php" class="nav-item">Privacy Policy</a></li>
                        <li><a href="<?php echo $base_url; ?>/terms.php" class="nav-item">Terms of Service</a></li>
                        <li><a href="<?php echo $base_url; ?>/index.php#services" class="nav-item">Services</a></li>
                        <?php if ($nav_script !== 'login.php'): ?>
                            <li><a href="<?php echo $base_url; ?>/auth/login.php" class="nav-item" style="font-weight: 600;">Log in</a></li>
                        <?php endif; ?>
                        <?php if ($nav_script !== 'register.php'): ?>
                            <li><a href="<?php echo $base_url; ?>/auth/register.php" class="btn-primary">Register</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">