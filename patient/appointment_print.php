<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

$patient_id = (int) $_SESSION['user_id'];
$hName = hospital_name_expr($conn);
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT a.*, {$hName} AS hospital_name, hu.location, v.vaccine_name, u.name AS patient_name,
           r.test_result, r.vaccination_status, r.notes
    FROM appointments a
    " . hospital_join_sql($conn, 'a') . "
    JOIN users u ON a.patient_id = u.id
    LEFT JOIN vaccines v ON a.vaccine_id = v.id
    LEFT JOIN results r ON r.appointment_id = a.id
    WHERE a.id = :aid AND a.patient_id = :pid AND a.status = 'Completed'
");
$stmt->execute([':aid' => $id, ':pid' => $patient_id]);
$row = $stmt->fetch();

if (!$row) {
    header('HTTP/1.1 404 Not Found');
    echo 'Completed appointment not found.';
    exit;
}

$ref = 'VC-' . str_pad((string) $row['id'], 8, '0', STR_PAD_LEFT);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate / result — <?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body { font-family: Georgia, serif; max-width: 700px; margin: 2rem auto; padding: 1rem; color: #0f172a; }
        h1 { font-size: 1.4rem; border-bottom: 2px solid #4f46e5; padding-bottom: .5rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: .5rem 0; border-bottom: 1px solid #e2e8f0; }
        th { width: 40%; color: #64748b; font-weight: 600; }
        .foot { margin-top: 2rem; font-size: .85rem; color: #64748b; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <h1>VaxiCare — digital record</h1>
    <p><strong>Reference:</strong> <?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?></p>
    <table>
        <tr><th>Patient</th><td><?php echo htmlspecialchars($row['patient_name'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><th>Hospital</th><td><?php echo htmlspecialchars($row['hospital_name'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <tr><th>Service</th><td><?php echo htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <?php if ($row['type'] === 'Vaccination' && !empty($row['vaccine_name'])): ?>
        <tr><th>Vaccine</th><td><?php echo htmlspecialchars($row['vaccine_name'], ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <?php endif; ?>
        <tr><th>Date</th><td><?php echo htmlspecialchars(date('F j, Y', strtotime($row['appointment_date'])), ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <?php if ($row['type'] === 'Test'): ?>
        <tr><th>Test result</th><td><?php echo htmlspecialchars($row['test_result'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <?php else: ?>
        <tr><th>Vaccination status</th><td><?php echo htmlspecialchars($row['vaccination_status'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($row['notes'])): ?>
        <tr><th>Notes</th><td><?php echo nl2br(htmlspecialchars($row['notes'], ENT_QUOTES, 'UTF-8')); ?></td></tr>
        <?php endif; ?>
    </table>
    <p class="foot">Use your browser menu: Print → Save as PDF. Issued via VaxiCare patient portal.</p>
</body>
</html>
