<?php
// List all patients for admin
require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once __DIR__ . '/includes/admin_layout.php';

$patientRows = array();

$sql = "SELECT id, name, email, phone, address, created_at
        FROM users
        WHERE role_id = 3 OR role = 'Patient'
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $patientRows[] = $row;
    }
}

admin_layout_start('Patients', 'patients');
?>

<div class="admin-topbar">
    <h1>Patients</h1>
    <p style="color:#64748b;margin:0;">List of all registered patients in the system.</p>
</div>

<section id="sec-patients" class="admin-section">
    <h2>Patients (<?php echo count($patientRows); ?>)</h2>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th></tr></thead>
            <tbody>
                <?php foreach ($patientRows as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p['phone'] ? $p['phone'] : '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php admin_layout_end(); ?>
