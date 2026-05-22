<?php
// Admin dashboard — full platform overview
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
        if (mysqli_query($conn, "UPDATE users SET facility_status = '$statusSafe' WHERE id = $uid AND role_id = 2")) {
            $message = 'Hospital status updated to ' . $status . '.';
        } else {
            $error = 'Could not update hospital.';
        }
    }
}

function fetchCount($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $row = mysqli_fetch_row($res);
        return (int) ($row[0] ? $row[0] : 0);
    }
    return 0;
}

$total_hospitals = fetchCount($conn, 'SELECT COUNT(*) FROM users WHERE role_id = 2');
$pending_hospitals = fetchCount($conn, "SELECT COUNT(*) FROM users WHERE role_id = 2 AND facility_status = 'Pending'");
$approved_hospitals = fetchCount($conn, "SELECT COUNT(*) FROM users WHERE role_id = 2 AND facility_status = 'Approved'");
$total_patients = fetchCount($conn, "SELECT COUNT(*) FROM users WHERE role_id = 3 OR role = 'Patient'");
$total_admins = fetchCount($conn, "SELECT COUNT(*) FROM users WHERE role_id = 1 OR role = 'Admin'");
$total_appointments = fetchCount($conn, 'SELECT COUNT(*) FROM appointments');
$pending_bookings = fetchCount($conn, "SELECT COUNT(*) FROM appointments WHERE status = 'Pending'");
$completed_bookings = fetchCount($conn, "SELECT COUNT(*) FROM appointments WHERE status = 'Completed'");
$total_vaccines = fetchCount($conn, 'SELECT COUNT(*) FROM vaccines');

$hName = hospital_name_expr($conn, 'hu');

$pendingHospitalList = array();
$sql_pending = "SELECT id, hospital_name, name AS rep_name, email, facility_status AS status, created_at
                FROM users WHERE role_id = 2 AND facility_status = 'Pending' ORDER BY created_at DESC LIMIT 8";
$res_ph = mysqli_query($conn, $sql_pending);
if ($res_ph) {
    while ($row = mysqli_fetch_assoc($res_ph)) {
        $pendingHospitalList[] = $row;
    }
}

$recentBookings = array();
$sql_book = "SELECT a.id, a.type, a.appointment_date, a.status, u.name AS patient_name, " . $hName . " AS hospital_name
             FROM appointments a
             JOIN users u ON a.patient_id = u.id
             " . hospital_join_sql($conn, 'a') . "
             ORDER BY a.created_at DESC LIMIT 10";
$res_bk = mysqli_query($conn, $sql_book);
if ($res_bk) {
    while ($row = mysqli_fetch_assoc($res_bk)) {
        $recentBookings[] = $row;
    }
}

admin_layout_start('Central Command', 'dashboard');
?>

<div class="admin-hero-banner">
    <div>
        <h1>Platform control center</h1>
        <p>Manage users, hospitals, bookings, vaccines, and download reports — all from one place.</p>
    </div>
    <div class="admin-hero-actions">
        <a href="hospitals.php" class="btn-primary">Manage hospitals</a>
        <a href="export.php" class="btn-outline" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.4);">Export CSV</a>
    </div>
</div>

<?php if ($message): ?>
    <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<div class="admin-stats">
    <a href="patients.php" class="stat-card-link">
        <div class="stat-num"><?php echo $total_patients; ?></div>
        <div class="stat-label">Patients</div>
    </a>
    <a href="hospitals.php" class="stat-card-link">
        <div class="stat-num"><?php echo $total_hospitals; ?></div>
        <div class="stat-label">Hospitals (<?php echo $approved_hospitals; ?> approved)</div>
    </a>
    <a href="bookings.php" class="stat-card-link">
        <div class="stat-num"><?php echo $total_appointments; ?></div>
        <div class="stat-label">Total bookings</div>
    </a>
    <a href="hospitals.php" class="stat-card-link stat-card-warn">
        <div class="stat-num"><?php echo $pending_hospitals; ?></div>
        <div class="stat-label">Pending approval</div>
    </a>
    <a href="bookings.php" class="stat-card-link">
        <div class="stat-num"><?php echo $pending_bookings; ?></div>
        <div class="stat-label">Pending bookings</div>
    </a>
    <a href="bookings.php" class="stat-card-link">
        <div class="stat-num"><?php echo $completed_bookings; ?></div>
        <div class="stat-label">Completed visits</div>
    </a>
    <a href="users.php" class="stat-card-link">
        <div class="stat-num"><?php echo $total_admins; ?></div>
        <div class="stat-label">Admins</div>
    </a>
    <a href="vaccines.php" class="stat-card-link">
        <div class="stat-num"><?php echo $total_vaccines; ?></div>
        <div class="stat-label">Vaccines in system</div>
    </a>
