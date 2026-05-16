-- =============================================================================
-- VaxiCare: Single `users` table (role_id 1=Admin, 2=Hospital, 3=Patient)
-- Removes separate `hospitals` table — run in phpMyAdmin on covid_booking_db
-- BACK UP your database before running.
-- =============================================================================

USE `covid_booking_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- Step 1: Extend users (safe if columns already exist — ignore duplicate errors in phpMyAdmin)
ALTER TABLE `users`
  ADD COLUMN `role_id` TINYINT UNSIGNED NOT NULL DEFAULT 3 AFTER `id`,
  ADD COLUMN `hospital_name` VARCHAR(255) NULL DEFAULT NULL AFTER `address`,
  ADD COLUMN `location` VARCHAR(255) NULL DEFAULT NULL AFTER `hospital_name`,
  ADD COLUMN `license_number` VARCHAR(100) NULL DEFAULT NULL AFTER `location`,
  ADD COLUMN `facility_status` ENUM('Pending','Approved','Rejected') NULL DEFAULT NULL AFTER `license_number`;

-- Sync role_id from existing enum role
UPDATE `users` SET `role_id` = 1 WHERE `role` = 'Admin';
UPDATE `users` SET `role_id` = 2 WHERE `role` = 'Hospital';
UPDATE `users` SET `role_id` = 3 WHERE `role` = 'Patient';

-- Step 2: Copy hospital facility data into users (if hospitals table exists)
UPDATE `users` u
INNER JOIN `hospitals` h ON h.user_id = u.id
SET
  u.hospital_name = h.hospital_name,
  u.location = h.location,
  u.license_number = h.license_number,
  u.facility_status = h.status,
  u.role_id = 2;

-- Step 3: Point appointments at hospital USER id (not hospitals.id)
UPDATE `appointments` a
INNER JOIN `hospitals` h ON a.hospital_id = h.id
SET a.hospital_id = h.user_id;

-- Step 4: Point reviews at hospital USER id
UPDATE `reviews` r
INNER JOIN `hospitals` h ON r.hospital_id = h.id
SET r.hospital_id = h.user_id;

-- Step 5: Slip image path on appointments
ALTER TABLE `appointments`
  ADD COLUMN `slip_image` VARCHAR(255) NULL DEFAULT NULL AFTER `status`;

-- Step 6: Drop old hospitals table
DROP TABLE IF EXISTS `hospitals`;

SET FOREIGN_KEY_CHECKS = 1;

-- Optional: ensure reviews table exists
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL COMMENT 'users.id where role_id=2',
  `appointment_id` int(11) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('Pending','Approved') NOT NULL DEFAULT 'Approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_review_appointment` (`appointment_id`),
  KEY `patient_id` (`patient_id`),
  KEY `hospital_id` (`hospital_id`),
  CONSTRAINT `reviews_patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_hospital_fk` FOREIGN KEY (`hospital_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_appt_fk` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
