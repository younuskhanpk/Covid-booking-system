-- =============================================================================
-- VaxiCare — FRESH COMPLETE DATABASE (import this ONE file only)
-- =============================================================================
-- phpMyAdmin: Import → choose fresh.sql → Go
-- WARNING: Deletes all old tables and data in covid_booking_db
--
-- Default logins (password for all demo accounts: password)
--   Admin:    admin@covidbooking.com
--   Hospital: hospital@demo.com
--   Patient:  patient@demo.com
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `covid_booking_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `covid_booking_db`;

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `results`;
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `vaccines`;
DROP TABLE IF EXISTS `hospitals`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- USERS (single table — role_id: 1 Admin, 2 Hospital, 3 Patient)
-- -----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 3 COMMENT '1=Admin 2=Hospital 3=Patient',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Hospital','Patient') NOT NULL DEFAULT 'Patient',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `facility_status` enum('Pending','Approved','Rejected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_role_id` (`role_id`),
  KEY `idx_users_facility_status` (`facility_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- password = "password" (bcrypt)
INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `password`, `role`, `phone`, `address`, `hospital_name`, `location`, `license_number`, `facility_status`) VALUES
(1, 1, 'System Admin', 'admin@covidbooking.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', '03001234567', 'Admin Office, Islamabad', NULL, NULL, NULL, NULL),
(2, 2, 'City Care Hospital', 'hospital@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hospital', '03007654321', 'Main Hospital Road', 'City Care Hospital', 'Lahore', 'LIC-CCH-2024-001', 'Approved'),
(3, 3, 'Ahmed Khan', 'patient@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patient', '03009876543', 'Gulberg, Lahore', NULL, NULL, NULL, NULL),
(4, 3, 'Fatima Ali', 'fatima@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patient', '03001112233', 'Model Town, Lahore', NULL, NULL, NULL, NULL);

-- -----------------------------------------------------------------------------
-- VACCINES
-- -----------------------------------------------------------------------------
CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vaccine_name` varchar(100) NOT NULL,
  `availability_status` enum('Available','Out of Stock') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vaccines` (`id`, `vaccine_name`, `availability_status`) VALUES
(1, 'Pfizer-BioNTech', 'Available'),
(2, 'Moderna', 'Available'),
(3, 'Johnson & Johnson', 'Available'),
(4, 'AstraZeneca', 'Available');

-- -----------------------------------------------------------------------------
-- APPOINTMENTS (hospital_id = users.id where role_id = 2)
-- -----------------------------------------------------------------------------
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `type` enum('Test','Vaccination') NOT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected','Completed') NOT NULL DEFAULT 'Pending',
  `slip_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_appt_patient` (`patient_id`),
  KEY `idx_appt_hospital` (`hospital_id`),
  KEY `idx_appt_status` (`status`),
  CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_hospital` FOREIGN KEY (`hospital_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appt_vaccine` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Demo booking: patient 3 → hospital user 2
INSERT INTO `appointments` (`id`, `patient_id`, `hospital_id`, `type`, `vaccine_id`, `appointment_date`, `status`, `slip_image`) VALUES
(1, 3, 2, 'Test', NULL, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Pending', NULL);

-- -----------------------------------------------------------------------------
-- RESULTS (test / vaccination outcomes)
-- -----------------------------------------------------------------------------
CREATE TABLE `results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `test_result` enum('Pending','Positive','Negative') DEFAULT NULL,
  `vaccination_status` enum('Not Started','Dose 1','Dose 2','Completed') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_results_appointment` (`appointment_id`),
  CONSTRAINT `fk_results_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------------------------------
-- REVIEWS (patient_id + hospital_id both reference users.id)
-- -----------------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('Pending','Approved') NOT NULL DEFAULT 'Approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_review_appointment` (`appointment_id`),
  KEY `idx_reviews_patient` (`patient_id`),
  KEY `idx_reviews_hospital` (`hospital_id`),
  CONSTRAINT `fk_reviews_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_hospital` FOREIGN KEY (`hospital_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================================
-- DONE — No separate `hospitals` table. All hospital data is in `users`.
-- =============================================================================
