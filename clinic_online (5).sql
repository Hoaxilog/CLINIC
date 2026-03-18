-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2026 at 06:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

SET SESSION sql_require_primary_key = 0;


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
(40, 'default', 'Unlinked Appointment from Patient Record', 'App\\Models\\Appointment', 'appointment_patient_unlinked', 9, 'App\\Models\\User', 10, '{\"attributes\":{\"appointment_id\":9,\"previous_patient_id\":5}}', NULL, '2026-03-18 16:34:44', '2026-03-18 16:34:44');

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
(13, '2026-03-19 12:00:00', 'Pending', 2, NULL, 10, 'RENZ', 'ROSALES', '2003-04-29', '09979775797', 'renzzluigi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 22:34:20', '2026-03-17 22:34:20', 'online_appointment', NULL, NULL, NULL),
(14, '2026-03-18 18:00:00', 'Pending', 2, NULL, 10, 'hfg', 'hfghfg', '2003-04-29', '09979775797', 'dsadasi@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'renzzluigi@gmail.com', '2026-03-17 22:36:06', '2026-03-17 22:36:06', 'online_appointment', NULL, NULL, NULL),
(15, '2026-03-18 16:15:16', 'Ongoing', 1, 6, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 10, 'renzzluigi@gmail.com', '2026-03-18 08:15:16', '2026-03-18 08:16:31', 'walk_in', NULL, NULL, NULL);

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
(6, 'my last name', 'my first name', '123312123', 'my middle name', '', 'occupation', '2026-03-18', 'Male', 'single', 'ropsal', '', '', '', 'me@mydomain.com', '', 'ewq', '231', 'asd', 'qwe', 'sdasad', '', '', '', '', '', '', 'renzzluigi@gmail.com', '2026-03-18 08:15:16', '2026-03-18 08:15:16');

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

INSERT INTO `users` (`id`, `username`, `email`, `patient_id`, `email_verified_at`, `verification_token`, `password`, `google_id`, `role`, `created_at`, `updated_at`) VALUES
(9, 'nobovaruco@sharebot.net', 'nobovaruco@sharebot.net', NULL, '2026-03-15 16:30:27', NULL, '$2y$12$LCBGQ7rRbPpqaa1Dar5ai.OZU87xW99P9GTth6DgI7fw64cgR.7y.', NULL, 3, '2026-03-16 00:29:22', '2026-03-18 01:10:17'),
(10, 'renzzluigi@gmail.com', 'renzzluigi@gmail.com', NULL, '2026-03-17 17:30:10', NULL, '$2y$12$SW8pOIs1MIJyB7kp0sKl/ug5qXLBzK4QBucK5K1icTPik2OLpuGVe', '114082662441983874861', 4, '2026-03-17 15:13:24', '2026-03-18 17:15:06');

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
  ADD KEY `idx_users_patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patient_form_drafts`
--
ALTER TABLE `patient_form_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
