-- =====================================================================
-- DRAMS - Multi Number Request  |  branch: new_custom_request
-- Target: MariaDB 10.4 / MySQL 5.7+   Database: aiesplus
-- Charset/engine follow the existing `user_request` table (InnoDB, utf8mb4_unicode_ci)
--
-- A submission groups many mobile numbers; every number becomes an ordinary
-- user_request row (one Telco email each) that the existing send / receive /
-- parse crons process. These tables only record the grouping, so no existing
-- table is altered.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. One row per submission.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_request_batch` (
  `batch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_request_type_id` int(10) unsigned NOT NULL COMMENT 'Request type chosen for the batch (email_templates_type.id)',
  `company_name` int(2) NOT NULL COMMENT 'MNC (Mobile Network Value)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`batch_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. One row per individual request created for a number (plus one row,
--    without a request, for every number that was skipped).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_request_batch_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` int(10) unsigned NOT NULL,
  `requested_value` varchar(25) NOT NULL COMMENT 'Mobile number, 10 digits (3XXXXXXXXX)',
  `request_id` int(10) unsigned DEFAULT NULL COMMENT 'user_request.request_id, NULL when the number was skipped',
  `is_auto_subscriber` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '1=Subscriber request queued automatically to fill Name/CNIC',
  `profile_created` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '1=profile without Name/CNIC created by this batch',
  `note` varchar(255) DEFAULT NULL COMMENT 'Skip reason, or why no Subscriber request was queued',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_request_id` (`request_id`),
  KEY `idx_batch_id` (`batch_id`),
  KEY `idx_requested_value` (`requested_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Per-user rights, assigned in Access Control List -> Manage Rights:
--    13 sends Multi Number Requests, 14 opens the batch report.
--    The ids are referenced by Model_Multirequest::ACCESS_TYPE_ID and
--    REPORT_ACCESS_TYPE_ID. If one is already taken on the target server
--    this INSERT fails on the primary key: pick a free id and change the
--    constant to match.
-- ---------------------------------------------------------------------
INSERT INTO `lu_user_access_type` (`id`, `label`, `internal_name`, `description`)
VALUES (13, 'Multi Number Request', 'multi_number_request', 'Send one Telco request per mobile number for a list of numbers'),
       (14, 'Multi Number Request Report', 'multi_number_request_report', 'View the Multi Number Request batch report');

-- ---------------------------------------------------------------------
-- 4. Verification
-- ---------------------------------------------------------------------
SHOW CREATE TABLE `user_request_batch`;
SHOW CREATE TABLE `user_request_batch_item`;
SELECT `id`, `label`, `internal_name` FROM `lu_user_access_type` WHERE `id` IN (13, 14);

-- ---------------------------------------------------------------------
-- 5. Rollback (run manually only if the feature is withdrawn).
--    Requests already created by the feature are ordinary user_request rows
--    and stay in place.
-- ---------------------------------------------------------------------
-- DELETE FROM `user_access_matrix` WHERE `user_activity_type` IN (13, 14);
-- DELETE FROM `lu_user_access_type` WHERE `id` IN (13, 14);
-- DROP TABLE IF EXISTS `user_request_batch_item`;
-- DROP TABLE IF EXISTS `user_request_batch`;
