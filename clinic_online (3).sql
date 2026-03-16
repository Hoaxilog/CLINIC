-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 14, 2026 at 03:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SESSION sql_require_primary_key = 0;


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
-- Table structure for table `account_recovery_requests`
--

CREATE TABLE `account_recovery_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lookup_identifier` varchar(255) NOT NULL,
  `new_email` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `identity_verified_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewer_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_recovery_requests`
--

INSERT INTO `account_recovery_requests` (`id`, `user_id`, `lookup_identifier`, `new_email`, `reason`, `status`, `identity_verified_at`, `reviewed_by`, `reviewed_at`, `reviewer_notes`, `created_at`, `updated_at`) VALUES
(1, NULL, 'romebernacer123@gmail.com', 'renzzluigi@gmail.com', NULL, 'pending', NULL, NULL, NULL, NULL, '2026-02-22 07:54:44', '2026-02-22 07:54:44');

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
(1, 'default', 'created', 'App\\Models\\User', 'created', NULL, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"sample2\",\"contact\":\"123456789\",\"password\":\"$2y$12$qlglSHNydptPz.bHXqs6QeOSPiCZfIijywv97JoArgDmFXSAsDuBq\",\"role\":\"2\",\"created_at\":\"2026-01-17T16:41:28.366815Z\",\"updated_at\":\"2026-01-17T16:41:28.366830Z\"}}', NULL, '2026-01-17 16:41:28', '2026-01-17 16:41:28'),
(2, 'default', 'Created new user', 'App\\Models\\User', 'Created new user', 10, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"sample7\",\"contact\":\"123123121\",\"password\":\"$2y$12$YFciHQwIthIHDHLhzg2bc.ljFcaGF1Qiwqus3zNM7nCODvl3GO82u\",\"role\":\"2\",\"created_at\":\"2026-01-17T17:13:46.746932Z\",\"updated_at\":\"2026-01-17T17:13:46.746946Z\"}}', NULL, '2026-01-17 17:13:46', '2026-01-17 17:13:46'),
(3, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 10, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":10,\"username\":\"sample7\",\"password\":\"$2y$12$YFciHQwIthIHDHLhzg2bc.ljFcaGF1Qiwqus3zNM7nCODvl3GO82u\",\"contact\":\"123123121\",\"role\":2,\"security_question\":null,\"security_answer\":null,\"created_at\":\"2026-01-18 01:13:46\",\"updated_at\":\"2026-01-18 01:13:46\"}}', NULL, '2026-01-17 18:54:22', '2026-01-17 18:54:22'),
(4, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 6, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"hoax\",\"contact\":\"12341231231\",\"role\":\"2\",\"updated_at\":\"2026-01-17T19:13:29.173235Z\"},\"old\":{\"id\":6,\"username\":\"hoax\",\"password\":\"$2y$12$MU7\\/gCnRBUPMK18UqF5O9.J7lf6pfFzQ.qplLzSsEZKJ\\/epKWRoWa\",\"contact\":\"12341231231\",\"role\":1,\"security_question\":null,\"security_answer\":null,\"created_at\":\"2026-01-18 00:12:04\",\"updated_at\":\"2026-01-18 00:12:04\"}}', NULL, '2026-01-17 19:13:29', '2026-01-17 19:13:29'),
(5, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 6, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"hoax\",\"contact\":\"12341231231\",\"role\":\"1\",\"updated_at\":\"2026-01-17T19:31:07.864110Z\"},\"old\":{\"id\":6,\"username\":\"hoax\",\"password\":\"$2y$12$MU7\\/gCnRBUPMK18UqF5O9.J7lf6pfFzQ.qplLzSsEZKJ\\/epKWRoWa\",\"contact\":\"12341231231\",\"role\":2,\"security_question\":null,\"security_answer\":null,\"created_at\":\"2026-01-18 00:12:04\",\"updated_at\":\"2026-01-18 03:13:29\"}}', NULL, '2026-01-17 19:31:07', '2026-01-17 19:31:07'),
(6, 'default', 'Created New Patient Record', 'App\\Models\\Patient', 'patient_created', 78, 'App\\Models\\User', 2, '{\"attributes\":{\"last_name\":\"W\",\"first_name\":\"W\",\"middle_name\":\"W\",\"nickname\":\"\",\"occupation\":\"W\",\"birth_date\":\"2026-01-18\",\"gender\":\"Male\",\"civil_status\":\"W\",\"home_address\":\"W\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"231312\",\"email_address\":\"\",\"referral\":\"\",\"emergency_contact_name\":\"W\",\"emergency_contact_number\":\"123\",\"relationship\":\"W\",\"who_answering\":\"W\",\"relationship_to_patient\":\"W\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-01-17 19:49:22', '2026-01-17 19:49:22'),
(8, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 78, 'App\\Models\\User', 2, '{\"old\":{\"last_name\":\"WEEEEEEE\"},\"attributes\":{\"last_name\":\"RErerere\"}}', NULL, '2026-01-17 19:56:51', '2026-01-17 19:56:51'),
(9, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 6, 'App\\Models\\User', 2, '{\"old\":{\"role\":1},\"attributes\":{\"role\":\"2\"}}', NULL, '2026-01-17 20:07:49', '2026-01-17 20:07:49'),
(10, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 6, 'App\\Models\\User', 2, '{\"old\":{\"contact\":\"12341231231\",\"role\":2},\"attributes\":{\"contact\":\"123123123\",\"role\":\"1\"}}', NULL, '2026-01-17 20:08:21', '2026-01-17 20:08:21'),
(11, 'default', 'Created User Account', 'App\\Models\\User', 'user_created', 11, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"eyyyy\",\"contact\":\"321312123\",\"password\":\"$2y$12$7zMoY3sQ\\/ilwKd5eVJkcc.unstVqqmIiSoB8DRu80mAhLlCc4aWu2\",\"role\":\"1\",\"created_at\":\"2026-01-17T20:11:54.348646Z\",\"updated_at\":\"2026-01-17T20:11:54.348660Z\"}}', NULL, '2026-01-17 20:11:54', '2026-01-17 20:11:54'),
(12, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 11, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":11,\"username\":\"eyyyy\",\"password\":\"$2y$12$7zMoY3sQ\\/ilwKd5eVJkcc.unstVqqmIiSoB8DRu80mAhLlCc4aWu2\",\"contact\":\"321312123\",\"role\":1,\"security_question\":null,\"security_answer\":null,\"created_at\":\"2026-01-18 04:11:54\",\"updated_at\":\"2026-01-18 04:11:54\"}}', NULL, '2026-01-17 20:12:10', '2026-01-17 20:12:10'),
(13, 'default', 'Updated Dental Chart', 'App\\Models\\Patient', 'dental_chart_updated', 78, 'App\\Models\\User', 2, '{\"attributes\":{\"Dental Chart\":\"Visual Chart Updated\"}}', NULL, '2026-01-17 20:22:55', '2026-01-17 20:22:55'),
(14, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 78, 'App\\Models\\User', 2, '{\"old\":{\"is_difficulty_opening_closing_q3c\":0,\"is_clench_grind_q4\":0},\"attributes\":{\"is_difficulty_opening_closing_q3c\":\"1\",\"is_clench_grind_q4\":\"1\"}}', NULL, '2026-01-18 07:35:36', '2026-01-18 07:35:36'),
(15, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 41, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":79,\"service_id\":\"2\",\"appointment_date\":\"2026-01-18 10:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-18T08:53:11.849276Z\",\"updated_at\":\"2026-01-18T08:53:11.849288Z\"}}', NULL, '2026-01-18 08:53:12', '2026-01-18 08:53:12'),
(16, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 41, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Ongoing\"}}', NULL, '2026-01-18 08:53:24', '2026-01-18 08:53:24'),
(17, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 41, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-01-18 08:53:46', '2026-01-18 08:53:46'),
(18, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 42, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":79,\"service_id\":\"3\",\"appointment_date\":\"2026-01-19 09:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T02:52:03.809511Z\",\"updated_at\":\"2026-01-19T02:52:03.809521Z\"}}', NULL, '2026-01-19 02:52:03', '2026-01-19 02:52:03'),
(19, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 79, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"\",\"is_clicking_jaw_q3a\":\"1\",\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":\"1\",\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":\"1\",\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":79,\"created_at\":\"2026-01-19T02:54:04.725962Z\",\"updated_at\":\"2026-01-19T02:54:04.725976Z\"}}', NULL, '2026-01-19 02:54:04', '2026-01-19 02:54:04'),
(20, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 43, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":80,\"service_id\":\"3\",\"appointment_date\":\"2026-01-19 10:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T14:45:38.821402Z\",\"updated_at\":\"2026-01-19T14:45:38.821410Z\"}}', NULL, '2026-01-19 14:45:39', '2026-01-19 14:45:39'),
(21, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 44, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":81,\"service_id\":\"2\",\"appointment_date\":\"2026-01-19 12:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T14:49:46.822955Z\",\"updated_at\":\"2026-01-19T14:49:46.822965Z\"}}', NULL, '2026-01-19 14:49:46', '2026-01-19 14:49:46'),
(22, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 45, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":64,\"service_id\":\"3\",\"appointment_date\":\"2026-01-20 10:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T17:24:26.732139Z\",\"updated_at\":\"2026-01-19T17:24:26.732159Z\"}}', NULL, '2026-01-19 17:24:26', '2026-01-19 17:24:26'),
(23, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 46, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":82,\"service_id\":\"2\",\"appointment_date\":\"2026-01-20 11:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T17:38:47.772087Z\",\"updated_at\":\"2026-01-19T17:38:47.772110Z\"}}', NULL, '2026-01-19 17:38:47', '2026-01-19 17:38:47'),
(24, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 47, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":65,\"service_id\":\"2\",\"appointment_date\":\"2026-01-20 11:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T17:57:21.486279Z\",\"updated_at\":\"2026-01-19T17:57:21.486291Z\"}}', NULL, '2026-01-19 17:57:21', '2026-01-19 17:57:21'),
(25, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 48, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":83,\"service_id\":\"3\",\"appointment_date\":\"2026-01-20 12:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:05:52.922526Z\",\"updated_at\":\"2026-01-19T18:05:52.922534Z\"}}', NULL, '2026-01-19 18:05:52', '2026-01-19 18:05:52'),
(26, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 49, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":65,\"service_id\":\"3\",\"appointment_date\":\"2026-01-20 09:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:09:06.690898Z\",\"updated_at\":\"2026-01-19T18:09:06.690913Z\"}}', NULL, '2026-01-19 18:09:06', '2026-01-19 18:09:06'),
(27, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 50, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":17,\"service_id\":\"2\",\"appointment_date\":\"2026-01-20 10:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:11:36.024802Z\",\"updated_at\":\"2026-01-19T18:11:36.024824Z\"}}', NULL, '2026-01-19 18:11:36', '2026-01-19 18:11:36'),
(28, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 51, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":84,\"service_id\":\"2\",\"appointment_date\":\"2026-01-20 21:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:19:51.485486Z\",\"updated_at\":\"2026-01-19T18:19:51.485501Z\"}}', NULL, '2026-01-19 18:19:51', '2026-01-19 18:19:51'),
(29, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 52, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":72,\"service_id\":\"3\",\"appointment_date\":\"2026-01-20 22:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:45:30.623343Z\",\"updated_at\":\"2026-01-19T18:45:30.623358Z\"}}', NULL, '2026-01-19 18:45:30', '2026-01-19 18:45:30'),
(30, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 53, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":85,\"service_id\":\"2\",\"appointment_date\":\"2026-01-20 21:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:46:01.660095Z\",\"updated_at\":\"2026-01-19T18:46:01.660106Z\"}}', NULL, '2026-01-19 18:46:01', '2026-01-19 18:46:01'),
(31, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 54, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":67,\"service_id\":\"3\",\"appointment_date\":\"2026-01-20 09:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T18:59:13.179582Z\",\"updated_at\":\"2026-01-19T18:59:13.179599Z\"}}', NULL, '2026-01-19 18:59:13', '2026-01-19 18:59:13'),
(32, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 55, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":86,\"service_id\":\"2\",\"appointment_date\":\"2026-01-20 10:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T19:09:58.891280Z\",\"updated_at\":\"2026-01-19T19:09:58.891290Z\"}}', NULL, '2026-01-19 19:09:58', '2026-01-19 19:09:58'),
(33, 'default', 'Created New Patient Record', 'App\\Models\\Patient', 'patient_created', 87, 'App\\Models\\User', 2, '{\"attributes\":{\"last_name\":\"qwe\",\"first_name\":\"qweeqw\",\"middle_name\":\"qwe\",\"nickname\":\"\",\"occupation\":\"qwe\",\"birth_date\":\"2026-01-16\",\"gender\":\"Male\",\"civil_status\":\"qwe\",\"home_address\":\"eqw\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"123312\",\"email_address\":\"\",\"referral\":\"\",\"emergency_contact_name\":\"qwe\",\"emergency_contact_number\":\"123\",\"relationship\":\"qwe\",\"who_answering\":\"eq\",\"relationship_to_patient\":\"qwe\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-01-19 19:11:13', '2026-01-19 19:11:13'),
(34, 'default', 'Registered Walk-In (Waiting Room)', 'App\\Models\\Appointment', 'appointment_created', 56, 'App\\Models\\User', 2, '{\"type\":\"Walk-In\"}', NULL, '2026-01-19 19:11:13', '2026-01-19 19:11:13'),
(35, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 57, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":88,\"service_id\":\"3\",\"appointment_date\":\"2026-01-20 11:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-19T19:33:59.485308Z\",\"updated_at\":\"2026-01-19T19:33:59.485317Z\"}}', NULL, '2026-01-19 19:33:59', '2026-01-19 19:33:59'),
(36, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 88, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"\",\"is_clicking_jaw_q3a\":\"1\",\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":88,\"created_at\":\"2026-01-20T07:19:48.711767Z\",\"updated_at\":\"2026-01-20T07:19:48.711778Z\"}}', NULL, '2026-01-20 07:19:49', '2026-01-20 07:19:49'),
(37, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 88, 'App\\Models\\User', 2, '{\"old\":{\"is_difficulty_opening_closing_q3c\":0},\"attributes\":{\"is_difficulty_opening_closing_q3c\":\"1\"}}', NULL, '2026-01-20 13:41:55', '2026-01-20 13:41:55'),
(49, 'default', 'Created New Patient Record', 'App\\Models\\Patient', 'patient_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"last_name\":\"poopop\",\"first_name\":\"opopop\",\"middle_name\":\"opop\",\"nickname\":\"\",\"occupation\":\"\",\"birth_date\":null,\"gender\":null,\"civil_status\":\"\",\"home_address\":\"\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"\",\"email_address\":\"\",\"referral\":\"\",\"emergency_contact_name\":\"\",\"emergency_contact_number\":\"\",\"relationship\":\"\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-01-20 16:40:01', '2026-01-20 16:40:01'),
(50, 'default', 'Registered Walk-In (Waiting Room)', 'App\\Models\\Appointment', 'appointment_created', 58, 'App\\Models\\User', 2, '{\"type\":\"Walk-In\"}', NULL, '2026-01-20 16:40:01', '2026-01-20 16:40:01'),
(51, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"sdffsdfsd\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"qweqweqwe\"}}', NULL, '2026-01-20 16:47:08', '2026-01-20 16:47:08'),
(52, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"qweqweqwe\",\"is_clicking_jaw_q3a\":0,\"is_difficulty_opening_closing_q3c\":0},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"re\",\"is_clicking_jaw_q3a\":\"1\",\"is_difficulty_opening_closing_q3c\":\"1\"}}', NULL, '2026-01-20 16:47:39', '2026-01-20 16:47:39'),
(53, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"re\",\"is_difficulty_opening_closing_q3c\":1},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"qewqweqwe\",\"is_difficulty_opening_closing_q3c\":false}}', NULL, '2026-01-20 16:48:11', '2026-01-20 16:48:11'),
(54, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"tyttytyytty\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":100,\"created_at\":\"2026-01-20T17:07:47.168985Z\",\"updated_at\":\"2026-01-20T17:07:47.169001Z\"}}', NULL, '2026-01-20 17:07:47', '2026-01-20 17:07:47'),
(55, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"trtrtrt\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":100,\"created_at\":\"2026-01-20T17:08:29.156026Z\",\"updated_at\":\"2026-01-20T17:08:29.156040Z\"}}', NULL, '2026-01-20 17:08:29', '2026-01-20 17:08:29'),
(56, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"TRETER\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":100,\"created_at\":\"2026-01-20T17:16:34.179407Z\",\"updated_at\":\"2026-01-20T17:16:34.179421Z\"}}', NULL, '2026-01-20 17:16:34', '2026-01-20 17:16:34'),
(57, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"ewqweqqew\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":100,\"created_at\":\"2026-01-20T17:17:00.319689Z\",\"updated_at\":\"2026-01-20T17:17:00.319701Z\"}}', NULL, '2026-01-20 17:17:00', '2026-01-20 17:17:00'),
(58, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"cvbcvdfg\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":100,\"created_at\":\"2026-01-20T17:24:40.049742Z\",\"updated_at\":\"2026-01-20T17:24:40.049752Z\"}}', NULL, '2026-01-20 17:24:40', '2026-01-20 17:24:40'),
(59, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"qewqweqwe\",\"is_clicking_jaw_q3a\":1},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"QWE\",\"is_clicking_jaw_q3a\":0}}', NULL, '2026-01-20 17:41:46', '2026-01-20 17:41:46'),
(60, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"QWE\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"qweqweqwe\"}}', NULL, '2026-01-20 17:42:23', '2026-01-20 17:42:23'),
(61, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"qweqweqwe\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"werwerwerwer\"}}', NULL, '2026-01-20 17:43:42', '2026-01-20 17:43:42'),
(62, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 59, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":101,\"service_id\":\"2\",\"appointment_date\":\"2026-01-21 09:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-20T17:48:03.540625Z\",\"updated_at\":\"2026-01-20T17:48:03.540635Z\"}}', NULL, '2026-01-20 17:48:03', '2026-01-20 17:48:03'),
(63, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"werwerwerwer\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"qweqewqwe\"}}', NULL, '2026-01-20 17:52:13', '2026-01-20 17:52:13'),
(64, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"qweqewqwe\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"OIIOIOIO\"}}', NULL, '2026-01-20 17:52:32', '2026-01-20 17:52:32'),
(65, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"last_name\":\"poopop\"},\"attributes\":{\"last_name\":\"poopop1\"}}', NULL, '2026-01-20 17:53:06', '2026-01-20 17:53:06'),
(66, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"cvbcvdfg\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"werrwewer\"}}', NULL, '2026-01-20 18:06:21', '2026-01-20 18:06:21'),
(67, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"ewqweqqew\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"ewqweqqew1\"}}', NULL, '2026-01-20 18:19:45', '2026-01-20 18:19:45'),
(68, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"werrwewer\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"QWEQWEQWE\"}}', NULL, '2026-01-20 18:34:19', '2026-01-20 18:34:19'),
(69, 'default', 'Updated Medical History', 'App\\Models\\Patient', 'health_history_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"what_seeing_dentist_reason_q2\":\"QWEQWEQWE\"},\"attributes\":{\"what_seeing_dentist_reason_q2\":\"qweqweqqew\"}}', NULL, '2026-01-20 18:38:20', '2026-01-20 18:38:20'),
(70, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"last_name\":\"poopop1\"},\"attributes\":{\"last_name\":\"poopop1QWE\"}}', NULL, '2026-01-20 18:42:00', '2026-01-20 18:42:00'),
(71, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 101, 'App\\Models\\User', 2, '{\"old\":{\"last_name\":\"JOEL\",\"who_answering\":null,\"relationship_to_patient\":null},\"attributes\":{\"last_name\":\"JOELQWE\",\"who_answering\":\"QWE\",\"relationship_to_patient\":\"QWE\"}}', NULL, '2026-01-20 18:42:17', '2026-01-20 18:42:17'),
(72, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 60, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":66,\"service_id\":\"3\",\"appointment_date\":\"2026-01-21 10:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-20T18:43:22.545497Z\",\"updated_at\":\"2026-01-20T18:43:22.545530Z\"}}', NULL, '2026-01-20 18:43:22', '2026-01-20 18:43:22'),
(73, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 61, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":72,\"service_id\":\"3\",\"appointment_date\":\"2026-01-21 11:30:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-20T18:44:09.546660Z\",\"updated_at\":\"2026-01-20T18:44:09.546670Z\"}}', NULL, '2026-01-20 18:44:09', '2026-01-20 18:44:09'),
(74, 'default', 'Booked New Appointment', 'App\\Models\\Appointment', 'appointment_created', 62, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":102,\"service_id\":\"3\",\"appointment_date\":\"2026-01-21 13:00:00\",\"status\":\"Scheduled\",\"modified_by\":\"sample\",\"created_at\":\"2026-01-20T18:44:32.521833Z\",\"updated_at\":\"2026-01-20T18:44:32.521843Z\"}}', NULL, '2026-01-20 18:44:32', '2026-01-20 18:44:32'),
(75, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 62, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Arrived\"}}', NULL, '2026-01-21 02:30:01', '2026-01-21 02:30:01'),
(76, 'default', 'Created New Patient Record', 'App\\Models\\Patient', 'patient_created', 105, 'App\\Models\\User', 2, '{\"attributes\":{\"last_name\":\"WALKIN\",\"first_name\":\"WALK\",\"middle_name\":\"IN\",\"nickname\":\"\",\"occupation\":\"\",\"birth_date\":null,\"gender\":null,\"civil_status\":\"\",\"home_address\":\"\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"\",\"email_address\":\"\",\"referral\":\"\",\"emergency_contact_name\":\"\",\"emergency_contact_number\":\"\",\"relationship\":\"\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-01-21 14:14:49', '2026-01-21 14:14:49'),
(77, 'default', 'Registered Walk-In (Waiting Room)', 'App\\Models\\Appointment', 'appointment_created', 65, 'App\\Models\\User', 2, '{\"type\":\"Walk-In\"}', NULL, '2026-01-21 14:14:49', '2026-01-21 14:14:49'),
(78, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 69, 'App\\Models\\User', 7, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Arrived\"}}', NULL, '2026-01-22 01:42:35', '2026-01-22 01:42:35'),
(79, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 109, 'App\\Models\\User', 7, '{\"old\":{\"nickname\":null,\"occupation\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null},\"attributes\":{\"nickname\":\"kura\",\"occupation\":\"student\",\"civil_status\":\"widowed\",\"home_address\":\"fairview\",\"office_address\":\"makati\",\"home_number\":\"asdasd\",\"office_number\":\"dadsa\",\"email_address\":\"romebernacer123@gmail.com\",\"referral\":\"123321\",\"emergency_contact_name\":\"ASD\",\"emergency_contact_number\":\"213\",\"relationship\":\"asd\"}}', NULL, '2026-01-22 01:43:19', '2026-01-22 01:43:19'),
(80, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 109, 'App\\Models\\User', 7, '{\"attributes\":{\"when_last_visit_q1\":\"2017-11-22\",\"what_last_visit_reason_q1\":\"cleanuing\",\"what_seeing_dentist_reason_q2\":\"hatddog\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample1\",\"patient_id\":109,\"created_at\":\"2026-01-22T01:44:05.740158Z\",\"updated_at\":\"2026-01-22T01:44:05.740167Z\"}}', NULL, '2026-01-22 01:44:05', '2026-01-22 01:44:05'),
(81, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 109, 'App\\Models\\User', 7, '{\"attributes\":{\"when_last_visit_q1\":\"2017-11-22\",\"what_last_visit_reason_q1\":\"cleanuing\",\"what_seeing_dentist_reason_q2\":\"hatddog\",\"is_clicking_jaw_q3a\":false,\"is_pain_jaw_q3b\":false,\"is_difficulty_opening_closing_q3c\":false,\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":false,\"is_bad_experience_q5\":false,\"is_nervous_q6\":false,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample1\",\"patient_id\":109,\"created_at\":\"2026-01-22T01:44:08.960559Z\",\"updated_at\":\"2026-01-22T01:44:08.960573Z\"}}', NULL, '2026-01-22 01:44:08', '2026-01-22 01:44:08'),
(82, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 65, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Waiting\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-01-22 03:36:17', '2026-01-22 03:36:17'),
(83, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 69, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\"}}', NULL, '2026-01-22 04:32:16', '2026-01-22 04:32:16'),
(84, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 110, 'App\\Models\\User', 2, '{\"old\":{\"occupation\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null},\"attributes\":{\"occupation\":\"UT\",\"civil_status\":\"widowed\",\"home_address\":\"ASDASD\",\"office_address\":\"ASD\",\"home_number\":\"432434324\",\"office_number\":\"235253253\",\"email_address\":\"romick@GMAIL.COM\",\"referral\":\"ACE\",\"emergency_contact_name\":\"SUSAN\",\"emergency_contact_number\":\"019231313\",\"relationship\":\"Mother\"}}', NULL, '2026-01-22 04:42:27', '2026-01-22 04:42:27'),
(85, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 110, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":\"2026-01-31\",\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"Swollen\",\"is_clicking_jaw_q3a\":\"1\",\"is_pain_jaw_q3b\":\"1\",\"is_difficulty_opening_closing_q3c\":\"0\",\"is_locking_jaw_q3d\":false,\"is_clench_grind_q4\":\"1\",\"is_bad_experience_q5\":\"1\",\"is_nervous_q6\":\"1\",\"what_nervous_concern_q6\":\"Concern Citizen\",\"is_condition_q1\":false,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":false,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":false,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":false,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":false,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":false,\"is_pregnant_q7\":false,\"is_breast_feeding_q8\":false,\"modified_by\":\"sample\",\"patient_id\":110,\"created_at\":\"2026-01-22T04:47:50.850362Z\",\"updated_at\":\"2026-01-22T04:47:50.850373Z\"}}', NULL, '2026-01-22 04:47:50', '2026-01-22 04:47:50'),
(86, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 72, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Arrived\"}}', NULL, '2026-01-22 04:50:54', '2026-01-22 04:50:54'),
(87, 'default', 'Created New Patient Record', 'App\\Models\\Patient', 'patient_created', 111, 'App\\Models\\User', 2, '{\"attributes\":{\"last_name\":\"Legaspina\",\"first_name\":\"MDave\",\"middle_name\":\"Dela Vega\",\"nickname\":\"MCDave\",\"occupation\":\"UTI\",\"birth_date\":\"2003-07-08\",\"gender\":\"Male\",\"civil_status\":\"Married\",\"home_address\":\"Winston\",\"office_address\":\"Datamex\",\"home_number\":\"0912345678\",\"office_number\":\"0912345678\",\"mobile_number\":\"0912345678\",\"email_address\":\"dave@GMAIL.COM\",\"referral\":\"ACE\",\"emergency_contact_name\":\"NANAY\",\"emergency_contact_number\":\"926099304\",\"relationship\":\"Mother\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-01-22 04:56:43', '2026-01-22 04:56:43'),
(88, 'default', 'Registered Walk-In (Waiting Room)', 'App\\Models\\Appointment', 'appointment_created', 73, 'App\\Models\\User', 2, '{\"type\":\"Walk-In\"}', NULL, '2026-01-22 04:56:43', '2026-01-22 04:56:43'),
(89, 'default', 'Created User Account', 'App\\Models\\User', 'user_created', 12, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"Jerome_123\",\"contact\":\"912345678\",\"password\":\"$2y$12$qv.\\/2RMGEKJnDRt0awF8W.t5gXtJlyupVZWR6yOpC9FF3lD49nO\\/m\",\"role\":\"1\",\"created_at\":\"2026-01-22T05:08:04.155668Z\",\"updated_at\":\"2026-01-22T05:08:04.155679Z\",\"security_question\":\"What is your mother\'s maiden name?\",\"security_answer\":\"$2y$12$VMh1IEbADryszjB5Fr9QGOT4JUideR6nAlrNwN7D5vRq28km\\/U6RG\"}}', NULL, '2026-01-22 05:08:04', '2026-01-22 05:08:04'),
(90, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 72, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Arrived\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-01-22 05:22:02', '2026-01-22 05:22:02'),
(91, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 112, 'App\\Models\\User', 2, '{\"old\":{\"occupation\":null,\"gender\":null,\"civil_status\":null,\"home_address\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null},\"attributes\":{\"occupation\":\"fsdfsd\",\"gender\":\"Female\",\"civil_status\":\"sdasa\",\"home_address\":\"321321\",\"emergency_contact_name\":\"Rosales\",\"emergency_contact_number\":\"321321321\",\"relationship\":\"sadsa\",\"who_answering\":\"asdsasdsa\",\"relationship_to_patient\":\"qewqew\"}}', NULL, '2026-01-22 05:34:10', '2026-01-22 05:34:10'),
(92, 'default', 'Updated Patient Demographics', 'App\\Models\\Patient', 'patient_updated', 112, 'App\\Models\\User', 2, '{\"old\":{\"gender\":\"Female\"},\"attributes\":{\"gender\":\"Male\"}}', NULL, '2026-01-22 05:45:26', '2026-01-22 05:45:26'),
(93, 'default', 'Created Medical History (New Visit)', 'App\\Models\\Patient', 'health_history_created', 112, 'App\\Models\\User', 2, '{\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"dassdadsa\",\"is_clicking_jaw_q3a\":0,\"is_pain_jaw_q3b\":0,\"is_difficulty_opening_closing_q3c\":0,\"is_locking_jaw_q3d\":0,\"is_clench_grind_q4\":0,\"is_bad_experience_q5\":0,\"is_nervous_q6\":0,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":0,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":0,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":0,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":0,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":0,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":0,\"is_pregnant_q7\":0,\"is_breast_feeding_q8\":0,\"modified_by\":\"sample\",\"patient_id\":112,\"created_at\":\"2026-01-22T06:11:03.300267Z\",\"updated_at\":\"2026-01-22T06:11:03.300276Z\"}}', NULL, '2026-01-22 06:11:03', '2026-01-22 06:11:03'),
(94, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 74, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-01-22 07:07:05', '2026-01-22 07:07:05'),
(95, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 73, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Waiting\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-01-22 07:07:47', '2026-01-22 07:07:47'),
(96, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 73, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-01-22 07:08:56', '2026-01-22 07:08:56'),
(97, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 2, 'App\\Models\\User', 2, '{\"old\":{\"password\":\"$2y$12$4bJu2sqF1u4i\\/gsCyAox\\/OcEC6vqqgcwv2OJB25sn.4U7WN19NZkS\"},\"attributes\":{\"password\":\"$2y$12$mhpZDgiHO.QafpgDME7tBOyiBFqPL72sn759rVTjVPOgBR2smzOBe\"}}', NULL, '2026-01-22 13:48:56', '2026-01-22 13:48:56'),
(98, 'default', 'Created Patient', 'App\\Models\\Patient', 'patient_created', 114, 'App\\Models\\User', 2, '{\"attributes\":{\"first_name\":\"Dale\",\"last_name\":\"Rosales\",\"middle_name\":\"Salumbides\",\"mobile_number\":\"0997727222\",\"birth_date\":\"2000-01-22\"}}', NULL, '2026-01-22 16:32:07', '2026-01-22 16:32:07'),
(99, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 77, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":114,\"service_id\":\"3\",\"appointment_date\":\"2026-01-23 09:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-01-22 16:32:07', '2026-01-22 16:32:07'),
(100, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 114, 'App\\Models\\User', 2, '{\"old\":{\"id\":114,\"last_name\":\"Rosales\",\"first_name\":\"Dale\",\"mobile_number\":\"0997727222\",\"middle_name\":\"Salumbides\",\"nickname\":null,\"occupation\":null,\"birth_date\":\"2000-01-22\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-01-23 00:32:07\",\"updated_at\":\"2026-01-23 00:32:07\"},\"attributes\":{\"last_name\":\"Rosales\",\"first_name\":\"Dale\",\"middle_name\":\"Salumbides\",\"nickname\":null,\"occupation\":\"Construction Worker\",\"birth_date\":\"2000-01-22\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"2107 Rosal St Batasan Hills Quezon City\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"0997727222\",\"email_address\":null,\"referral\":null,\"emergency_contact_name\":\"Luis Rosales\",\"emergency_contact_number\":\"77237832782378\",\"relationship\":\"Father\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-01-22 16:33:51', '2026-01-22 16:33:51'),
(101, 'default', 'Deleted Patient', 'App\\Models\\Patient', 'patient_deleted', 71, 'App\\Models\\User', 2, '{\"old\":{\"id\":71,\"last_name\":\"a\",\"first_name\":\"Nicolas\",\"mobile_number\":\"dsadas\",\"middle_name\":\"a\",\"nickname\":null,\"occupation\":null,\"birth_date\":\"3123-12-31\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"SYSTEM\",\"created_at\":\"2025-12-16 22:19:10\",\"updated_at\":\"2025-12-16 22:19:10\"}}', NULL, '2026-01-22 16:34:28', '2026-01-22 16:34:28'),
(102, 'default', 'Deleted Patient', 'App\\Models\\Patient', 'patient_deleted', 106, 'App\\Models\\User', 2, '{\"old\":{\"id\":106,\"last_name\":\"XCVCXVXC\",\"first_name\":\"DASDAS\",\"mobile_number\":\"213321132\",\"middle_name\":\"FGDGDFCV\",\"nickname\":null,\"occupation\":null,\"birth_date\":\"2026-01-20\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-01-22 09:17:43\",\"updated_at\":\"2026-01-22 09:17:43\"},\"attributes\":{\"first_name\":\"DASDAS\",\"last_name\":\"XCVCXVXC\",\"middle_name\":\"FGDGDFCV\",\"mobile_number\":\"213321132\"}}', NULL, '2026-01-22 16:43:23', '2026-01-22 16:43:23'),
(103, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 78, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":25,\"patient_name\":\"ROSALES, CLARENZ LUIGI SALUMBIDES\",\"service_id\":\"2\",\"appointment_date\":\"2026-01-23 11:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-01-22 16:49:36', '2026-01-22 16:49:36'),
(104, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 79, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":110,\"service_id\":\"3\",\"appointment_date\":\"2026-01-23 12:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-01-22 16:51:33', '2026-01-22 16:51:33'),
(105, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 80, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":70,\"service_id\":\"2\",\"appointment_date\":\"2026-01-23 12:30:00\",\"status\":\"Scheduled\"}}', NULL, '2026-01-22 16:56:17', '2026-01-22 16:56:17'),
(106, 'default', 'Cancelled Appointment', 'App\\Models\\Appointment', 'appointment_cancelled', 80, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\",\"patient_name\":\"Nicolas, Stephen D\"}}', NULL, '2026-01-22 17:02:48', '2026-01-22 17:02:48'),
(107, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 78, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Arrived\",\"patient_name\":\"ROSALES, CLARENZ LUIGI SALUMBIDES\"}}', NULL, '2026-01-22 17:42:33', '2026-01-22 17:42:33'),
(108, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 77, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Arrived\",\"patient_name\":\"Rosales, Dale Salumbides\"}}', NULL, '2026-01-22 18:12:58', '2026-01-22 18:12:58');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(109, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 114, 'App\\Models\\User', 2, '{\"old\":{\"id\":114,\"last_name\":\"Rosales\",\"first_name\":\"Dale\",\"mobile_number\":\"0997727222\",\"middle_name\":\"Salumbides\",\"nickname\":null,\"occupation\":\"Construction Worker\",\"birth_date\":\"2000-01-22\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"2107 Rosal St Batasan Hills Quezon City\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":\"Luis Rosales\",\"emergency_contact_number\":\"77237832782378\",\"relationship\":\"Father\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-01-23 00:32:07\",\"updated_at\":\"2026-01-23 00:33:51\"},\"attributes\":{\"last_name\":\"Rosales\",\"first_name\":\"Dale\",\"middle_name\":\"Salumbides\",\"nickname\":\"DAASDDAS\",\"occupation\":\"Construction Worker\",\"birth_date\":\"2000-01-22\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"2107 Rosal St Batasan Hills Quezon City\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"0997727222\",\"email_address\":null,\"referral\":null,\"emergency_contact_name\":\"Luis Rosales\",\"emergency_contact_number\":\"77237832782378\",\"relationship\":\"Father\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-01-22 19:25:26', '2026-01-22 19:25:26'),
(110, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 22, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":114,\"chart_data\":\"{\\\"teeth\\\":{\\\"17\\\":{\\\"top\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"DR\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"DR\\\",\\\"color\\\":\\\"red\\\"},\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"DR\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Fair\\\",\\\"gingiva\\\":\\\"Severe Inflamed\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"Moderate\\\",\\\"complete_denture\\\":\\\"Lower\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-01-22 19:26:41', '2026-01-22 19:26:41'),
(111, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 23, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":114,\"chart_data\":\"{\\\"teeth\\\":{\\\"17\\\":{\\\"top\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"DR\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"DR\\\",\\\"color\\\":\\\"red\\\"},\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"DR\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Fair\\\",\\\"gingiva\\\":\\\"Severe Inflamed\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"Moderate\\\",\\\"complete_denture\\\":\\\"Lower\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-01-22 19:27:01', '2026-01-22 19:27:01'),
(112, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 24, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":113,\"chart_data\":\"{\\\"teeth\\\":{\\\"17\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CP\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CP\\\",\\\"color\\\":\\\"red\\\"},\\\"left\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CP\\\"},\\\"bottom\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CP\\\"},\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CP\\\"},\\\"top\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CP\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Poor\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"Severe\\\",\\\"stains\\\":\\\"Severe\\\",\\\"complete_denture\\\":\\\"Upper\\\",\\\"partial_denture\\\":\\\"Lower\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-01-22 19:33:58', '2026-01-22 19:33:58'),
(113, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 8, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":113,\"dmd\":\"dasasd\",\"treatment\":\"asdasd\",\"cost_of_treatment\":\"123\",\"amount_charged\":\"321\",\"remarks\":\"wre\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-01-22T19:33:58.304734Z\"}}', NULL, '2026-01-22 19:33:58', '2026-01-22 19:33:58'),
(114, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 81, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-02-07 17:43:39', '2026-02-07 17:43:39'),
(115, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 25, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Good\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"Lower\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-14 07:54:52', '2026-02-14 07:54:52'),
(116, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 9, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"dmd\":\"Me\",\"treatment\":\"Plan\",\"cost_of_treatment\":\"123\",\"amount_charged\":\"1231\",\"remarks\":\"Nothing\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-14T07:54:52.523334Z\"}}', NULL, '2026-02-14 07:54:52', '2026-02-14 07:54:52'),
(117, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 26, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Bad\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"Moderate\\\",\\\"stains\\\":\\\"Moderate\\\",\\\"complete_denture\\\":\\\"Upper\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-14 08:02:50', '2026-02-14 08:02:50'),
(118, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 10, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"dmd\":\"abcd\",\"treatment\":\"Mbaba\",\"cost_of_treatment\":\"123345\",\"amount_charged\":\"1232345\",\"remarks\":\"Adsa\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-14T08:02:50.573026Z\"}}', NULL, '2026-02-14 08:02:50', '2026-02-14 08:02:50'),
(119, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 27, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Fair\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"Moderate\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-14 08:06:47', '2026-02-14 08:06:47'),
(120, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 11, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"dmd\":\"DSFSDF\",\"treatment\":\"FGDFDGDFG\",\"cost_of_treatment\":\"213\",\"amount_charged\":\"345\",\"remarks\":\"SDF\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-14T08:06:47.879575Z\"}}', NULL, '2026-02-14 08:06:47', '2026-02-14 08:06:47'),
(121, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 28, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":116,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Poor\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"Slight\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-14 08:12:56', '2026-02-14 08:12:56'),
(122, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 12, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":116,\"dmd\":\"gdf\",\"treatment\":\"fgh\",\"cost_of_treatment\":\"345345\",\"amount_charged\":\"6456456\",\"remarks\":\"ghfgh\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-14T08:12:56.486631Z\"}}', NULL, '2026-02-14 08:12:56', '2026-02-14 08:12:56'),
(123, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 29, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"chart_data\":\"{\\\"teeth\\\":[null,null,null,null,null,null,null,null,null,null,null,null,null,{\\\"left\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CD\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CD\\\",\\\"color\\\":\\\"red\\\"},\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"---\\\"},\\\"line_2\\\":{\\\"code\\\":\\\"---\\\",\\\"color\\\":\\\"red\\\"},\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"C\\\"},\\\"line_3\\\":{\\\"code\\\":\\\"C\\\",\\\"color\\\":\\\"red\\\"}},null,null,null,{\\\"center\\\":{\\\"color\\\":\\\"blue\\\",\\\"code\\\":\\\"LC\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"LC\\\",\\\"color\\\":\\\"blue\\\"},\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CI\\\"},\\\"line_2\\\":{\\\"code\\\":\\\"CI\\\",\\\"color\\\":\\\"red\\\"}}],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Good\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"Slight\\\",\\\"stains\\\":\\\"Severe\\\",\\\"complete_denture\\\":\\\"Upper\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-14 08:22:06', '2026-02-14 08:22:06'),
(124, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 13, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":117,\"dmd\":\"SDF\",\"treatment\":\"123\",\"cost_of_treatment\":\"345345\",\"amount_charged\":\"243234\",\"remarks\":\"GDFDFG\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-14T08:22:06.698543Z\"}}', NULL, '2026-02-14 08:22:06', '2026-02-14 08:22:06'),
(125, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 84, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-02-14 11:55:44', '2026-02-14 11:55:44'),
(126, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 82, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-02-14 11:55:52', '2026-02-14 11:55:52'),
(127, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 83, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-02-14 11:55:56', '2026-02-14 11:55:56'),
(128, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 89, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":25,\"patient_name\":\"ROSALES, CLARENZ LUIGI SALUMBIDES\",\"service_id\":\"2\",\"appointment_date\":\"2026-02-15 09:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-15 15:33:46', '2026-02-15 15:33:46'),
(129, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 89, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\",\"patient_name\":\"ROSALES, CLARENZ LUIGI SALUMBIDES\"}}', NULL, '2026-02-15 15:34:06', '2026-02-15 15:34:06'),
(130, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 89, 'App\\Models\\User', 2, '[]', NULL, '2026-02-15 15:42:23', '2026-02-15 15:42:23'),
(131, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 90, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":110,\"patient_name\":\"Legaspina, MDave Dela Vega\",\"service_id\":\"1\",\"appointment_date\":\"2026-02-18 10:30:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-17 16:05:16', '2026-02-17 16:05:16'),
(132, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 90, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\",\"patient_name\":\"Legaspina, MDave Dela Vega\"}}', NULL, '2026-02-17 16:05:27', '2026-02-17 16:05:27'),
(133, 'default', 'Admitted Appointment', 'App\\Models\\Appointment', 'appointment_admitted', 90, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Waiting\",\"service_id\":1,\"dentist_id\":null},\"attributes\":{\"status\":\"Ongoing\",\"service_id\":1,\"dentist_id\":2}}', NULL, '2026-02-17 16:05:28', '2026-02-17 16:05:28'),
(134, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 91, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":101,\"patient_name\":\"JOELQWE, JOEL JOEL\",\"service_id\":\"2\",\"appointment_date\":\"2026-02-19 18:30:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-19 09:21:58', '2026-02-19 09:21:58'),
(135, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 92, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":66,\"patient_name\":\"QWEQWE, ASDASDASD QWEQWE\",\"service_id\":\"2\",\"appointment_date\":\"2026-02-19 15:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-19 11:18:00', '2026-02-19 11:18:00'),
(136, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 93, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":25,\"patient_name\":\"ROSALES, CLARENZ LUIGI SALUMBIDES\",\"service_id\":\"1\",\"appointment_date\":\"2026-02-22 16:30:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-22 11:43:37', '2026-02-22 11:43:37'),
(137, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 94, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-02-22 15:23:01', '2026-02-22 15:23:01'),
(138, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 99, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":110,\"patient_name\":\"Legaspina, MDave Dela Vega\",\"service_id\":\"1\",\"appointment_date\":\"2026-02-23 09:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-23 01:05:09', '2026-02-23 01:05:09'),
(139, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 99, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\",\"patient_name\":\"Legaspina, MDave Dela Vega\"}}', NULL, '2026-02-23 01:14:44', '2026-02-23 01:14:44'),
(140, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 99, 'App\\Models\\User', 2, '[]', NULL, '2026-02-23 01:15:55', '2026-02-23 01:15:55'),
(141, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 99, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\",\"patient_name\":\"Legaspina, MDave Dela Vega\"}}', NULL, '2026-02-23 01:52:23', '2026-02-23 01:52:23'),
(142, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 96, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-02-23 02:18:47', '2026-02-23 02:18:47'),
(143, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 100, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":114,\"patient_name\":\"Rosales, Dale Salumbides\",\"service_id\":\"1\",\"appointment_date\":\"2026-02-23 17:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-02-23 02:25:07', '2026-02-23 02:25:07'),
(144, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 30, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Fair\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-24 17:41:37', '2026-02-24 17:41:37'),
(145, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 14, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"dmd\":\"eqwqew\",\"treatment\":\"eqwqwe\",\"cost_of_treatment\":\"231\",\"amount_charged\":\"312\",\"remarks\":\"sdasd\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-24T17:41:37.428734Z\"}}', NULL, '2026-02-24 17:41:37', '2026-02-24 17:41:37'),
(146, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 31, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"chart_data\":\"{\\\"teeth\\\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CP\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CP\\\",\\\"color\\\":\\\"red\\\"}}],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Good\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-25 04:56:26', '2026-02-25 04:56:26'),
(147, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 15, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"dmd\":\"Dr. Rosales\",\"treatment\":\"Extraction\",\"cost_of_treatment\":\"500\",\"amount_charged\":\"500\",\"remarks\":\"\",\"image\":null,\"modified_by\":\"sample\",\"updated_at\":\"2026-02-25T04:56:27.027173Z\"}}', NULL, '2026-02-25 04:56:27', '2026-02-25 04:56:27'),
(148, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 32, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"chart_data\":\"{\\\"teeth\\\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CD\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CD\\\",\\\"color\\\":\\\"red\\\"}}],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Excellent\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"Moderate\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-25 06:12:10', '2026-02-25 06:12:10'),
(149, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 16, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"dmd\":\"Dr me\",\"treatment\":\"Longganog\",\"cost_of_treatment\":\"123\",\"amount_charged\":\"345\",\"remarks\":\"asd\",\"modified_by\":\"sample\",\"updated_at\":\"2026-02-25T06:12:10.484106Z\"}}', NULL, '2026-02-25 06:12:10', '2026-02-25 06:12:10'),
(150, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 33, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"chart_data\":\"{\\\"teeth\\\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"C\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"C\\\",\\\"color\\\":\\\"red\\\"}}],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Poor\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(151, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 17, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"dmd\":\"SDA\",\"treatment\":\"QWE\",\"cost_of_treatment\":\"123\",\"amount_charged\":\"342\",\"remarks\":\"DAS\",\"modified_by\":\"sample\",\"updated_at\":\"2026-02-25T06:17:55.659656Z\"}}', NULL, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(152, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 34, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"chart_data\":\"{\\\"teeth\\\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\\\"bottom\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CD\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CD\\\",\\\"color\\\":\\\"red\\\"}}],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Excellent\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"Moderate\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"}}\"}}', NULL, '2026-02-25 06:32:37', '2026-02-25 06:32:37'),
(153, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 18, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"dmd\":\"DAS\",\"treatment\":\"ASD\",\"cost_of_treatment\":\"231\",\"amount_charged\":\"231\",\"remarks\":\"ASD\",\"modified_by\":\"sample\",\"updated_at\":\"2026-02-25T06:32:37.980281Z\"}}', NULL, '2026-02-25 06:32:37', '2026-02-25 06:32:37'),
(154, 'default', 'Deleted Patient', 'App\\Models\\Patient', 'patient_deleted', 116, 'App\\Models\\User', 2, '{\"old\":{\"id\":116,\"last_name\":\"DSA\",\"first_name\":\"ASD\",\"mobile_number\":\"213\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":null,\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"sdjklsdfjkl@gmail.com\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"GUEST\",\"created_at\":\"2026-02-08 00:35:49\",\"updated_at\":\"2026-02-08 00:35:49\"},\"attributes\":{\"first_name\":\"ASD\",\"last_name\":\"DSA\",\"middle_name\":null,\"mobile_number\":\"213\"}}', NULL, '2026-02-25 07:30:51', '2026-02-25 07:30:51'),
(155, 'default', 'Deleted Patient', 'App\\Models\\Patient', 'patient_deleted', 112, 'App\\Models\\User', 2, '{\"old\":{\"id\":112,\"last_name\":\"sadsads\",\"first_name\":\"fgdgd\",\"mobile_number\":\"213231213\",\"middle_name\":\"gfdfggfd\",\"nickname\":null,\"occupation\":\"fsdfsd\",\"birth_date\":\"2026-01-21\",\"gender\":\"Male\",\"civil_status\":\"sdasa\",\"home_address\":\"321321\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":\"Rosales\",\"emergency_contact_number\":\"321321321\",\"relationship\":\"sadsa\",\"who_answering\":\"asdsasdsa\",\"relationship_to_patient\":\"qewqew\",\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-01-22 13:19:46\",\"updated_at\":\"2026-01-22 13:45:26\"},\"attributes\":{\"first_name\":\"fgdgd\",\"last_name\":\"sadsads\",\"middle_name\":\"gfdfggfd\",\"mobile_number\":\"213231213\"}}', NULL, '2026-02-25 07:35:53', '2026-02-25 07:35:53'),
(156, 'default', 'Deleted Patient', 'App\\Models\\Patient', 'patient_deleted', 81, 'App\\Models\\User', 2, '{\"old\":{\"id\":81,\"last_name\":\"TR\",\"first_name\":\"TR\",\"mobile_number\":\"231321\",\"middle_name\":\"TR\",\"nickname\":null,\"occupation\":null,\"birth_date\":\"2026-01-19\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-01-19 22:49:46\",\"updated_at\":\"2026-01-19 22:49:46\"},\"attributes\":{\"first_name\":\"TR\",\"last_name\":\"TR\",\"middle_name\":\"TR\",\"mobile_number\":\"231321\"}}', NULL, '2026-02-25 07:37:07', '2026-02-25 07:37:07'),
(157, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 96, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-02-25 10:39:22', '2026-02-25 10:39:22'),
(158, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 100, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-02-25 10:39:51', '2026-02-25 10:39:51'),
(159, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 90, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-02-25 10:39:58', '2026-02-25 10:39:58'),
(160, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 101, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":110,\"patient_name\":\"Legaspina, MDave Dela Vega\",\"service_id\":\"1\",\"appointment_date\":\"2026-03-06 11:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-06 09:30:12', '2026-03-06 09:30:12'),
(161, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 115, 'App\\Models\\User', 2, '{\"old\":{\"first_name\":\"GHJK\",\"last_name\":\"ZCX\",\"middle_name\":null,\"birth_date\":null},\"attributes\":{\"first_name\":\"GHJK\",\"last_name\":\"ZCX\",\"middle_name\":null,\"birth_date\":\"2026-03-13\"}}', NULL, '2026-03-06 14:50:52', '2026-03-06 14:50:52'),
(162, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 102, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":115,\"patient_name\":\"ZCX, GHJK\",\"service_id\":\"1\",\"appointment_date\":\"2026-03-06 17:30:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-06 14:50:52', '2026-03-06 14:50:52'),
(163, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 95, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-08 17:04:57', '2026-03-08 17:04:57'),
(164, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 103, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":113,\"patient_name\":\"9, Gloc\",\"service_id\":\"2\",\"appointment_date\":\"2026-03-12 14:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-12 05:21:06', '2026-03-12 05:21:06'),
(165, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 103, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-12 12:41:03', '2026-03-12 12:41:03'),
(166, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 104, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":113,\"patient_name\":\"9, Gloc\",\"service_id\":\"2\",\"appointment_date\":\"2026-03-12 17:30:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-12 12:52:03', '2026-03-12 12:52:03'),
(167, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 105, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":114,\"patient_name\":\"Rosales, Dale Salumbides\",\"service_id\":\"3\",\"appointment_date\":\"2026-03-12 12:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-12 12:53:46', '2026-03-12 12:53:46'),
(168, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 104, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-12 15:15:14', '2026-03-12 15:15:14'),
(169, 'default', 'Admitted Appointment', 'App\\Models\\Appointment', 'appointment_admitted', 103, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Waiting\",\"service_id\":2,\"dentist_id\":null},\"attributes\":{\"status\":\"Ongoing\",\"service_id\":2,\"dentist_id\":2}}', NULL, '2026-03-12 15:15:21', '2026-03-12 15:15:21'),
(170, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 106, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":25,\"patient_name\":\"ROSALES, CLARENZ LUIGI SALUMBIDES\",\"service_id\":\"2\",\"appointment_date\":\"2026-03-12 12:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 04:04:01', '2026-03-13 04:04:01'),
(171, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 118, 'App\\Models\\User', 2, '{\"old\":{\"first_name\":\"mn\",\"last_name\":\"nm\",\"middle_name\":null,\"birth_date\":null},\"attributes\":{\"first_name\":\"mn\",\"last_name\":\"nm\",\"middle_name\":null,\"birth_date\":\"2003-01-22\"}}', NULL, '2026-03-13 04:05:53', '2026-03-13 04:05:53'),
(172, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 107, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"patient_name\":\"nm, mn\",\"service_id\":\"2\",\"appointment_date\":\"2026-03-12 14:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 04:05:53', '2026-03-13 04:05:53'),
(173, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 97, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 05:37:13', '2026-03-13 05:37:13'),
(174, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 98, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 05:37:18', '2026-03-13 05:37:18'),
(175, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 97, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-13 05:38:46', '2026-03-13 05:38:46'),
(176, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 98, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-13 05:38:55', '2026-03-13 05:38:55'),
(177, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 108, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 05:40:30', '2026-03-13 05:40:30'),
(178, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 109, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 05:40:34', '2026-03-13 05:40:34'),
(179, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 108, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-13 05:40:52', '2026-03-13 05:40:52'),
(180, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 109, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-13 05:41:05', '2026-03-13 05:41:05'),
(181, 'default', 'Admitted Appointment', 'App\\Models\\Appointment', 'appointment_admitted', 109, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Waiting\",\"service_id\":3,\"dentist_id\":null},\"attributes\":{\"status\":\"Ongoing\",\"service_id\":3,\"dentist_id\":2}}', NULL, '2026-03-13 05:41:15', '2026-03-13 05:41:15'),
(182, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 109, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\",\"patient_name\":\"El, Jo\"}}', NULL, '2026-03-13 05:41:51', '2026-03-13 05:41:51'),
(183, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 108, 'App\\Models\\User', 2, '[]', NULL, '2026-03-13 05:41:57', '2026-03-13 05:41:57'),
(184, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 108, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\",\"patient_name\":\"Cerineoo, Deeyowil\"}}', NULL, '2026-03-13 05:42:12', '2026-03-13 05:42:12'),
(185, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 110, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 06:44:08', '2026-03-13 06:44:08'),
(186, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 111, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 06:44:15', '2026-03-13 06:44:15'),
(187, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 110, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-13 06:44:29', '2026-03-13 06:44:29'),
(188, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 111, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-13 06:44:40', '2026-03-13 06:44:40'),
(189, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 110, 'App\\Models\\User', 2, '[]', NULL, '2026-03-13 06:44:57', '2026-03-13 06:44:57'),
(190, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 35, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":119,\"chart_data\":\"{\\\"teeth\\\":{\\\"11\\\":null,\\\"12\\\":{\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CD\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CD\\\",\\\"color\\\":\\\"red\\\"}},\\\"13\\\":null,\\\"14\\\":null,\\\"15\\\":null,\\\"16\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CI\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CI\\\",\\\"color\\\":\\\"red\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Good\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"Upper\\\",\\\"partial_denture\\\":\\\"Lower\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"asd\\\",\\\"treatment_plan\\\":\\\"step 123\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-13 06:46:37', '2026-03-13 06:46:37'),
(191, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 19, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":119,\"dmd\":\"Dr. Hiluluk\",\"treatment\":\"Extraction\",\"cost_of_treatment\":\"1200\",\"amount_charged\":\"1000\",\"remarks\":\"celan\",\"modified_by\":\"sample\",\"updated_at\":\"2026-03-13T06:46:37.360989Z\"}}', NULL, '2026-03-13 06:46:37', '2026-03-13 06:46:37'),
(192, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 112, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":27,\"patient_name\":\"ROSALES, LUIS\",\"service_id\":\"2\",\"appointment_date\":\"2026-03-13 18:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 06:49:41', '2026-03-13 06:49:41'),
(193, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 113, 'App\\Models\\User', 2, '{\"old\":{\"first_name\":\"Gloc\",\"last_name\":\"9\",\"middle_name\":\"\",\"birth_date\":\"2026-01-13\"},\"attributes\":{\"first_name\":\"Jo\",\"last_name\":\"El\",\"middle_name\":null,\"birth_date\":\"2003-11-01\"}}', NULL, '2026-03-13 06:57:31', '2026-03-13 06:57:31'),
(194, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 113, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":113,\"patient_name\":\"El, Jo\",\"service_id\":\"2\",\"appointment_date\":\"2026-03-13 18:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 06:57:31', '2026-03-13 06:57:31'),
(195, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 114, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":110,\"patient_name\":\"Legaspina, MDave Dela Vega\",\"service_id\":\"3\",\"appointment_date\":\"2026-03-13 17:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 07:01:53', '2026-03-13 07:01:53'),
(196, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 120, 'App\\Models\\User', 2, '{\"old\":{\"id\":120,\"last_name\":\"El\",\"first_name\":\"Jo\",\"mobile_number\":\"123456789\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":null,\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cerineo123@gmail.com\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"cerineo123@gmail.com\",\"created_at\":\"2026-03-13 13:39:51\",\"updated_at\":\"2026-03-13 14:42:50\"},\"attributes\":{\"last_name\":\"El\",\"first_name\":\"Jo\",\"middle_name\":\"Qu\",\"nickname\":null,\"occupation\":\"Dancer\",\"birth_date\":\"2000-11-11\",\"gender\":\"Female\",\"civil_status\":\"Single\",\"home_address\":\"Bahay\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"123456789\",\"email_address\":\"cerineo123@gmail.com\",\"referral\":null,\"emergency_contact_name\":\"Mama\",\"emergency_contact_number\":\"1231313\",\"relationship\":\"mama\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 07:34:01', '2026-03-13 07:34:01'),
(197, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 120, 'App\\Models\\User', 2, '{\"old\":{\"id\":120,\"last_name\":\"El\",\"first_name\":\"Jo\",\"mobile_number\":\"123456789\",\"middle_name\":\"Qu\",\"nickname\":null,\"occupation\":\"Dancer\",\"birth_date\":\"2000-11-11\",\"gender\":\"Female\",\"civil_status\":\"Single\",\"home_address\":\"Bahay\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cerineo123@gmail.com\",\"referral\":null,\"emergency_contact_name\":\"Mama\",\"emergency_contact_number\":\"1231313\",\"relationship\":\"mama\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 13:39:51\",\"updated_at\":\"2026-03-13 15:34:01\"},\"attributes\":{\"last_name\":\"El\",\"first_name\":\"Jo\",\"middle_name\":\"Qu\",\"nickname\":null,\"occupation\":\"Dancer\",\"birth_date\":\"2000-11-11\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"Bahay\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"123456789\",\"email_address\":\"cerineo123@gmail.com\",\"referral\":null,\"emergency_contact_name\":\"Mama\",\"emergency_contact_number\":\"1231313\",\"relationship\":\"mama\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 07:34:15', '2026-03-13 07:34:15'),
(198, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 110, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\",\"patient_name\":\"Cerineoo, Deeyowil\"}}', NULL, '2026-03-13 07:36:16', '2026-03-13 07:36:16'),
(199, 'default', 'Cancelled Appointment', 'App\\Models\\Appointment', 'appointment_cancelled', 113, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\",\"patient_name\":\"El, Jo\"}}', NULL, '2026-03-13 07:36:33', '2026-03-13 07:36:33'),
(200, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 111, 'App\\Models\\User', 2, '[]', NULL, '2026-03-13 07:36:59', '2026-03-13 07:36:59'),
(201, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 36, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":120,\"chart_data\":\"{\\\"teeth\\\":{\\\"11\\\":{\\\"left\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"C\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"C\\\",\\\"color\\\":\\\"red\\\"}},\\\"12\\\":null,\\\"13\\\":null,\\\"14\\\":null,\\\"15\\\":null,\\\"16\\\":null,\\\"17\\\":null,\\\"18\\\":null,\\\"21\\\":null,\\\"22\\\":null,\\\"23\\\":null,\\\"24\\\":null,\\\"25\\\":null,\\\"26\\\":null,\\\"27\\\":null,\\\"28\\\":null,\\\"31\\\":null,\\\"32\\\":{\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"C\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"C\\\",\\\"color\\\":\\\"red\\\"}},\\\"33\\\":null,\\\"34\\\":null,\\\"35\\\":null,\\\"36\\\":null,\\\"37\\\":null,\\\"38\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"X\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"X\\\",\\\"color\\\":\\\"red\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Excellent\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"Slight\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-13 07:38:46', '2026-03-13 07:38:46'),
(202, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 20, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":120,\"dmd\":\"Dr. Dave\",\"treatment\":\"Cleaning\",\"cost_of_treatment\":\"1000\",\"amount_charged\":\"1000\",\"remarks\":\"GUDS\",\"modified_by\":\"sample\",\"updated_at\":\"2026-03-13T07:38:46.167944Z\"}}', NULL, '2026-03-13 07:38:46', '2026-03-13 07:38:46'),
(203, 'default', 'Created Patient', 'App\\Models\\Patient', 'patient_created', 121, 'App\\Models\\User', 2, '{\"attributes\":{\"first_name\":\"Ichi\",\"last_name\":\"Kawa\",\"middle_name\":\"\",\"mobile_number\":\"122333\",\"birth_date\":\"2003-01-02\"}}', NULL, '2026-03-13 07:42:56', '2026-03-13 07:42:56'),
(204, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 115, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":121,\"patient_name\":\"Kawa, Ichi\",\"service_id\":\"1\",\"appointment_date\":\"2026-03-13 19:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 07:42:56', '2026-03-13 07:42:56'),
(205, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 111, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\",\"patient_name\":\"El, Jo Qu\"}}', NULL, '2026-03-13 07:44:04', '2026-03-13 07:44:04'),
(206, 'default', 'Cancelled Appointment', 'App\\Models\\Appointment', 'appointment_cancelled', 112, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\",\"patient_name\":\"ROSALES, LUIS\"}}', NULL, '2026-03-13 07:44:19', '2026-03-13 07:44:19'),
(207, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 116, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":121,\"patient_name\":\"Kawa, Ichi\",\"service_id\":\"1\",\"appointment_date\":\"2026-03-13 18:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-13 07:46:09', '2026-03-13 07:46:09'),
(208, 'default', 'Created Patient', 'App\\Models\\Patient', 'patient_created', 122, 'App\\Models\\User', 2, '{\"attributes\":{\"last_name\":\"Hinomori\",\"first_name\":\"Shisho\",\"middle_name\":\"Mori\",\"nickname\":\"\",\"occupation\":\"Actress\",\"birth_date\":\"2002-02-02\",\"gender\":\"Female\",\"civil_status\":\"Single\",\"home_address\":\"School\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"123123132\",\"email_address\":\"Shisho@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Emu\",\"emergency_contact_number\":\"12312323\",\"relationship\":\"BFF\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\"}}', NULL, '2026-03-13 07:49:07', '2026-03-13 07:49:07'),
(209, 'default', 'Created Health History', 'App\\Models\\Patient', 'health_history_created', 122, 'App\\Models\\User', 2, '{\"health_history_id\":24,\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"Check Up\",\"is_clicking_jaw_q3a\":0,\"is_pain_jaw_q3b\":0,\"is_difficulty_opening_closing_q3c\":0,\"is_locking_jaw_q3d\":0,\"is_clench_grind_q4\":0,\"is_bad_experience_q5\":0,\"is_nervous_q6\":0,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":0,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":0,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":0,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":0,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":0,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":0,\"is_pregnant_q7\":0,\"is_breast_feeding_q8\":0,\"selectedHistoryId\":\"new\"}}', NULL, '2026-03-13 07:49:07', '2026-03-13 07:49:07'),
(210, 'default', 'Created Walk-in Appointment', 'App\\Models\\Appointment', 'appointment_created', 117, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":122,\"patient_name\":\"Hinomori, Shisho Mori\",\"status\":\"Waiting\"}}', NULL, '2026-03-13 07:49:07', '2026-03-13 07:49:07'),
(211, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 122, 'App\\Models\\User', 2, '{\"old\":{\"id\":122,\"last_name\":\"Hinomori\",\"first_name\":\"Shisho\",\"mobile_number\":\"123123132\",\"middle_name\":\"Mori\",\"nickname\":\"\",\"occupation\":\"Actress\",\"birth_date\":\"2002-02-02\",\"gender\":\"Female\",\"civil_status\":\"Single\",\"home_address\":\"School\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"email_address\":\"Shisho@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Emu\",\"emergency_contact_number\":\"12312323\",\"relationship\":\"BFF\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 15:49:07\",\"updated_at\":\"2026-03-13 15:49:07\"},\"attributes\":{\"last_name\":\"Hinomori\",\"first_name\":\"Shisho\",\"middle_name\":\"Mori\",\"nickname\":\"\",\"occupation\":\"Actress\",\"birth_date\":\"2002-02-02\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"School\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"123123132\",\"email_address\":\"Shisho@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Emu\",\"emergency_contact_number\":\"12312323\",\"relationship\":\"BFF\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-03-13 07:50:50', '2026-03-13 07:50:50');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(212, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 122, 'App\\Models\\User', 2, '{\"old\":{\"id\":122,\"last_name\":\"Hinomori\",\"first_name\":\"Shisho\",\"mobile_number\":\"123123132\",\"middle_name\":\"Mori\",\"nickname\":\"\",\"occupation\":\"Actress\",\"birth_date\":\"2002-02-02\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"School\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"email_address\":\"Shisho@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Emu\",\"emergency_contact_number\":\"12312323\",\"relationship\":\"BFF\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 15:49:07\",\"updated_at\":\"2026-03-13 15:50:49\"},\"attributes\":{\"last_name\":\"Hinomori\",\"first_name\":\"Shisho\",\"middle_name\":\"Mori\",\"nickname\":\"\",\"occupation\":\"Actress\",\"birth_date\":\"2002-02-02\",\"gender\":\"Female\",\"civil_status\":\"Single\",\"home_address\":\"School\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"123123132\",\"email_address\":\"Shisho@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Emu\",\"emergency_contact_number\":\"12312323\",\"relationship\":\"BFF\",\"who_answering\":\"\",\"relationship_to_patient\":\"\",\"father_name\":\"\",\"father_number\":\"\",\"mother_name\":\"\",\"mother_number\":\"\",\"guardian_name\":\"\",\"guardian_number\":\"\",\"modified_by\":\"sample\"}}', NULL, '2026-03-13 07:51:01', '2026-03-13 07:51:01'),
(213, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 117, 'App\\Models\\User', 2, '[]', NULL, '2026-03-13 07:51:21', '2026-03-13 07:51:21'),
(214, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 37, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":122,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Excellent\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-13 07:52:29', '2026-03-13 07:52:29'),
(215, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 21, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":122,\"dmd\":\"Dr. Dave\",\"treatment\":\"Cleaning\",\"cost_of_treatment\":\"1000\",\"amount_charged\":\"1000\",\"remarks\":\"\",\"modified_by\":\"sample\",\"updated_at\":\"2026-03-13T07:52:29.690471Z\"}}', NULL, '2026-03-13 07:52:29', '2026-03-13 07:52:29'),
(216, 'default', 'Cancelled Appointment', 'App\\Models\\Appointment', 'appointment_cancelled', 117, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Cancelled\",\"patient_name\":\"Hinomori, Shisho Mori\"}}', NULL, '2026-03-13 07:53:09', '2026-03-13 07:53:09'),
(217, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 118, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 08:03:07', '2026-03-13 08:03:07'),
(218, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 118, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\",\"patient_name\":\"Rui, Rui\"}}', NULL, '2026-03-13 08:05:05', '2026-03-13 08:05:05'),
(219, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 123, 'App\\Models\\User', 2, '{\"old\":{\"id\":123,\"last_name\":\"Rui\",\"first_name\":\"Rui\",\"mobile_number\":\"1801233123\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":null,\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"twgrd90335@minitts.net\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"twgrd90335@minitts.net\",\"created_at\":\"2026-03-13 16:01:41\",\"updated_at\":\"2026-03-13 16:01:41\"},\"attributes\":{\"last_name\":\"Rui\",\"first_name\":\"Rui\",\"middle_name\":\"Rui\",\"nickname\":null,\"occupation\":\"Inventor\",\"birth_date\":\"2003-03-31\",\"gender\":\"Male\",\"civil_status\":\"Single\",\"home_address\":\"WonderLand\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"1801233123\",\"email_address\":\"twgrd90335@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"Emu\",\"emergency_contact_number\":\"123123123\",\"relationship\":\"Friend\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 08:07:01', '2026-03-13 08:07:01'),
(220, 'default', 'Created Health History', 'App\\Models\\Patient', 'health_history_created', 123, 'App\\Models\\User', 2, '{\"health_history_id\":25,\"attributes\":{\"when_last_visit_q1\":null,\"what_last_visit_reason_q1\":\"\",\"what_seeing_dentist_reason_q2\":\"Cleaning\",\"is_clicking_jaw_q3a\":0,\"is_pain_jaw_q3b\":0,\"is_difficulty_opening_closing_q3c\":0,\"is_locking_jaw_q3d\":0,\"is_clench_grind_q4\":0,\"is_bad_experience_q5\":0,\"is_nervous_q6\":0,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":0,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":0,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":0,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":0,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":0,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":0,\"is_pregnant_q7\":0,\"is_breast_feeding_q8\":0,\"modified_by\":\"sample\",\"patient_id\":123,\"created_at\":\"2026-03-13T08:12:05.474607Z\",\"updated_at\":\"2026-03-13T08:12:05.474624Z\"}}', NULL, '2026-03-13 08:12:05', '2026-03-13 08:12:05'),
(221, 'default', 'Admitted Patient to Chair', 'App\\Models\\Appointment', 'appointment_admitted', 118, 'App\\Models\\User', 2, '[]', NULL, '2026-03-13 08:12:28', '2026-03-13 08:12:28'),
(222, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 38, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":123,\"chart_data\":\"{\\\"teeth\\\":[],\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Excellent\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-13 08:13:41', '2026-03-13 08:13:41'),
(223, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 22, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":123,\"dmd\":\"Dr. Dave\",\"treatment\":\"Cleaning\",\"cost_of_treatment\":\"2000\",\"amount_charged\":\"2000\",\"remarks\":\"Excellent!\",\"modified_by\":\"sample\",\"updated_at\":\"2026-03-13T08:13:41.499860Z\"}}', NULL, '2026-03-13 08:13:41', '2026-03-13 08:13:41'),
(224, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 118, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\",\"patient_name\":\"Rui, Rui Rui\"}}', NULL, '2026-03-13 08:14:06', '2026-03-13 08:14:06'),
(225, 'default', 'Deleted Patient', 'App\\Models\\Patient', 'patient_deleted', 113, 'App\\Models\\User', 2, '{\"old\":{\"id\":113,\"last_name\":\"El\",\"first_name\":\"Jo\",\"mobile_number\":\"123456789\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":\"2003-11-01\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":null,\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"Jerome_123\",\"created_at\":\"2026-01-22 15:56:06\",\"updated_at\":\"2026-03-13 14:57:31\"},\"attributes\":{\"first_name\":\"Jo\",\"last_name\":\"El\",\"middle_name\":null,\"mobile_number\":\"123456789\"}}', NULL, '2026-03-13 08:21:40', '2026-03-13 08:21:40'),
(226, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 39, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":39,\"username\":\"twgrd90335@minitts.net\",\"email\":\"twgrd90335@minitts.net\",\"email_verified_at\":\"2026-03-13 15:56:21\",\"google_id\":null,\"role\":3,\"created_at\":\"2026-03-13 15:55:43\",\"updated_at\":\"2026-03-13 15:56:21\"}}', NULL, '2026-03-13 08:23:23', '2026-03-13 08:23:23'),
(227, 'default', 'Created User Account', 'App\\Models\\User', 'user_created', 40, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"SICKAA@GMAIL.COM\",\"email\":\"SICKAA@GMAIL.COM\",\"role\":\"1\",\"email_verified_at\":null,\"created_at\":\"2026-03-13T08:32:49.325229Z\",\"updated_at\":\"2026-03-13T08:32:49.325240Z\"}}', NULL, '2026-03-13 08:32:51', '2026-03-13 08:32:51'),
(228, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 40, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":40,\"username\":\"SICKAA@GMAIL.COM\",\"email\":\"SICKAA@GMAIL.COM\",\"email_verified_at\":null,\"google_id\":null,\"role\":1,\"created_at\":\"2026-03-13 16:32:49\",\"updated_at\":\"2026-03-13 16:32:49\"}}', NULL, '2026-03-13 08:33:33', '2026-03-13 08:33:33'),
(229, 'default', 'Created User Account', 'App\\Models\\User', 'user_created', 41, 'App\\Models\\User', 2, '{\"attributes\":{\"username\":\"SICKAA@GMAIL.COM\",\"email\":\"SICKAA@GMAIL.COM\",\"role\":\"1\",\"email_verified_at\":null,\"created_at\":\"2026-03-13T08:34:01.287917Z\",\"updated_at\":\"2026-03-13T08:34:01.287929Z\"}}', NULL, '2026-03-13 08:34:03', '2026-03-13 08:34:03'),
(230, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 41, 'App\\Models\\User', 2, '{\"old\":{\"role\":1},\"attributes\":{\"role\":\"2\"}}', NULL, '2026-03-13 08:34:19', '2026-03-13 08:34:19'),
(231, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 41, 'App\\Models\\User', 2, '{\"attributes\":{\"id\":41,\"username\":\"SICKAA@GMAIL.COM\",\"email\":\"SICKAA@GMAIL.COM\",\"email_verified_at\":null,\"google_id\":null,\"role\":2,\"created_at\":\"2026-03-13 16:34:01\",\"updated_at\":\"2026-03-13 16:34:19\"}}', NULL, '2026-03-13 08:34:29', '2026-03-13 08:34:29'),
(232, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 39, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"chart_data\":\"{\\\"teeth\\\":{\\\"51\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CI\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CI\\\",\\\"color\\\":\\\"red\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Fair\\\",\\\"gingiva\\\":\\\"Severe Inflamed\\\",\\\"calcular_deposits\\\":\\\"Slight\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"child\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-13 11:00:40', '2026-03-13 11:00:40'),
(233, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 23, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":118,\"dmd\":\"ROSALES\",\"treatment\":\"EXTRACTION\",\"cost_of_treatment\":\"1500\",\"amount_charged\":\"2000\",\"remarks\":\"THIS PATIENT IS SO FUCKING STUPUD\",\"modified_by\":\"sample\",\"updated_at\":\"2026-03-13T11:00:40.720305Z\"}}', NULL, '2026-03-13 11:00:40', '2026-03-13 11:00:40'),
(234, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 115, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-13 11:07:12', '2026-03-13 11:07:12'),
(235, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 119, 'App\\Models\\User', 2, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-13 11:30:01', '2026-03-13 11:30:01'),
(236, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 40, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":124,\"chart_data\":\"{\\\"teeth\\\":{\\\"11\\\":null,\\\"12\\\":null,\\\"13\\\":null,\\\"14\\\":null,\\\"15\\\":null,\\\"16\\\":{\\\"top\\\":{\\\"color\\\":\\\"blue\\\",\\\"code\\\":\\\"LC\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"LC\\\",\\\"color\\\":\\\"blue\\\"}},\\\"17\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CI\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CI\\\",\\\"color\\\":\\\"red\\\"}},\\\"18\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"---\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"---\\\",\\\"color\\\":\\\"red\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Poor\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"Moderate\\\",\\\"stains\\\":\\\"None\\\",\\\"complete_denture\\\":\\\"None\\\",\\\"partial_denture\\\":\\\"None\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-13 23:00:59', '2026-03-13 23:00:59'),
(237, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 24, 'App\\Models\\User', 2, '{\"attributes\":{\"patient_id\":124,\"dmd\":\"ROSALES\",\"treatment\":\"MABUHAY\",\"cost_of_treatment\":\"5000\",\"amount_charged\":\"6000\",\"remarks\":\"HEHEHE\",\"modified_by\":\"sample\",\"updated_at\":\"2026-03-13T23:00:59.103884Z\"}}', NULL, '2026-03-13 23:00:59', '2026-03-13 23:00:59'),
(238, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 124, 'App\\Models\\User', 2, '{\"old\":{\"id\":124,\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"mobile_number\":\"12345678\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":null,\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"cxhya47030@minitts.net\",\"created_at\":\"2026-03-13 19:25:15\",\"updated_at\":\"2026-03-13 19:25:15\"},\"attributes\":{\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLE\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"12345678\",\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 23:12:03', '2026-03-13 23:12:03'),
(239, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 124, 'App\\Models\\User', 2, '{\"old\":{\"id\":124,\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"mobile_number\":\"12345678\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLE\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 19:25:15\",\"updated_at\":\"2026-03-14 07:12:03\"},\"attributes\":{\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLEAAAAAAA\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"12345678\",\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 23:15:03', '2026-03-13 23:15:03'),
(240, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 124, 'App\\Models\\User', 2, '{\"old\":{\"id\":124,\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"mobile_number\":\"12345678\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLEAAAAAAA\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 19:25:15\",\"updated_at\":\"2026-03-14 07:15:03\"},\"attributes\":{\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLEAAAAAAADSADASDSA\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"12345678\",\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 23:17:59', '2026-03-13 23:17:59'),
(241, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 124, 'App\\Models\\User', 2, '{\"old\":{\"id\":124,\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"mobile_number\":\"12345678\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLEAAAAAAADSADASDSA\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 19:25:15\",\"updated_at\":\"2026-03-14 07:17:59\"},\"attributes\":{\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLEAAAAAAADSADASDSAdsadsa\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"12345678\",\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 23:22:58', '2026-03-13 23:22:58'),
(242, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 124, 'App\\Models\\User', 2, '{\"old\":{\"id\":124,\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"mobile_number\":\"12345678\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLEAAAAAAADSADASDSAdsadsa\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 19:25:15\",\"updated_at\":\"2026-03-14 07:22:58\"},\"attributes\":{\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLES\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"mobile_number\":\"12345678\",\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 23:26:13', '2026-03-13 23:26:13'),
(243, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 124, 'App\\Models\\User', 2, '{\"old\":{\"id\":124,\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"mobile_number\":\"12345678\",\"middle_name\":\"AS\",\"nickname\":null,\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLES\",\"home_address\":\"SDADSA\",\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cxhya47030@minitts.net\",\"referral\":null,\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\",\"created_at\":\"2026-03-13 19:25:15\",\"updated_at\":\"2026-03-14 07:26:13\"},\"attributes\":{\"last_name\":\"Monkey\",\"first_name\":\"Luffy\",\"middle_name\":\"AS\",\"nickname\":\"\",\"occupation\":\"WALA\",\"birth_date\":\"2003-11-21\",\"gender\":\"Female\",\"civil_status\":\"SINGLESafds\",\"home_address\":\"SDADSA\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"12345678\",\"email_address\":\"cxhya47030@minitts.net\",\"referral\":\"\",\"emergency_contact_name\":\"SADDASDSA\",\"emergency_contact_number\":\"231231\",\"relationship\":\"ASDSA\",\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"sample\"}}', NULL, '2026-03-13 23:28:13', '2026-03-13 23:28:13');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('Pending','Scheduled','Ongoing','Completed','Cancelled','Waiting','Arrived') NOT NULL DEFAULT 'Pending',
  `service_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `modified_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `booking_type` varchar(30) NOT NULL DEFAULT 'online_appointment'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_date`, `status`, `service_id`, `patient_id`, `dentist_id`, `modified_by`, `created_at`, `updated_at`, `booking_type`) VALUES
(3, '2025-11-10 11:30:00', 'Scheduled', 2, 6, NULL, 'SYSTEM', '2025-11-16 15:10:32', '2025-11-16 15:10:32', 'online_appointment'),
(4, '2025-11-10 13:30:00', 'Scheduled', 1, 7, NULL, 'SYSTEM', '2025-11-16 15:10:50', '2025-11-16 15:10:50', 'online_appointment'),
(5, '2025-11-10 14:30:00', 'Scheduled', 1, 10, NULL, 'SYSTEM', '2025-11-16 15:13:48', '2025-11-16 15:13:48', 'online_appointment'),
(6, '2025-11-10 15:30:00', 'Cancelled', 1, 11, NULL, 'SYSTEM', '2025-11-16 15:13:57', '2025-11-18 00:44:03', 'online_appointment'),
(7, '2025-11-11 09:30:00', 'Scheduled', 1, 17, NULL, 'SYSTEM', '2025-11-16 15:14:49', '2025-11-16 15:14:49', 'online_appointment'),
(8, '2025-11-11 10:30:00', 'Scheduled', 1, 20, NULL, 'SYSTEM', '2025-11-16 15:22:10', '2025-11-16 15:22:10', 'online_appointment'),
(9, '2025-11-12 09:30:00', 'Ongoing', 2, 11, NULL, 'SYSTEM', '2025-11-16 15:22:18', '2025-11-18 12:02:47', 'online_appointment'),
(10, '2025-11-10 10:00:00', 'Cancelled', 1, 21, NULL, 'SYSTEM', '2025-11-16 15:25:43', '2025-11-17 01:15:39', 'online_appointment'),
(11, '2025-11-11 12:30:00', 'Scheduled', 3, 11, NULL, 'SYSTEM', '2025-11-16 15:32:06', '2025-11-16 15:32:06', 'online_appointment'),
(12, '2025-11-10 12:30:00', 'Scheduled', 1, 22, NULL, 'SYSTEM', '2025-11-16 17:31:44', '2025-11-16 17:31:44', 'online_appointment'),
(14, '2025-11-17 09:00:00', 'Completed', 3, 7, NULL, 'SYSTEM', '2025-11-17 00:23:27', '2025-11-17 01:13:34', 'online_appointment'),
(15, '2025-11-17 10:30:00', 'Cancelled', 2, 24, NULL, 'SYSTEM', '2025-11-17 01:30:45', '2025-11-17 01:50:51', 'online_appointment'),
(16, '2025-11-18 09:00:00', 'Ongoing', 3, 25, NULL, 'SYSTEM', '2025-11-17 03:53:26', '2025-11-17 03:53:40', 'online_appointment'),
(17, '2025-11-17 12:00:00', 'Ongoing', 3, 26, NULL, 'SYSTEM', '2025-11-17 14:40:33', '2025-11-17 14:43:35', 'online_appointment'),
(18, '2025-11-18 11:00:00', 'Ongoing', 2, 28, NULL, 'SYSTEM', '2025-11-18 22:41:05', '2025-11-19 00:27:42', 'online_appointment'),
(19, '2025-12-08 09:00:00', 'Cancelled', 1, 42, NULL, 'SYSTEM', '2025-12-08 14:53:45', '2025-12-10 02:31:47', 'online_appointment'),
(20, '2025-12-10 09:00:00', 'Completed', 2, 43, NULL, 'SYSTEM', '2025-12-10 02:35:58', '2025-12-10 04:55:15', 'online_appointment'),
(21, '2025-12-09 09:00:00', 'Scheduled', 2, 62, NULL, 'SYSTEM', '2025-12-13 08:08:42', '2025-12-13 08:08:42', 'online_appointment'),
(22, '2025-12-13 18:30:00', 'Scheduled', 1, 63, NULL, 'SYSTEM', '2025-12-13 10:26:10', '2025-12-13 10:26:10', 'online_appointment'),
(24, '2025-12-13 22:00:00', 'Scheduled', 2, 65, NULL, 'SYSTEM', '2025-12-13 14:17:28', '2025-12-13 14:17:28', 'online_appointment'),
(25, '2025-12-13 21:00:00', 'Scheduled', 2, 66, NULL, 'SYSTEM', '2025-12-13 14:18:00', '2025-12-13 14:18:00', 'online_appointment'),
(26, '2025-12-11 18:30:00', 'Scheduled', 2, 67, NULL, 'SYSTEM', '2025-12-13 14:18:15', '2025-12-13 14:18:15', 'online_appointment'),
(27, '2025-12-10 13:00:00', 'Scheduled', 2, 17, NULL, 'SYSTEM', '2025-12-13 14:45:16', '2025-12-13 14:45:16', 'online_appointment'),
(28, '2025-12-14 09:00:00', 'Scheduled', 1, 67, NULL, 'SYSTEM', '2025-12-13 14:50:12', '2025-12-13 14:50:12', 'online_appointment'),
(29, '2025-12-16 09:00:00', 'Completed', 1, 67, NULL, 'SYSTEM', '2025-12-16 13:29:02', '2025-12-16 13:40:39', 'online_appointment'),
(30, '2025-12-16 11:00:00', 'Cancelled', 3, 68, NULL, 'SYSTEM', '2025-12-16 13:38:19', '2025-12-16 13:44:45', 'online_appointment'),
(31, '2025-12-15 11:30:00', 'Completed', 2, 69, NULL, 'SYSTEM', '2025-12-16 13:45:38', '2025-12-16 17:14:33', 'online_appointment'),
(32, '2025-12-16 13:00:00', 'Ongoing', 1, 64, NULL, 'SYSTEM', '2025-12-16 13:49:31', '2025-12-16 13:49:43', 'online_appointment'),
(33, '2025-12-15 09:00:00', 'Ongoing', 2, 70, NULL, 'SYSTEM', '2025-12-16 14:12:10', '2025-12-16 14:27:13', 'online_appointment'),
(35, '2025-12-18 09:00:00', 'Scheduled', 1, 72, NULL, 'SYSTEM', '2025-12-16 14:19:47', '2025-12-16 14:19:47', 'online_appointment'),
(36, '2025-12-19 09:00:00', 'Scheduled', 3, 73, NULL, 'SYSTEM', '2025-12-16 14:20:02', '2025-12-16 14:20:02', 'online_appointment'),
(37, '2025-12-18 11:00:00', 'Ongoing', 2, 74, NULL, 'SYSTEM', '2025-12-16 14:20:14', '2025-12-16 14:21:18', 'online_appointment'),
(38, '2025-12-19 11:00:00', 'Scheduled', 2, 75, NULL, 'SYSTEM', '2025-12-16 14:20:25', '2025-12-16 14:20:25', 'online_appointment'),
(39, '2025-12-19 12:00:00', 'Scheduled', 3, 76, NULL, 'SYSTEM', '2025-12-18 13:52:26', '2025-12-18 13:52:26', 'online_appointment'),
(54, '2026-01-20 09:00:00', 'Completed', 3, 67, NULL, 'sample', '2026-01-19 18:59:13', '2026-01-19 18:59:25', 'online_appointment'),
(55, '2026-01-20 10:30:00', 'Cancelled', 2, 86, NULL, 'sample', '2026-01-19 19:09:58', '2026-01-19 19:25:50', 'online_appointment'),
(57, '2026-01-20 11:30:00', 'Ongoing', 3, 88, NULL, 'sample', '2026-01-19 19:33:59', '2026-01-19 19:37:52', 'online_appointment'),
(58, '2026-01-21 00:40:01', 'Waiting', 1, 100, NULL, 'sample', '2026-01-20 16:40:01', '2026-01-20 16:40:01', 'online_appointment'),
(59, '2026-01-21 09:00:00', 'Ongoing', 2, 101, NULL, 'sample', '2026-01-20 17:48:03', '2026-01-20 17:49:33', 'online_appointment'),
(60, '2026-01-21 10:00:00', 'Scheduled', 3, 66, NULL, 'sample', '2026-01-20 18:43:22', '2026-01-20 18:43:22', 'online_appointment'),
(61, '2026-01-21 11:30:00', 'Scheduled', 3, 72, NULL, 'sample', '2026-01-20 18:44:09', '2026-01-20 18:44:09', 'online_appointment'),
(62, '2026-01-21 13:00:00', 'Completed', 3, 102, NULL, 'sample', '2026-01-20 18:44:32', '2026-01-21 14:11:59', 'online_appointment'),
(63, '2026-01-21 14:30:00', 'Scheduled', 3, 103, NULL, 'sample', '2026-01-21 13:55:36', '2026-01-21 13:55:36', 'online_appointment'),
(64, '2026-01-21 16:00:00', 'Arrived', 3, 104, NULL, 'sample', '2026-01-21 14:00:00', '2026-01-21 14:02:21', 'online_appointment'),
(67, '2026-01-22 10:30:00', 'Completed', 2, 107, 2, 'staff', '2026-01-22 01:18:34', '2026-01-22 03:11:32', 'online_appointment'),
(68, '2026-01-22 11:30:00', 'Ongoing', 1, 108, NULL, 'staff', '2026-01-22 01:19:30', '2026-01-22 02:00:34', 'online_appointment'),
(69, '2026-01-22 13:30:00', 'Completed', 1, 109, NULL, 'sample1', '2026-01-22 01:42:26', '2026-01-22 04:32:16', 'online_appointment'),
(70, '2026-01-22 14:30:00', 'Cancelled', 3, 110, NULL, 'sample', '2026-01-22 04:22:07', '2026-01-22 04:22:38', 'online_appointment'),
(71, '2026-01-22 16:00:00', 'Completed', 1, 110, 12, 'sample', '2026-01-22 04:33:32', '2026-01-22 08:05:35', 'online_appointment'),
(72, '2026-01-22 17:00:00', 'Cancelled', 3, 110, NULL, 'sample', '2026-01-22 04:34:34', '2026-01-22 05:22:01', 'online_appointment'),
(73, '2026-01-22 12:56:43', 'Cancelled', 1, 111, NULL, 'sample', '2026-01-22 04:56:43', '2026-01-22 07:08:56', 'online_appointment'),
(76, '2026-01-22 17:30:00', 'Arrived', 3, 110, NULL, 'sample', '2026-01-22 14:23:21', '2026-01-22 14:53:24', 'online_appointment'),
(77, '2026-01-23 09:00:00', 'Arrived', 3, 114, NULL, 'sample', '2026-01-22 16:32:07', '2026-01-22 18:12:58', 'online_appointment'),
(78, '2026-01-23 11:00:00', 'Arrived', 2, 25, NULL, 'sample', '2026-01-22 16:49:36', '2026-01-22 17:42:33', 'online_appointment'),
(79, '2026-01-23 12:00:00', 'Cancelled', 3, 110, NULL, 'sample', '2026-01-22 16:51:33', '2026-01-22 16:55:05', 'online_appointment'),
(80, '2026-01-23 12:30:00', 'Cancelled', 2, 70, NULL, 'sample', '2026-01-22 16:56:17', '2026-01-22 17:02:48', 'online_appointment'),
(83, '2026-02-20 11:00:00', 'Cancelled', 2, 117, NULL, 'GUEST', '2026-02-07 17:14:19', '2026-02-14 11:55:56', 'online_appointment'),
(84, '2026-02-10 11:00:00', 'Cancelled', 3, 117, NULL, 'GUEST', '2026-02-07 17:15:08', '2026-02-14 11:55:58', 'online_appointment'),
(87, '2026-02-10 10:00:00', 'Scheduled', 1, 1, NULL, 'SYSTEM', '2026-02-08 10:07:21', '2026-02-08 10:07:21', 'online_appointment'),
(88, '2026-02-10 10:00:00', 'Scheduled', 1, 3, NULL, 'SYSTEM', '2026-02-08 10:07:21', '2026-02-08 10:07:21', 'online_appointment'),
(89, '2026-02-15 09:00:00', 'Ongoing', 2, 25, 2, 'sample', '2026-02-15 15:33:46', '2026-02-15 15:42:23', 'online_appointment'),
(90, '2026-02-18 10:30:00', 'Cancelled', 1, 110, 2, 'sample', '2026-02-17 16:05:16', '2026-02-25 10:39:58', 'online_appointment'),
(91, '2026-02-19 18:30:00', 'Scheduled', 2, 101, NULL, 'sample', '2026-02-19 09:21:57', '2026-02-19 09:21:57', 'online_appointment'),
(92, '2026-02-19 15:00:00', 'Scheduled', 2, 66, NULL, 'sample', '2026-02-19 11:17:59', '2026-02-19 11:17:59', 'online_appointment'),
(93, '2026-02-22 16:30:00', 'Scheduled', 1, 25, NULL, 'sample', '2026-02-22 11:43:37', '2026-02-22 11:43:37', 'online_appointment'),
(94, '2026-02-21 12:30:00', 'Scheduled', 1, 118, NULL, 'GUEST', '2026-02-22 11:59:55', '2026-02-22 15:23:01', 'online_appointment'),
(99, '2026-02-23 09:00:00', 'Completed', 1, 110, 2, 'sample', '2026-02-23 01:05:09', '2026-02-23 01:52:23', 'online_appointment'),
(100, '2026-02-23 17:00:00', 'Cancelled', 1, 114, NULL, 'sample', '2026-02-23 02:25:07', '2026-02-25 10:39:51', 'online_appointment'),
(101, '2026-03-06 11:00:00', 'Scheduled', 1, 110, NULL, 'sample', '2026-03-06 09:30:11', '2026-03-06 09:30:11', 'online_appointment'),
(105, '2026-03-12 12:00:00', 'Scheduled', 3, 114, NULL, 'sample', '2026-03-12 12:53:46', '2026-03-12 12:53:46', 'online_appointment'),
(106, '2026-03-12 12:00:00', 'Scheduled', 2, 25, NULL, 'sample', '2026-03-13 04:04:01', '2026-03-13 04:04:01', 'online_appointment'),
(107, '2026-03-12 14:00:00', 'Scheduled', 2, 118, NULL, 'sample', '2026-03-13 04:05:53', '2026-03-13 04:05:53', 'online_appointment'),
(108, '2026-03-13 15:00:00', 'Completed', 1, 119, 2, 'cerineo159@gmail.com', '2026-03-13 05:34:48', '2026-03-13 05:42:12', 'online_appointment'),
(109, '2026-03-13 15:00:00', 'Completed', 3, 120, 2, 'cerineo123@gmail.com', '2026-03-13 05:39:51', '2026-03-13 05:41:51', 'online_appointment'),
(110, '2026-03-13 16:00:00', 'Completed', 2, 119, 2, 'cerineo159@gmail.com', '2026-03-13 06:42:19', '2026-03-13 07:36:16', 'online_appointment'),
(111, '2026-03-13 16:00:00', 'Completed', 3, 120, 2, 'cerineo123@gmail.com', '2026-03-13 06:42:50', '2026-03-13 07:44:04', 'online_appointment'),
(112, '2026-03-13 18:00:00', 'Cancelled', 2, 27, NULL, 'sample', '2026-03-13 06:49:41', '2026-03-13 07:44:19', 'online_appointment'),
(114, '2026-03-13 17:00:00', 'Scheduled', 3, 110, NULL, 'sample', '2026-03-13 07:01:53', '2026-03-13 07:01:53', 'online_appointment'),
(115, '2026-03-13 19:00:00', 'Cancelled', 1, 121, NULL, 'sample', '2026-03-13 07:42:56', '2026-03-13 11:07:12', 'online_appointment'),
(116, '2026-03-13 18:00:00', 'Scheduled', 1, 121, NULL, 'sample', '2026-03-13 07:46:09', '2026-03-13 07:46:09', 'online_appointment'),
(117, '2026-03-13 15:49:07', 'Cancelled', 1, 122, 2, 'sample', '2026-03-13 07:49:07', '2026-03-13 07:53:09', 'online_appointment'),
(118, '2026-03-13 19:00:00', 'Completed', 1, 123, 2, 'twgrd90335@minitts.net', '2026-03-13 08:01:41', '2026-03-13 08:14:06', 'online_appointment'),
(119, '2026-03-14 11:00:00', 'Scheduled', 1, 124, NULL, 'cxhya47030@minitts.net', '2026-03-13 11:25:15', '2026-03-13 11:30:01', 'online_appointment');

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
(4, '2026-03-13', '09:00:00', '10:00:00', NULL, 'MEETING', 'sample', '2026-03-13 02:40:52', '2026-03-13 05:45:11'),
(5, '2026-03-13', '20:00:00', '21:00:00', NULL, NULL, 'sample', '2026-03-13 06:51:35', '2026-03-13 06:51:35'),
(6, '2026-03-14', '09:00:00', '10:00:00', NULL, NULL, 'sample', '2026-03-13 21:01:42', '2026-03-13 21:01:42'),
(7, '2026-03-14', '13:00:00', '14:00:00', NULL, NULL, 'sample', '2026-03-13 21:02:50', '2026-03-13 21:02:50'),
(8, '2026-03-14', '14:00:00', '15:00:00', NULL, NULL, 'sample', '2026-03-13 21:03:03', '2026-03-13 21:03:03');

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
(6, 6, '{\"teeth\":{\"18\":{\"right\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"},\"top\":{\"color\":\"red\",\"code\":\"CI\"},\"bottom\":{\"color\":\"red\",\"code\":\"CI\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-08 13:37:29', '2025-12-08 13:37:29'),
(7, 42, '{\"teeth\":{\"18\":{\"right\":{\"color\":\"red\",\"code\":\"CC\"},\"line_1\":{\"code\":\"CC\",\"color\":\"red\"},\"top\":{\"color\":\"red\",\"code\":\"CC\"},\"left\":{\"color\":\"red\",\"code\":\"CC\"},\"center\":{\"color\":\"red\",\"code\":\"CC\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-08 16:33:00', '2025-12-08 16:33:00'),
(8, 42, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-09 12:49:27', '2025-12-09 12:49:54'),
(9, 42, '{\"teeth\":{\"11\":{\"center\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"},\"right\":{\"color\":\"red\",\"code\":\"CI\"},\"top\":{\"color\":\"red\",\"code\":\"CI\"}},\"18\":{\"center\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"},\"top\":{\"color\":\"red\",\"code\":\"CI\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-09 15:03:28', '2025-12-09 15:03:28'),
(10, 43, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-10 05:40:24', '2025-12-10 05:40:24'),
(11, 20, '{\"teeth\":{\"18\":{\"right\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"},\"top\":{\"color\":\"red\",\"code\":\"CI\"},\"left\":{\"color\":\"red\",\"code\":\"CI\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-10 06:48:15', '2025-12-10 06:48:15'),
(12, 43, '{\"teeth\":{\"18\":{\"center\":{\"color\":\"red\",\"code\":\"CC\"},\"line_1\":{\"code\":\"CC\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-11 23:38:44', '2025-12-11 23:38:44'),
(15, 65, '{\"teeth\":{\"18\":{\"center\":{\"color\":\"red\",\"code\":\"C\"},\"line_1\":{\"code\":\"C\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-16 15:21:11', '2025-12-16 15:21:11'),
(16, 65, '{\"teeth\":{\"18\":{\"center\":{\"color\":\"red\",\"code\":\"C\"},\"line_1\":{\"code\":\"C\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-16 15:21:29', '2025-12-16 15:21:29'),
(17, 65, '{\"teeth\":{\"18\":{\"center\":{\"color\":\"red\",\"code\":\"C\"},\"line_1\":{\"code\":\"C\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2025-12-16 15:22:05', '2025-12-16 15:22:05'),
(18, 78, '{\"teeth\":{\"18\":{\"center\":{\"color\":\"red\",\"code\":\"CC\"},\"line_1\":{\"code\":\"CC\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"\",\"gingiva\":\"\",\"calcular_deposits\":\"\",\"stains\":\"\",\"complete_denture\":\"\",\"partial_denture\":\"\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-01-17 20:22:55', '2026-01-17 20:22:55'),
(21, 110, '{\"teeth\":{\"32\":{\"bottom\":{\"color\":\"red\",\"code\":\"---\"},\"line_1\":{\"code\":\"---\",\"color\":\"red\"},\"left\":{\"color\":\"red\",\"code\":\"---\"},\"center\":{\"color\":\"red\",\"code\":\"---\"},\"right\":{\"color\":\"red\",\"code\":\"---\"},\"top\":{\"color\":\"red\",\"code\":\"---\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Bad\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Severe\",\"stains\":\"Moderate\",\"complete_denture\":\"Lower\",\"partial_denture\":\"Lower\"},\"comments\":{\"notes\":\"Help\",\"treatment_plan\":\"Tomorrow\"}}', 'Jerome_123', '2026-01-22 08:04:42', '2026-01-22 08:04:42'),
(22, 114, '{\"teeth\":{\"17\":{\"top\":{\"color\":\"red\",\"code\":\"DR\"},\"line_1\":{\"code\":\"DR\",\"color\":\"red\"},\"center\":{\"color\":\"red\",\"code\":\"DR\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Severe Inflamed\",\"calcular_deposits\":\"None\",\"stains\":\"Moderate\",\"complete_denture\":\"Lower\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-01-22 19:26:41', '2026-01-22 19:26:41'),
(23, 114, '{\"teeth\":{\"17\":{\"top\":{\"color\":\"red\",\"code\":\"DR\"},\"line_1\":{\"code\":\"DR\",\"color\":\"red\"},\"center\":{\"color\":\"red\",\"code\":\"DR\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Severe Inflamed\",\"calcular_deposits\":\"None\",\"stains\":\"Moderate\",\"complete_denture\":\"Lower\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-01-22 19:27:01', '2026-01-22 19:27:01'),
(25, 117, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"None\",\"stains\":\"Slight\",\"complete_denture\":\"Lower\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-14 07:54:52', '2026-02-14 07:54:52'),
(26, 117, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"Bad\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Moderate\",\"stains\":\"Moderate\",\"complete_denture\":\"Upper\",\"partial_denture\":\"Upper\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-14 08:02:50', '2026-02-14 08:02:50'),
(27, 117, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Moderate\",\"stains\":\"Slight\",\"complete_denture\":\"None\",\"partial_denture\":\"Upper\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-14 08:06:47', '2026-02-14 08:06:47'),
(29, 117, '{\"teeth\":[null,null,null,null,null,null,null,null,null,null,null,null,null,{\"left\":{\"color\":\"red\",\"code\":\"CD\"},\"line_1\":{\"code\":\"CD\",\"color\":\"red\"},\"right\":{\"color\":\"red\",\"code\":\"---\"},\"line_2\":{\"code\":\"---\",\"color\":\"red\"},\"center\":{\"color\":\"red\",\"code\":\"C\"},\"line_3\":{\"code\":\"C\",\"color\":\"red\"}},null,null,null,{\"center\":{\"color\":\"blue\",\"code\":\"LC\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"},\"right\":{\"color\":\"red\",\"code\":\"CI\"},\"line_2\":{\"code\":\"CI\",\"color\":\"red\"}}],\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Slight\",\"stains\":\"Severe\",\"complete_denture\":\"Upper\",\"partial_denture\":\"Upper\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-14 08:22:06', '2026-02-14 08:22:06'),
(30, 118, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"None\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"Upper\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-24 17:41:37', '2026-02-24 17:41:37'),
(31, 118, '{\"teeth\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\"center\":{\"color\":\"red\",\"code\":\"CP\"},\"line_1\":{\"code\":\"CP\",\"color\":\"red\"}}],\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"None\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-25 04:56:26', '2026-02-25 04:56:26'),
(32, 118, '{\"teeth\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\"right\":{\"color\":\"red\",\"code\":\"CD\"},\"line_1\":{\"code\":\"CD\",\"color\":\"red\"}}],\"oral_exam\":{\"oral_hygiene_status\":\"Excellent\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Moderate\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"Upper\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-25 06:12:10', '2026-02-25 06:12:10'),
(33, 118, '{\"teeth\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\"right\":{\"color\":\"red\",\"code\":\"C\"},\"line_1\":{\"code\":\"C\",\"color\":\"red\"}}],\"oral_exam\":{\"oral_hygiene_status\":\"Poor\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"None\",\"stains\":\"Slight\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(34, 118, '{\"teeth\":[null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{\"bottom\":{\"color\":\"red\",\"code\":\"CD\"},\"line_1\":{\"code\":\"CD\",\"color\":\"red\"}}],\"oral_exam\":{\"oral_hygiene_status\":\"Excellent\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Moderate\",\"stains\":\"Slight\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"}}', 'sample', '2026-02-25 06:32:37', '2026-02-25 06:32:37'),
(35, 119, '{\"teeth\":{\"11\":null,\"12\":{\"right\":{\"color\":\"red\",\"code\":\"CD\"},\"line_1\":{\"code\":\"CD\",\"color\":\"red\"}},\"13\":null,\"14\":null,\"15\":null,\"16\":{\"center\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"None\",\"stains\":\"Slight\",\"complete_denture\":\"Upper\",\"partial_denture\":\"Lower\"},\"comments\":{\"notes\":\"asd\",\"treatment_plan\":\"step 123\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'sample', '2026-03-13 06:46:37', '2026-03-13 06:46:37'),
(36, 120, '{\"teeth\":{\"11\":{\"left\":{\"color\":\"red\",\"code\":\"C\"},\"line_1\":{\"code\":\"C\",\"color\":\"red\"}},\"12\":null,\"13\":null,\"14\":null,\"15\":null,\"16\":null,\"17\":null,\"18\":null,\"21\":null,\"22\":null,\"23\":null,\"24\":null,\"25\":null,\"26\":null,\"27\":null,\"28\":null,\"31\":null,\"32\":{\"right\":{\"color\":\"red\",\"code\":\"C\"},\"line_1\":{\"code\":\"C\",\"color\":\"red\"}},\"33\":null,\"34\":null,\"35\":null,\"36\":null,\"37\":null,\"38\":{\"center\":{\"color\":\"red\",\"code\":\"X\"},\"line_1\":{\"code\":\"X\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Excellent\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Slight\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'sample', '2026-03-13 07:38:46', '2026-03-13 07:38:46'),
(37, 122, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"Excellent\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"None\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'sample', '2026-03-13 07:52:29', '2026-03-13 07:52:29'),
(38, 123, '{\"teeth\":[],\"oral_exam\":{\"oral_hygiene_status\":\"Excellent\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"None\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'sample', '2026-03-13 08:13:41', '2026-03-13 08:13:41'),
(39, 118, '{\"teeth\":{\"51\":{\"center\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Severe Inflamed\",\"calcular_deposits\":\"Slight\",\"stains\":\"Slight\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"child\",\"numbering_system\":\"FDI\"}}', 'sample', '2026-03-13 11:00:40', '2026-03-13 11:00:40'),
(40, 124, '{\"teeth\":{\"11\":null,\"12\":null,\"13\":null,\"14\":null,\"15\":null,\"16\":{\"top\":{\"color\":\"blue\",\"code\":\"LC\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}},\"17\":{\"center\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"}},\"18\":{\"center\":{\"color\":\"red\",\"code\":\"---\"},\"line_1\":{\"code\":\"---\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Poor\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Moderate\",\"stains\":\"None\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'sample', '2026-03-13 23:00:58', '2026-03-13 23:00:58');

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
(1, 27, NULL, '', 'Toothache', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'SYSTEM', '2025-11-18 08:07:06', '2025-11-18 08:07:06'),
(11, 100, NULL, '', 'OIIOIOIO', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-20 16:40:01', '2026-01-20 17:52:32'),
(13, 100, NULL, '', 'tyttytyytty', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-20 17:07:47', '2026-01-20 17:07:47'),
(14, 100, NULL, '', 'trtrtrt', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-20 17:08:29', '2026-01-20 17:08:29'),
(15, 100, NULL, '', 'TRETER', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-20 17:16:34', '2026-01-20 17:16:34'),
(16, 100, NULL, '', 'ewqweqqew1', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-20 17:17:00', '2026-01-20 18:19:45'),
(17, 100, NULL, '', 'qweqweqqew', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-20 17:24:40', '2026-01-20 18:38:20'),
(18, 105, NULL, 'N / A', 'MASAKIT NGIPIN KO', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-21 14:14:49', '2026-01-21 14:14:49'),
(19, 109, '2017-11-22', 'cleanuing', 'hatddog', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample1', '2026-01-22 01:44:05', '2026-01-22 01:44:05'),
(20, 109, '2017-11-22', 'cleanuing', 'hatddog', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample1', '2026-01-22 01:44:08', '2026-01-22 01:44:08'),
(21, 110, '2025-01-24', '', 'Swollen', 0, 0, 0, 0, 0, 0, 1, 'Concern Citizen', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'Jerome_123', '2026-01-22 04:47:50', '2026-01-22 08:03:23'),
(22, 111, '2026-01-05', '', 'Check up', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-01-22 04:56:43', '2026-01-22 04:56:43'),
(24, 122, NULL, '', 'Check Up', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-03-13 07:49:07', '2026-03-13 07:49:07'),
(25, 123, NULL, '', 'Cleaning', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'sample', '2026-03-13 08:12:05', '2026-03-13 08:12:05');

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

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `title`, `notes`, `updated_at`, `created_at`, `user_id`) VALUES
(1, 'MeetingWTH', 'Meeting with client\nWTH', '2025-11-19 07:29:35', '2025-11-10 20:52:58', 2),
(6, 'UWIAN TUWING 8 PM', 'clean toothbrush before uwian', '2026-01-18 03:34:35', '2026-01-18 03:34:35', 2),
(10, 'DASDSADAS', '', '2026-01-22 13:16:55', '2026-01-22 13:16:55', 12),
(11, 'Denture', 'No Lunch Break', '2026-01-22 15:54:12', '2026-01-22 15:54:12', 12),
(12, 'DAS', 'DS', '2026-03-09 01:04:26', '2026-03-09 01:04:26', 2);

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
(1, 'SAMPLE', 'SAMPLE', '09979775797', 'SAMPLE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 14:50:06', '2025-11-16 14:50:06'),
(3, 'S', 'S', '099797757', 'S', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 14:52:10', '2025-11-16 14:52:10'),
(6, 'AAAAAAAAA', 'A', '1', 'A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2025-11-16 15:10:32', '2025-12-08 13:22:23'),
(7, 'RE', 'RE', '32', 'RE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 15:10:50', '2025-11-17 00:23:27'),
(10, 'E', 'E', '2', 'E', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 15:13:48', '2025-11-16 15:13:48'),
(11, 'R', 'R', '23', 'R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 15:13:57', '2025-11-16 15:32:06'),
(17, 'werwer', 'rwerwe', '123', 'werwer', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 15:14:49', '2026-01-19 18:11:36'),
(20, 'a', 'a', '64', 'a', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 15:22:10', '2025-11-16 15:22:10'),
(21, 'u', 'u', '45', 'u', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 15:25:43', '2025-11-16 15:25:43'),
(22, 'N', 'N', '23411', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-16 17:31:44', '2025-11-16 17:31:44'),
(24, 'ROSALES', 'RENZ ', '67', 'SALUMBIDES', NULL, NULL, NULL, NULL, NULL, '2107 Rosal Street, Batasan Hills Quezon City', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-17 01:30:45', '2025-11-17 08:41:18'),
(25, 'ROSALES', 'CLARENZ LUIGI', '092259951316', 'SALUMBIDES', NULL, NULL, '2003-04-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-17 03:53:26', '2025-11-17 03:53:26'),
(26, 'LAARA', 'LAARA', '543333434', 'LAARA', NULL, NULL, '2025-11-17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-17 14:40:33', '2025-11-17 14:40:33'),
(27, 'ROSALES', 'LUIS', '0951316', '', '', 'ELECTRICIAN', '2003-04-29', 'Male', 'SINGLE', '2107 ROSAL STREET', '', '', '', '', '', 'LAARA ROSALES', '0997', 'DAUGHTER', '', '', '', '', '', '', '', '', 'SYSTEM', '2025-11-18 08:07:06', '2025-11-18 08:07:06'),
(28, 'THIS IS FIRST', 'THIS IS FIRST', '78437843', 'THIS IS FIRST', NULL, NULL, '2003-04-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-11-18 22:41:05', '2025-11-18 22:41:05'),
(42, 'TEST', 'TEST', '321803821', 'TEST', NULL, NULL, '2003-04-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-08 14:53:45', '2025-12-08 14:53:45'),
(43, 'TESTAGAIN', 'TESTAGAIN', '31212332', 'TESTAGAIN', NULL, NULL, '2025-12-10', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2025-12-10 02:35:58', '2025-12-11 18:03:26'),
(59, 'ASD', 'ASD', '312123345', 'ASD', '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'staff', '2025-12-13 05:08:51', '2025-12-13 05:08:51'),
(62, 'A', 'A', '12', '', NULL, NULL, '2025-12-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-13 08:08:42', '2025-12-13 08:08:42'),
(63, 'YES', 'YES', '1231231', 'YES', NULL, NULL, '2025-12-13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-13 10:26:10', '2025-12-13 10:26:10'),
(64, 'yt', 'yt', '231', 'yt', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-13 14:15:02', '2026-01-19 17:24:26'),
(65, 'terter', 'uyrty', '321', ' uiy', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-13 14:17:28', '2026-01-19 18:09:06'),
(66, 'QWEQWE', 'ASDASDASD', '231312', 'QWEQWE', NULL, NULL, '2026-01-21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-13 14:18:00', '2026-01-20 18:43:22'),
(67, 'ytre', 'try', '321321', 'ytr', NULL, NULL, '2026-01-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-13 14:18:15', '2026-01-19 18:59:13'),
(68, '1', '421', 'dsa', '32132', NULL, NULL, '2025-12-16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 13:38:19', '2025-12-16 13:38:19'),
(69, 'das', 'dsa', '234312', 'dsa', NULL, NULL, '2025-12-17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 13:45:38', '2025-12-16 13:45:38'),
(70, 'Nicolas', 'Stephen', '26123231', 'D', NULL, NULL, '2025-12-16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 14:12:10', '2025-12-16 14:12:10'),
(72, 'ZZZZ', 'ZZZZZZZ', '123123', 'TZZZZZZZZZZZ', NULL, NULL, '2026-01-21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 14:19:47', '2026-01-20 18:44:09'),
(73, 'dsa', 'Nicolas', 'asd', 'dsa', NULL, NULL, '0031-12-31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 14:20:02', '2025-12-16 14:20:02'),
(74, 'dsa', 'Nicolas', '123312', 'dsadas', NULL, NULL, '0003-12-31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 14:20:13', '2025-12-16 14:20:13'),
(75, 'asd', 'Nicolas', '123213', 'dfssdf', NULL, NULL, '2025-12-17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-16 14:20:25', '2025-12-16 14:20:25'),
(76, 'Rosales', 'Clarenz', '9812321', 'Luigi', NULL, NULL, '2004-12-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2025-12-18 13:52:26', '2025-12-18 13:52:26'),
(77, 'Rosales', 'Rosales', '312132132', 'Rosales', NULL, NULL, '2026-01-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SYSTEM', '2026-01-17 18:33:57', '2026-01-17 18:33:57'),
(78, 'RErerere', 'W', '231312', 'W', '', 'W', '2026-01-18', 'Male', 'W', 'W', '', '', '', '', '', 'W', '123', 'W', 'W', 'W', '', '', '', '', '', '', 'sample', '2026-01-17 19:49:22', '2026-01-17 19:56:51'),
(79, 'ew', 'ew', '121', 'ew', NULL, NULL, '2026-01-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-18 08:53:11', '2026-01-18 08:53:11'),
(84, 'tyrertert', 'ewtwer', '31223132', 'werwerwer', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-19 18:19:51', '2026-01-19 18:19:51'),
(85, 'uyiuy', 'ytuiyi', '234423', 'yui', NULL, NULL, '2026-01-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-19 18:46:01', '2026-01-19 18:46:01'),
(86, 'dfgdfg', 'rgfwr', '321312', 'fgddfgdfg', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-19 19:09:58', '2026-01-19 19:09:58'),
(88, 'wqeeqw', 'erwqw', '213', 'qwe', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-19 19:33:59', '2026-01-19 19:33:59'),
(100, 'poopop1QWE', 'opopop', '', 'opop', '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'sample', '2026-01-20 16:40:01', '2026-01-20 18:42:00'),
(101, 'JOELQWE', 'JOEL', '312231', 'JOEL', NULL, NULL, '2026-01-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'QWE', 'QWE', NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-20 17:48:03', '2026-01-20 18:42:17'),
(102, 'QQQQQQQQQQQ', 'Q', 'QQWEQWE', 'QQ', NULL, NULL, '2026-01-21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-20 18:44:32', '2026-01-20 18:44:32'),
(103, 'FDGDFGDFGFD', 'GFHFGHHGF', '53453435354354', 'HFGFHGFGH', NULL, NULL, '2026-01-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-21 13:55:36', '2026-01-21 13:55:36'),
(104, 'GALOR', 'JHONRICK', '342342434', 'BILOG', NULL, NULL, '2000-11-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-21 14:00:00', '2026-01-21 14:00:00'),
(105, 'WALKIN', 'WALK', '', 'IN', '', '', NULL, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'sample', '2026-01-21 14:14:49', '2026-01-21 14:14:49'),
(107, 'legaspoijkln', 'davestaff', '12354324234', 'ad', NULL, NULL, '2026-01-17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'staff', '2026-01-22 01:18:34', '2026-01-22 01:18:34'),
(108, 'k;aklwel', 'staff', '12351123123', 'dave', NULL, NULL, '2020-06-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'staff', '2026-01-22 01:19:30', '2026-01-22 01:19:30'),
(109, 'Morada', 'Christian Ace', '09494642734', 'Parungao', 'kura', 'student', '2004-03-25', NULL, 'widowed', 'fairview', 'makati', 'asdasd', 'dadsa', 'romebernacer123@gmail.com', '123321', 'ASD', '213', 'asd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample1', '2026-01-22 01:42:26', '2026-01-22 01:43:19'),
(110, 'Legaspina', 'MDave', '0912345678', 'Dela Vega', NULL, 'Tech Support', '2003-07-08', 'Male', 'Married', 'San Bartolome', 'Datamex', '123456789', '123456789', 'romick@GMAIL.COM', 'ACE', 'SUSAN', '019231313', 'Mother', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jerome_123', '2026-01-22 04:22:07', '2026-01-22 14:23:21'),
(111, 'Legaspina', 'MDave', '0912345678', 'Dela Vega', 'MCDave', 'UTI', '2003-07-08', 'Male', 'Married', 'Winston', 'Datamex', '0912345678', '0912345678', 'dave@GMAIL.COM', 'ACE', 'NANAY', '926099304', 'Mother', '', '', '', '', '', '', '', '', 'sample', '2026-01-22 04:56:43', '2026-01-22 04:56:43'),
(114, 'Rosales', 'Dale', '0997727222', 'Salumbides', 'DAASDDAS', 'Construction Worker', '2000-01-22', 'Male', 'Single', '2107 Rosal St Batasan Hills Quezon City', NULL, NULL, NULL, NULL, NULL, 'Luis Rosales', '77237832782378', 'Father', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-01-22 16:32:07', '2026-01-22 19:25:26'),
(117, 'dasasdsda', 'sdaasd', '21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'dasasd@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-02-07 17:14:19', '2026-02-07 17:15:08'),
(118, 'nm', 'mn', '3456345345', NULL, NULL, NULL, '2003-01-22', NULL, NULL, NULL, NULL, NULL, NULL, 'jskdfjkdfs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-02-22 11:59:55', '2026-03-13 04:05:53'),
(119, 'Cerineoo', 'Deeyowil', '09123456789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cerineo159@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cerineo159@gmail.com', '2026-03-13 05:34:48', '2026-03-13 06:42:19'),
(120, 'El', 'Jo', '123456789', 'Qu', NULL, 'Dancer', '2000-11-11', 'Male', 'Single', 'Bahay', NULL, NULL, NULL, 'cerineo123@gmail.com', NULL, 'Mama', '1231313', 'mama', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-03-13 05:39:51', '2026-03-13 07:34:15'),
(121, 'Kawa', 'Ichi', '122333', '', NULL, NULL, '2003-01-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-03-13 07:42:56', '2026-03-13 07:42:56'),
(122, 'Hinomori', 'Shisho', '123123132', 'Mori', '', 'Actress', '2002-02-02', 'Female', 'Single', 'School', '', '', '', 'Shisho@gmail.com', '', 'Emu', '12312323', 'BFF', '', '', '', '', '', '', '', '', 'sample', '2026-03-13 07:49:07', '2026-03-13 07:51:01'),
(123, 'Rui', 'Rui', '1801233123', 'Rui', NULL, 'Inventor', '2003-03-31', 'Male', 'Single', 'WonderLand', NULL, NULL, NULL, 'twgrd90335@minitts.net', NULL, 'Emu', '123123123', 'Friend', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-03-13 08:01:41', '2026-03-13 08:07:01'),
(124, 'Monkey', 'Luffy', '12345678', 'AS', '', 'WALA', '2003-11-21', 'Female', 'SINGLESafds', 'SDADSA', '', '', '', 'cxhya47030@minitts.net', '', 'SADDASDSA', '231231', 'ASDSA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sample', '2026-03-13 11:25:15', '2026-03-13 23:28:13');

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

--
-- Dumping data for table `treatment_records`
--

INSERT INTO `treatment_records` (`id`, `patient_id`, `dental_chart_id`, `dmd`, `treatment`, `cost_of_treatment`, `amount_charged`, `remarks`, `image`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 43, 12, 'TEST', 'TEST', 1.00, 1.00, 'TEST', NULL, 'sample', '2025-12-11 23:38:44', '2025-12-11 23:38:44'),
(3, 65, 17, 'DSADSA', 'DSADAS', 21.00, 12.00, 'DSA', NULL, 'sample', '2025-12-16 15:22:05', '2025-12-16 15:22:05'),
(4, 78, 18, 'Dr. Rosales', 'Extraction', 500.00, 500.00, 'I mess up', NULL, 'sample', '2026-01-17 20:22:55', '2026-01-17 20:22:55'),
(7, 110, 21, 'Jerome', 'Extraction', 12455.00, 251.00, 'Mefenamic Acid', NULL, 'Jerome_123', '2026-01-22 08:04:42', '2026-01-22 08:04:42'),
(9, 117, 25, 'Me', 'Plan', 123.00, 1231.00, 'Nothing', NULL, 'sample', '2026-02-14 07:54:52', '2026-02-14 07:54:52'),
(10, 117, 26, 'abcd', 'Mbaba', 123345.00, 1232345.00, 'Adsa', NULL, 'sample', '2026-02-14 08:02:50', '2026-02-14 08:02:50'),
(11, 117, 27, 'DSFSDF', 'FGDFDGDFG', 213.00, 345.00, 'SDF', NULL, 'sample', '2026-02-14 08:06:47', '2026-02-14 08:06:47'),
(13, 117, 29, 'SDF', '123', 345345.00, 243234.00, 'GDFDFG', NULL, 'sample', '2026-02-14 08:22:06', '2026-02-14 08:22:06'),
(14, 118, 30, 'eqwqew', 'eqwqwe', 231.00, 312.00, 'sdasd', NULL, 'sample', '2026-02-24 17:41:37', '2026-02-24 17:41:37'),
(15, 118, 31, 'Dr. Rosales', 'Extraction', 500.00, 500.00, '', NULL, 'sample', '2026-02-25 04:56:27', '2026-02-25 04:56:27'),
(16, 118, 32, 'Dr me', 'Longganog', 123.00, 345.00, 'asd', NULL, 'sample', '2026-02-25 06:12:10', '2026-02-25 06:12:10'),
(17, 118, 33, 'SDA', 'QWE', 123.00, 342.00, 'DAS', NULL, 'sample', '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(18, 118, 34, 'DAS', 'ASD', 231.00, 231.00, 'ASD', NULL, 'sample', '2026-02-25 06:32:37', '2026-02-25 06:32:37'),
(19, 119, 35, 'Dr. Hiluluk', 'Extraction', 1200.00, 1000.00, 'celan', NULL, 'sample', '2026-03-13 06:46:37', '2026-03-13 06:46:37'),
(20, 120, 36, 'Dr. Dave', 'Cleaning', 1000.00, 1000.00, 'GUDS', NULL, 'sample', '2026-03-13 07:38:46', '2026-03-13 07:38:46'),
(21, 122, 37, 'Dr. Dave', 'Cleaning', 1000.00, 1000.00, '', NULL, 'sample', '2026-03-13 07:52:29', '2026-03-13 07:52:29'),
(22, 123, 38, 'Dr. Dave', 'Cleaning', 2000.00, 2000.00, 'Excellent!', NULL, 'sample', '2026-03-13 08:13:41', '2026-03-13 08:13:41'),
(23, 118, 39, 'ROSALES', 'EXTRACTION', 1500.00, 2000.00, 'THIS PATIENT IS SO FUCKING STUPUD', NULL, 'sample', '2026-03-13 11:00:40', '2026-03-13 11:00:40'),
(24, 124, 40, 'ROSALES', 'MABUHAY', 5000.00, 6000.00, 'HEHEHE', NULL, 'sample', '2026-03-13 23:00:59', '2026-03-13 23:00:59');

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

--
-- Dumping data for table `treatment_record_images`
--

INSERT INTO `treatment_record_images` (`id`, `treatment_record_id`, `image_path`, `image_type`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 16, 'treatment-records/2026/02/3V3tqSedBAagUxrdrBQfOkHwtDMwKNNi1uVfoTGx.png', 'other', 0, '2026-02-25 06:12:10', '2026-02-25 06:12:10'),
(2, 17, 'treatment-records/2026/02/sj9AMW7x0WWOEyCxhNTYBsGg6zkzAhYbOisg99EN.png', 'other', 0, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(3, 17, 'treatment-records/2026/02/NWs12ppgw9841Rag26LZrq3T63QPC39TbWGDdaLb.png', 'other', 1, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(4, 17, 'treatment-records/2026/02/ieBNljlQeXVeQtF7ldllRVOSUy3hx39DS5L3lY1I.png', 'other', 2, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(5, 17, 'treatment-records/2026/02/BKu3JvzVrVwVNvNO1dUqBN5XQW95dUP33jkGE4cQ.png', 'other', 3, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(6, 17, 'treatment-records/2026/02/UGZiM8faEpAfqnEgYoqSx8iLDWiIMCzCzSzPGUf1.png', 'other', 4, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(7, 17, 'treatment-records/2026/02/3Ajg3ZPnBDy291oUgQhctJGFehfyJKEe9gfnXP7p.png', 'other', 5, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(8, 17, 'treatment-records/2026/02/POw0zhtc3QoZdiybdBbUwsbK0L7sx5tP8trcgUVv.png', 'other', 6, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(9, 17, 'treatment-records/2026/02/GtLiWcQmgXwAwdmmXmAWu7OVKHLLbSQRJQsDjcoc.png', 'other', 7, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(10, 17, 'treatment-records/2026/02/tLHngYYcHcPeX4ZwaVSh6HPuvgoyc5vjFeGjyDqh.png', 'other', 8, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(11, 17, 'treatment-records/2026/02/9NCZQmOwK9TcGmFbKCXvJ3T7dI54AiWJ1SRDGh7B.png', 'other', 9, '2026-02-25 06:17:55', '2026-02-25 06:17:55'),
(12, 18, 'treatment-records/before/2026/02/5zI0h8uVimCTd7Dk3gxuZEGrzspiURlnY3aFsE84.png', 'before', 0, '2026-02-25 06:32:37', '2026-02-25 06:32:37'),
(13, 18, 'treatment-records/after/2026/02/WATjkGznXLJ4DcqEN5oq4MYzOxWjTDmOEs0E1meV.png', 'after', 1, '2026-02-25 06:32:37', '2026-02-25 06:32:37');

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
(2, 'sample', 'renzzluigi@gmail.com', '2026-03-08 06:20:36', NULL, '$2y$12$mhpZDgiHO.QafpgDME7tBOyiBFqPL72sn759rVTjVPOgBR2smzOBe', '114082662441983874861', 1, '2025-10-18 22:35:29', '2026-03-14 10:12:13'),
(3, 'staff', NULL, NULL, NULL, '$2y$12$H2wUJzlDXoKZCt7N1wpod.riMDp00RGiUqBgN1bI.XfxH1vIrqUCm', NULL, 2, '2025-12-12 08:02:31', '2025-12-12 08:02:31'),
(6, 'hoax', NULL, NULL, NULL, '$2y$12$MU7/gCnRBUPMK18UqF5O9.J7lf6pfFzQ.qplLzSsEZKJ/epKWRoWa', NULL, 1, '2026-01-18 00:12:04', '2026-01-18 04:08:21'),
(7, 'sample1', NULL, NULL, NULL, '$2y$12$1KtwWXN4P14biU2QNqlLGeOgr45rjUm0Aq3eM0pOeH7CXKQelbLFS', NULL, 2, '2026-01-18 00:39:44', '2026-01-18 00:39:44'),
(8, 'sample2', NULL, NULL, NULL, '$2y$12$qlglSHNydptPz.bHXqs6QeOSPiCZfIijywv97JoArgDmFXSAsDuBq', NULL, 2, '2026-01-18 00:41:28', '2026-01-18 00:41:28'),
(9, 'sample5', NULL, NULL, NULL, '$2y$12$hn.4sy62p37W8tD2P2TSQuREHoBzVuyJFnT6pjIXipSe69N4RUExi', NULL, 2, '2026-01-18 01:07:14', '2026-01-18 01:07:14'),
(12, 'Jerome_123', NULL, NULL, NULL, '$2y$12$qv./2RMGEKJnDRt0awF8W.t5gXtJlyupVZWR6yOpC9FF3lD49nO/m', NULL, 1, '2026-01-22 13:08:04', '2026-01-22 13:08:04'),
(37, 'cerineo159@gmail.com', 'cerineo159@gmail.com', '2026-03-13 05:33:59', NULL, '$2y$12$ABRPmzey5MLMnilBwMR1e.Hlbpu.rJHK6NAsPLnPV1cXVwiJgLzsy', '104819844258462214303', 3, '2026-03-13 13:33:59', '2026-03-13 13:33:59'),
(38, 'cerineo123@gmail.com', 'cerineo123@gmail.com', '2026-03-13 05:39:25', NULL, '$2y$12$2nHnhtFVok4/A6.K5grLA.MWrLxvK4jMvQR4haJLBLwnsPEyDubaO', '113587128745289601695', 3, '2026-03-13 13:39:25', '2026-03-13 13:39:25'),
(42, 'cxhya47030@minitts.net', 'cxhya47030@minitts.net', '2026-03-13 11:22:40', NULL, '$2y$12$T3NJCoQj8V2M8fEhhKRz3uzvR7GenLOjMFKkRp45IipLnFcH5WTiy', NULL, 3, '2026-03-13 19:22:27', '2026-03-13 19:22:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_recovery_requests`
--
ALTER TABLE `account_recovery_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arr_status_created_idx` (`status`,`created_at`),
  ADD KEY `arr_lookup_identifier_idx` (`lookup_identifier`),
  ADD KEY `arr_user_id_idx` (`user_id`),
  ADD KEY `arr_reviewed_by_idx` (`reviewed_by`);

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
  ADD KEY `fk_appointments_dentist` (`dentist_id`);

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
-- AUTO_INCREMENT for table `account_recovery_requests`
--
ALTER TABLE `account_recovery_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dental_charts`
--
ALTER TABLE `dental_charts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `health_histories`
--
ALTER TABLE `health_histories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `patient_form_drafts`
--
ALTER TABLE `patient_form_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `treatment_record_images`
--
ALTER TABLE `treatment_record_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
