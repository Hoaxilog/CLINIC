-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2026 at 09:04 AM
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
(2, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 2, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-15 16:42:02', '2026-03-15 16:42:02'),
(3, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 3, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-15 17:36:30', '2026-03-15 17:36:30'),
(4, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 4, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-15 18:29:50', '2026-03-15 18:29:50'),
(5, 'default', 'Created Patient from Appointment Request', 'App\\Models\\Patient', 'patient_created_from_request', 3, 'App\\Models\\User', 8, '{\"attributes\":{\"patient_id\":3,\"source_appointment_id\":5,\"first_name\":\"A\",\"last_name\":\"B\"}}', NULL, '2026-03-15 18:37:35', '2026-03-15 18:37:35'),
(6, 'default', 'Linked Appointment Request to New Patient', 'App\\Models\\Appointment', 'appointment_request_linked_new_patient', 5, 'App\\Models\\User', 8, '{\"attributes\":{\"appointment_id\":5,\"patient_id\":3}}', NULL, '2026-03-15 18:37:35', '2026-03-15 18:37:35'),
(7, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 5, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-15 18:39:17', '2026-03-15 18:39:17'),
(8, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 5, 'App\\Models\\User', 8, '{\"attributes\":{\"patient_id\":3,\"appointment_id\":5}}', NULL, '2026-03-15 18:39:17', '2026-03-15 18:39:17'),
(9, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 5, 'App\\Models\\User', 8, '{\"attributes\":{\"appointment_id\":5,\"patient_id\":3,\"status\":\"Scheduled\"}}', NULL, '2026-03-15 18:39:17', '2026-03-15 18:39:17'),
(10, 'default', 'Created Patient from Appointment Request', 'App\\Models\\Patient', 'patient_created_from_request', 4, 'App\\Models\\User', 8, '{\"attributes\":{\"patient_id\":4,\"source_appointment_id\":7,\"first_name\":\"MAMA\",\"last_name\":\"MIA\"}}', NULL, '2026-03-16 09:00:20', '2026-03-16 09:00:20'),
(11, 'default', 'Linked Appointment Request to New Patient', 'App\\Models\\Appointment', 'appointment_request_linked_new_patient', 7, 'App\\Models\\User', 8, '{\"attributes\":{\"appointment_id\":7,\"patient_id\":4}}', NULL, '2026-03-16 09:00:20', '2026-03-16 09:00:20'),
(12, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 7, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-16 09:00:50', '2026-03-16 09:00:50'),
(13, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 7, 'App\\Models\\User', 8, '{\"attributes\":{\"patient_id\":4,\"appointment_id\":7}}', NULL, '2026-03-16 09:00:50', '2026-03-16 09:00:50'),
(14, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 7, 'App\\Models\\User', 8, '{\"attributes\":{\"appointment_id\":7,\"patient_id\":4,\"status\":\"Scheduled\"}}', NULL, '2026-03-16 09:00:50', '2026-03-16 09:00:50'),
(15, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 6, 'App\\Models\\User', 8, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-17 03:51:44', '2026-03-17 03:51:44'),
(16, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 6, 'App\\Models\\User', 8, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":6}}', NULL, '2026-03-17 03:51:44', '2026-03-17 03:51:44'),
(17, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 6, 'App\\Models\\User', 8, '{\"attributes\":{\"appointment_id\":6,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-17 03:51:44', '2026-03-17 03:51:44'),
(18, 'default', 'Patient Cancelled Appointment', 'App\\Models\\Appointment', 'appointment_cancelled_by_patient', 8, 'App\\Models\\User', 10, '{\"appointment_id\":8,\"appointment_date\":\"2026-03-17 19:00:00\"}', NULL, '2026-03-17 13:16:02', '2026-03-17 13:16:02'),
(19, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 9, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-17 19:04:07', '2026-03-17 19:04:07'),
(20, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 9, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":9}}', NULL, '2026-03-17 19:04:07', '2026-03-17 19:04:07'),
(21, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 9, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":9,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-17 19:04:07', '2026-03-17 19:04:07'),
(22, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 10, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-17 19:04:38', '2026-03-17 19:04:38'),
(23, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 10, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":10}}', NULL, '2026-03-17 19:04:38', '2026-03-17 19:04:38'),
(24, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 10, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":10,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-17 19:04:38', '2026-03-17 19:04:38'),
(25, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 10, 'App\\Models\\User', 10, '{\"old\":{\"username\":\"renzzluigi@gmail.com\",\"email\":\"renzzluigi@gmail.com\"},\"attributes\":{\"username\":\"renzzluigis@gmail.com\",\"email\":\"renzzluigis@gmail.com\"}}', NULL, '2026-03-17 20:27:09', '2026-03-17 20:27:09'),
(26, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 10, 'App\\Models\\User', 10, '{\"old\":{\"username\":\"renzzluigis@gmail.com\",\"email\":\"renzzluigis@gmail.com\"},\"attributes\":{\"username\":\"renzzluigi@gmail.com\",\"email\":\"renzzluigi@gmail.com\"}}', NULL, '2026-03-17 20:27:27', '2026-03-17 20:27:27'),
(27, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 12, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-17 22:10:18', '2026-03-17 22:10:18'),
(28, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 12, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":12}}', NULL, '2026-03-17 22:10:18', '2026-03-17 22:10:18'),
(29, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 12, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":12,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-17 22:10:18', '2026-03-17 22:10:18'),
(30, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 11, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-18 01:22:54', '2026-03-18 01:22:54'),
(31, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 11, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":11}}', NULL, '2026-03-18 01:22:54', '2026-03-18 01:22:54'),
(32, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 11, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":11,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-18 01:22:54', '2026-03-18 01:22:54'),
(33, 'default', 'Created Patient from Appointment Request', 'App\\Models\\Patient', 'patient_created_from_request', 5, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":5,\"source_appointment_id\":9,\"first_name\":\"RENZ\",\"last_name\":\"ROSALES\"}}', NULL, '2026-03-18 06:20:58', '2026-03-18 06:20:58'),
(34, 'default', 'Linked Appointment Request to New Patient', 'App\\Models\\Appointment', 'appointment_request_linked_new_patient', 9, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":9,\"patient_id\":5}}', NULL, '2026-03-18 06:20:58', '2026-03-18 06:20:58'),
(35, 'default', 'Created Patient', 'App\\Models\\Patient', 'patient_created', 6, 'App\\Models\\User', 10, '{\"attributes\":{\"last_name\":\"my last name\",\"first_name\":\"my first name\",\"middle_name\":\"my middle name\",\"nickname\":\"\",\"occupation\":\"occupation\",\"birth_date\":\"2026-03-18\",\"gender\":\"Male\",\"civil_status\":\"single\",\"home_address\":\"ropsal\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"123312123\",\"email_address\":\"me@mydomain.com\",\"referral\":\"\",\"emergency_contact_name\":\"ewq\",\"emergency_contact_number\":\"231\",\"relationship\":\"asd\",\"who_answering\":\"qwe\",\"relationship_to_patient\":\"sdasad\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\"}}', NULL, '2026-03-18 08:15:16', '2026-03-18 08:15:16'),
(36, 'default', 'Created Health History', 'App\\Models\\Patient', 'health_history_created', 6, 'App\\Models\\User', 10, '{\"health_history_id\":1,\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"masakit ngipin\",\"is_clicking_jaw_q3a\":0,\"is_pain_jaw_q3b\":0,\"is_difficulty_opening_closing_q3c\":0,\"is_locking_jaw_q3d\":0,\"is_clench_grind_q4\":0,\"is_bad_experience_q5\":0,\"is_nervous_q6\":0,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":0,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":0,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":0,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":0,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":0,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":0,\"is_pregnant_q7\":0,\"is_breast_feeding_q8\":0,\"selectedHistoryId\":\"new\"}}', NULL, '2026-03-18 08:15:16', '2026-03-18 08:15:16'),
(37, 'default', 'Created Walk-in Appointment', 'App\\Models\\Appointment', 'appointment_created', 15, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":6,\"patient_name\":\"my last name, my first name my middle name\",\"status\":\"Waiting\"}}', NULL, '2026-03-18 08:15:16', '2026-03-18 08:15:16'),
(38, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 15, 'App\\Models\\User', 10, '[]', NULL, '2026-03-18 08:16:31', '2026-03-18 08:16:31'),
(39, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 11, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-18 15:57:29', '2026-03-18 15:57:29'),
(40, 'default', 'Unlinked Appointment from Patient Record', 'App\\Models\\Appointment', 'appointment_patient_unlinked', 9, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":9,\"previous_patient_id\":5}}', NULL, '2026-03-18 16:34:44', '2026-03-18 16:34:44'),
(41, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 13, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-18 20:06:10', '2026-03-18 20:06:10'),
(42, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 13, 'App\\Models\\User', 10, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":13}}', NULL, '2026-03-18 20:06:10', '2026-03-18 20:06:10'),
(43, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 13, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":13,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-18 20:06:10', '2026-03-18 20:06:10'),
(44, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 42, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\",\"patient_name\":\"Reyes, Alyssa Demo\"}}', NULL, '2026-03-19 00:53:45', '2026-03-19 00:53:45'),
(45, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 42, 'App\\Models\\User', 10, '[]', NULL, '2026-03-19 00:53:53', '2026-03-19 00:53:53'),
(46, 'default', 'Cancelled Appointment', 'App\\Models\\Appointment', 'appointment_cancelled', 42, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Cancelled\",\"patient_name\":\"Reyes, Alyssa Demo\"}}', NULL, '2026-03-19 00:56:37', '2026-03-19 00:56:37'),
(47, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 44, 'App\\Models\\User', 10, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\",\"patient_name\":\"Torres, Shane Demo\"}}', NULL, '2026-03-19 00:56:43', '2026-03-19 00:56:43');

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
  `requester_birth_date` date DEFAULT NULL,
  `requester_contact_number` varchar(30) DEFAULT NULL,
  `requester_email` varchar(255) DEFAULT NULL,
  `booking_for_other` tinyint(1) NOT NULL DEFAULT 0,
  `requested_patient_first_name` varchar(255) DEFAULT NULL,
  `requested_patient_last_name` varchar(255) DEFAULT NULL,
  `requested_patient_birth_date` date DEFAULT NULL,
  `requester_relationship_to_patient` varchar(255) DEFAULT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `modified_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `booking_type` varchar(30) NOT NULL DEFAULT 'online_appointment',
  `cancellation_reason` text DEFAULT NULL,
  `requester_middle_name` varchar(255) DEFAULT NULL,
  `requested_patient_middle_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_date`, `status`, `service_id`, `patient_id`, `requester_user_id`, `requester_first_name`, `requester_last_name`, `requester_birth_date`, `requester_contact_number`, `requester_email`, `booking_for_other`, `requested_patient_first_name`, `requested_patient_last_name`, `requested_patient_birth_date`, `requester_relationship_to_patient`, `dentist_id`, `modified_by`, `created_at`, `updated_at`, `booking_type`, `cancellation_reason`, `requester_middle_name`, `requested_patient_middle_name`) VALUES
(4, '2026-03-16 10:00:00', 'Cancelled', 3, NULL, NULL, 'PATIENT 3', 'PATIENT 3', NULL, '02', 'abcd@a.com', 0, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-03-15 18:27:29', '2026-03-15 18:29:50', 'online_appointment', NULL, NULL, NULL),
(5, '2026-03-16 17:00:00', 'Scheduled', 2, 3, NULL, 'A', 'B', NULL, '21', 'AB@GA.COM', 0, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-03-15 18:35:50', '2026-03-15 18:39:17', 'online_appointment', NULL, NULL, NULL),
(6, '2026-03-16 16:00:00', 'Scheduled', 2, NULL, NULL, 'A', 'B', NULL, '09', 'AAA@GA.COM', 0, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-03-15 18:39:57', '2026-03-17 03:51:44', 'online_appointment', NULL, NULL, NULL),
(8, '2026-03-17 19:00:00', 'Cancelled', 2, NULL, 10, 'RENZ', 'ROSALES', '2003-04-29', '09979775797', 'renzzluigi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 10:51:36', '2026-03-17 13:16:02', 'online_appointment', NULL, NULL, NULL),
(9, '2026-03-18 09:00:00', 'Scheduled', 2, NULL, 10, 'RENZ', 'ROSALES', '2003-04-29', '09979775797', 'renzzluigi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 15:17:23', '2026-03-18 16:34:43', 'online_appointment', NULL, NULL, NULL),
(10, '2026-03-18 12:00:00', 'Scheduled', 2, NULL, 10, 'RENZ', 'ROSALES', '2003-04-29', '09979775797', 'renzzluigi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 17:46:35', '2026-03-17 19:04:38', 'online_appointment', NULL, NULL, NULL),
(11, '2026-03-18 18:00:00', 'Waiting', 1, NULL, 10, 'RENZ', 'ROSALES', '2003-04-29', '09979775797', 'renzzldsauigi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 18:40:26', '2026-03-18 15:57:29', 'online_appointment', NULL, NULL, NULL),
(12, '2026-03-18 13:00:00', 'Scheduled', 3, NULL, NULL, 'DS', 'ADSA', '2026-03-17', '31232132132', 'ngz9zxfy@eacademia.uk', 0, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-03-17 22:05:17', '2026-03-17 22:10:18', 'online_appointment', NULL, NULL, NULL),
(13, '2026-03-19 12:00:00', 'Scheduled', 2, NULL, 10, 'RENZ', 'ROSALES', '2003-04-29', '09979775797', 'renzzluigi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 22:34:20', '2026-03-18 20:06:10', 'online_appointment', NULL, NULL, NULL),
(14, '2026-03-18 18:00:00', 'Pending', 2, NULL, 10, 'hfg', 'hfghfg', '2003-04-29', '09979775797', 'dsadasi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 22:36:06', '2026-03-17 22:36:06', 'online_appointment', NULL, NULL, NULL),
(15, '2026-03-18 16:15:16', 'Ongoing', 1, 6, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 10, 'renzzluigi@gmail.com', '2026-03-18 08:15:16', '2026-03-18 08:16:31', 'walk_in', NULL, NULL, NULL),
(17, '2026-03-16 09:00:00', 'Completed', 1, 45, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-14 01:00:00', '2026-03-16 01:00:00', 'walk_in', NULL, NULL, NULL),
(18, '2026-03-11 10:00:00', 'Completed', 2, 46, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-09 02:00:00', '2026-03-11 02:00:00', 'walk_in', NULL, NULL, NULL),
(19, '2026-03-06 11:00:00', 'Completed', 3, 47, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-04 03:00:00', '2026-03-06 03:00:00', 'walk_in', NULL, NULL, NULL),
(20, '2026-02-23 13:00:00', 'Completed', 4, 48, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-21 05:00:00', '2026-02-23 05:00:00', 'walk_in', NULL, NULL, NULL),
(21, '2026-02-18 14:00:00', 'Completed', 5, 49, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-16 06:00:00', '2026-02-18 06:00:00', 'walk_in', NULL, NULL, NULL),
(22, '2026-02-13 15:00:00', 'Completed', 6, 50, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-11 07:00:00', '2026-02-13 07:00:00', 'walk_in', NULL, NULL, NULL),
(23, '2026-03-16 16:00:00', 'Completed', 7, 51, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-14 08:00:00', '2026-03-16 08:00:00', 'walk_in', NULL, NULL, NULL),
(24, '2026-03-11 09:00:00', 'Completed', 8, 52, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-09 01:00:00', '2026-03-11 01:00:00', 'walk_in', NULL, NULL, NULL),
(25, '2026-03-06 10:00:00', 'Completed', 9, 53, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-04 02:00:00', '2026-03-06 02:00:00', 'walk_in', NULL, NULL, NULL),
(26, '2026-02-23 11:00:00', 'Completed', 10, 54, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-21 03:00:00', '2026-02-23 03:00:00', 'walk_in', NULL, NULL, NULL),
(27, '2026-02-18 13:00:00', 'Completed', 11, 55, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-16 05:00:00', '2026-02-18 05:00:00', 'walk_in', NULL, NULL, NULL),
(28, '2026-02-13 14:00:00', 'Completed', 12, 56, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-11 06:00:00', '2026-02-13 06:00:00', 'walk_in', NULL, NULL, NULL),
(29, '2026-03-16 15:00:00', 'Completed', 13, 57, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-14 07:00:00', '2026-03-16 07:00:00', 'walk_in', NULL, NULL, NULL),
(30, '2026-03-11 16:00:00', 'Completed', 16, 58, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-09 08:00:00', '2026-03-11 08:00:00', 'walk_in', NULL, NULL, NULL),
(31, '2026-03-06 09:00:00', 'Completed', 1, 59, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-04 01:00:00', '2026-03-06 01:00:00', 'walk_in', NULL, NULL, NULL),
(32, '2026-02-23 10:00:00', 'Completed', 2, 60, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-21 02:00:00', '2026-02-23 02:00:00', 'walk_in', NULL, NULL, NULL),
(33, '2026-02-18 11:00:00', 'Completed', 3, 61, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-16 03:00:00', '2026-02-18 03:00:00', 'walk_in', NULL, NULL, NULL),
(34, '2026-02-13 13:00:00', 'Completed', 4, 62, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-02-11 05:00:00', '2026-02-13 05:00:00', 'walk_in', NULL, NULL, NULL),
(35, '2026-03-16 14:00:00', 'Completed', 5, 63, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-14 06:00:00', '2026-03-16 06:00:00', 'walk_in', NULL, NULL, NULL),
(36, '2026-03-11 15:00:00', 'Completed', 6, 64, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-09 07:00:00', '2026-03-11 07:00:00', 'walk_in', NULL, NULL, NULL),
(37, '2026-03-20 09:00:00', 'Pending', 1, 45, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(38, '2026-03-21 10:00:00', 'Pending', 2, 46, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(39, '2026-03-22 11:00:00', 'Pending', 3, 47, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(40, '2026-03-23 12:00:00', 'Pending', 4, 48, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(41, '2026-03-24 13:00:00', 'Pending', 5, 49, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(42, '2026-03-19 10:00:00', 'Cancelled', 2, 50, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 10, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:56:37', 'online_appointment', NULL, NULL, NULL),
(43, '2026-03-19 11:00:00', 'Scheduled', 3, 51, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(44, '2026-03-19 12:00:00', 'Waiting', 4, 52, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:56:43', 'online_appointment', NULL, NULL, NULL),
(45, '2026-03-19 13:00:00', 'Scheduled', 5, 53, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06', 'online_appointment', NULL, NULL, NULL),
(46, '2026-02-02 09:00:00', 'Completed', 1, 45, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-01 01:00:00', '2026-02-02 01:00:00', 'walk_in', NULL, NULL, NULL),
(47, '2026-02-04 10:00:00', 'Completed', 2, 46, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-03 02:00:00', '2026-02-04 02:00:00', 'walk_in', NULL, NULL, NULL),
(48, '2026-02-06 11:00:00', 'Completed', 3, 47, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-05 03:00:00', '2026-02-06 03:00:00', 'walk_in', NULL, NULL, NULL),
(49, '2026-02-08 13:00:00', 'Completed', 4, 48, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-07 05:00:00', '2026-02-08 05:00:00', 'walk_in', NULL, NULL, NULL),
(50, '2026-02-10 14:00:00', 'Completed', 5, 49, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-09 06:00:00', '2026-02-10 06:00:00', 'walk_in', NULL, NULL, NULL),
(51, '2026-02-12 15:00:00', 'Completed', 6, 50, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-11 07:00:00', '2026-02-12 07:00:00', 'walk_in', NULL, NULL, NULL),
(52, '2026-02-14 09:00:00', 'Completed', 7, 51, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-13 01:00:00', '2026-02-14 01:00:00', 'walk_in', NULL, NULL, NULL),
(53, '2026-02-16 10:00:00', 'Completed', 8, 52, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-15 02:00:00', '2026-02-16 02:00:00', 'walk_in', NULL, NULL, NULL),
(54, '2026-02-18 11:00:00', 'Completed', 9, 53, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-17 03:00:00', '2026-02-18 03:00:00', 'walk_in', NULL, NULL, NULL),
(55, '2026-02-20 13:00:00', 'Completed', 10, 54, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-19 05:00:00', '2026-02-20 05:00:00', 'walk_in', NULL, NULL, NULL),
(56, '2026-02-22 14:00:00', 'Completed', 11, 55, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-21 06:00:00', '2026-02-22 06:00:00', 'walk_in', NULL, NULL, NULL),
(57, '2026-02-24 15:00:00', 'Completed', 12, 56, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 51, 'previous-month-seeder', '2026-02-23 07:00:00', '2026-02-24 07:00:00', 'walk_in', NULL, NULL, NULL);

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

--
-- Dumping data for table `blocked_slots`
--

INSERT INTO `blocked_slots` (`id`, `date`, `start_time`, `end_time`, `chair_id`, `reason`, `created_by`, `created_at`, `updated_at`) VALUES
(1, '2026-03-20', '09:00:00', '10:00:00', NULL, NULL, 'renzzluigi@gmail.com', '2026-03-19 16:47:58', '2026-03-19 16:47:58'),
(2, '2026-03-20', '10:00:00', '11:00:00', NULL, NULL, 'renzzluigi@gmail.com', '2026-03-19 16:48:18', '2026-03-19 16:48:18');

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

--
-- Dumping data for table `dental_charts`
--

INSERT INTO `dental_charts` (`id`, `patient_id`, `chart_data`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 45, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-16 01:00:00', '2026-03-16 01:00:00'),
(2, 46, '{\"teeth\":{\"16\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-11 02:00:00', '2026-03-11 02:00:00'),
(3, 47, '{\"teeth\":{\"21\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-06 03:00:00', '2026-03-06 03:00:00'),
(4, 48, '{\"teeth\":{\"26\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-23 05:00:00', '2026-02-23 05:00:00'),
(5, 49, '{\"teeth\":{\"31\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-18 06:00:00', '2026-02-18 06:00:00'),
(6, 50, '{\"teeth\":{\"36\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-13 07:00:00', '2026-02-13 07:00:00'),
(7, 51, '{\"teeth\":{\"41\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-16 08:00:00', '2026-03-16 08:00:00'),
(8, 52, '{\"teeth\":{\"46\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-11 01:00:00', '2026-03-11 01:00:00'),
(9, 53, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-06 02:00:00', '2026-03-06 02:00:00'),
(10, 54, '{\"teeth\":{\"16\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-23 03:00:00', '2026-02-23 03:00:00'),
(11, 55, '{\"teeth\":{\"21\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-18 05:00:00', '2026-02-18 05:00:00'),
(12, 56, '{\"teeth\":{\"26\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-13 06:00:00', '2026-02-13 06:00:00'),
(13, 57, '{\"teeth\":{\"31\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-16 07:00:00', '2026-03-16 07:00:00'),
(14, 58, '{\"teeth\":{\"36\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-11 08:00:00', '2026-03-11 08:00:00'),
(15, 59, '{\"teeth\":{\"41\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-06 01:00:00', '2026-03-06 01:00:00'),
(16, 60, '{\"teeth\":{\"46\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-23 02:00:00', '2026-02-23 02:00:00'),
(17, 61, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-18 03:00:00', '2026-02-18 03:00:00'),
(18, 62, '{\"teeth\":{\"16\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-02-13 05:00:00', '2026-02-13 05:00:00'),
(19, 63, '{\"teeth\":{\"21\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-16 06:00:00', '2026-03-16 06:00:00'),
(20, 64, '{\"teeth\":{\"26\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Auto-generated defense chart entry.\",\"treatment_plan\":\"Continue preventive care and scheduled treatment.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'defense-seeder', '2026-03-11 07:00:00', '2026-03-11 07:00:00'),
(21, 45, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-02 01:00:00', '2026-02-02 01:00:00'),
(22, 46, '{\"teeth\":{\"16\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-04 02:00:00', '2026-02-04 02:00:00'),
(23, 47, '{\"teeth\":{\"21\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-06 03:00:00', '2026-02-06 03:00:00'),
(24, 48, '{\"teeth\":{\"26\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-08 05:00:00', '2026-02-08 05:00:00'),
(25, 49, '{\"teeth\":{\"31\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-10 06:00:00', '2026-02-10 06:00:00'),
(26, 50, '{\"teeth\":{\"36\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-12 07:00:00', '2026-02-12 07:00:00'),
(27, 51, '{\"teeth\":{\"41\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-14 01:00:00', '2026-02-14 01:00:00'),
(28, 52, '{\"teeth\":{\"46\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-16 02:00:00', '2026-02-16 02:00:00'),
(29, 53, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-18 03:00:00', '2026-02-18 03:00:00'),
(30, 54, '{\"teeth\":{\"16\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-20 05:00:00', '2026-02-20 05:00:00'),
(31, 55, '{\"teeth\":{\"21\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-22 06:00:00', '2026-02-22 06:00:00'),
(32, 56, '{\"teeth\":{\"26\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Normal\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Additional previous-month completed record.\",\"treatment_plan\":\"Continue follow-up care as needed.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'previous-month-seeder', '2026-02-24 07:00:00', '2026-02-24 07:00:00');

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

--
-- Dumping data for table `health_histories`
--

INSERT INTO `health_histories` (`id`, `patient_id`, `when_last_visit_q1`, `what_last_visit_reason_q1`, `what_seeing_dentist_reason_q2`, `is_clicking_jaw_q3a`, `is_pain_jaw_q3b`, `is_difficulty_opening_closing_q3c`, `is_locking_jaw_q3d`, `is_clench_grind_q4`, `is_bad_experience_q5`, `is_nervous_q6`, `what_nervous_concern_q6`, `is_condition_q1`, `what_condition_reason_q1`, `is_hospitalized_q2`, `what_hospitalized_reason_q2`, `is_serious_illness_operation_q3`, `what_serious_illness_operation_reason_q3`, `is_taking_medications_q4`, `what_medications_list_q4`, `is_allergic_medications_q5`, `what_allergies_list_q5`, `is_allergic_latex_rubber_metals_q6`, `is_pregnant_q7`, `is_breast_feeding_q8`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 6, NULL, '', 'masakit ngipin', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'renzzluigi@gmail.com', '2026-03-18 08:15:16', '2026-03-18 08:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(120) NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `actor_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `cleared_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(3, 'B', 'A', '21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'AB@GA.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-15 18:37:35', '2026-03-15 18:37:35'),
(4, 'MIA', 'MAMA', '09979775797', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-16 09:00:20', '2026-03-16 09:00:20'),
(5, 'ROSALES', 'RENZ', '09979775797', NULL, NULL, NULL, '2003-04-29', NULL, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-18 06:20:58', '2026-03-18 06:20:58'),
(6, 'my last name', 'my first name', '123312123', 'my middle name', '', 'occupation', '2026-03-18', 'Male', 'single', 'ropsal', '', '', '', 'me@mydomain.com', '', 'ewq', '231', 'asd', 'qwe', 'sdasad', '', '', '', '', '', '', 'renzzluigi@gmail.com', '2026-03-18 08:15:16', '2026-03-18 08:15:16'),
(45, 'Rosales', 'Renz', '0917100001', 'Demo', 'Renz', 'Teacher', '2005-03-18', 'Male', 'Single', 'Demo Address 1, Tejada City', NULL, NULL, NULL, 'renzrosales1@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 1', '0918200001', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:01', '2026-03-19 00:22:01'),
(46, 'Santos', 'Mia', '0917100002', 'Demo', 'Mia', 'Engineer', '2004-03-17', 'Female', 'Single', 'Demo Address 2, Tejada City', NULL, NULL, NULL, 'miasantos2@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 2', '0918200002', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:02', '2026-03-19 00:22:02'),
(47, 'Dela Cruz', 'Paolo', '0917100003', 'Demo', 'Paolo', 'Cashier', '2003-03-16', 'Male', 'Single', 'Demo Address 3, Tejada City', NULL, NULL, NULL, 'paolodela-cruz3@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 3', '0918200003', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:02', '2026-03-19 00:22:02'),
(48, 'Garcia', 'Jessa', '0917100004', 'Demo', 'Jessa', 'OFW', '2002-03-15', 'Female', 'Married', 'Demo Address 4, Tejada City', NULL, NULL, NULL, 'jessagarcia4@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 4', '0918200004', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:02', '2026-03-19 00:22:02'),
(49, 'Mendoza', 'Carlo', '0917100005', 'Demo', 'Carlo', 'Freelancer', '2001-03-14', 'Male', 'Single', 'Demo Address 5, Tejada City', NULL, NULL, NULL, 'carlomendoza5@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 5', '0918200005', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:02', '2026-03-19 00:22:02'),
(50, 'Reyes', 'Alyssa', '0917100006', 'Demo', 'Alyssa', 'Student', '2000-03-13', 'Female', 'Single', 'Demo Address 6, Tejada City', NULL, NULL, NULL, 'alyssareyes6@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 6', '0918200006', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:02', '2026-03-19 00:22:02'),
(51, 'Navarro', 'Mark', '0917100007', 'Demo', 'Mark', 'Teacher', '1999-03-12', 'Male', 'Single', 'Demo Address 7, Tejada City', NULL, NULL, NULL, 'marknavarro7@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 7', '0918200007', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:03', '2026-03-19 00:22:03'),
(52, 'Torres', 'Shane', '0917100008', 'Demo', 'Shane', 'Engineer', '1998-03-11', 'Female', 'Married', 'Demo Address 8, Tejada City', NULL, NULL, NULL, 'shanetorres8@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 8', '0918200008', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:03', '2026-03-19 00:22:03'),
(53, 'Aquino', 'Nicole', '0917100009', 'Demo', 'Nicole', 'Cashier', '1997-03-10', 'Male', 'Single', 'Demo Address 9, Tejada City', NULL, NULL, NULL, 'nicoleaquino9@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 9', '0918200009', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:03', '2026-03-19 00:22:03'),
(54, 'Villanueva', 'Adrian', '0917100010', 'Demo', 'Adrian', 'OFW', '1996-03-09', 'Female', 'Single', 'Demo Address 10, Tejada City', NULL, NULL, NULL, 'adrianvillanueva10@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 10', '0918200010', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:03', '2026-03-19 00:22:03'),
(55, 'Castillo', 'Bianca', '0917100011', 'Demo', 'Bianca', 'Freelancer', '1995-03-08', 'Male', 'Single', 'Demo Address 11, Tejada City', NULL, NULL, NULL, 'biancacastillo11@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 11', '0918200011', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:04', '2026-03-19 00:22:04'),
(56, 'Ramos', 'Joshua', '0917100012', 'Demo', 'Joshua', 'Student', '1994-03-07', 'Female', 'Married', 'Demo Address 12, Tejada City', NULL, NULL, NULL, 'joshuaramos12@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 12', '0918200012', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:04', '2026-03-19 00:22:04'),
(57, 'Bautista', 'Andrea', '0917100013', 'Demo', 'Andrea', 'Teacher', '1993-03-06', 'Male', 'Single', 'Demo Address 13, Tejada City', NULL, NULL, NULL, 'andreabautista13@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 13', '0918200013', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:04', '2026-03-19 00:22:04'),
(58, 'Domingo', 'Kyle', '0917100014', 'Demo', 'Kyle', 'Engineer', '1992-03-05', 'Female', 'Single', 'Demo Address 14, Tejada City', NULL, NULL, NULL, 'kyledomingo14@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 14', '0918200014', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:04', '2026-03-19 00:22:04'),
(59, 'Soriano', 'Patricia', '0917100015', 'Demo', 'Patricia', 'Cashier', '1991-03-04', 'Male', 'Single', 'Demo Address 15, Tejada City', NULL, NULL, NULL, 'patriciasoriano15@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 15', '0918200015', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:04', '2026-03-19 00:22:04'),
(60, 'Manalang', 'Jomel', '0917100016', 'Demo', 'Jomel', 'OFW', '1990-03-03', 'Female', 'Married', 'Demo Address 16, Tejada City', NULL, NULL, NULL, 'jomelmanalang16@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 16', '0918200016', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:05', '2026-03-19 00:22:05'),
(61, 'Lopez', 'Kristine', '0917100017', 'Demo', 'Kristine', 'Freelancer', '1989-03-02', 'Male', 'Single', 'Demo Address 17, Tejada City', NULL, NULL, NULL, 'kristinelopez17@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 17', '0918200017', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:05', '2026-03-19 00:22:05'),
(62, 'Fernandez', 'Noel', '0917100018', 'Demo', 'Noel', 'Student', '1988-03-01', 'Female', 'Single', 'Demo Address 18, Tejada City', NULL, NULL, NULL, 'noelfernandez18@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 18', '0918200018', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:05', '2026-03-19 00:22:05'),
(63, 'Mercado', 'Janelle', '0917100019', 'Demo', 'Janelle', 'Teacher', '1987-02-28', 'Male', 'Single', 'Demo Address 19, Tejada City', NULL, NULL, NULL, 'janellemercado19@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 19', '0918200019', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:05', '2026-03-19 00:22:05'),
(64, 'Salazar', 'Victor', '0917100020', 'Demo', 'Victor', 'Engineer', '1986-02-27', 'Female', 'Married', 'Demo Address 20, Tejada City', NULL, NULL, NULL, 'victorsalazar20@demo.tejada.test', 'Defense Demo Seed', 'Emergency Contact 20', '0918200020', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'defense-seeder', '2026-03-19 00:22:06', '2026-03-19 00:22:06');

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

--
-- Dumping data for table `patient_form_drafts`
--

INSERT INTO `patient_form_drafts` (`id`, `user_id`, `patient_id`, `mode`, `step`, `payload_json`, `expires_at`, `created_at`, `updated_at`) VALUES
(11, 10, 4, 'edit', 1, '{\"currentStep\":1,\"basicInfo\":{\"last_name\":\"MIA\",\"first_name\":\"MAMA\",\"middle_name\":\"\",\"nickname\":\"\",\"occupation\":\"\",\"birth_date\":\"\",\"gender\":\"Male\",\"civil_status\":\"\",\"home_address\":\"\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"09979775797\",\"email_address\":\"renzzluigi@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"\",\"emergency_contact_number\":\"\",\"relationship\":\"\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null},\"healthHistory\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":null,\"what_seeing_dentist_reason_q2\":null,\"is_clicking_jaw_q3a\":null,\"is_pain_jaw_q3b\":null,\"is_difficulty_opening_closing_q3c\":null,\"is_locking_jaw_q3d\":null,\"is_clench_grind_q4\":null,\"is_bad_experience_q5\":null,\"is_nervous_q6\":null,\"what_nervous_concern_q6\":null,\"is_condition_q1\":null,\"what_condition_reason_q1\":null,\"is_hospitalized_q2\":null,\"what_hospitalized_reason_q2\":null,\"is_serious_illness_operation_q3\":null,\"what_serious_illness_operation_reason_q3\":null,\"is_taking_medications_q4\":null,\"what_medications_list_q4\":null,\"is_allergic_medications_q5\":null,\"what_allergies_list_q5\":null,\"is_allergic_latex_rubber_metals_q6\":null,\"is_pregnant_q7\":null,\"is_breast_feeding_q8\":null},\"dentalChart\":{\"teeth\":[],\"oralExam\":{\"oral_hygiene_status\":null,\"gingiva\":null,\"calcular_deposits\":null,\"stains\":null,\"complete_denture\":null,\"partial_denture\":null},\"chartComments\":{\"notes\":null,\"treatment_plan\":null},\"dentitionType\":\"adult\",\"numberingSystem\":\"FDI\"},\"treatmentRecord\":{\"dmd\":null,\"treatment\":null,\"cost_of_treatment\":null,\"amount_charged\":null,\"remarks\":null},\"updatedAt\":\"2026-03-17T19:47:03.601Z\",\"mode\":\"edit\",\"patientId\":4}', '2026-03-24 19:47:46', '2026-03-17 19:47:18', '2026-03-17 19:47:46');

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
(1, 'dentist'),
(2, 'staff'),
(3, 'patient'),
(4, 'admin');

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
(3, 'Full Consultation', '01:00:00'),
(4, 'Teeth Whitening', '01:00:00'),
(5, 'Tooth Filling', '01:00:00'),
(6, 'Root Canal Treatment', '01:00:00'),
(7, 'Dental Check-up', '01:00:00'),
(8, 'Braces Adjustment', '01:00:00'),
(9, 'Tooth Extraction (Surgical)', '01:00:00'),
(10, 'Dental Crown Placement', '01:00:00'),
(11, 'Denture Fitting', '01:00:00'),
(12, 'Oral Prophylaxis', '01:00:00'),
(13, 'Fluoride Treatment', '01:00:00'),
(16, 'Dental Filling', '01:00:00');

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

--
-- Dumping data for table `treatment_records`
--

INSERT INTO `treatment_records` (`id`, `patient_id`, `dental_chart_id`, `dmd`, `treatment`, `cost_of_treatment`, `amount_charged`, `remarks`, `image`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 45, 1, 'Dr. Demo Dentist, DMD', 'Oral prophylaxis completed', 400.00, 900.00, 'Completed with scaling and polishing.', NULL, 'defense-seeder', '2026-03-16 01:00:00', '2026-03-16 01:00:00'),
(2, 46, 2, 'Dr. Demo Dentist, DMD', 'Tooth extraction completed', 700.00, 1820.00, 'Post-op instructions provided.', NULL, 'defense-seeder', '2026-03-11 02:00:00', '2026-03-11 02:00:00'),
(3, 47, 3, 'Dr. Demo Dentist, DMD', 'Comprehensive oral consultation', 200.00, 640.00, 'Treatment plan reviewed with patient.', NULL, 'defense-seeder', '2026-03-06 03:00:00', '2026-03-06 03:00:00'),
(4, 48, 4, 'Dr. Demo Dentist, DMD', 'Teeth Whitening completed', 300.00, 845.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-02-23 05:00:00', '2026-02-23 05:00:00'),
(5, 49, 5, 'Dr. Demo Dentist, DMD', 'Tooth Filling completed', 300.00, 860.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-02-18 06:00:00', '2026-02-18 06:00:00'),
(6, 50, 6, 'Dr. Demo Dentist, DMD', 'Root Canal Treatment completed', 300.00, 875.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-02-13 07:00:00', '2026-02-13 07:00:00'),
(7, 51, 7, 'Dr. Demo Dentist, DMD', 'Dental Check-up completed', 300.00, 890.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-03-16 08:00:00', '2026-03-16 08:00:00'),
(8, 52, 8, 'Dr. Demo Dentist, DMD', 'Braces Adjustment completed', 300.00, 905.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-03-11 01:00:00', '2026-03-11 01:00:00'),
(9, 53, 9, 'Dr. Demo Dentist, DMD', 'Tooth Extraction (Surgical) completed', 300.00, 920.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-03-06 02:00:00', '2026-03-06 02:00:00'),
(10, 54, 10, 'Dr. Demo Dentist, DMD', 'Dental Crown Placement completed', 300.00, 935.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-02-23 03:00:00', '2026-02-23 03:00:00'),
(11, 55, 11, 'Dr. Demo Dentist, DMD', 'Denture Fitting completed', 300.00, 950.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-02-18 05:00:00', '2026-02-18 05:00:00'),
(12, 56, 12, 'Dr. Demo Dentist, DMD', 'Prophylaxis session completed', 350.00, 1070.00, 'Plaque and stains removed successfully.', NULL, 'defense-seeder', '2026-02-13 06:00:00', '2026-02-13 06:00:00'),
(13, 57, 13, 'Dr. Demo Dentist, DMD', 'Fluoride Treatment completed', 300.00, 980.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-03-16 07:00:00', '2026-03-16 07:00:00'),
(14, 58, 14, 'Dr. Demo Dentist, DMD', 'Composite filling completed', 500.00, 1460.00, 'Occlusion adjusted after restoration.', NULL, 'defense-seeder', '2026-03-11 08:00:00', '2026-03-11 08:00:00'),
(15, 59, 15, 'Dr. Demo Dentist, DMD', 'Oral prophylaxis completed', 400.00, 1180.00, 'Completed with scaling and polishing.', NULL, 'defense-seeder', '2026-03-06 01:00:00', '2026-03-06 01:00:00'),
(16, 60, 16, 'Dr. Demo Dentist, DMD', 'Tooth extraction completed', 700.00, 2100.00, 'Post-op instructions provided.', NULL, 'defense-seeder', '2026-02-23 02:00:00', '2026-02-23 02:00:00'),
(17, 61, 17, 'Dr. Demo Dentist, DMD', 'Comprehensive oral consultation', 200.00, 920.00, 'Treatment plan reviewed with patient.', NULL, 'defense-seeder', '2026-02-18 03:00:00', '2026-02-18 03:00:00'),
(18, 62, 18, 'Dr. Demo Dentist, DMD', 'Teeth Whitening completed', 300.00, 1055.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-02-13 05:00:00', '2026-02-13 05:00:00'),
(19, 63, 19, 'Dr. Demo Dentist, DMD', 'Tooth Filling completed', 300.00, 1070.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-03-16 06:00:00', '2026-03-16 06:00:00'),
(20, 64, 20, 'Dr. Demo Dentist, DMD', 'Root Canal Treatment completed', 300.00, 1085.00, 'Completed during defense demo seed run.', NULL, 'defense-seeder', '2026-03-11 07:00:00', '2026-03-11 07:00:00'),
(21, 45, 21, 'Dr. Demo Dentist, DMD', 'Oral prophylaxis completed', 420.00, 980.00, 'Completed previous-month hygiene visit.', NULL, 'previous-month-seeder', '2026-02-02 01:00:00', '2026-02-02 01:00:00'),
(22, 46, 22, 'Dr. Demo Dentist, DMD', 'Tooth extraction completed', 760.00, 1915.00, 'Completed extraction with post-op advice.', NULL, 'previous-month-seeder', '2026-02-04 02:00:00', '2026-02-04 02:00:00'),
(23, 47, 23, 'Dr. Demo Dentist, DMD', 'Comprehensive oral consultation', 240.00, 680.00, 'Consultation and treatment planning completed.', NULL, 'previous-month-seeder', '2026-02-06 03:00:00', '2026-02-06 03:00:00'),
(24, 48, 24, 'Dr. Demo Dentist, DMD', 'Teeth Whitening completed', 380.00, 895.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-08 05:00:00', '2026-02-08 05:00:00'),
(25, 49, 25, 'Dr. Demo Dentist, DMD', 'Tooth Filling completed', 390.00, 910.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-10 06:00:00', '2026-02-10 06:00:00'),
(26, 50, 26, 'Dr. Demo Dentist, DMD', 'Root Canal Treatment completed', 400.00, 925.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-12 07:00:00', '2026-02-12 07:00:00'),
(27, 51, 27, 'Dr. Demo Dentist, DMD', 'Dental Check-up completed', 410.00, 940.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-14 01:00:00', '2026-02-14 01:00:00'),
(28, 52, 28, 'Dr. Demo Dentist, DMD', 'Braces Adjustment completed', 420.00, 955.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-16 02:00:00', '2026-02-16 02:00:00'),
(29, 53, 29, 'Dr. Demo Dentist, DMD', 'Tooth Extraction (Surgical) completed', 430.00, 970.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-18 03:00:00', '2026-02-18 03:00:00'),
(30, 54, 30, 'Dr. Demo Dentist, DMD', 'Dental Crown Placement completed', 440.00, 985.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-20 05:00:00', '2026-02-20 05:00:00'),
(31, 55, 31, 'Dr. Demo Dentist, DMD', 'Denture Fitting completed', 450.00, 1000.00, 'Completed previous-month treatment.', NULL, 'previous-month-seeder', '2026-02-22 06:00:00', '2026-02-22 06:00:00'),
(32, 56, 32, 'Dr. Demo Dentist, DMD', 'Prophylaxis session completed', 490.00, 1065.00, 'Routine cleaning completed.', NULL, 'previous-month-seeder', '2026-02-24 07:00:00', '2026-02-24 07:00:00');

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
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `mobile_number`, `patient_id`, `email_verified_at`, `verification_token`, `password`, `google_id`, `role`, `created_at`, `updated_at`) VALUES
(9, 'nobovaruco@sharebot.net', NULL, NULL, 'nobovaruco@sharebot.net', NULL, NULL, '2026-03-15 16:30:27', NULL, '$2y$12$LCBGQ7rRbPpqaa1Dar5ai.OZU87xW99P9GTth6DgI7fw64cgR.7y.', NULL, 3, '2026-03-16 00:29:22', '2026-03-18 01:10:17'),
(10, 'renzzluigi@gmail.com', NULL, NULL, 'renzzluigi@gmail.com', NULL, NULL, '2026-03-17 17:30:10', NULL, '$2y$12$SW8pOIs1MIJyB7kp0sKl/ug5qXLBzK4QBucK5K1icTPik2OLpuGVe', '114082662441983874861', 3, '2026-03-17 15:13:24', '2026-03-20 15:41:52'),
(51, 'demo.dentist', 'Demo', 'Dentist', 'demo.dentist@tejada.test', '09170000001', NULL, '2026-03-19 00:22:01', NULL, '$2y$12$lEramct/3L4tSfZZMevTlehuOiAW3HtHNl9dGH/99j6W7S8IGOj5K', NULL, 1, '2026-03-19 08:22:01', '2026-03-19 08:22:01'),
(52, 'patient.demo.1', 'Renz', 'Rosales', 'renzrosales1@demo.tejada.test', '0919300001', 45, '2026-03-19 00:22:01', NULL, '$2y$12$v.ciIMCOIiWMi38WRYPdfOcbEMpHNkS5KdkSyk8d/3/DIaSAwr9ii', NULL, 3, '2026-03-19 08:22:02', '2026-03-19 08:22:02'),
(53, 'patient.demo.2', 'Mia', 'Santos', 'miasantos2@demo.tejada.test', '0919300002', 46, '2026-03-19 00:22:02', NULL, '$2y$12$GXbD5nU1EGQb2MaC232nz.Gvy5gE14GzKTcbmLKMTyZSKwtVxXaYC', NULL, 3, '2026-03-19 08:22:02', '2026-03-19 08:22:02'),
(54, 'patient.demo.3', 'Paolo', 'Dela Cruz', 'paolodela-cruz3@demo.tejada.test', '0919300003', 47, '2026-03-19 00:22:02', NULL, '$2y$12$3GVzT6h0/CqgVZTK2ykfwe9iCBIjFkN15D/HoefWY438EKHYk3Q5K', NULL, 3, '2026-03-19 08:22:02', '2026-03-19 08:22:02'),
(55, 'patient.demo.4', 'Jessa', 'Garcia', 'jessagarcia4@demo.tejada.test', '0919300004', 48, '2026-03-19 00:22:02', NULL, '$2y$12$Y75ZEE5Ld8fsRRpbQrGNbuKBbmFxBsCMqCgZ7d5J77jLrro9CJcTq', NULL, 3, '2026-03-19 08:22:02', '2026-03-19 08:22:02'),
(56, 'patient.demo.5', 'Carlo', 'Mendoza', 'carlomendoza5@demo.tejada.test', '0919300005', 49, '2026-03-19 00:22:02', NULL, '$2y$12$6Mqngbm5dGi4mbMbERhyWe4luyVSVJHOza58kT8SwsdotWotIuzx.', NULL, 3, '2026-03-19 08:22:02', '2026-03-19 08:22:02'),
(57, 'patient.demo.6', 'Alyssa', 'Reyes', 'alyssareyes6@demo.tejada.test', '0919300006', 50, '2026-03-19 00:22:02', NULL, '$2y$12$8TlHC1/R02.gaX6nA.4EAOCOEX3uNCTNThoZ3ZHgNBD60v6cD2eUi', NULL, 3, '2026-03-19 08:22:03', '2026-03-19 08:22:03'),
(58, 'patient.demo.7', 'Mark', 'Navarro', 'marknavarro7@demo.tejada.test', '0919300007', 51, '2026-03-19 00:22:03', NULL, '$2y$12$/bPVjbXuql8oq2gyLGnJN.tNrRPt7lztELcjoyEo7Bar2lREpPd2C', NULL, 3, '2026-03-19 08:22:03', '2026-03-19 08:22:03'),
(59, 'patient.demo.8', 'Shane', 'Torres', 'shanetorres8@demo.tejada.test', '0919300008', 52, '2026-03-19 00:22:03', NULL, '$2y$12$jSOA90eAvU8xEko01I4FmORi6KUvVg0hG9nHgQIsDJ8BlNFuIgBHK', NULL, 3, '2026-03-19 08:22:03', '2026-03-19 08:22:03'),
(60, 'patient.demo.9', 'Nicole', 'Aquino', 'nicoleaquino9@demo.tejada.test', '0919300009', 53, '2026-03-19 00:22:03', NULL, '$2y$12$CcMv/ZMQjcmStzozHrJxQOs5fff49sLyUmOWIebB.wp3o3xswIkDK', NULL, 3, '2026-03-19 08:22:03', '2026-03-19 08:22:03'),
(61, 'patient.demo.10', 'Adrian', 'Villanueva', 'adrianvillanueva10@demo.tejada.test', '0919300010', 54, '2026-03-19 00:22:03', NULL, '$2y$12$Yr2yI6cU49/M7UyPv481TuLAVQitWNReccOJ7Don8js5A7XgJAlAa', NULL, 3, '2026-03-19 08:22:04', '2026-03-19 08:22:04'),
(62, 'patient.demo.11', 'Bianca', 'Castillo', 'biancacastillo11@demo.tejada.test', '0919300011', 55, '2026-03-19 00:22:04', NULL, '$2y$12$/BkUDqe9Qy5cJOS4ZoWT0O62mvtymP/jahrkQOUmaW2J3Au1MZ2AC', NULL, 3, '2026-03-19 08:22:04', '2026-03-19 08:22:04'),
(63, 'patient.demo.12', 'Joshua', 'Ramos', 'joshuaramos12@demo.tejada.test', '0919300012', 56, '2026-03-19 00:22:04', NULL, '$2y$12$JBzK0k2Wxl7tGUn0uuQFAufH3.gQL8LJY1zZc.NWLSFNU60Ztt7ke', NULL, 3, '2026-03-19 08:22:04', '2026-03-19 08:22:04'),
(64, 'patient.demo.13', 'Andrea', 'Bautista', 'andreabautista13@demo.tejada.test', '0919300013', 57, '2026-03-19 00:22:04', NULL, '$2y$12$vY2oF3LT7Hu16XH3QIyNcuQqr//Myt4/PW3vv9oMrXuodUmGq96O6', NULL, 3, '2026-03-19 08:22:04', '2026-03-19 08:22:04'),
(65, 'patient.demo.14', 'Kyle', 'Domingo', 'kyledomingo14@demo.tejada.test', '0919300014', 58, '2026-03-19 00:22:04', NULL, '$2y$12$8wioBuETpJl6usf1QI7/dO04c6ytr8staQ5PfB8vZffg20AAo683G', NULL, 3, '2026-03-19 08:22:04', '2026-03-19 08:22:04'),
(66, 'patient.demo.15', 'Patricia', 'Soriano', 'patriciasoriano15@demo.tejada.test', '0919300015', 59, '2026-03-19 00:22:04', NULL, '$2y$12$MT6nyYXsmb0Uv2a5na6.7.AOru1Smc.9pwsrU4PJ9F7bBBPOmWu2G', NULL, 3, '2026-03-19 08:22:05', '2026-03-19 08:22:05'),
(67, 'patient.demo.16', 'Jomel', 'Manalang', 'jomelmanalang16@demo.tejada.test', '0919300016', 60, '2026-03-19 00:22:05', NULL, '$2y$12$AtPErjVZgv9bP8yy1ei.IOFtI4ssMZZlBiqBIfDCtnjzavx8RshlK', NULL, 3, '2026-03-19 08:22:05', '2026-03-19 08:22:05'),
(68, 'patient.demo.17', 'Kristine', 'Lopez', 'kristinelopez17@demo.tejada.test', '0919300017', 61, '2026-03-19 00:22:05', NULL, '$2y$12$Ad2aemqJg9K1KZpy1fGiuOibbQm/UQtM6Jev6UyZkHVXVQHztsDM2', NULL, 3, '2026-03-19 08:22:05', '2026-03-19 08:22:05'),
(69, 'patient.demo.18', 'Noel', 'Fernandez', 'noelfernandez18@demo.tejada.test', '0919300018', 62, '2026-03-19 00:22:05', NULL, '$2y$12$j0DSTORzHOs6HhzTjRogKuOI15QsoBsxcQ3dG8LrJFPk1FBNCqRtq', NULL, 3, '2026-03-19 08:22:05', '2026-03-19 08:22:05'),
(70, 'patient.demo.19', 'Janelle', 'Mercado', 'janellemercado19@demo.tejada.test', '0919300019', 63, '2026-03-19 00:22:05', NULL, '$2y$12$QxvPRoVETW/DHvQpq44wmecou2D6DGTDZe/BrPbgCLYpPXKe9kzYu', NULL, 3, '2026-03-19 08:22:06', '2026-03-19 08:22:06'),
(71, 'patient.demo.20', 'Victor', 'Salazar', 'victorsalazar20@demo.tejada.test', '0919300020', 64, '2026-03-19 00:22:06', NULL, '$2y$12$y.C1koc0g4EHLOncNzHVXOstxspYEI9JR/NWWH8hJjbbYPuO2R.oi', NULL, 3, '2026-03-19 08:22:06', '2026-03-19 08:22:06');

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_index` (`user_id`),
  ADD KEY `notifications_type_created_at_index` (`type`,`created_at`),
  ADD KEY `notifications_appointment_id_index` (`appointment_id`),
  ADD KEY `notifications_actor_user_id_index` (`actor_user_id`),
  ADD KEY `notifications_user_read_index` (`user_id`,`read_at`),
  ADD KEY `notifications_user_cleared_index` (`user_id`,`cleared_at`);

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
  ADD KEY `role` (`role`),
  ADD KEY `idx_users_patient_id` (`patient_id`),
  ADD KEY `idx_users_mobile_number` (`mobile_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dental_charts`
--
ALTER TABLE `dental_charts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `health_histories`
--
ALTER TABLE `health_histories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `patient_form_drafts`
--
ALTER TABLE `patient_form_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `treatment_records`
--
ALTER TABLE `treatment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `treatment_record_images`
--
ALTER TABLE `treatment_record_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

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
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
