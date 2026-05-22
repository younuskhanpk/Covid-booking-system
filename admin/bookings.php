<?php
// All bookings for admin
require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once '../includes/hospital_queries.php';
require_once __DIR__ . '/includes/admin_layout.php';

$hName = hospital_name_expr($conn, 'hu');

$sql = "SELECT a.id, a.type, a.appointment_date, a.status,
               u.name AS patient_name, " . $hName . " AS hospital_name
        FROM appointments a
        JOIN users u ON a.patient_id = u.id
        " . hospital_join_sql($conn, 'a') . "
        ORDER BY a.created_at DESC
        LIMIT 100";

$result = mysqli_query($conn, $sql);
$appointments = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}

admin_layout_start('Bookings', 'bookings');
?>

<div class="admin-topbar">
    <h1>All Bookings</h1>
    <p style="color:#64748b;margin:0;">Overview of all testing and vaccination appointments.</p>
</div>

<section id="sec-appointments" class="admin-section">
    <h2>Bookings (<?php echo count($appointments); ?>)</h2>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead><tr><th>ID</th><th>Patient</th><th>Hospital</th><th>Type</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?php echo (int) $a['id']; ?></td>
                        <td><?php echo htmlspecialchars($a['patient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($a['hospital_name'] ? $a['hospital_name'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($a['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($a['appointment_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge-role" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;"><?php echo htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php admin_layout_end(); ?>
