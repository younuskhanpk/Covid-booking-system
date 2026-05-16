-- Reviews table (connects patient + hospital USER ids + appointments)
USE `covid_booking_db`;

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
