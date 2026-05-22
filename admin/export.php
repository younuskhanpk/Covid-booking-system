<?php
// Export bookings as CSV — form page + download
require_once '../config/database.php';
require_once '../includes/auth_helpers.php';
require_once '../includes/hospital_queries.php';

// If dates sent, download CSV file
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    if (!isset($_SESSION['user_id']) || !session_role_check('Admin')) {
        header('Location: ../auth/login.php');
        exit;
    }

    $hName = hospital_name_expr($conn);
    $start = $_GET['start_date'];
    $end = $_GET['end_date'];

    $startSafe = mysqli_real_escape_string($conn, $start);
    $endSafe = mysqli_real_escape_string($conn, $end);

    $sql = "SELECT
                a.id as Booking_ID,
                u.name as Patient_Name,
                u.email as Patient_Email,
                u.phone as Patient_Phone,
                " . $hName . " as Hospital_Name,
                a.type as Appointment_Type,
                v.vaccine_name as Vaccine,
                a.appointment_date as Date,
                a.status as Booking_Status,
                r.test_result as Test_Result,
                r.vaccination_status as Vaccination_Status
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            " . hospital_join_sql($conn, 'a') . "
            LEFT JOIN vaccines v ON a.vaccine_id = v.id
            LEFT JOIN results r ON a.id = r.appointment_id
            WHERE a.appointment_date BETWEEN '$startSafe' AND '$endSafe'
            ORDER BY a.appointment_date DESC";

    $res = mysqli_query($conn, $sql);
    if (!$res) {
        die('Error generating export: ' . mysqli_error($conn));
    }

    $data = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=covid_bookings_' . $start . '_to_' . $end . '.csv');

    $output = fopen('php://output', 'w');

    if (count($data) > 0) {
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    } else {
        fputcsv($output, array('No records found for the selected date range.'));
    }

    fclose($output);
    exit;
}

// Show export form page
require_once __DIR__ . '/includes/admin_layout.php';

admin_layout_start('Export Reports', 'export');
?>

<div class="admin-topbar">
    <h1>Export Reports</h1>
    <p style="color:#64748b;margin:0;">Download all bookings between two dates as a CSV file for Excel or Google Sheets.</p>
</div>

<section class="admin-section">
    <h2>Select date range</h2>
    <form method="get" action="export.php" style="display:flex;flex-wrap:wrap;gap:1.25rem;align-items:flex-end;">
        <div>
            <label style="display:block;font-weight:600;margin-bottom:0.35rem;color:#475569;">Start date</label>
            <input type="date" name="start_date" class="form-control" required style="padding:0.65rem 1rem;border:1px solid #cbd5e1;border-radius:8px;">
        </div>
        <div>
            <label style="display:block;font-weight:600;margin-bottom:0.35rem;color:#475569;">End date</label>
            <input type="date" name="end_date" class="form-control" required style="padding:0.65rem 1rem;border:1px solid #cbd5e1;border-radius:8px;">
        </div>
        <button type="submit" class="btn-primary" style="padding:0.75rem 1.5rem;">Download CSV</button>
    </form>
    <p style="margin-top:1.25rem;color:#64748b;font-size:0.95rem;">The file includes patient name, hospital, appointment type, vaccine, status, and test/vaccination results.</p>
</section>

<?php admin_layout_end(); ?>
