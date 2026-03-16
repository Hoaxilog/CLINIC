-- Safe patient-linking support for appointment requests
-- Run manually in phpMyAdmin (XAMPP), one statement at a time if preferred.

-- 1) Optional: store request birth date (improves matching quality)
ALTER TABLE appointments
ADD COLUMN requester_birth_date DATE NULL AFTER requester_last_name;

-- 2) Optional: track who reviewed a request and when
ALTER TABLE appointments
ADD COLUMN reviewed_by_user_id BIGINT UNSIGNED NULL AFTER requester_email,
ADD COLUMN reviewed_at DATETIME NULL AFTER reviewed_by_user_id,
ADD COLUMN review_notes VARCHAR(255) NULL AFTER reviewed_at;

-- 3) Performance indexes for safe matching and pending review screens
ALTER TABLE appointments
ADD INDEX idx_appointments_status_date (status, appointment_date),
ADD INDEX idx_appointments_requester_contact (requester_contact_number),
ADD INDEX idx_appointments_requester_email (requester_email);

ALTER TABLE patients
ADD INDEX idx_patients_mobile (mobile_number),
ADD INDEX idx_patients_email (email_address),
ADD INDEX idx_patients_name_birth (last_name, first_name, birth_date);
