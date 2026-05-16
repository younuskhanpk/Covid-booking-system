<?php
require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once '../includes/hospital_queries.php';
require_once __DIR__ . '/includes/admin_layout.php';

$message = '';
$error = '';

// --- POST actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'hospital_status' && isset($_POST['user_id'], $_POST['status'])) {
        $uid = (int) $_POST['user_id'];
        $status = $_POST['status'] === 'Approved' ? 'Approved' : ($_POST['status'] === 'Rejected' ? 'Rejected' : 'Pending');
        try {
            $conn->prepare('UPDATE users SET facility_status = ? WHERE id = ? AND role_id = 2')->execute([$status, $uid]);
            $message = 'Hospital status updated to ' . $status . '.';
        } catch (PDOException $e) {
            $error = 'Could not update hospital.';
        }
    }

    if ($action === 'set_role' && isset($_POST['user_id'], $_POST['role_id'])) {
        $uid = (int) $_POST['user_id'];
        $rid = (int) $_POST['role_id'];
        if ($rid >= 1 && $rid <= 3 && $uid !== (int) $_SESSION['user_id']) {
            $rname = role_name_from_id($rid);
            $conn->prepare('UPDATE users SET role_id = ?, role = ? WHERE id = ?')->execute([$rid, $rname, $uid]);
            $message = 'User role updated to ' . $rname . '.';
        }
    }

    if ($action === 'delete_user' && isset($_POST['user_id'])) {
        $uid = (int) $_POST['user_id'];
        if ($uid !== (int) $_SESSION['user_id']) {
            try {
                $conn->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]);
                $message = 'User removed.';
            } catch (PDOException $e) {
                $error = 'Cannot delete user (linked records may exist).';
            }
        }
    }
}

// --- Stats ---
$total_hospitals = (int) $conn->query('SELECT COUNT(*) FROM users WHERE role_id = 2')->fetchColumn();
$pending_hospitals = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role_id = 2 AND facility_status = 'Pending'")->fetchColumn();

$total_patients = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role_id = 3 OR role = 'Patient'")->fetchColumn();
$total_admins = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role_id = 1 OR role = 'Admin'")->fetchColumn();
$total_appointments = (int) $conn->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$total_reviews = 0;
try {
    $total_reviews = (int) $conn->query("SELECT COUNT(*) FROM reviews WHERE status = 'Approved'")->fetchColumn();
} catch (Throwable $e) {
}

