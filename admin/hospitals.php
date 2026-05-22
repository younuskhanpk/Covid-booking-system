<?php
// Hospital approval management
require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once '../includes/hospital_queries.php';
require_once __DIR__ . '/includes/admin_layout.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'hospital_status' && isset($_POST['user_id'], $_POST['status'])) {
        $uid = (int) $_POST['user_id'];
        $status = $_POST['status'];
        if ($status === 'Approved') {
            $status = 'Approved';
        } elseif ($status === 'Rejected') {
            $status = 'Rejected';
        } else {
            $status = 'Pending';
        }
        $statusSafe = mysqli_real_escape_string($conn, $status);
        $update_sql = "UPDATE users SET facility_status = '$statusSafe' WHERE id = $uid AND role_id = 2";
        if (mysqli_query($conn, $update_sql)) {
            $message = 'Hospital status updated to ' . $status . '.';
        } else {
            $error = 'Could not update hospital.';
        }
    }
}

$hospitalRows = array();
$sql = "SELECT id, name AS rep_name, email, phone, hospital_name, location, license_number,
               facility_status AS status, created_at, id AS user_id
        FROM users
        WHERE role_id = 2
        ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $hospitalRows[] = $row;
    }
}

admin_layout_start('Hospitals Management', 'hospitals');
?>

<div class="admin-topbar">
    <h1>Hospitals Management</h1>
    <p style="color:#64748b;margin:0;">Approve or reject hospital registrations. Approved hospitals can receive bookings.</p>
</div>

<?php if ($message): ?>
    <div class="admin-section" style="background:#ecfdf5;border-color:#a7f3d0;color:#047857;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-section" style="background:#fef2f2;border-color:#fecaca;color:#b91c1c;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

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
                <?php foreach ($hospitalRows as $h):
                    $hStatus = $h['status'] ? $h['status'] : 'Pending';
                    $isApproved = ($hStatus === 'Approved');
                    $isRejected = ($hStatus === 'Rejected');
                    ?>
                    <tr>
                        <td>
                            <?php if ($isApproved): ?>
                                <span style="color:#16a34a;font-weight:800;margin-right:0.35rem;" title="Approved">✓</span>
                            <?php endif; ?>
                            <strong><?php echo htmlspecialchars($h['hospital_name'] ? $h['hospital_name'] : '', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                            <small><?php echo htmlspecialchars($h['location'] ? $h['location'] : '', ENT_QUOTES, 'UTF-8'); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($h['rep_name'] ? $h['rep_name'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($h['email'] ? $h['email'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <span class="badge-role badge-hospital"><?php echo htmlspecialchars($hStatus, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if ($isApproved): ?>
                                <span style="color:#16a34a;font-size:0.85rem;margin-left:0.25rem;">✓ Verified</span>
                            <?php endif; ?>
                        </td>
                        <td style="display:flex;gap:0.35rem;flex-wrap:wrap;align-items:center;">
                            <?php if ($isApproved): ?>
                                <button type="button" class="btn-primary" disabled style="padding:0.35rem 0.75rem;font-size:0.8rem;opacity:0.6;cursor:not-allowed;">✓ Approved</button>
                            <?php else: ?>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="action" value="hospital_status">
                                    <input type="hidden" name="user_id" value="<?php echo (int) ($h['user_id'] ? $h['user_id'] : $h['id']); ?>">
                                    <input type="hidden" name="status" value="Approved">
                                    <button type="submit" class="btn-primary" style="padding:0.35rem 0.75rem;font-size:0.8rem;">Approve</button>
                                </form>
                            <?php endif; ?>
                            <?php if (!$isRejected): ?>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="action" value="hospital_status">
                                    <input type="hidden" name="user_id" value="<?php echo (int) ($h['user_id'] ? $h['user_id'] : $h['id']); ?>">
                                    <input type="hidden" name="status" value="Rejected">
                                    <button type="submit" class="btn-outline" style="padding:0.35rem 0.75rem;font-size:0.8rem;">Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php admin_layout_end(); ?>
