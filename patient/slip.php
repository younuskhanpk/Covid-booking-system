<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';
require_once '../includes/slip_generator.php';
require_once '../includes/auth_helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . (session_role_check('Patient') ? 'search.php' : '/eproject/admin/index.php'));
    exit;
}

$hName = hospital_name_expr($conn);
$sql = "
    SELECT a.*, u.name AS patient_name, {$hName} AS hospital_name, hu.location, v.vaccine_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    " . hospital_join_sql($conn, 'a') . "
    LEFT JOIN vaccines v ON a.vaccine_id = v.id
    WHERE a.id = :id
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    echo 'Booking not found.';
    exit;
}

$role = $_SESSION['role'] ?? '';
$uid = (int) $_SESSION['user_id'];
$canView = ($role === 'Admin')
    || ($role === 'Patient' && (int) $row['patient_id'] === $uid)
    || ($role === 'Hospital' && (int) $row['hospital_id'] === $uid);

if (!$canView) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied.';
    exit;
}

if (empty($row['slip_image'])) {
    $path = generate_booking_slip_png($row, $id);
    if ($path) {
        try {
            save_appointment_slip($conn, $id, $path);
            $row['slip_image'] = $path;
        } catch (Throwable $e) {
        }
    }
}

$slipUrl = $row['slip_image'] ?? '';
$download = isset($_GET['download']);

if ($download && $slipUrl && is_file(dirname(__DIR__) . parse_url($slipUrl, PHP_URL_PATH))) {
    $file = dirname(__DIR__) . parse_url($slipUrl, PHP_URL_PATH);
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="vaxicare-slip-' . $id . '.png"');
    readfile($file);
    exit;
}

include '../includes/header.php';
?>

<div class="container" style="max-width: 720px; padding: 3rem 0;">
    <div class="card animate-fade-up">
        <h2>Booking confirmation slip</h2>
        <p style="color: var(--text-muted);">Reference VC-<?php echo str_pad((string) $id, 8, '0', STR_PAD_LEFT); ?></p>

        <?php if ($slipUrl): ?>
            <p style="text-align:center;margin:1.5rem 0;">
                <img src="<?php echo htmlspecialchars($slipUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Booking slip" style="max-width:100%;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.12);">
            </p>
            <p style="text-align:center;">
                <a href="?id=<?php echo $id; ?>&download=1" class="btn-primary">Download PNG slip</a>
            </p>
        <?php else: ?>
            <p>Slip image could not be generated. Enable PHP GD extension in XAMPP.</p>
        <?php endif; ?>

        <p style="margin-top:1.5rem;">
            <?php if ($role === 'Patient'): ?>
                <a href="index.php" class="btn-outline">Dashboard</a>
            <?php elseif ($role === 'Admin'): ?>
                <a href="/eproject/admin/index.php#sec-appointments" class="btn-outline">Admin bookings</a>
            <?php else: ?>
                <a href="/eproject/hospital/index.php" class="btn-outline">Hospital dashboard</a>
            <?php endif; ?>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
