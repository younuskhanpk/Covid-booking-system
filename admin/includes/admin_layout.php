<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__, 2) . '/includes/auth_helpers.php';

if (!isset($_SESSION['user_id']) || !session_role_check('Admin')) {
    header('Location: /eproject/auth/login.php');
    exit;
}

$base_url = '/eproject';

function admin_layout_start(string $title, string $active = 'dashboard'): void
{
    global $base_url;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> | VaxiCare Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/admin/assets/admin-panel.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="brand-icon">+</span>
            <div>
                <strong>VaxiCare</strong>
                <small>Admin Console</small>
            </div>
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="<?php echo $active === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
            <a href="hospitals.php" class="<?php echo $active === 'hospitals' ? 'active' : ''; ?>">Hospitals</a>
            <a href="patients.php" class="<?php echo $active === 'patients' ? 'active' : ''; ?>">Patients</a>
            <a href="users.php" class="<?php echo $active === 'users' ? 'active' : ''; ?>">All users</a>
            <a href="bookings.php" class="<?php echo $active === 'bookings' ? 'active' : ''; ?>">Bookings</a>
            <a href="vaccines.php" class="<?php echo $active === 'vaccines' ? 'active' : ''; ?>">Vaccines</a>
            <a href="export.php" class="<?php echo $active === 'export' ? 'active' : ''; ?>">Export reports</a>
        </nav>
        <div class="admin-sidebar-foot">
            <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="<?php echo $base_url; ?>/auth/logout.php">Logout</a>
        </div>
    </aside>
    <main class="admin-main">
    <?php
}

function admin_layout_end(): void
{
    ?>
    </main>
</body>
</html>
    <?php
}
