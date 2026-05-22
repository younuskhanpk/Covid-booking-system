<?php
// Simple database connection for XAMPP localhost

$host = 'localhost';
$dbname = 'covid_booking_db';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
