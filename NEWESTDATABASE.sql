/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ANSI_QUOTES,NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE "activity_log" (
  "id" bigint unsigned NOT NULL AUTO_INCREMENT,
  "log_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "description" text COLLATE utf8mb4_general_ci NOT NULL,
  "subject_type" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "event" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "subject_id" bigint unsigned DEFAULT NULL,
  "causer_type" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "causer_id" bigint unsigned DEFAULT NULL,
  "properties" longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  "batch_uuid" char(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "created_at" timestamp NULL DEFAULT NULL,
  "updated_at" timestamp NULL DEFAULT NULL,
  PRIMARY KEY ("id"),
  KEY "subject" ("subject_type","subject_id"),
  KEY "causer" ("causer_type","causer_id"),
  KEY "log_name" ("log_name"),
  CONSTRAINT "activity_log_chk_1" CHECK (json_valid(`properties`))
);

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE "appointments" (
  "id" int NOT NULL AUTO_INCREMENT,
  "appointment_date" datetime NOT NULL,
  "status" enum('Pending','Scheduled','Ongoing','Completed','Cancelled','Waiting','Arrived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  "service_id" int NOT NULL,
  "patient_id" int DEFAULT NULL,
  "requester_user_id" bigint DEFAULT NULL,
  "requester_first_name" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "requester_last_name" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "requester_birth_date" date DEFAULT NULL,
  "requester_contact_number" varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "requester_email" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "booking_for_other" tinyint(1) NOT NULL DEFAULT '0',
  "requested_patient_first_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "requested_patient_last_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "requested_patient_birth_date" date DEFAULT NULL,
  "requester_relationship_to_patient" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "dentist_id" int DEFAULT NULL,
  "modified_by" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  "booking_type" varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'online_appointment',
  "cancellation_reason" text COLLATE utf8mb4_general_ci,
  "requester_middle_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "requested_patient_middle_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY ("id"),
  KEY "patient_id" ("patient_id"),
  KEY "service_id" ("service_id"),
  KEY "fk_appointments_dentist" ("dentist_id"),
  KEY "appointments_requester_user_id_index" ("requester_user_id"),
  KEY "appointments_requester_email_index" ("requester_email"),
  CONSTRAINT "appointments_ibfk_1" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT "appointments_ibfk_2" FOREIGN KEY ("service_id") REFERENCES "services" ("id"),
  CONSTRAINT "fk_appointments_dentist" FOREIGN KEY ("dentist_id") REFERENCES "users" ("id") ON DELETE SET NULL
);

DROP TABLE IF EXISTS `blocked_slots`;
CREATE TABLE "blocked_slots" (
  "id" bigint unsigned NOT NULL AUTO_INCREMENT,
  "date" date NOT NULL,
  "start_time" time NOT NULL,
  "end_time" time NOT NULL,
  "chair_id" bigint unsigned DEFAULT NULL,
  "reason" varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  "created_by" varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  "created_at" timestamp NULL DEFAULT NULL,
  "updated_at" timestamp NULL DEFAULT NULL,
  PRIMARY KEY ("id"),
  KEY "blocked_slots_date_start_time_index" ("date","start_time"),
  KEY "blocked_slots_date_end_time_index" ("date","end_time"),
  KEY "blocked_slots_chair_id_index" ("chair_id")
);

DROP TABLE IF EXISTS `dental_charts`;
CREATE TABLE "dental_charts" (
  "id" int NOT NULL AUTO_INCREMENT,
  "patient_id" int NOT NULL,
  "chart_data" longtext COLLATE utf8mb4_general_ci NOT NULL,
  "modified_by" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("id"),
  KEY "patient_id" ("patient_id"),
  CONSTRAINT "dental_charts_ibfk_1" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE
);

DROP TABLE IF EXISTS `health_histories`;
CREATE TABLE "health_histories" (
  "id" int NOT NULL AUTO_INCREMENT,
  "patient_id" int NOT NULL,
  "when_last_visit_q1" date DEFAULT NULL,
  "what_last_visit_reason_q1" text COLLATE utf8mb4_general_ci,
  "what_seeing_dentist_reason_q2" text COLLATE utf8mb4_general_ci,
  "is_clicking_jaw_q3a" tinyint(1) DEFAULT NULL,
  "is_pain_jaw_q3b" tinyint(1) DEFAULT NULL,
  "is_difficulty_opening_closing_q3c" tinyint(1) DEFAULT NULL,
  "is_locking_jaw_q3d" tinyint(1) DEFAULT NULL,
  "is_clench_grind_q4" tinyint(1) DEFAULT NULL,
  "is_bad_experience_q5" tinyint(1) DEFAULT NULL,
  "is_nervous_q6" tinyint(1) DEFAULT NULL,
  "what_nervous_concern_q6" text COLLATE utf8mb4_general_ci,
  "is_condition_q1" tinyint(1) DEFAULT NULL,
  "what_condition_reason_q1" text COLLATE utf8mb4_general_ci,
  "is_hospitalized_q2" tinyint(1) DEFAULT NULL,
  "what_hospitalized_reason_q2" text COLLATE utf8mb4_general_ci,
  "is_serious_illness_operation_q3" tinyint(1) DEFAULT NULL,
  "what_serious_illness_operation_reason_q3" text COLLATE utf8mb4_general_ci,
  "is_taking_medications_q4" tinyint(1) DEFAULT NULL,
  "what_medications_list_q4" text COLLATE utf8mb4_general_ci,
  "is_allergic_medications_q5" tinyint(1) DEFAULT NULL,
  "what_allergies_list_q5" text COLLATE utf8mb4_general_ci,
  "is_allergic_latex_rubber_metals_q6" tinyint(1) DEFAULT NULL,
  "is_pregnant_q7" tinyint(1) DEFAULT NULL,
  "is_breast_feeding_q8" tinyint(1) DEFAULT NULL,
  "modified_by" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  "is_chest_pain_angina" tinyint(1) NOT NULL DEFAULT '0',
  "is_shortness_of_breath" tinyint(1) NOT NULL DEFAULT '0',
  "is_heart_disease_heart_attack" tinyint(1) NOT NULL DEFAULT '0',
  "is_heart_surgery" tinyint(1) NOT NULL DEFAULT '0',
  "is_artificial_heart_valve_pacemaker" tinyint(1) NOT NULL DEFAULT '0',
  "is_rheumatic_fever_heart_disease" tinyint(1) NOT NULL DEFAULT '0',
  "is_heart_murmur" tinyint(1) NOT NULL DEFAULT '0',
  "is_mitral_valve_prolapse" tinyint(1) NOT NULL DEFAULT '0',
  "is_high_low_blood_pressure" tinyint(1) NOT NULL DEFAULT '0',
  "is_stroke" tinyint(1) NOT NULL DEFAULT '0',
  "is_respiratory_lung_problem" tinyint(1) NOT NULL DEFAULT '0',
  "is_emphysema" tinyint(1) NOT NULL DEFAULT '0',
  "is_asthma" tinyint(1) NOT NULL DEFAULT '0',
  "is_tuberculosis" tinyint(1) NOT NULL DEFAULT '0',
  "is_blood_disease" tinyint(1) NOT NULL DEFAULT '0',
  "is_bleeding_problems_disorders" tinyint(1) NOT NULL DEFAULT '0',
  "is_diabetes" tinyint(1) NOT NULL DEFAULT '0',
  "is_liver_problem_jaundice_hepatitis" tinyint(1) NOT NULL DEFAULT '0',
  "is_kidney_bladder_problem" tinyint(1) NOT NULL DEFAULT '0',
  "is_ulcers_hyperacidity" tinyint(1) NOT NULL DEFAULT '0',
  "is_tumors_cancer_malignancies" tinyint(1) NOT NULL DEFAULT '0',
  "is_aids_hiv_positive" tinyint(1) NOT NULL DEFAULT '0',
  "is_fainting_epilepsy_seizures" tinyint(1) NOT NULL DEFAULT '0',
  "is_mental_health_disorder" tinyint(1) NOT NULL DEFAULT '0',
  "is_other_disease_condition_problem" tinyint(1) NOT NULL DEFAULT '0',
  "what_other_disease_condition_problem" text COLLATE utf8mb4_general_ci,
  PRIMARY KEY ("id"),
  KEY "fk_health_history_patient" ("patient_id"),
  CONSTRAINT "fk_health_history_patient" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE
);

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE "migrations" (
  "id" int unsigned NOT NULL AUTO_INCREMENT,
  "migration" varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  "batch" int NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS `notes`;
CREATE TABLE "notes" (
  "id" int NOT NULL AUTO_INCREMENT,
  "title" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "notes" mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  "updated_at" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  "created_at" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "user_id" int NOT NULL,
  PRIMARY KEY ("id"),
  KEY "user_id" ("user_id"),
  CONSTRAINT "notes_ibfk_1" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE
);

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE "notifications" (
  "id" bigint unsigned NOT NULL AUTO_INCREMENT,
  "user_id" bigint unsigned NOT NULL,
  "type" varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  "appointment_id" bigint unsigned DEFAULT NULL,
  "actor_user_id" bigint unsigned DEFAULT NULL,
  "title" varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  "message" text COLLATE utf8mb4_unicode_ci NOT NULL,
  "link" varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  "read_at" timestamp NULL DEFAULT NULL,
  "cleared_at" timestamp NULL DEFAULT NULL,
  "created_at" timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("id"),
  KEY "notifications_user_id_index" ("user_id"),
  KEY "notifications_type_created_at_index" ("type","created_at"),
  KEY "notifications_appointment_id_index" ("appointment_id"),
  KEY "notifications_actor_user_id_index" ("actor_user_id"),
  KEY "notifications_user_read_index" ("user_id","read_at"),
  KEY "notifications_user_cleared_index" ("user_id","cleared_at")
);

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE "password_reset_tokens" (
  "email" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "token" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "created_at" timestamp NULL DEFAULT NULL,
  PRIMARY KEY ("email")
);

DROP TABLE IF EXISTS `patient_form_drafts`;
CREATE TABLE "patient_form_drafts" (
  "id" bigint unsigned NOT NULL AUTO_INCREMENT,
  "user_id" bigint unsigned NOT NULL,
  "patient_id" bigint unsigned NOT NULL DEFAULT '0',
  "mode" varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  "step" tinyint unsigned NOT NULL DEFAULT '1',
  "payload_json" longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  "expires_at" timestamp NULL DEFAULT NULL,
  "created_at" timestamp NULL DEFAULT NULL,
  "updated_at" timestamp NULL DEFAULT NULL,
  PRIMARY KEY ("id"),
  UNIQUE KEY "patient_form_drafts_context_unique" ("user_id","mode","patient_id"),
  KEY "patient_form_drafts_expires_at_index" ("expires_at")
);

DROP TABLE IF EXISTS `patients`;
CREATE TABLE "patients" (
  "id" int NOT NULL AUTO_INCREMENT,
  "last_name" varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  "first_name" varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  "mobile_number" varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  "middle_name" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "nickname" varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "occupation" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "birth_date" date DEFAULT NULL,
  "gender" enum('Male','Female','Other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  "civil_status" varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "home_address" text COLLATE utf8mb4_general_ci,
  "office_address" text COLLATE utf8mb4_general_ci,
  "home_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "office_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "email_address" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "referral" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "emergency_contact_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "emergency_contact_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "relationship" varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "who_answering" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "relationship_to_patient" varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "father_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "father_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "mother_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "mother_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "guardian_name" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "guardian_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "modified_by" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("id"),
  FULLTEXT KEY "mobile_number_2" ("mobile_number")
);

DROP TABLE IF EXISTS `roles`;
CREATE TABLE "roles" (
  "id" int NOT NULL AUTO_INCREMENT,
  "role_name" varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS `services`;
CREATE TABLE "services" (
  "id" int NOT NULL AUTO_INCREMENT,
  "service_name" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "duration" time NOT NULL,
  PRIMARY KEY ("id")
);

DROP TABLE IF EXISTS `treatment_record_images`;
CREATE TABLE "treatment_record_images" (
  "id" int NOT NULL AUTO_INCREMENT,
  "treatment_record_id" int NOT NULL,
  "image_path" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "image_type" enum('before','after','other') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'other',
  "sort_order" int NOT NULL DEFAULT '0',
  "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("id"),
  KEY "treatment_record_images_treatment_record_id_idx" ("treatment_record_id"),
  CONSTRAINT "treatment_record_images_treatment_record_id_fk" FOREIGN KEY ("treatment_record_id") REFERENCES "treatment_records" ("id") ON DELETE CASCADE
);

DROP TABLE IF EXISTS `treatment_records`;
CREATE TABLE "treatment_records" (
  "id" int NOT NULL AUTO_INCREMENT,
  "patient_id" int NOT NULL,
  "dental_chart_id" int NOT NULL,
  "dmd" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "treatment" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "cost_of_treatment" decimal(10,2) DEFAULT NULL,
  "amount_charged" decimal(10,2) DEFAULT NULL,
  "remarks" text COLLATE utf8mb4_general_ci,
  "image" longtext COLLATE utf8mb4_general_ci,
  "modified_by" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "created_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("id"),
  KEY "patient_id" ("patient_id"),
  KEY "dental_chart_id" ("dental_chart_id"),
  CONSTRAINT "treatment_records_ibfk_1" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE CASCADE,
  CONSTRAINT "treatment_records_ibfk_2" FOREIGN KEY ("dental_chart_id") REFERENCES "dental_charts" ("id") ON DELETE CASCADE
);

DROP TABLE IF EXISTS `users`;
CREATE TABLE "users" (
  "id" int NOT NULL AUTO_INCREMENT,
  "username" varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  "first_name" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "middle_name" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "last_name" varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "email" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "mobile_number" varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "birth_date" date DEFAULT NULL,
  "patient_id" int DEFAULT NULL,
  "email_verified_at" timestamp NULL DEFAULT NULL,
  "verification_token" varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "password" varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  "google_id" varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  "role" int NOT NULL,
  "created_at" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY ("id"),
  UNIQUE KEY "username" ("username"),
  UNIQUE KEY "user_id" ("id","password"),
  UNIQUE KEY "google_id" ("google_id"),
  UNIQUE KEY "email" ("email"),
  KEY "role" ("role"),
  KEY "idx_users_patient_id" ("patient_id"),
  KEY "idx_users_mobile_number" ("mobile_number"),
  CONSTRAINT "users_ibfk_1" FOREIGN KEY ("role") REFERENCES "roles" ("id"),
  CONSTRAINT "users_patient_id_foreign" FOREIGN KEY ("patient_id") REFERENCES "patients" ("id") ON DELETE SET NULL
);

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 118, 'App\\Models\\User', 118, '{\"attributes\":{\"ip_address\":\"108.162.227.65\",\"login_at\":\"2026-03-28 09:19:57\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Mobile Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Android\",\"device\":\"Mobile\"}}', NULL, '2026-03-28 09:19:57', '2026-03-28 09:19:57'),
(2, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 81, 'App\\Models\\User', 81, '{\"attributes\":{\"ip_address\":\"162.158.193.13\",\"login_at\":\"2026-03-28 09:27:54\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Windows\",\"device\":\"Desktop\"}}', NULL, '2026-03-28 09:27:54', '2026-03-28 09:27:54'),
(3, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 130, 'App\\Models\\User', 130, '{\"attributes\":{\"ip_address\":\"172.68.211.32\",\"login_at\":\"2026-03-28 10:41:54\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Windows\",\"device\":\"Desktop\"}}', NULL, '2026-03-28 10:41:54', '2026-03-28 10:41:54'),
(4, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 127, 'App\\Models\\User', 127, '{\"attributes\":{\"ip_address\":\"172.68.211.127\",\"login_at\":\"2026-03-28 10:44:17\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Windows\",\"device\":\"Desktop\"}}', NULL, '2026-03-28 10:44:17', '2026-03-28 10:44:17'),
(5, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 1, 'App\\Models\\User', 115, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-28 10:45:28', '2026-03-28 10:45:28'),
(6, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":1}}', NULL, '2026-03-28 10:45:28', '2026-03-28 10:45:28'),
(7, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"appointment_id\":1,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-28 10:45:28', '2026-03-28 10:45:28'),
(8, 'default', 'Created Patient from Appointment Request', 'App\\Models\\Patient', 'patient_created_from_request', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"patient_id\":1,\"source_appointment_id\":1,\"first_name\":\"Michael Dave\",\"last_name\":\"Legaspina\",\"middle_name\":null}}', NULL, '2026-03-28 10:45:43', '2026-03-28 10:45:43'),
(9, 'default', 'Linked Appointment Request to New Patient', 'App\\Models\\Appointment', 'appointment_request_linked_new_patient', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"appointment_id\":1,\"patient_id\":1}}', NULL, '2026-03-28 10:45:43', '2026-03-28 10:45:43'),
(10, 'default', 'Linked Patient Account to Patient Record', 'App\\Models\\Appointment', 'user_patient_linked', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"user_id\":127,\"patient_id\":1,\"appointment_id\":1}}', NULL, '2026-03-28 10:45:43', '2026-03-28 10:45:43'),
(11, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 1, 'App\\Models\\User', 115, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-28 10:45:48', '2026-03-28 10:45:48'),
(12, 'default', 'Admitted Appointment', 'App\\Models\\Appointment', 'appointment_admitted', 1, 'App\\Models\\User', 115, '{\"old\":{\"status\":\"Waiting\",\"service_id\":2,\"dentist_id\":null},\"attributes\":{\"status\":\"Ongoing\",\"service_id\":2,\"dentist_id\":115}}', NULL, '2026-03-28 10:45:55', '2026-03-28 10:45:55'),
(13, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 1, 'App\\Models\\User', 115, '{\"old\":{\"id\":1,\"last_name\":\"Legaspina\",\"first_name\":\"Michael Dave\",\"mobile_number\":\"9779234567\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":\"2003-07-08\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"mdlwork8@gmail.com\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"michaeldavelegaspina@gmail.com\",\"created_at\":\"2026-03-28 10:45:43\",\"updated_at\":\"2026-03-28 10:45:43\"},\"attributes\":{\"id\":1,\"last_name\":\"Legaspina\",\"first_name\":\"Michael Dave\",\"mobile_number\":\"9779234567\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":\"2003-07-08\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"mdlwork8@gmail.com\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"michaeldavelegaspina@gmail.com\",\"created_at\":\"2026-03-28 10:45:43\",\"updated_at\":\"2026-03-28 10:45:43\"}}', NULL, '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(14, 'default', 'Created Health History', 'App\\Models\\Patient', 'health_history_created', 1, 'App\\Models\\User', 115, '{\"health_history_id\":1,\"attributes\":{\"when_last_visit_q1\":\"2003-07-08\",\"what_last_visit_reason_q1\":\"Cleaning\",\"what_seeing_dentist_reason_q2\":\"Check up\",\"is_clicking_jaw_q3a\":0,\"is_pain_jaw_q3b\":0,\"is_difficulty_opening_closing_q3c\":0,\"is_locking_jaw_q3d\":0,\"is_clench_grind_q4\":0,\"is_bad_experience_q5\":0,\"is_nervous_q6\":0,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":0,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":0,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":0,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":0,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":0,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":0,\"is_chest_pain_angina\":0,\"is_shortness_of_breath\":0,\"is_heart_disease_heart_attack\":0,\"is_heart_surgery\":0,\"is_artificial_heart_valve_pacemaker\":0,\"is_rheumatic_fever_heart_disease\":0,\"is_heart_murmur\":0,\"is_mitral_valve_prolapse\":0,\"is_high_low_blood_pressure\":0,\"is_stroke\":0,\"is_respiratory_lung_problem\":0,\"is_emphysema\":0,\"is_asthma\":0,\"is_tuberculosis\":0,\"is_blood_disease\":0,\"is_bleeding_problems_disorders\":0,\"is_diabetes\":0,\"is_liver_problem_jaundice_hepatitis\":0,\"is_kidney_bladder_problem\":0,\"is_ulcers_hyperacidity\":0,\"is_tumors_cancer_malignancies\":0,\"is_aids_hiv_positive\":0,\"is_fainting_epilepsy_seizures\":0,\"is_mental_health_disorder\":0,\"is_other_disease_condition_problem\":0,\"what_other_disease_condition_problem\":\"\",\"is_pregnant_q7\":0,\"is_breast_feeding_q8\":0,\"patient_id\":1,\"modified_by\":\"michaeldavelegaspina@gmail.com\",\"created_at\":\"2026-03-28T02:48:49.335051Z\",\"updated_at\":\"2026-03-28T02:48:49.335051Z\"}}', NULL, '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(15, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"patient_id\":1,\"chart_data\":\"{\\\"teeth\\\":{\\\"11\\\":null,\\\"12\\\":null,\\\"13\\\":null,\\\"14\\\":{\\\"right\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CI\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CI\\\",\\\"color\\\":\\\"red\\\"}},\\\"15\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CI\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CI\\\",\\\"color\\\":\\\"red\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Good\\\",\\\"gingiva\\\":\\\"Healthy\\\",\\\"calcular_deposits\\\":\\\"Slight\\\",\\\"stains\\\":\\\"Moderate\\\",\\\"complete_denture\\\":\\\"Lower\\\",\\\"partial_denture\\\":\\\"Upper\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(16, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 1, 'App\\Models\\User', 115, '{\"attributes\":{\"patient_id\":1,\"dmd\":\"Michael Legaspina\",\"treatment\":\"Cleaning, Dental Check-up\",\"cost_of_treatment\":\"1500\",\"amount_charged\":\"1000\",\"remarks\":\"\",\"modified_by\":\"michaeldavelegaspina@gmail.com\",\"updated_at\":\"2026-03-28T02:48:49.335051Z\",\"created_at\":\"2026-03-28T02:48:49.335051Z\"}}', NULL, '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(17, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 1, 'App\\Models\\User', 115, '{\"old\":{\"status\":\"Ongoing\"},\"attributes\":{\"status\":\"Completed\"}}', NULL, '2026-03-28 10:48:59', '2026-03-28 10:48:59'),
(18, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 119, 'App\\Models\\User', 119, '{\"attributes\":{\"ip_address\":\"172.69.231.130\",\"login_at\":\"2026-03-28 10:53:17\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Windows\",\"device\":\"Desktop\"}}', NULL, '2026-03-28 10:53:17', '2026-03-28 10:53:17'),
(19, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 10, 'App\\Models\\User', 10, '{\"attributes\":{\"ip_address\":\"172.69.231.130\",\"login_at\":\"2026-03-28 10:59:46\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Safari\\/537.36 Edg\\/146.0.0.0\",\"browser\":\"Edge\",\"platform\":\"Windows\",\"device\":\"Desktop\"}}', NULL, '2026-03-28 10:59:46', '2026-03-28 10:59:46'),
(20, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 117, 'App\\Models\\User', 117, '{\"attributes\":{\"ip_address\":\"172.71.152.61\",\"login_at\":\"2026-03-28 11:12:07\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Mobile Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Android\",\"device\":\"Mobile\"}}', NULL, '2026-03-28 11:12:07', '2026-03-28 11:12:07'),
(21, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 131, 'App\\Models\\User', 131, '{\"attributes\":{\"ip_address\":\"172.69.221.11\",\"login_at\":\"2026-03-28 11:16:50\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/137.0.0.0 Mobile Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Android\",\"device\":\"Mobile\"}}', NULL, '2026-03-28 11:16:50', '2026-03-28 11:16:50'),
(22, 'default', 'Rescheduled And Approved Appointment', 'App\\Models\\Appointment', 'appointment_rescheduled_and_approved', 15, 'App\\Models\\User', 118, '{\"attributes\":{\"appointment_id\":15,\"appointment_date\":\"2026-03-30 14:00:00\",\"service_id\":5,\"status\":\"Scheduled\"}}', NULL, '2026-03-28 11:19:09', '2026-03-28 11:19:09'),
(23, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 15, 'App\\Models\\User', 118, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-28 11:20:02', '2026-03-28 11:20:02'),
(24, 'default', 'Created Patient from Appointment Request', 'App\\Models\\Patient', 'patient_created_from_request', 3, 'App\\Models\\User', 118, '{\"attributes\":{\"patient_id\":3,\"source_appointment_id\":15,\"first_name\":\"Roamer\",\"last_name\":\"Acervo\",\"middle_name\":\"Bascara\"}}', NULL, '2026-03-28 11:20:51', '2026-03-28 11:20:51'),
(25, 'default', 'Linked Appointment Request to New Patient', 'App\\Models\\Appointment', 'appointment_request_linked_new_patient', 15, 'App\\Models\\User', 118, '{\"attributes\":{\"appointment_id\":15,\"patient_id\":3}}', NULL, '2026-03-28 11:20:51', '2026-03-28 11:20:51'),
(26, 'default', 'Linked Patient Account to Patient Record', 'App\\Models\\Appointment', 'user_patient_linked', 15, 'App\\Models\\User', 118, '{\"attributes\":{\"user_id\":131,\"patient_id\":3,\"appointment_id\":15}}', NULL, '2026-03-28 11:20:51', '2026-03-28 11:20:51'),
(27, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 132, 'App\\Models\\User', 132, '{\"attributes\":{\"ip_address\":\"162.158.178.231\",\"login_at\":\"2026-03-28 13:47:35\",\"user_agent\":\"Mozilla\\/5.0 (X11; Linux x86_64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Linux\",\"device\":\"Desktop\"}}', NULL, '2026-03-28 13:47:35', '2026-03-28 13:47:35'),
(28, 'default', 'Created User Account', 'App\\Models\\User', 'user_created', 133, 'App\\Models\\User', 115, '{\"attributes\":{\"username\":\"kijac84552@izkat.com\",\"email\":\"kijac84552@izkat.com\",\"first_name\":\"Allan\",\"last_name\":\"Tejada\",\"mobile_number\":\"9876456782\",\"role\":\"1\",\"email_verified_at\":null,\"created_at\":\"2026-03-28T07:17:35.007459Z\",\"updated_at\":\"2026-03-28T07:17:35.007471Z\"}}', NULL, '2026-03-28 15:17:36', '2026-03-28 15:17:36'),
(29, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 127, 'App\\Models\\User', 115, '{\"old\":{\"role\":3},\"attributes\":{\"role\":\"2\"}}', NULL, '2026-03-28 16:37:10', '2026-03-28 16:37:10'),
(30, 'default', 'Updated User Account', 'App\\Models\\User', 'user_updated', 81, 'App\\Models\\User', 119, '{\"old\":{\"role\":4},\"attributes\":{\"role\":\"1\"}}', NULL, '2026-03-28 16:39:04', '2026-03-28 16:39:04'),
(31, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 32, 'App\\Models\\User', 127, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-28 16:50:19', '2026-03-28 16:50:19'),
(32, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 134, 'App\\Models\\User', 134, '{\"attributes\":{\"ip_address\":\"172.71.214.30\",\"login_at\":\"2026-03-28 16:54:00\",\"user_agent\":\"Mozilla\\/5.0 (Linux; Android 10; K) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/146.0.0.0 Mobile Safari\\/537.36\",\"browser\":\"Chrome\",\"platform\":\"Android\",\"device\":\"Mobile\"}}', NULL, '2026-03-28 16:54:00', '2026-03-28 16:54:00'),
(33, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 31, 'App\\Models\\User', 81, '{\"old\":{\"status\":\"Pending\"},\"attributes\":{\"status\":\"Scheduled\"}}', NULL, '2026-03-28 16:59:40', '2026-03-28 16:59:40'),
(34, 'default', 'Approved Appointment Request', 'App\\Models\\Appointment', 'appointment_request_approved', 31, 'App\\Models\\User', 81, '{\"attributes\":{\"patient_id\":null,\"appointment_id\":31}}', NULL, '2026-03-28 16:59:40', '2026-03-28 16:59:40'),
(35, 'default', 'Official Appointment Linked to Patient', 'App\\Models\\Appointment', 'official_appointment_created', 31, 'App\\Models\\User', 81, '{\"attributes\":{\"appointment_id\":31,\"patient_id\":null,\"status\":\"Scheduled\"}}', NULL, '2026-03-28 16:59:40', '2026-03-28 16:59:40'),
(36, 'default', 'Created Patient from Appointment Request', 'App\\Models\\Patient', 'patient_created_from_request', 14, 'App\\Models\\User', 81, '{\"attributes\":{\"patient_id\":14,\"source_appointment_id\":31,\"first_name\":\"Adrian\",\"last_name\":\"Legaspina\",\"middle_name\":null}}', NULL, '2026-03-28 17:01:14', '2026-03-28 17:01:14'),
(37, 'default', 'Linked Appointment Request to New Patient', 'App\\Models\\Appointment', 'appointment_request_linked_new_patient', 31, 'App\\Models\\User', 81, '{\"attributes\":{\"appointment_id\":31,\"patient_id\":14}}', NULL, '2026-03-28 17:01:14', '2026-03-28 17:01:14'),
(38, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 14, 'App\\Models\\User', 81, '{\"old\":{\"id\":14,\"last_name\":\"Legaspina\",\"first_name\":\"Adrian\",\"mobile_number\":\"9876543456\",\"middle_name\":null,\"nickname\":null,\"occupation\":null,\"birth_date\":\"2022-07-14\",\"gender\":null,\"civil_status\":null,\"home_address\":null,\"office_address\":null,\"home_number\":null,\"office_number\":null,\"email_address\":\"cerineo123@gmail.com\",\"referral\":null,\"emergency_contact_name\":null,\"emergency_contact_number\":null,\"relationship\":null,\"who_answering\":null,\"relationship_to_patient\":null,\"father_name\":null,\"father_number\":null,\"mother_name\":null,\"mother_number\":null,\"guardian_name\":null,\"guardian_number\":null,\"modified_by\":\"soosickk1@gmail.com\",\"created_at\":\"2026-03-28 17:01:14\",\"updated_at\":\"2026-03-28 17:01:14\"},\"attributes\":{\"last_name\":\"Legaspina\",\"first_name\":\"Adrian\",\"middle_name\":\"\",\"nickname\":\"\",\"occupation\":\"Engineer\",\"birth_date\":\"2022-07-14\",\"gender\":\"Male\",\"civil_status\":\"Sda\",\"home_address\":\"Demo address 20, tejada city\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"mobile_number\":\"9876543456\",\"email_address\":\"cerineo123@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Joel Cerineo\",\"emergency_contact_number\":\"9725367823\",\"relationship\":\"Sibling\",\"who_answering\":\"Dsa\",\"relationship_to_patient\":\"Dsa\",\"father_name\":\"Ddfr\",\"father_number\":\"7627864783\",\"mother_name\":\"Dfref\",\"mother_number\":\"\",\"guardian_name\":\"Sdfg\",\"guardian_number\":\"\",\"modified_by\":\"soosickk1@gmail.com\"}}', NULL, '2026-03-28 17:03:32', '2026-03-28 17:03:32'),
(39, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 31, 'App\\Models\\User', 81, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-28 17:05:36', '2026-03-28 17:05:36'),
(40, 'default', 'Created Appointment', 'App\\Models\\Appointment', 'appointment_created', 33, 'App\\Models\\User', 81, '{\"attributes\":{\"patient_id\":null,\"patient_name\":\"Legaspina, Adrian\",\"service_id\":\"18\",\"appointment_date\":\"2026-03-28 11:00:00\",\"status\":\"Scheduled\"}}', NULL, '2026-03-28 17:06:32', '2026-03-28 17:06:32'),
(41, 'default', 'Linked Appointment Request to Existing Patient', 'App\\Models\\Appointment', 'appointment_request_linked_existing', 33, 'App\\Models\\User', 81, '{\"attributes\":{\"appointment_id\":33,\"patient_id\":14}}', NULL, '2026-03-28 17:06:55', '2026-03-28 17:06:55'),
(42, 'default', 'Updated Patient', 'App\\Models\\Patient', 'patient_updated', 14, 'App\\Models\\User', 81, '{\"old\":{\"id\":14,\"last_name\":\"Legaspina\",\"first_name\":\"Adrian\",\"mobile_number\":\"9876543456\",\"middle_name\":\"\",\"nickname\":\"\",\"occupation\":\"Engineer\",\"birth_date\":\"2022-07-14\",\"gender\":\"Male\",\"civil_status\":\"Sda\",\"home_address\":\"Demo address 20, tejada city\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"email_address\":\"cerineo123@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Joel Cerineo\",\"emergency_contact_number\":\"9725367823\",\"relationship\":\"Sibling\",\"who_answering\":\"Dsa\",\"relationship_to_patient\":\"Dsa\",\"father_name\":\"Ddfr\",\"father_number\":\"7627864783\",\"mother_name\":\"Dfref\",\"mother_number\":\"\",\"guardian_name\":\"Sdfg\",\"guardian_number\":\"\",\"modified_by\":\"soosickk1@gmail.com\",\"created_at\":\"2026-03-28 17:01:14\",\"updated_at\":\"2026-03-28 09:03:32\"},\"attributes\":{\"id\":14,\"last_name\":\"Legaspina\",\"first_name\":\"Adrian\",\"mobile_number\":\"9876543456\",\"middle_name\":\"\",\"nickname\":\"\",\"occupation\":\"Engineer\",\"birth_date\":\"2022-07-14\",\"gender\":\"Male\",\"civil_status\":\"Sda\",\"home_address\":\"Demo address 20, tejada city\",\"office_address\":\"\",\"home_number\":\"\",\"office_number\":\"\",\"email_address\":\"cerineo123@gmail.com\",\"referral\":\"\",\"emergency_contact_name\":\"Joel Cerineo\",\"emergency_contact_number\":\"9725367823\",\"relationship\":\"Sibling\",\"who_answering\":\"Dsa\",\"relationship_to_patient\":\"Dsa\",\"father_name\":\"Ddfr\",\"father_number\":\"7627864783\",\"mother_name\":\"Dfref\",\"mother_number\":\"\",\"guardian_name\":\"Sdfg\",\"guardian_number\":\"\",\"modified_by\":\"soosickk1@gmail.com\",\"created_at\":\"2026-03-28 17:01:14\",\"updated_at\":\"2026-03-28 09:03:32\"}}', NULL, '2026-03-28 17:11:45', '2026-03-28 17:11:45'),
(43, 'default', 'Created Health History', 'App\\Models\\Patient', 'health_history_created', 14, 'App\\Models\\User', 81, '{\"health_history_id\":19,\"attributes\":{\"when_last_visit_q1\":\"2026-03-28\",\"what_last_visit_reason_q1\":\"Hagdsa\",\"what_seeing_dentist_reason_q2\":\"Asdewrfw\",\"is_clicking_jaw_q3a\":0,\"is_pain_jaw_q3b\":0,\"is_difficulty_opening_closing_q3c\":0,\"is_locking_jaw_q3d\":0,\"is_clench_grind_q4\":0,\"is_bad_experience_q5\":0,\"is_nervous_q6\":0,\"what_nervous_concern_q6\":\"\",\"is_condition_q1\":0,\"what_condition_reason_q1\":\"\",\"is_hospitalized_q2\":0,\"what_hospitalized_reason_q2\":\"\",\"is_serious_illness_operation_q3\":0,\"what_serious_illness_operation_reason_q3\":\"\",\"is_taking_medications_q4\":0,\"what_medications_list_q4\":\"\",\"is_allergic_medications_q5\":0,\"what_allergies_list_q5\":\"\",\"is_allergic_latex_rubber_metals_q6\":0,\"is_chest_pain_angina\":0,\"is_shortness_of_breath\":0,\"is_heart_disease_heart_attack\":0,\"is_heart_surgery\":0,\"is_artificial_heart_valve_pacemaker\":0,\"is_rheumatic_fever_heart_disease\":0,\"is_heart_murmur\":0,\"is_mitral_valve_prolapse\":0,\"is_high_low_blood_pressure\":0,\"is_stroke\":0,\"is_respiratory_lung_problem\":0,\"is_emphysema\":0,\"is_asthma\":0,\"is_tuberculosis\":0,\"is_blood_disease\":0,\"is_bleeding_problems_disorders\":0,\"is_diabetes\":0,\"is_liver_problem_jaundice_hepatitis\":0,\"is_kidney_bladder_problem\":0,\"is_ulcers_hyperacidity\":0,\"is_tumors_cancer_malignancies\":0,\"is_aids_hiv_positive\":0,\"is_fainting_epilepsy_seizures\":0,\"is_mental_health_disorder\":0,\"is_other_disease_condition_problem\":0,\"what_other_disease_condition_problem\":\"\",\"is_pregnant_q7\":0,\"is_breast_feeding_q8\":0,\"patient_id\":14,\"modified_by\":\"soosickk1@gmail.com\",\"created_at\":\"2026-03-28T09:11:45.956769Z\",\"updated_at\":\"2026-03-28T09:11:45.956769Z\"}}', NULL, '2026-03-28 17:11:45', '2026-03-28 17:11:45'),
(44, 'default', 'Created Dental Chart', 'App\\Models\\DentalChart', 'dental_chart_created', 19, 'App\\Models\\User', 81, '{\"attributes\":{\"patient_id\":14,\"chart_data\":\"{\\\"teeth\\\":{\\\"11\\\":null,\\\"12\\\":null,\\\"13\\\":null,\\\"14\\\":null,\\\"15\\\":null,\\\"16\\\":{\\\"top\\\":{\\\"color\\\":\\\"blue\\\",\\\"code\\\":\\\"GIC\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"GIC\\\",\\\"color\\\":\\\"blue\\\"}},\\\"17\\\":{\\\"center\\\":{\\\"color\\\":\\\"red\\\",\\\"code\\\":\\\"CD\\\"},\\\"line_1\\\":{\\\"code\\\":\\\"CD\\\",\\\"color\\\":\\\"red\\\"}}},\\\"oral_exam\\\":{\\\"oral_hygiene_status\\\":\\\"Excellent\\\",\\\"gingiva\\\":\\\"Mildly Inflamed\\\",\\\"calcular_deposits\\\":\\\"None\\\",\\\"stains\\\":\\\"Slight\\\",\\\"complete_denture\\\":\\\"Upper\\\",\\\"partial_denture\\\":\\\"Lower\\\"},\\\"comments\\\":{\\\"notes\\\":\\\"\\\",\\\"treatment_plan\\\":\\\"\\\"},\\\"meta\\\":{\\\"dentition_type\\\":\\\"adult\\\",\\\"numbering_system\\\":\\\"FDI\\\"}}\"}}', NULL, '2026-03-28 17:11:45', '2026-03-28 17:11:45'),
(45, 'default', 'Created Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_created', 19, 'App\\Models\\User', 81, '{\"attributes\":{\"patient_id\":14,\"dmd\":\"Gege Dayoo\",\"treatment\":\"Cleaning\",\"cost_of_treatment\":\"2000\",\"amount_charged\":\"2000\",\"remarks\":\"\",\"modified_by\":\"soosickk1@gmail.com\",\"updated_at\":\"2026-03-28T09:11:45.956769Z\",\"created_at\":\"2026-03-28T09:11:45.956769Z\"}}', NULL, '2026-03-28 17:11:46', '2026-03-28 17:11:46'),
(46, 'default', 'Updated Treatment Record', 'App\\Models\\TreatmentRecord', 'treatment_record_updated', 19, 'App\\Models\\User', 81, '{\"old\":{\"id\":19,\"patient_id\":14,\"dental_chart_id\":19,\"dmd\":\"Gege Dayoo\",\"treatment\":\"Cleaning\",\"cost_of_treatment\":\"2000.00\",\"amount_charged\":\"2000.00\",\"remarks\":\"\",\"image\":null,\"modified_by\":\"soosickk1@gmail.com\",\"created_at\":\"2026-03-28 17:11:45\",\"updated_at\":\"2026-03-28 17:11:45\"},\"attributes\":{\"patient_id\":14,\"dmd\":\"Gege Dayoo\",\"treatment\":\"Cleaning\",\"cost_of_treatment\":\"2000.00\",\"amount_charged\":\"2000.00\",\"remarks\":\"\",\"modified_by\":\"soosickk1@gmail.com\",\"updated_at\":\"2026-03-28T09:13:23.426969Z\"}}', NULL, '2026-03-28 17:13:23', '2026-03-28 17:13:23'),
(47, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_updated', 33, 'App\\Models\\User', 81, '{\"old\":{\"status\":\"Scheduled\"},\"attributes\":{\"status\":\"Waiting\"}}', NULL, '2026-03-28 17:15:22', '2026-03-28 17:15:22'),
(48, 'default', 'Updated Appointment Status', 'App\\Models\\Appointment', 'appointment_cancelled', 31, 'App\\Models\\User', 119, '{\"old\":{\"status\":\"Waiting\"},\"attributes\":{\"status\":\"Cancelled\"}}', NULL, '2026-03-28 17:17:36', '2026-03-28 17:17:36'),
(49, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 130, 'App\\Models\\User', 119, '{\"attributes\":{\"id\":130,\"username\":\"jhonrickgalor9@gmail.com\",\"first_name\":\"Jhonrick\",\"middle_name\":\"Baldovino\",\"last_name\":\"Galor\",\"email\":\"jhonrickgalor9@gmail.com\",\"mobile_number\":\"9668912347\",\"birth_date\":\"2002-05-03\",\"patient_id\":null,\"email_verified_at\":\"2026-03-28 10:41:54\",\"google_id\":\"110112540742364568380\",\"role\":4,\"created_at\":\"2026-03-28 10:41:54\",\"updated_at\":\"2026-03-28 02:42:46\"}}', NULL, '2026-03-28 17:19:38', '2026-03-28 17:19:38'),
(50, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 125, 'App\\Models\\User', 119, '{\"attributes\":{\"id\":125,\"username\":\"acemorada25@gmail.com\",\"first_name\":\"Christian Ace\",\"middle_name\":\"Parungao\",\"last_name\":\"Morada\",\"email\":\"acemorada25@gmail.com\",\"mobile_number\":\"9494642734\",\"birth_date\":\"2004-03-25\",\"patient_id\":null,\"email_verified_at\":\"2026-03-27 22:26:00\",\"google_id\":\"118255578744459997448\",\"role\":4,\"created_at\":\"2026-03-27 22:26:00\",\"updated_at\":\"2026-03-27 14:27:04\"}}', NULL, '2026-03-28 17:19:43', '2026-03-28 17:19:43'),
(51, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 115, 'App\\Models\\User', 119, '{\"attributes\":{\"id\":115,\"username\":\"michaeldavelegaspina@gmail.com\",\"first_name\":\"Michael\",\"middle_name\":\"Dave\",\"last_name\":\"Legaspina\",\"email\":\"michaeldavelegaspina@gmail.com\",\"mobile_number\":\"9770157872\",\"birth_date\":null,\"patient_id\":null,\"email_verified_at\":\"2026-03-27 15:35:23\",\"google_id\":\"107834301249603892358\",\"role\":4,\"created_at\":\"2026-03-27 15:35:23\",\"updated_at\":\"2026-03-27 15:38:57\"}}', NULL, '2026-03-28 17:19:48', '2026-03-28 17:19:48'),
(52, 'default', 'Deleted User Account', 'App\\Models\\User', 'user_deleted', 10, 'App\\Models\\User', 119, '{\"attributes\":{\"id\":10,\"username\":\"renzzluigi@gmail.com\",\"first_name\":\"Rosales\",\"middle_name\":\"Rosales\",\"last_name\":\"Rosales\",\"email\":\"renzzluigi@gmail.com\",\"mobile_number\":\"3211231231\",\"birth_date\":null,\"patient_id\":null,\"email_verified_at\":\"2026-03-17 17:30:10\",\"google_id\":\"114082662441983874861\",\"role\":4,\"created_at\":\"2026-03-17 15:13:24\",\"updated_at\":\"2026-03-28 03:21:21\"}}', NULL, '2026-03-28 17:19:51', '2026-03-28 17:19:51'),
(53, 'default', 'Logged In', 'App\\Models\\User', 'user_logged_in', 135, 'App\\Models\\User', 135, '{\"attributes\":{\"ip_address\":\"172.69.176.108\",\"login_at\":\"2026-04-14 18:38:11\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko\\/20100101 Firefox\\/150.0\",\"browser\":\"Firefox\",\"platform\":\"Windows\",\"device\":\"Desktop\"}}', NULL, '2026-04-14 18:38:11', '2026-04-14 18:38:11');
INSERT INTO `appointments` (`id`, `appointment_date`, `status`, `service_id`, `patient_id`, `requester_user_id`, `requester_first_name`, `requester_last_name`, `requester_birth_date`, `requester_contact_number`, `requester_email`, `booking_for_other`, `requested_patient_first_name`, `requested_patient_last_name`, `requested_patient_birth_date`, `requester_relationship_to_patient`, `dentist_id`, `modified_by`, `created_at`, `updated_at`, `booking_type`, `cancellation_reason`, `requester_middle_name`, `requested_patient_middle_name`) VALUES
(1, '2026-03-28 12:00:00', 'Completed', 2, 1, 127, 'Michael Dave', 'Legaspina', '2003-07-08', '9779234567', 'mdlwork8@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'mdlwork8@gmail.com', '2026-03-28 10:44:45', '2026-03-28 10:48:59', 'online_appointment', NULL, NULL, NULL),
(12, '2026-03-27 09:00:00', 'Completed', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-27 08:30:00', '2026-03-27 09:00:00', 'walk_in', NULL, NULL, NULL),
(13, '2026-03-24 09:00:00', 'Cancelled', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-23 16:30:00', '2026-03-24 08:15:00', 'walk_in', 'Patient cancelled due to personal schedule conflict', NULL, NULL),
(14, '2026-03-19 10:00:00', 'Completed', 7, 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-18 15:00:00', '2026-03-19 10:00:00', 'walk_in', NULL, NULL, NULL),
(15, '2026-03-30 14:00:00', 'Waiting', 5, 3, 131, 'Roamer', 'Acervo', '2003-06-17', '9134344346', 'smurfojt@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'smurfojt@gmail.com', '2026-03-28 11:18:43', '2026-03-28 11:20:51', 'online_appointment', NULL, 'Bascara', NULL),
(16, '2026-01-05 09:00:00', 'Completed', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-04 09:00:00', '2026-01-05 09:00:00', 'walk_in', NULL, NULL, NULL),
(17, '2026-01-08 10:00:00', 'Completed', 7, 5, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-07 10:00:00', '2026-01-08 10:00:00', 'walk_in', NULL, NULL, NULL),
(18, '2026-01-12 11:00:00', 'Completed', 5, 6, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-11 11:00:00', '2026-01-12 11:00:00', 'walk_in', NULL, NULL, NULL),
(19, '2026-01-16 09:30:00', 'Cancelled', 12, 7, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-15 09:30:00', '2026-01-16 08:45:00', 'walk_in', 'Patient requested reschedule', NULL, NULL),
(20, '2026-01-21 13:00:00', 'Completed', 2, 8, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-20 13:00:00', '2026-01-21 13:00:00', 'walk_in', NULL, NULL, NULL),
(21, '2026-01-25 14:00:00', 'Completed', 10, 9, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-24 14:00:00', '2026-01-25 14:00:00', 'walk_in', NULL, NULL, NULL),
(22, '2026-01-29 10:30:00', 'Cancelled', 7, 10, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-01-28 10:30:00', '2026-01-29 09:20:00', 'walk_in', 'Clinic unavailable on requested slot', NULL, NULL),
(23, '2026-02-02 09:15:00', 'Completed', 1, 11, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-02-01 09:15:00', '2026-02-02 09:15:00', 'walk_in', NULL, NULL, NULL),
(24, '2026-02-06 11:30:00', 'Completed', 5, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-02-05 11:30:00', '2026-02-06 11:30:00', 'walk_in', NULL, NULL, NULL),
(25, '2026-02-10 15:00:00', 'Cancelled', 12, 13, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-02-09 15:00:00', '2026-02-10 13:10:00', 'walk_in', 'Patient cancelled due to personal conflict', NULL, NULL),
(31, '2026-03-29 09:00:00', 'Cancelled', 1, 14, NULL, 'Adrian', 'Legaspina', '2022-07-14', '9876543456', 'cerineo123@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-03-28 16:45:45', '2026-03-28 17:17:41', 'online_appointment', NULL, NULL, NULL),
(32, '2026-03-29 09:00:00', 'Cancelled', 2, NULL, NULL, 'Adrian', 'Adad', '2016-06-15', '9876543322', 'jeromebernacer123@gmail.com', 0, NULL, NULL, NULL, NULL, NULL, 'GUEST', '2026-03-28 16:48:12', '2026-03-28 16:50:19', 'online_appointment', NULL, 'Dad', NULL),
(33, '2026-03-28 11:00:00', 'Waiting', 18, 14, NULL, 'Adrian', 'Legaspina', '2022-07-14', '9876543456', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'soosickk1@gmail.com', '2026-03-28 17:06:32', '2026-03-28 17:15:22', 'walk_in', NULL, NULL, NULL);

INSERT INTO `dental_charts` (`id`, `patient_id`, `chart_data`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"teeth\":{\"11\":null,\"12\":null,\"13\":null,\"14\":{\"right\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"}},\"15\":{\"center\":{\"color\":\"red\",\"code\":\"CI\"},\"line_1\":{\"code\":\"CI\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Slight\",\"stains\":\"Moderate\",\"complete_denture\":\"Lower\",\"partial_denture\":\"Upper\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'michaeldavelegaspina@gmail.com', '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(10, 2, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}},\"26\":{\"whole_tooth\":{\"code\":\"AM\",\"color\":\"blue\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Historical chart entry from raw SQL seed.\",\"treatment_plan\":\"Observe tooth sensitivity and continue cleaning visits.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-03-27 09:10:00', '2026-03-27 09:10:00'),
(11, 2, '{\"teeth\":{\"16\":{\"top\":{\"code\":\"C\",\"color\":\"red\"},\"line_1\":{\"code\":\"LC\",\"color\":\"blue\"}},\"17\":{\"whole_tooth\":{\"code\":\"RF\",\"color\":\"green\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"No\",\"partial_denture\":\"No\"},\"comments\":{\"notes\":\"Completed visit record dated March 19, 2026.\",\"treatment_plan\":\"Monitor restored tooth and return for follow-up cleaning.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-03-19 10:05:00', '2026-03-19 10:05:00'),
(12, 4, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Slight\",\"stains\":\"Slight\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"January cleaning record.\",\"treatment_plan\":\"Continue routine hygiene visits.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-01-05 09:00:00', '2026-01-05 09:00:00'),
(13, 5, '{\"teeth\":{\"16\":{\"whole_tooth\":{\"code\":\"CC\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"Sensitivity check on upper right.\",\"treatment_plan\":\"Observe and review if pain persists.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-01-08 10:00:00', '2026-01-08 10:00:00'),
(14, 6, '{\"teeth\":{\"36\":{\"whole_tooth\":{\"code\":\"C\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"Small cavity restored on lower molar.\",\"treatment_plan\":\"Monitor restoration integrity.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-01-12 11:00:00', '2026-01-12 11:00:00'),
(15, 8, '{\"teeth\":{\"47\":{\"whole_tooth\":{\"code\":\"X\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Poor\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"Moderate\",\"stains\":\"Light\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"Non-restorable tooth extracted.\",\"treatment_plan\":\"Monitor healing and review replacement options.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-01-21 13:00:00', '2026-01-21 13:00:00'),
(16, 9, '{\"teeth\":{\"14\":{\"whole_tooth\":{\"code\":\"RF\",\"color\":\"green\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Slight\",\"stains\":\"Minimal\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"Fractured cusp restored with crown workup.\",\"treatment_plan\":\"Review final crown placement.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-01-25 14:00:00', '2026-01-25 14:00:00'),
(17, 11, '{\"teeth\":{\"11\":{\"top\":{\"code\":\"C\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Good\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Slight\",\"stains\":\"Slight\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"Routine February hygiene visit.\",\"treatment_plan\":\"Maintain brushing and flossing.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-02-02 09:15:00', '2026-02-02 09:15:00'),
(18, 12, '{\"teeth\":{\"36\":{\"whole_tooth\":{\"code\":\"RF\",\"color\":\"green\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Fair\",\"gingiva\":\"Healthy\",\"calcular_deposits\":\"Light\",\"stains\":\"Minimal\",\"complete_denture\":\"None\",\"partial_denture\":\"None\"},\"comments\":{\"notes\":\"Filling follow-up with mild sensitivity.\",\"treatment_plan\":\"Observe for 2 weeks and reassess.\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'raw-sql-seed', '2026-02-06 11:30:00', '2026-02-06 11:30:00'),
(19, 14, '{\"teeth\":{\"11\":null,\"12\":null,\"13\":null,\"14\":null,\"15\":null,\"16\":{\"top\":{\"color\":\"blue\",\"code\":\"GIC\"},\"line_1\":{\"code\":\"GIC\",\"color\":\"blue\"}},\"17\":{\"center\":{\"color\":\"red\",\"code\":\"CD\"},\"line_1\":{\"code\":\"CD\",\"color\":\"red\"}}},\"oral_exam\":{\"oral_hygiene_status\":\"Excellent\",\"gingiva\":\"Mildly Inflamed\",\"calcular_deposits\":\"None\",\"stains\":\"Slight\",\"complete_denture\":\"Upper\",\"partial_denture\":\"Lower\"},\"comments\":{\"notes\":\"\",\"treatment_plan\":\"\"},\"meta\":{\"dentition_type\":\"adult\",\"numbering_system\":\"FDI\"}}', 'soosickk1@gmail.com', '2026-03-28 17:11:45', '2026-03-28 17:11:45');
INSERT INTO `health_histories` (`id`, `patient_id`, `when_last_visit_q1`, `what_last_visit_reason_q1`, `what_seeing_dentist_reason_q2`, `is_clicking_jaw_q3a`, `is_pain_jaw_q3b`, `is_difficulty_opening_closing_q3c`, `is_locking_jaw_q3d`, `is_clench_grind_q4`, `is_bad_experience_q5`, `is_nervous_q6`, `what_nervous_concern_q6`, `is_condition_q1`, `what_condition_reason_q1`, `is_hospitalized_q2`, `what_hospitalized_reason_q2`, `is_serious_illness_operation_q3`, `what_serious_illness_operation_reason_q3`, `is_taking_medications_q4`, `what_medications_list_q4`, `is_allergic_medications_q5`, `what_allergies_list_q5`, `is_allergic_latex_rubber_metals_q6`, `is_pregnant_q7`, `is_breast_feeding_q8`, `modified_by`, `created_at`, `updated_at`, `is_chest_pain_angina`, `is_shortness_of_breath`, `is_heart_disease_heart_attack`, `is_heart_surgery`, `is_artificial_heart_valve_pacemaker`, `is_rheumatic_fever_heart_disease`, `is_heart_murmur`, `is_mitral_valve_prolapse`, `is_high_low_blood_pressure`, `is_stroke`, `is_respiratory_lung_problem`, `is_emphysema`, `is_asthma`, `is_tuberculosis`, `is_blood_disease`, `is_bleeding_problems_disorders`, `is_diabetes`, `is_liver_problem_jaundice_hepatitis`, `is_kidney_bladder_problem`, `is_ulcers_hyperacidity`, `is_tumors_cancer_malignancies`, `is_aids_hiv_positive`, `is_fainting_epilepsy_seizures`, `is_mental_health_disorder`, `is_other_disease_condition_problem`, `what_other_disease_condition_problem`) VALUES
(1, 1, '2003-07-08', 'Cleaning', 'Check up', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'michaeldavelegaspina@gmail.com', '2026-03-28 10:48:49', '2026-03-28 10:48:49', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, ''),
(10, 2, '2026-02-10', 'Cleaning', 'Tooth sensitivity on upper left side', 0, 1, 0, 0, 1, 0, 0, '', 0, '', 0, '', 0, '', 1, 'Paracetamol as needed', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-03-27 09:05:00', '2026-03-27 09:05:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(11, 2, '2026-02-20', 'Routine dental check-up', 'Patient reported mild toothache on upper right side', 0, 1, 0, 0, 1, 0, 0, '', 0, '', 0, '', 0, '', 1, 'Ibuprofen as needed', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-03-19 10:05:00', '2026-03-19 10:05:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(12, 4, '2025-12-06', 'Routine dental check-up', 'Routine prophylaxis and oral exam', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-01-05 09:00:00', '2026-01-05 09:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(13, 5, '2025-12-09', 'Routine dental check-up', 'Mild tooth sensitivity upper right', 0, 1, 0, 0, 1, 0, 0, '', 0, '', 0, '', 0, '', 1, 'Ibuprofen as needed', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-01-08 10:00:00', '2026-01-08 10:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(14, 6, '2025-12-13', 'Routine dental check-up', 'Small cavity lower molar', 0, 1, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-01-12 11:00:00', '2026-01-12 11:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(15, 8, '2025-12-22', 'Routine dental check-up', 'Pain while chewing lower left side', 0, 1, 0, 0, 1, 0, 1, 'Slightly anxious about extraction', 0, '', 0, '', 0, '', 1, 'Amoxicillin', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-01-21 13:00:00', '2026-01-21 13:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(16, 9, '2025-12-26', 'Routine dental check-up', 'Broken cusp on upper premolar', 0, 1, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-01-25 14:00:00', '2026-01-25 14:00:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(17, 11, '2026-01-03', 'Routine dental check-up', 'Routine hygiene maintenance', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-02-02 09:15:00', '2026-02-02 09:15:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(18, 12, '2026-01-07', 'Routine dental check-up', 'Sensitivity around restored tooth', 0, 1, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'raw-sql-seed', '2026-02-06 11:30:00', '2026-02-06 11:30:00', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL),
(19, 14, '2026-03-28', 'Hagdsa', 'Asdewrfw', 0, 0, 0, 0, 0, 0, 0, '', 0, '', 0, '', 0, '', 0, '', 0, '', 0, 0, 0, 'soosickk1@gmail.com', '2026-03-28 17:11:45', '2026-03-28 17:11:45', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '');



INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('romnickacervo06@gmail.com', 'VLPNjJkQ7sl2WZZ31BfwIhygj88akQ3VeYerMBRIvAA6DY0oA2thxlUmTFCzJBag', '2026-03-28 13:47:46');

INSERT INTO `patients` (`id`, `last_name`, `first_name`, `mobile_number`, `middle_name`, `nickname`, `occupation`, `birth_date`, `gender`, `civil_status`, `home_address`, `office_address`, `home_number`, `office_number`, `email_address`, `referral`, `emergency_contact_name`, `emergency_contact_number`, `relationship`, `who_answering`, `relationship_to_patient`, `father_name`, `father_number`, `mother_name`, `mother_number`, `guardian_name`, `guardian_number`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 'Legaspina', 'Michael Dave', '9779234567', NULL, NULL, NULL, '2003-07-08', NULL, NULL, NULL, NULL, NULL, NULL, 'mdlwork8@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'michaeldavelegaspina@gmail.com', '2026-03-28 10:45:43', '2026-03-28 10:45:43'),
(2, 'Rosales', 'Juan', '09171234567', 'Dela Cruz', 'Juan', 'Student', '2002-05-10', 'Male', 'Single', 'Sample Address', NULL, NULL, NULL, 'juan@example.com', 'Walk-in', 'Maria Rosales', '09179876543', 'Mother', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-27 08:00:00', '2026-03-27 08:00:00'),
(3, 'Acervo', 'Roamer', '9134344346', 'Bascara', NULL, NULL, '2003-06-17', NULL, NULL, NULL, NULL, NULL, NULL, 'smurfojt@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'romnickacervo03@gmail.com', '2026-03-28 11:20:51', '2026-03-28 11:20:51'),
(4, 'Dela Cruz', 'Juan', '09170010001', 'Santos', 'Juan', 'Teacher', '1998-01-14', 'Male', 'Single', 'Blk 1 Lot 2 Sample St.', NULL, NULL, NULL, 'seed.patient01@example.com', 'raw-sql-seed', 'Emergency Contact 1', '09180020001', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(5, 'Reyes', 'Maria', '09170010002', 'Lopez', 'Maria', 'Nurse', '1997-03-02', 'Female', 'Single', 'Blk 2 Lot 3 Sample St.', NULL, NULL, NULL, 'seed.patient02@example.com', 'raw-sql-seed', 'Emergency Contact 2', '09180020002', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(6, 'Garcia', 'Paolo', '09170010003', 'Rivera', 'Paolo', 'Engineer', '1995-05-19', 'Male', 'Married', 'Blk 3 Lot 4 Sample St.', NULL, NULL, NULL, 'seed.patient03@example.com', 'raw-sql-seed', 'Emergency Contact 3', '09180020003', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(7, 'Torres', 'Andrea', '09170010004', 'Mendoza', 'Andrea', 'Student', '2000-07-11', 'Female', 'Single', 'Blk 4 Lot 5 Sample St.', NULL, NULL, NULL, 'seed.patient04@example.com', 'raw-sql-seed', 'Emergency Contact 4', '09180020004', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(8, 'Aquino', 'Miguel', '09170010005', 'Fernandez', 'Miguel', 'Driver', '1996-02-23', 'Male', 'Single', 'Blk 5 Lot 6 Sample St.', NULL, NULL, NULL, 'seed.patient05@example.com', 'raw-sql-seed', 'Emergency Contact 5', '09180020005', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(9, 'Bautista', 'Camille', '09170010006', 'Ramos', 'Camille', 'Cashier', '1999-09-08', 'Female', 'Single', 'Blk 6 Lot 7 Sample St.', NULL, NULL, NULL, 'seed.patient06@example.com', 'raw-sql-seed', 'Emergency Contact 6', '09180020006', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(10, 'Navarro', 'Joshua', '09170010007', 'Cruz', 'Joshua', 'Mechanic', '1994-12-01', 'Male', 'Married', 'Blk 7 Lot 8 Sample St.', NULL, NULL, NULL, 'seed.patient07@example.com', 'raw-sql-seed', 'Emergency Contact 7', '09180020007', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(11, 'Santos', 'Nicole', '09170010008', 'Perez', 'Nicole', 'Student', '2001-04-16', 'Female', 'Single', 'Blk 8 Lot 9 Sample St.', NULL, NULL, NULL, 'seed.patient08@example.com', 'raw-sql-seed', 'Emergency Contact 8', '09180020008', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(12, 'Villanueva', 'Adrian', '09170010009', 'Diaz', 'Adrian', 'Supervisor', '1993-10-10', 'Male', 'Married', 'Blk 9 Lot 10 Sample St.', NULL, NULL, NULL, 'seed.patient09@example.com', 'raw-sql-seed', 'Emergency Contact 9', '09180020009', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(13, 'Castillo', 'Patricia', '09170010010', 'Luna', 'Patricia', 'Freelancer', '1992-06-28', 'Female', 'Single', 'Blk 10 Lot 11 Sample St.', NULL, NULL, NULL, 'seed.patient10@example.com', 'raw-sql-seed', 'Emergency Contact 10', '09180020010', 'Sibling', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'raw-sql-seed', '2026-03-28 05:03:02', '2026-03-28 05:03:02'),
(14, 'Legaspina', 'Adrian', '9876543456', '', '', 'Engineer', '2022-07-14', 'Male', 'Sda', 'Demo address 20, tejada city', '', '', '', 'cerineo123@gmail.com', '', 'Joel Cerineo', '9725367823', 'Sibling', 'Dsa', 'Dsa', 'Ddfr', '7627864783', 'Dfref', '', 'Sdfg', '', 'soosickk1@gmail.com', '2026-03-28 17:01:14', '2026-03-28 09:03:32');
INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'dentist'),
(2, 'staff'),
(3, 'patient'),
(4, 'admin');
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
(16, 'Dental Filling', '01:00:00'),
(17, 'Tooth Extraction (Simple)', '01:00:00'),
(18, 'Tooth Extraction (Surgical)', '02:00:00'),
(19, 'Wisdom Tooth Removal', '02:00:00'),
(21, 'Dental Sealants', '01:00:00'),
(22, 'Mouth Guard Fitting', '01:00:00'),
(23, 'Retainer Fitting', '01:00:00'),
(24, 'Orthodontic Consultation', '01:00:00'),
(25, 'Braces Installation', '02:00:00'),
(26, 'Braces Removal', '01:00:00'),
(27, 'Dental Bridge Placement', '02:00:00'),
(28, 'Implant Consultation', '01:00:00'),
(29, 'Dental Implant Placement', '02:00:00'),
(30, 'Gum Treatment', '02:00:00'),
(31, 'Emergency Dental Treatment', '01:00:00'),
(32, 'X-ray and Diagnosis', '01:00:00'),
(33, 'Oral Surgery Minor', '02:00:00'),
(34, 'Cosmetic Bonding', '01:00:00');
INSERT INTO `treatment_record_images` (`id`, `treatment_record_id`, `image_path`, `image_type`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'treatment-records/before/2026/03/byY2xVLjpz03fmzPKbhxLS7Q2VbCrofgoUVGDUz2.png', 'before', 0, '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(2, 1, 'treatment-records/after/2026/03/ZdpYf4bD2XJfpuNcyEn5mvl4ZDaqdXNl090tcJ3H.png', 'after', 1, '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(3, 19, 'treatment-records/before/2026/03/WaxWbGrFjmAijG4GuRWC4J7dLwpsxsq3HXnsiW8K.png', 'before', 0, '2026-03-28 17:11:45', '2026-03-28 17:11:45'),
(4, 19, 'treatment-records/after/2026/03/vGgKXICLBrQa1jS5GkOkSojgGChzK2vAsauDQWEX.png', 'after', 1, '2026-03-28 17:11:45', '2026-03-28 17:11:45'),
(5, 19, 'treatment-records/before/2026/03/dDAkBPv4MnZtTfwdrMYxlqjl8JjTcB82Otp473il.png', 'before', 2, '2026-03-28 17:13:23', '2026-03-28 17:13:23');
INSERT INTO `treatment_records` (`id`, `patient_id`, `dental_chart_id`, `dmd`, `treatment`, `cost_of_treatment`, `amount_charged`, `remarks`, `image`, `modified_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Michael Legaspina', 'Cleaning, Dental Check-up', '1500.00', '1000.00', '', NULL, 'michaeldavelegaspina@gmail.com', '2026-03-28 10:48:49', '2026-03-28 10:48:49'),
(10, 2, 10, 'Dr. Demo Dentist, DMD', 'Oral prophylaxis completed', '500.00', '900.00', 'Scaling and polishing done. Advised desensitizing toothpaste.', NULL, 'raw-sql-seed', '2026-03-27 09:15:00', '2026-03-27 09:15:00'),
(11, 2, 11, 'Dr. Demo Dentist, DMD', 'Composite restoration on upper molar', '900.00', '1800.00', 'Restoration completed successfully. Occlusion checked and adjusted.', NULL, 'raw-sql-seed', '2026-03-19 10:15:00', '2026-03-19 10:15:00'),
(12, 4, 12, 'Dr. Demo Dentist, DMD', 'Oral prophylaxis completed', '450.00', '900.00', 'Scaling and polishing completed.', NULL, 'raw-sql-seed', '2026-01-05 09:00:00', '2026-01-05 09:00:00'),
(13, 5, 13, 'Dr. Demo Dentist, DMD', 'Comprehensive oral consultation', '250.00', '650.00', 'Advised fluoride toothpaste and follow-up.', NULL, 'raw-sql-seed', '2026-01-08 10:00:00', '2026-01-08 10:00:00'),
(14, 6, 14, 'Dr. Demo Dentist, DMD', 'Composite filling completed', '700.00', '1500.00', 'Restoration done with shade matching.', NULL, 'raw-sql-seed', '2026-01-12 11:00:00', '2026-01-12 11:00:00'),
(15, 8, 15, 'Dr. Demo Dentist, DMD', 'Tooth extraction completed', '850.00', '1800.00', 'Extraction completed with post-op instructions.', NULL, 'raw-sql-seed', '2026-01-21 13:00:00', '2026-01-21 13:00:00'),
(16, 9, 16, 'Dr. Demo Dentist, DMD', 'Dental crown placement completed', '3200.00', '5000.00', 'Crown placed and bite adjusted.', NULL, 'raw-sql-seed', '2026-01-25 14:00:00', '2026-01-25 14:00:00'),
(17, 11, 17, 'Dr. Demo Dentist, DMD', 'Oral prophylaxis completed', '450.00', '900.00', 'Routine cleaning completed.', NULL, 'raw-sql-seed', '2026-02-02 09:15:00', '2026-02-02 09:15:00'),
(18, 12, 18, 'Dr. Demo Dentist, DMD', 'Composite restoration on lower molar', '650.00', '1400.00', 'Margins checked; no fracture noted.', NULL, 'raw-sql-seed', '2026-02-06 11:30:00', '2026-02-06 11:30:00'),
(19, 14, 19, 'Gege Dayoo', 'Cleaning', '2000.00', '2000.00', '', NULL, 'soosickk1@gmail.com', '2026-03-28 17:11:45', '2026-03-28 17:13:23');
INSERT INTO `users` (`id`, `username`, `first_name`, `middle_name`, `last_name`, `email`, `mobile_number`, `birth_date`, `patient_id`, `email_verified_at`, `verification_token`, `password`, `google_id`, `role`, `created_at`, `updated_at`) VALUES
(81, 'soosickk1@gmail.com', 'Gege', NULL, 'Dayoo', 'soosickk1@gmail.com', NULL, NULL, NULL, '2026-03-23 19:24:33', NULL, '$2y$12$Pg6QF9sGtAD5iPLQ.Dr42uFVKEIOHiQoeIBMhANUWUpZJoELq64Le', NULL, 1, '2026-03-23 19:24:09', '2026-03-28 16:39:04'),
(116, 'nicolasjhonstephen22@gmail.com', 'Jhon Stephen', NULL, 'Nicolas', 'nicolasjhonstephen22@gmail.com', '9216456949', '2022-06-02', NULL, '2026-03-27 15:38:42', NULL, '$2y$12$zsndgrYodu3Ls0vMhWmuZebqJnS060DV31UhU7Oy3xH8eUWU/hHRS', '104044564708538367717', 2, '2026-03-27 15:38:42', '2026-03-27 14:32:38'),
(118, 'romnickacervo03@gmail.com', 'Romnick', 'Bascara', 'Acervo', 'romnickacervo03@gmail.com', '9234354664', '2003-06-17', NULL, '2026-03-28 01:24:11', NULL, '$2y$12$tnk2Mkn1qgZso2BnMtBNAOlJngeKYz1cNHCWLYNGjN6oR4Jx0GNLq', '111903548734390929984', 2, '2026-03-27 17:09:49', '2026-03-28 03:09:13'),
(119, 'cerineo123@gmail.com', 'Joel', NULL, 'Cerineo', 'cerineo123@gmail.com', '9743456789', '2022-03-02', NULL, '2026-03-27 17:12:17', NULL, '$2y$12$VKLFgK4t6.dgPAjPFc5x7u6CRs3/magOBZ2WzwRDOBPlKa4cEDybW', '113587128745289601695', 4, '2026-03-27 17:12:17', '2026-03-27 18:03:29'),
(120, 'ytrevenger@gmail.com', 'Clarenz', 'Luigi', 'Rosales', 'ytrevenger@gmail.com', '9909909990', '2026-03-26', NULL, '2026-03-27 17:18:26', NULL, '$2y$12$BEB39O8JXUGcn3d5HjcbtuB/2/hNizOhMtPVWp6bl.BiOOYlNfbE2', '109863628210558334584', 3, '2026-03-27 17:18:26', '2026-03-27 17:19:03'),
(123, 'jayzelcepres@gmail.com', 'Jayzel Anne', NULL, 'Cepres', 'jayzelcepres@gmail.com', '9876543456', NULL, NULL, '2026-03-27 21:21:47', NULL, '$2y$12$OlTuoWESrB10eToLls0ET.bUHbE1yaewX5BaVaMWdsKrWpwH590hy', NULL, 2, '2026-03-27 21:20:59', '2026-03-27 21:21:47'),
(127, 'mdlwork8@gmail.com', 'Michael Dave', 'Dela Vega', 'Legaspina', 'mdlwork8@gmail.com', '9779234567', '2003-07-08', 1, '2026-03-28 06:36:47', NULL, '$2y$12$2X9VbA3ZKS27v1pyZmVn3.MyqnVE612sz11O9.kPf0DlBp5fOPuv.', '118400836313794967910', 2, '2026-03-28 06:36:47', '2026-03-28 16:37:10'),
(131, 'smurfojt@gmail.com', 'Roamer', 'Bascara', 'Acervo', 'smurfojt@gmail.com', '9134344346', '2003-06-17', 3, '2026-03-28 11:16:50', NULL, '$2y$12$VU0De3aha2OgleGdL4mDeOiEDeXP9TKAZ.ADURoUyjuQbzrypj6I2', '107236374911057510900', 3, '2026-03-28 11:16:50', '2026-03-28 11:20:51'),
(132, 'romnickacervo06@gmail.com', NULL, NULL, NULL, 'romnickacervo06@gmail.com', NULL, NULL, NULL, '2026-03-28 13:47:35', NULL, '$2y$12$WZQ8O0hdcFFzKfeOlbibEup6dnm7JLqp7RrgDRJtGtaz8BP6rjYuy', '113829808128296061380', 3, '2026-03-28 13:47:35', '2026-03-28 13:47:35'),
(133, 'kijac84552@izkat.com', 'Allan', NULL, 'Tejada', 'kijac84552@izkat.com', '9876456782', NULL, NULL, NULL, '85k1158fKzFPuDub9jnUyCeDPQ5IEbSwyGdvvdmW0ncAFA3fi6ZxzttsWymCsbBL', '$2y$12$bhcc9jHB/4qIlxohWY7cbOE39E.t9Hq5GZzsMhmxpobN52TEj6p3S', NULL, 1, '2026-03-28 15:17:35', '2026-03-28 15:19:22'),
(134, 'rmnckacervo@gmail.com', NULL, NULL, NULL, 'rmnckacervo@gmail.com', NULL, NULL, NULL, '2026-03-28 16:54:00', NULL, '$2y$12$8OTZYlhPOiIsuxQ8pmq6hO5LdNVcEFUV2s3o2rjQ8/IbMZzHZ50ia', '105076369519288137459', 3, '2026-03-28 16:54:00', '2026-03-28 16:55:06'),
(135, 'renzzluigi@gmail.com', NULL, NULL, NULL, 'renzzluigi@gmail.com', NULL, NULL, NULL, '2026-04-14 18:38:10', NULL, '$2y$12$9uOTCiBJJAh9euzmbZFgLOofHCsA4Seky8HZdZ9n.oiaNRddYmC.y', '114082662441983874861', 4, '2026-04-14 18:38:10', '2026-04-14 10:40:32');


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;