-- =====================================================================
-- DRAMS - "DRAMS Databank" right  |  branch: enable_databank_person_profile
-- Target: MariaDB 10.4 / MySQL 5.7+   Database: aiesplus
--
-- The DRAMS Databank menu and page (/databank/all_sources) are shown to
-- users holding this per-user right, granted in Access Control List ->
-- Manage Rights (which lists every row of lu_user_access_type). Before
-- this right the page borrowed the role menus 34 "Identity Breakup" and
-- 35 "Verisys Pending" (roles admin, ts, devts) plus users 170 / 171.
--
-- The code finds the right by internal_name ('drams_databank',
-- Helpers_Databank::RIGHT_INTERNAL_NAME), so its auto-increment id may
-- differ between servers. Every statement can be run more than once.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. The right.
-- ---------------------------------------------------------------------
INSERT INTO `lu_user_access_type` (`label`, `internal_name`, `description`)
SELECT 'DRAMS Databank', 'drams_databank', 'Search all Databank sources (DRAMS Databank menu)'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `lu_user_access_type` WHERE `internal_name` = 'drams_databank');

-- ---------------------------------------------------------------------
-- 2. Keep access for the users who had it before the right existed
--    (roles with menu 34 or 35, and users 170 / 171), so nobody loses the
--    page on deployment. Skip this step to start with no one; grants and
--    removals are then made per user in Manage Rights.
-- ---------------------------------------------------------------------
INSERT INTO `user_access_matrix` (`user_id`, `user_activity_type`, `permission`, `created_by`, `created_at`)
SELECT DISTINCT u.`id`, t.`id`, 1, NULL, NOW()
FROM `users` u
JOIN `lu_user_access_type` t ON t.`internal_name` = 'drams_databank'
LEFT JOIN `roles_users` r ON r.`user_id` = u.`id`
WHERE u.`is_deleted` = 0
  AND (r.`role_id` IN (SELECT `role_id` FROM `manu_management` WHERE `manu_id` IN (34, 35) AND `access_status` = 1)
       OR u.`id` IN (170, 171))
  AND NOT EXISTS (SELECT 1 FROM `user_access_matrix` m
                  WHERE m.`user_id` = u.`id` AND m.`user_activity_type` = t.`id`);

-- ---------------------------------------------------------------------
-- 3. Verification
-- ---------------------------------------------------------------------
SELECT `id`, `label`, `internal_name` FROM `lu_user_access_type` WHERE `internal_name` = 'drams_databank';
SELECT COUNT(*) AS users_with_databank
FROM `user_access_matrix` m
JOIN `lu_user_access_type` t ON t.`id` = m.`user_activity_type` AND t.`internal_name` = 'drams_databank'
WHERE m.`permission` = 1;

-- ---------------------------------------------------------------------
-- 4. Rollback (run manually only if the right is withdrawn; the menu and
--    page are then shown to no one until the code is reverted too).
-- ---------------------------------------------------------------------
-- DELETE m FROM `user_access_matrix` m
--   JOIN `lu_user_access_type` t ON t.`id` = m.`user_activity_type` AND t.`internal_name` = 'drams_databank';
-- DELETE FROM `lu_user_access_type` WHERE `internal_name` = 'drams_databank';
