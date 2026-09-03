-- =====================================================================
-- DRAMS - Login OTP (WhatsApp + e-mail fallback)  |  branch: opt_login
-- Target: MariaDB 10.4 / MySQL 5.7+   Database: aiesplus
-- Charset/engine follow the existing `users` table (InnoDB, utf8mb4_unicode_ci)
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. OTP opt-in flag  [REQUIRED BY THE OTP FEATURE]
--    Read by Controller_Login::action_check to decide whether a password
--    login must be followed by OTP verification.
-- ---------------------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `is_login_otp_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
  COMMENT '1=require OTP verification after password login, 0=No'
  AFTER `is_password_changed`;

-- ---------------------------------------------------------------------
-- 2. Columns required by the OTHER two features committed in the same
--    branch (account lockout + single-session). The branch code reads
--    them on every login and on every authenticated page, so the
--    application throws a fatal error without them.
-- ---------------------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `failed_login_attempts` INT(11) NOT NULL DEFAULT 0
  COMMENT 'Consecutive failed password attempts; account disabled at 3';

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `current_session_token` VARCHAR(64) NULL DEFAULT NULL
  COMMENT 'Active single-session token; older sessions are logged out';

-- ---------------------------------------------------------------------
-- 3. Data: enable OTP for user_id = 1 only.
--    Every other account keeps the column default (0) and therefore the
--    original login behaviour - no existing user is forced into OTP.
-- ---------------------------------------------------------------------
UPDATE `users` SET `is_login_otp_enabled` = 1 WHERE `id` = 1;

-- Safety net for re-runs on an instance where the column already existed
-- with unintended values: leave user 1 enabled, everyone else untouched
-- unless they were never explicitly enabled.
-- (No blanket reset - deliberately omitted so manual opt-ins survive.)

-- ---------------------------------------------------------------------
-- 4. user_id = 1 must have a deliverable WhatsApp number, otherwise the
--    OTP cannot be sent and login falls back to e-mail (users.email).
--    Verify - do NOT blindly overwrite:
--      SELECT u.id, u.email, p.mobile_number
--        FROM users u LEFT JOIN users_profile p ON p.user_id = u.id
--       WHERE u.id = 1;
--    If blank, set it (local/national format, digits only):
--      UPDATE users_profile SET mobile_number = '<ADMIN_MOBILE>' WHERE user_id = 1;
-- ---------------------------------------------------------------------

-- ---------------------------------------------------------------------
-- 5. Verification
-- ---------------------------------------------------------------------
SELECT `id`, `username`, `is_login_otp_enabled`, `is_active`, `failed_login_attempts`
  FROM `users` WHERE `id` = 1;
SELECT `is_login_otp_enabled`, COUNT(*) AS accounts
  FROM `users` GROUP BY `is_login_otp_enabled`;
