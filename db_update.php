<?php
/**
 * Database helper — reviews feature removed.
 * Use phpMyAdmin and your main SQL file for schema changes.
 */
require_once 'config/database.php';

$messages = array();
$messages[] = 'VaxiCare no longer uses a reviews table.';
$messages[] = 'If you still have a reviews table, you can drop it in phpMyAdmin: DROP TABLE reviews;';
$messages[] = 'Database connection: OK (' . mysqli_get_host_info($conn) . ')';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database notice | VaxiCare</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 3rem auto; padding: 2rem; background: #f8fafc; }
        li { margin: 0.5rem 0; color: #334155; }
    </style>
</head>
<body>
    <h1>Database update</h1>
    <ul>
        <?php foreach ($messages as $msg): ?>
            <li><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></li>
        <?php endforeach; ?>
    </ul>
    <p><a href="index.php">Back to home</a></p>
</body>
</html>
