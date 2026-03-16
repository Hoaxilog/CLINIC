-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2026 at 06:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinic_online`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 1, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-15 16:36:35', '2026-03-15 16:36:35'),
(2, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 2, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-15 16:42:02', '2026-03-15 16:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('Pending','Scheduled','Ongoing','Completed','Cancelled','Waiting','Arrived') NOT NULL DEFAULT 'Pending',
  `service_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `requester_user_id` bigint(20) DEFAULT NULL,
  `requester_first_name` varchar(100) DEFAULT NULL,
  `requester_last_name` varchar(100) DEFAULT NULL,
  `requester_contact_number` varchar(30) DEFAULT NULL,
  `requester_email` varchar(255) DEFAULT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `modified_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `booking_type` varchar(30) NOT NULL DEFAULT 'online_appointment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_date`, `status`, `service_id`, `patient_id`, `requester_user_id`, `requester_first_name`, `requester_last_name`, `requester_contact_number`, `requester_email`, `dentist_id`, `modified_by`, `created_at`, `updated_at`, `booking_type`) VALUES
(2, '2026-03-16 09:00:00', 'Scheduled', 1, 1, 9, 'PATIENT 1', 'PATIENT 1', '123132312', 'nobovaruco@sharebot.net', NULL, 'nobovaruco@sharebot.net', '2026-03-15 16:41:31', '2026-03-15 16:42:02', 'online_appointment');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_slots`
--

CREATE TABLE `blocked_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `chair_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dental_charts`
--

CREATE TABLE `dental_charts` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `chart_data` longtext NOT NULL,
  `modified_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health_histories`
--

CREATE TABLE `health_histories` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `when_last_visit_q1` date DEFAULT NULL,
  `what_last_visit_reason_q1` text DEFAULT NULL,
  `what_seeing_dentist_reason_q2` text DEFAULT NULL,
  `is_clicking_jaw_q3a` tinyint(1) DEFAULT NULL,
  `is_pain_jaw_q3b` tinyint(1) DEFAULT NULL,
  `is_difficulty_opening_closing_q3c` tinyint(1) DEFAULT NULL,
  `is_locking_jaw_q3d` tinyint(1) DEFAULT NULL,
  `is_clench_grind_q4` tinyint(1) DEFAULT NULL,
  `is_bad_experience_q5` tinyint(1) DEFAULT NULL,
  `is_nervous_q6` tinyint(1) DEFAULT NULL,
  `what_nervous_concern_q6` text DEFAULT NULL,
  `is_condition_q1` tinyint(1) DEFAULT NULL,
  `what_condition_reason_q1` text DEFAULT NULL,
  `is_hospitalized_q2` tinyint(1) DEFAULT NULL,
  `what_hospitalized_reason_q2` text DEFAULT NULL,
  `is_serious_illness_operation_q3` tinyint(1) DEFAULT NULL,
  `what_serious_illness_operation_reason_q3` text DEFAULT NULL,
  `is_taking_medications_q4` tinyint(1) DEFAULT NULL,
  `what_medications_list_q4` text DEFAULT NULL,
  `is_allergic_medications_q5` tinyint(1) DEFAULT NULL,
  `what_allergies_list_q5` text DEFAULT NULL,
  `is_allergic_latex_rubber_metals_q6` tinyint(1) DEFAULT NULL,
  `is_pregnant_q7` tinyint(1) DEFAULT NULL,
  `is_breast_feeding_q8` tinyint(1) DEFAULT NULL,
  `modified_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `notes` mediumtext NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `office_address` text DEFAULT NULL,
  `home_number` varchar(20) DEFAULT NULL,
  `office_number` varchar(20) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `referral` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `who_answering` varchar(255) DEFAULT NULL,
  `relationship_to_patient` varchar(50) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `father_number` varchar(20) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mother_number` varchar(20) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_number` varchar(20) DEFAULT NULL,
  `modified_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `last_name`, `first_name`, `mobile_number`, `middle_name`, `nickname`, `occupation`, `birth_date`, `gender`, `civil_status`, `home_address`, `office_address`, `home_number`, `office_number`, `email_address`, `referral`, `emergency_contact_name`, `emergency_contact_number`, `relationship`, `who_answering`, `relationship_to_patient`, `father_name`, `father_number`, `mother_name`, `mother_number`, `guardian_name`, `guardian_number`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 'PATIENT 1', 'PATIENT 1', '123132312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'nobovaruco@sharebot.net', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-15 16:42:02', '2026-03-15 16:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `patient_form_drafts`
--

