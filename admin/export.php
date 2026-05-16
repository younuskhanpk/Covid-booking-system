<?php
// admin/export.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/database.php';
require_once '../includes/hospital_queries.php';

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $hName = hospital_name_expr($conn);
    $start = $_GET['start_date'];
    $end = $_GET['end_date'];

    try {
        $stmt = $conn->prepare("
            SELECT 
                a.id as Booking_ID,
                u.name as Patient_Name,
                u.email as Patient_Email,
                u.phone as Patient_Phone,
                {$hName} as Hospital_Name,
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
            WHERE a.appointment_date BETWEEN :start AND :end
            ORDER BY a.appointment_date DESC
        ");
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set headers to trigger file download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=covid_bookings_' . $start . '_to_' . $end . '.csv');

        // Create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // Output the column headings
        if (count($data) > 0) {
            fputcsv($output, array_keys($data[0]));
            
            // Output data rows
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        } else {
            fputcsv($output, ['No records found for the selected date range.']);
        }
        
        fclose($output);
        exit;
        
    } catch(PDOException $e) {
        die("Error generating export: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>