</div>

<div class="admin-charts-row">
    <div class="admin-section admin-chart-box">
        <h2>User distribution</h2>
        <canvas id="userChart" height="220"></canvas>
    </div>
    <div class="admin-section admin-chart-box">
        <h2>Platform activity</h2>
        <canvas id="overviewChart" height="220"></canvas>
    </div>
</div>

<?php if (count($pendingHospitalList) > 0): ?>
<section class="admin-section">
    <h2>Pending hospitals — quick approve</h2>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead><tr><th>Facility</th><th>Contact</th><th>Registered</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($pendingHospitalList as $ph): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($ph['hospital_name'] ? $ph['hospital_name'] : '—', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                            <small><?php echo htmlspecialchars($ph['rep_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($ph['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($ph['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form method="post" style="margin:0;display:inline;">
                                <input type="hidden" name="action" value="hospital_status">
                                <input type="hidden" name="user_id" value="<?php echo (int) $ph['id']; ?>">
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="btn-primary" style="padding:0.4rem 0.9rem;font-size:0.85rem;">✓ Approve</button>
                            </form>
                            <a href="hospitals.php" style="margin-left:0.5rem;font-size:0.85rem;">View all</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<section class="admin-section">
    <h2>Recent bookings</h2>
    <p style="color:#64748b;margin:-0.5rem 0 1rem;"><a href="bookings.php">View all bookings →</a></p>
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead><tr><th>ID</th><th>Patient</th><th>Hospital</th><th>Type</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
                <?php if (count($recentBookings) === 0): ?>
                    <tr><td colspan="6" style="color:#64748b;">No bookings yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $bk): ?>
                        <tr>
                            <td><?php echo (int) $bk['id']; ?></td>
                            <td><?php echo htmlspecialchars($bk['patient_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($bk['hospital_name'] ? $bk['hospital_name'] : '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($bk['type'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($bk['appointment_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge-role" style="background:#f1f5f9;color:#334155;"><?php echo htmlspecialchars($bk['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-section">
    <h2>Quick links</h2>
    <div class="admin-quick-links">
        <a href="users.php">All users & roles</a>
        <a href="patients.php">Patients list</a>
        <a href="hospitals.php">Hospitals</a>
        <a href="bookings.php">Bookings</a>
        <a href="vaccines.php">Vaccine inventory</a>
        <a href="export.php">Export CSV report</a>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctxUser = document.getElementById('userChart').getContext('2d');
    new Chart(ctxUser, {
        type: 'doughnut',
        data: {
            labels: ['Patients', 'Hospitals', 'Admins'],
            datasets: [{
                data: [<?php echo $total_patients; ?>, <?php echo $total_hospitals; ?>, <?php echo $total_admins; ?>],
                backgroundColor: ['#4f46e5', '#14b8a6', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    var ctxOverview = document.getElementById('overviewChart').getContext('2d');
    new Chart(ctxOverview, {
        type: 'bar',
        data: {
            labels: ['Total bookings', 'Pending hospitals', 'Pending bookings', 'Completed'],
            datasets: [{
                label: 'Count',
                data: [<?php echo $total_appointments; ?>, <?php echo $pending_hospitals; ?>, <?php echo $pending_bookings; ?>, <?php echo $completed_bookings; ?>],
                backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b', '#10b981'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>

<?php admin_layout_end(); ?>
