<?php
/**
 * Run once in browser to migrate database (reviews + appointment link).
 * Safe to run multiple times.
 */
require_once 'config/database.php';

$messages = [];

try {
    $hasReviews = $conn->query("SHOW TABLES LIKE 'reviews'")->rowCount() > 0;

    if (!$hasReviews) {
        $sql = "CREATE TABLE reviews (
            id int(11) NOT NULL AUTO_INCREMENT,
            patient_id int(11) NOT NULL,
            hospital_id int(11) NOT NULL,
            appointment_id int(11) DEFAULT NULL,
            rating int(1) NOT NULL,
            comment text NOT NULL,
            status enum('Pending','Approved') NOT NULL DEFAULT 'Approved',
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            UNIQUE KEY uniq_review_appointment (appointment_id),
            KEY patient_id (patient_id),
            KEY hospital_id (hospital_id),
            CONSTRAINT reviews_user_fk FOREIGN KEY (patient_id) REFERENCES users (id) ON DELETE CASCADE,
            CONSTRAINT reviews_hospital_fk FOREIGN KEY (hospital_id) REFERENCES hospitals (id) ON DELETE CASCADE,
            CONSTRAINT reviews_appt_fk FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
        $conn->exec($sql);
        $messages[] = 'Created reviews table.';
    } else {
        $messages[] = 'Reviews table already exists.';
        $col = $conn->query("SHOW COLUMNS FROM reviews LIKE 'appointment_id'")->fetch();
        if (!$col) {
            $conn->exec("ALTER TABLE reviews ADD COLUMN appointment_id int(11) DEFAULT NULL AFTER hospital_id");
            $messages[] = 'Added appointment_id column.';
        }
        try {
            $conn->exec("ALTER TABLE reviews ADD UNIQUE KEY uniq_review_appointment (appointment_id)");
            $messages[] = 'Added unique key on appointment_id.';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') === false) {
                $messages[] = 'Unique appointment_id: ' . $e->getMessage();
            }
        }
        try {
            $conn->exec("ALTER TABLE reviews ADD CONSTRAINT reviews_appt_fk FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON DELETE SET NULL");
            $messages[] = 'Added foreign key appointment_id.';
        } catch (PDOException $e) {
            if (stripos($e->getMessage(), 'Duplicate') === false && stripos($e->getMessage(), 'already exists') === false) {
                $messages[] = 'FK note: ' . $e->getMessage();
            }
        }
    }
} catch (PDOException $e) {
    $messages[] = 'Error: ' . $e->getMessage();
}

header('Content-Type: text/plain; charset=utf-8');
echo implode("\n", $messages);