$hospitalRows = $conn->query("
    SELECT id, name AS rep_name, email, phone, hospital_name, location, license_number,
           facility_status AS status, created_at, id AS user_id
    FROM users WHERE role_id = 2 ORDER BY created_at DESC
")->fetchAll();

$patientRows = $conn->query("
    SELECT id, name, email, phone, address, created_at
    FROM users WHERE role_id = 3 OR role = 'Patient'
    ORDER BY created_at DESC
")->fetchAll();

try {
    $allUsers = $conn->query('SELECT id, name, email, phone, role, role_id, created_at FROM users ORDER BY role_id ASC, created_at DESC')->fetchAll();
} catch (PDOException $e) {
    $allUsers = $conn->query('SELECT id, name, email, phone, role, created_at FROM users ORDER BY role ASC, created_at DESC')->fetchAll();
    foreach ($allUsers as &$u) {
        $u['role_id'] = role_id_from_name($u['role'] ?? 'Patient');
    }
}

$hName = hospital_name_expr($conn, 'hu');
$apptSql = "
    SELECT a.id, a.type, a.appointment_date, a.status, a.slip_image,
           u.name AS patient_name, {$hName} AS hospital_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    " . hospital_join_sql($conn, 'a') . "
    ORDER BY a.created_at DESC LIMIT 50
";
$appointments = $conn->query($apptSql)->fetchAll();

$reviewsList = [];
try {
    $reviewsList = $conn->query("
        SELECT r.*, pu.name AS patient_name, {$hName} AS hospital_name
        FROM reviews r
        JOIN users pu ON r.patient_id = pu.id
        " . hospital_join_sql($conn, 'r') . "
        ORDER BY r.created_at DESC LIMIT 30
    ")->fetchAll();
} catch (Throwable $e) {
    $reviewsList = [];
}

admin_layout_start('Central Command', 'dashboard');
?>

<div class="admin-topbar">
    <h1>Platform control center</h1>
    <p style="color:#64748b;margin:0;">Full access: users, hospitals, bookings, reviews, and reports. Passwords are never shown — only secure hashes are stored.</p>
</div>

<?php if ($message): ?>
    <div class="admin-section" style="background:#ecfdf5;border-color:#a7f3d0;color:#047857;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-section" style="background:#fef2f2;border-color:#fecaca;color:#b91c1c;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="admin-stats">
    <a href="#sec-hospitals" class="stat-card-link">
        <div class="stat-num"><?php echo $total_hospitals; ?></div>
        <div class="stat-label">Total hospitals</div>
    </a>
    <a href="#sec-hospitals" class="stat-card-link">
        <div class="stat-num" style="color:#d97706;"><?php echo $pending_hospitals; ?></div>
        <div class="stat-label">Pending approvals</div>
    </a>
    <a href="#sec-patients" class="stat-card-link">
        <div class="stat-num" style="color:#059669;"><?php echo $total_patients; ?></div>
        <div class="stat-label">Registered patients</div>
    </a>
    <a href="#sec-appointments" class="stat-card-link">
        <div class="stat-num" style="color:#0369a1;"><?php echo $total_appointments; ?></div>
        <div class="stat-label">Platform appointments</div>
    </a>
</div>

<section id="sec-hospitals" class="admin-section">
    <h2>Hospital facilities (<?php echo count($hospitalRows); ?>)</h2>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Facility</th>
                    <th>Representative</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hospitalRows as $h): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($h['hospital_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                            <small><?php echo htmlspecialchars($h['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($h['rep_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($h['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="badge-role badge-hospital"><?php echo htmlspecialchars($h['status'] ?? 'Pending', ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td style="display:flex;gap:0.35rem;flex-wrap:wrap;">
                            <form method="post" style="margin:0;"><input type="hidden" name="action" value="hospital_status"><input type="hidden" name="user_id" value="<?php echo (int) ($h['user_id'] ?? $h['id']); ?>"><input type="hidden" name="status" value="Approved"><button type="submit" class="btn-primary" style="padding:0.35rem 0.75rem;font-size:0.8rem;">Approve</button></form>
                            <form method="post" style="margin:0;"><input type="hidden" name="action" value="hospital_status"><input type="hidden" name="user_id" value="<?php echo (int) ($h['user_id'] ?? $h['id']); ?>"><input type="hidden" name="status" value="Rejected"><button type="submit" class="btn-outline" style="padding:0.35rem 0.75rem;font-size:0.8rem;">Reject</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section id="sec-patients" class="admin-section">
    <h2>Patients (<?php echo $total_patients; ?>)</h2>
    <table class="admin-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Registered</th></tr></thead>
        <tbody>
            <?php foreach ($patientRows as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($p['phone'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($p['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section id="sec-users" class="admin-section">
    <h2>All users — manage roles (<?php echo count($allUsers); ?> total, <?php echo $total_admins; ?> admins)</h2>
    <p style="color:#64748b;font-size:0.9rem;margin-bottom:1rem;">Promote any user to Admin (role 1), Hospital (2), or Patient (3). Password column is hidden for security.</p>
    <table class="admin-table">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Change role</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($allUsers as $u):
                $rid = (int) ($u['role_id'] ?? role_id_from_name($u['role'] ?? 'Patient'));
                ?>
                <tr>
                    <td><?php echo (int) $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php
                        $badge = $rid === 1 ? 'badge-admin' : ($rid === 2 ? 'badge-hospital' : 'badge-patient');
                ?>
                        <span class="badge-role <?php echo $badge; ?>"><?php echo role_name_from_id($rid); ?> (<?php echo $rid; ?>)</span>
                    </td>
                    <td>
                        <form method="post" style="display:flex;gap:0.35rem;align-items:center;margin:0;">
                            <input type="hidden" name="action" value="set_role">
                            <input type="hidden" name="user_id" value="<?php echo (int) $u['id']; ?>">
                            <select name="role_id" class="form-control" style="padding:0.35rem;max-width:120px;">
                                <option value="1" <?php echo $rid === 1 ? 'selected' : ''; ?>>Admin</option>
                                <option value="2" <?php echo $rid === 2 ? 'selected' : ''; ?>>Hospital</option>
                                <option value="3" <?php echo $rid === 3 ? 'selected' : ''; ?>>Patient</option>
                            </select>
                            <button type="submit" class="btn-primary" style="padding:0.35rem 0.75rem;font-size:0.8rem;">Save</button>
                        </form>
                    </td>
                    <td>
                        <?php if ((int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?php echo (int) $u['id']; ?>">
                                <button type="submit" class="btn-outline" style="padding:0.35rem 0.6rem;font-size:0.75rem;color:#dc2626;">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section id="sec-appointments" class="admin-section">
    <h2>Bookings & slip images</h2>
    <table class="admin-table">
        <thead><tr><th>ID</th><th>Patient</th><th>Hospital</th><th>Type</th><th>Date</th><th>Status</th><th>Slip</th></tr></thead>
        <tbody>
            <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?php echo (int) $a['id']; ?></td>
                    <td><?php echo htmlspecialchars($a['patient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($a['hospital_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($a['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($a['appointment_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php if (!empty($a['slip_image'])): ?>
                            <a href="<?php echo htmlspecialchars($a['slip_image'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener"><img src="<?php echo htmlspecialchars($a['slip_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Slip" class="slip-thumb"></a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section id="sec-reviews" class="admin-section">
    <h2>Community reviews (<?php echo $total_reviews; ?>)</h2>
    <?php if (count($reviewsList) === 0): ?>
        <p style="color:#64748b;">No reviews yet. Patients can submit after completed visits.</p>
    <?php else: ?>
        <?php foreach ($reviewsList as $rev): ?>
            <div class="review-card-admin">
                <strong><?php echo htmlspecialchars($rev['patient_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                → <?php echo htmlspecialchars($rev['hospital_name'], ENT_QUOTES, 'UTF-8'); ?>
                <span style="color:#fbbf24;margin-left:0.5rem;"><?php echo str_repeat('★', (int) $rev['rating']); ?></span>
                <p style="margin:0.75rem 0 0;font-style:italic;"><?php echo htmlspecialchars($rev['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                <small style="color:#94a3b8;"><?php echo htmlspecialchars($rev['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
admin_layout_end();
