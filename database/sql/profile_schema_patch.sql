-- Direct database patch for the existing clinic_online schema.
-- Use this if you want to update the database manually instead of running Laravel migrations.
-- Tested against MariaDB/MySQL-style syntax used by your dump.

USE `clinic_online`;

ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `profile_picture` VARCHAR(255) NULL AFTER `email`,
    ADD COLUMN IF NOT EXISTS `profile_background_color` VARCHAR(20) NOT NULL DEFAULT '#0b84d8' AFTER `profile_picture`,
    ADD COLUMN IF NOT EXISTS `first_name` VARCHAR(255) NULL AFTER `username`,
    ADD COLUMN IF NOT EXISTS `last_name` VARCHAR(255) NULL AFTER `first_name`,
    ADD COLUMN IF NOT EXISTS `bio` TEXT NULL AFTER `profile_background_color`,
    ADD COLUMN IF NOT EXISTS `phone` VARCHAR(50) NULL AFTER `bio`,
    ADD COLUMN IF NOT EXISTS `position` VARCHAR(100) NULL AFTER `phone`,
    ADD COLUMN IF NOT EXISTS `contact` VARCHAR(20) NULL AFTER `position`;

ALTER TABLE `patients`
    ADD COLUMN IF NOT EXISTS `user_id` INT(11) NULL AFTER `id`;

SET @has_patients_user_id_index := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'patients'
      AND index_name = 'idx_patients_user_id'
);

SET @create_patients_user_id_index := IF(
    @has_patients_user_id_index = 0,
    'CREATE INDEX `idx_patients_user_id` ON `patients` (`user_id`)',
    'SELECT 1'
);

PREPARE stmt_patients_user_id_index FROM @create_patients_user_id_index;
EXECUTE stmt_patients_user_id_index;
DEALLOCATE PREPARE stmt_patients_user_id_index;

-- Backfill patient-to-user links using email when possible.
UPDATE `patients` p
JOIN `users` u
    ON (
        LOWER(TRIM(p.`email_address`)) = LOWER(TRIM(u.`email`))
        OR LOWER(TRIM(p.`email_address`)) = LOWER(TRIM(u.`username`))
    )
SET p.`user_id` = u.`id`
WHERE p.`user_id` IS NULL
  AND p.`email_address` IS NOT NULL
  AND TRIM(p.`email_address`) <> '';
