-- =====================================================================
-- DRAMS - "Request Viewer Only" right
-- Target: MariaDB 10.4 / MySQL 5.7+   Database: aiesplus
--
-- A user holding this per-user right sees only User Requests ->
-- NADRA Requests / Family Tree Requests / Travel History in the sidebar.
-- DRAMS Plus (the send forms) and every other menu are hidden, whatever
-- the user's role (e.g. Technical Support) allows.
--
-- Looked up by internal_name ('request_viewer_only'), so the id may differ
-- between servers. Safe to run more than once.
-- =====================================================================

-- 1. The right (appears in Access Control List -> Manage Rights).
INSERT INTO `lu_user_access_type` (`label`, `internal_name`, `description`)
SELECT 'Request Viewer Only', 'request_viewer_only', 'Menu limited to NADRA, Family Tree and Travel History request lists'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `lu_user_access_type` WHERE `internal_name` = 'request_viewer_only');

-- 2. Grant it to irumkalsoom (or tick it in Manage Rights instead).
INSERT INTO `user_access_matrix` (`user_id`, `user_activity_type`, `permission`, `created_by`, `created_at`)
SELECT u.`id`, t.`id`, 1, NULL, NOW()
FROM `users` u
JOIN `lu_user_access_type` t ON t.`internal_name` = 'request_viewer_only'
WHERE u.`username` = 'irumkalsoom'
  AND NOT EXISTS (SELECT 1 FROM `user_access_matrix` m
                  WHERE m.`user_id` = u.`id` AND m.`user_activity_type` = t.`id`);

-- 3. Verification
SELECT u.`username`, t.`internal_name`, m.`permission`
FROM `user_access_matrix` m
JOIN `lu_user_access_type` t ON t.`id` = m.`user_activity_type` AND t.`internal_name` = 'request_viewer_only'
JOIN `users` u ON u.`id` = m.`user_id`;

-- 4. Rollback
-- DELETE m FROM `user_access_matrix` m
--   JOIN `lu_user_access_type` t ON t.`id` = m.`user_activity_type` AND t.`internal_name` = 'request_viewer_only';
-- DELETE FROM `lu_user_access_type` WHERE `internal_name` = 'request_viewer_only';
