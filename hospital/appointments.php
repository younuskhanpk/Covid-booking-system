<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Hospital') {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

$user_id = (int) $_SESSION['user_id'];
$hospital = fetch_hospital_account_by_user($conn, $user_id);

if (!$hospital || ($hospital['status'] ?? '') !== 'Approved') {
    header('Location: ../auth/login.php');
    exit;
}

$hospital_id = hospital_appointment_filter_id($conn, $user_id);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
    $action = $_POST['action'];
    $appt_id = (int) $_POST['appointment_id'];
    $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';

    try {
        mysqli_begin_transaction($conn);

        $statusSafe = mysqli_real_escape_string($conn, $new_status);
        mysqli_query($conn, "UPDATE appointments SET status = '$statusSafe' WHERE id = $appt_id AND hospital_id = $hospital_id");

        if ($new_status === 'Approved') {
            $res = mysqli_query($conn, "SELECT type FROM appointments WHERE id = $appt_id");
            $row = mysqli_fetch_row($res);
            $type = $row ? $row[0] : null;

            $test_res = ($type === 'Test') ? 'Pending' : null;
            $vax_stat = ($type === 'Vaccination') ? 'Not Started' : null;

            $testResSafe = $test_res ? "'$test_res'" : 'NULL';
            $vaxStatSafe = $vax_stat ? "'$vax_stat'" : 'NULL';

            mysqli_query($conn, "INSERT INTO results (appointment_id, test_result, vaccination_status) VALUES ($appt_id, $testResSafe, $vaxStatSafe)");
        }

        mysqli_commit($conn);
        $message = 'Appointment ' . strtolower($new_status) . '.';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = 'Error: ' . $e->getMessage();
    }
}

$appointments = [];
$res = mysqli_query($conn, "
    SELECT a.*, u.name AS patient_name, u.phone, u.email, v.vaccine_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    LEFT JOIN vaccines v ON a.vaccine_id = v.id
    WHERE a.hospital_id = $hospital_id AND a.status IN ('Pending', 'Approved')
    ORDER BY a.appointment_date ASC
");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $appointments[] = $row;
    }
}

include '../includes/header.php';
?>

<div class="container" style="padding-bottom: 4rem;">
    <h2>Manage appointments</h2>
    <?php if ($message): ?>
        <div class="alert alert-success" style="padding:1rem;margin:1rem 0;"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <div class="table-responsive card" style="padding:1rem;">
        <table class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($appointments) > 0): ?>
                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($a['patient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($a['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($a['appointment_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($a['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ($a['status'] === 'Pending'): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo (int) $a['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-primary" style="padding:0.3rem 0.8rem;font-size:0.85rem;">Approve</button>
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo (int) $a['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-outline" style="padding:0.3rem 0.8rem;font-size:0.85rem;">Reject</button>
                                    </form>
                                <?php else: ?>
                                    <a href="results.php">Update results</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No appointments.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
