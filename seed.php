<?php
require_once 'config/database.php';

$password = password_hash('password', PASSWORD_DEFAULT);

$hospitals = [
    ['name' => 'City Care Hospital', 'email' => 'contact@citycare.com', 'rep' => 'Dr. Smith', 'phone' => '1234567890', 'loc' => 'Downtown', 'lic' => 'LIC-001'],
    ['name' => 'General Medical Center', 'email' => 'admin@gmc.com', 'rep' => 'Dr. Jones', 'phone' => '1234567891', 'loc' => 'Uptown', 'lic' => 'LIC-002'],
    ['name' => 'Metro Health', 'email' => 'info@metrohealth.com', 'rep' => 'Alice Wong', 'phone' => '1234567892', 'loc' => 'Westside', 'lic' => 'LIC-003'],
    ['name' => 'Sunrise Clinic', 'email' => 'hello@sunriseclinic.com', 'rep' => 'Bob Miller', 'phone' => '1234567893', 'loc' => 'Eastside', 'lic' => 'LIC-004'],
    ['name' => 'Valley Hospital', 'email' => 'support@valleyhospital.com', 'rep' => 'Sarah Connor', 'phone' => '1234567894', 'loc' => 'Valley', 'lic' => 'LIC-005'],
    ['name' => 'Pioneer Medical', 'email' => 'contact@pioneermedical.com', 'rep' => 'John Doe', 'phone' => '1234567895', 'loc' => 'North District', 'lic' => 'LIC-006'],
    ['name' => 'Southside Health', 'email' => 'admin@southsidehealth.com', 'rep' => 'Jane Smith', 'phone' => '1234567896', 'loc' => 'Southside', 'lic' => 'LIC-007'],
    ['name' => 'Central Care', 'email' => 'info@centralcare.com', 'rep' => 'Emily Chen', 'phone' => '1234567897', 'loc' => 'Central', 'lic' => 'LIC-008'],
    ['name' => 'Evergreen Hospital', 'email' => 'hello@evergreenhospital.com', 'rep' => 'Michael Brown', 'phone' => '1234567898', 'loc' => 'Suburbs', 'lic' => 'LIC-009'],
    ['name' => 'Unity Medical Center', 'email' => 'support@unitymedical.com', 'rep' => 'David Lee', 'phone' => '1234567899', 'loc' => 'Downtown', 'lic' => 'LIC-010'],
];

foreach ($hospitals as $h) {
    $n = mysqli_real_escape_string($conn, $h['rep']);
    $e = mysqli_real_escape_string($conn, $h['email']);
    $hn = mysqli_real_escape_string($conn, $h['name']);
    $p = mysqli_real_escape_string($conn, $h['phone']);
    $l = mysqli_real_escape_string($conn, $h['loc']);
    $lic = mysqli_real_escape_string($conn, $h['lic']);

    $sql = "INSERT INTO users (name, email, password, role_id, role, phone, hospital_name, location, license_number, facility_status) 
            VALUES ('$n', '$e', '$password', 2, 'Hospital', '$p', '$hn', '$l', '$lic', 'Approved')";
    mysqli_query($conn, $sql);
}

$patients = [
    ['name' => 'Alice Johnson', 'email' => 'alice@patient.com', 'phone' => '0987654321', 'addr' => '123 Maple St'],
    ['name' => 'Bob Smith', 'email' => 'bob@patient.com', 'phone' => '0987654322', 'addr' => '456 Oak St'],
    ['name' => 'Charlie Brown', 'email' => 'charlie@patient.com', 'phone' => '0987654323', 'addr' => '789 Pine St'],
    ['name' => 'Diana Prince', 'email' => 'diana@patient.com', 'phone' => '0987654324', 'addr' => '101 Elm St'],
    ['name' => 'Evan Wright', 'email' => 'evan@patient.com', 'phone' => '0987654325', 'addr' => '202 Cedar St'],
    ['name' => 'Fiona Gallagher', 'email' => 'fiona@patient.com', 'phone' => '0987654326', 'addr' => '303 Birch St'],
    ['name' => 'George Washington', 'email' => 'george@patient.com', 'phone' => '0987654327', 'addr' => '404 Walnut St'],
    ['name' => 'Hannah Montana', 'email' => 'hannah@patient.com', 'phone' => '0987654328', 'addr' => '505 Spruce St'],
    ['name' => 'Ian Malcolm', 'email' => 'ian@patient.com', 'phone' => '0987654329', 'addr' => '606 Ash St'],
    ['name' => 'Julia Roberts', 'email' => 'julia@patient.com', 'phone' => '0987654330', 'addr' => '707 Chestnut St'],
];

foreach ($patients as $pt) {
    $n = mysqli_real_escape_string($conn, $pt['name']);
    $e = mysqli_real_escape_string($conn, $pt['email']);
    $p = mysqli_real_escape_string($conn, $pt['phone']);
    $a = mysqli_real_escape_string($conn, $pt['addr']);

    $sql = "INSERT INTO users (name, email, password, role_id, role, phone, address) 
            VALUES ('$n', '$e', '$password', 3, 'Patient', '$p', '$a')";
    mysqli_query($conn, $sql);
}

echo "Database seeded successfully!\n";
?>
