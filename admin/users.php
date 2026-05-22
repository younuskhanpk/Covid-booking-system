<?php
require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once __DIR__ . '/includes/admin_layout.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'set_role' && isset($_POST['user_id'], $_POST['role_id'])) {
        $uid = (int) $_POST['user_id'];
        $rid = (int) $_POST['role_id'];
        if ($rid >= 1 && $rid <= 3 && $uid !== (int) $_SESSION['user_id']) {
            $rname = mysqli_real_escape_string($conn, role_name_from_id($rid));
            if (mysqli_query($conn, "UPDATE users SET role_id = $rid, role = '$rname' WHERE id = $uid")) {
                $message = 'User role updated to ' . $rname . '.';
            }
        }
    }

    if ($action === 'delete_user' && isset($_POST['user_id'])) {
        $uid = (int) $_POST['user_id'];
        if ($uid !== (int) $_SESSION['user_id']) {
            if (mysqli_query($conn, "DELETE FROM users WHERE id = $uid")) {
                $message = 'User removed.';
            } else {
                $error = 'Cannot delete user (linked records may exist).';
            }
        }
    }
}

$allUsers = [];
$res = mysqli_query($conn, 'SELECT id, name, email, phone, role, role_id, created_at FROM users ORDER BY role_id ASC, created_at DESC');
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $allUsers[] = $row;
    }
} else {
    $res = mysqli_query($conn, 'SELECT id, name, email, phone, role, created_at FROM users ORDER BY role ASC, created_at DESC');
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['role_id'] = role_id_from_name($row['role'] ?? 'Patient');
            $allUsers[] = $row;
        }
    }
}

$total_admins = 0;
foreach($allUsers as $u) {
    if ((int)($u['role_id'] ?? role_id_from_name($u['role'])) === 1) $total_admins++;
}

admin_layout_start('All Users', 'users');
?>

<div class="admin-topbar">
    <h1>All Users Management</h1>
    <p style="color:#64748b;margin:0;">Promote any user to Admin (role 1), Hospital (role 2), or Patient (role 3).</p>
</div>

<?php if ($message): ?>
    <div class="admin-section" style="background:#ecfdf5;border-color:#a7f3d0;color:#047857;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-section" style="background:#fef2f2;border-color:#fecaca;color:#b91c1c;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<section id="sec-users" class="admin-section">
    <h2>All users — manage roles (<?php echo count($allUsers); ?> total, <?php echo $total_admins; ?> admins)</h2>
    <div style="overflow-x:auto;">
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
    </div>
</section>

<?php admin_layout_end(); ?>