CREATE TABLE `patient_form_drafts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `mode` varchar(10) NOT NULL,
  `step` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `payload_json` longtext NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_records`
--

CREATE TABLE `patient_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(2, 'staff'),
(3, 'patient');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `duration` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `duration`) VALUES
(1, 'Cleaning', '01:00:00'),
(2, 'Tooth extractions', '01:00:00'),
(3, 'Full Consultation', '01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `treatment_records`
--

CREATE TABLE `treatment_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `dental_chart_id` int(11) NOT NULL,
  `dmd` varchar(255) DEFAULT NULL,
  `treatment` varchar(255) DEFAULT NULL,
  `cost_of_treatment` decimal(10,2) DEFAULT NULL,
  `amount_charged` decimal(10,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `image` longtext DEFAULT NULL,
  `modified_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_record_images`
--

CREATE TABLE `treatment_record_images` (
  `id` int(11) NOT NULL,
  `treatment_record_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_type` enum('before','after','other') NOT NULL DEFAULT 'other',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `role` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `email_verified_at`, `verification_token`, `password`, `google_id`, `role`, `created_at`, `updated_at`) VALUES
(8, 'renzzluigi@gmail.com', 'renzzluigi@gmail.com', '2026-03-15 16:27:17', NULL, '$2y$12$ee5/UeDYjyOG6gqZDZujqu0oThWoDl45FWS3L3UGcNK5b1W.aMiHO', '114082662441983874861', 1, '2026-03-16 00:27:17', '2026-03-16 00:27:35'),
(9, 'nobovaruco@sharebot.net', 'nobovaruco@sharebot.net', '2026-03-15 16:30:27', NULL, '$2y$12$LCBGQ7rRbPpqaa1Dar5ai.OZU87xW99P9GTth6DgI7fw64cgR.7y.', NULL, 3, '2026-03-16 00:29:22', '2026-03-16 00:30:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `log_name` (`log_name`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `fk_appointments_dentist` (`dentist_id`),
  ADD KEY `appointments_requester_user_id_index` (`requester_user_id`),
  ADD KEY `appointments_requester_email_index` (`requester_email`);

--
-- Indexes for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blocked_slots_date_start_time_index` (`date`,`start_time`),
  ADD KEY `blocked_slots_date_end_time_index` (`date`,`end_time`),
  ADD KEY `blocked_slots_chair_id_index` (`chair_id`);

--
-- Indexes for table `dental_charts`
--
ALTER TABLE `dental_charts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `health_histories`
--
ALTER TABLE `health_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_health_history_patient` (`patient_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `patients` ADD FULLTEXT KEY `mobile_number_2` (`mobile_number`);

--
-- Indexes for table `patient_form_drafts`
--
ALTER TABLE `patient_form_drafts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_form_drafts_context_unique` (`user_id`,`mode`,`patient_id`),
  ADD KEY `patient_form_drafts_expires_at_index` (`expires_at`);

--
-- Indexes for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `patient_records_ibfk_1` (`patient_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treatment_records`
--
ALTER TABLE `treatment_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `dental_chart_id` (`dental_chart_id`);

--
-- Indexes for table `treatment_record_images`
--
ALTER TABLE `treatment_record_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `treatment_record_images_treatment_record_id_idx` (`treatment_record_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_id` (`id`,`password`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dental_charts`
--
ALTER TABLE `dental_charts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `health_histories`
--
ALTER TABLE `health_histories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_form_drafts`
--
ALTER TABLE `patient_form_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_records`
--
ALTER TABLE `patient_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `treatment_records`
--
ALTER TABLE `treatment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment_record_images`
--
ALTER TABLE `treatment_record_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `fk_appointments_dentist` FOREIGN KEY (`dentist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dental_charts`
--
ALTER TABLE `dental_charts`
  ADD CONSTRAINT `dental_charts_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `health_histories`
--
ALTER TABLE `health_histories`
  ADD CONSTRAINT `fk_health_history_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_records`
--
ALTER TABLE `patient_records`
  ADD CONSTRAINT `patient_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `patient_records_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`);

--
-- Constraints for table `treatment_records`
--
ALTER TABLE `treatment_records`
  ADD CONSTRAINT `treatment_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `treatment_records_ibfk_2` FOREIGN KEY (`dental_chart_id`) REFERENCES `dental_charts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `treatment_record_images`
--
ALTER TABLE `treatment_record_images`
  ADD CONSTRAINT `treatment_record_images_treatment_record_id_fk` FOREIGN KEY (`treatment_record_id`) REFERENCES `treatment_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
