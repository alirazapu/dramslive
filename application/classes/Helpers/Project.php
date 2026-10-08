<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Project access rules
 *
 * - Admin and Development Tech Support see and manage every project.
 * - Admin and regional tech support users with the "Project Management"
 *   right (Access Control List) can create projects; users with the right
 *   manage members of the projects they created.
 * - Everyone else sees only projects they created or are assigned to.
 */
class Helpers_Project {

    const RIGHT_INTERNAL_NAME = 'project_management';

    /* roles allowed to create projects: 1 = admin, 4 = regional tech support (rts) */
    public static $create_roles = array(1, 4);

    /* admin (permission 1) and development tech support (permission 5) */
    public static function is_super_user($user_id) {
        $permission = Helpers_Utilities::get_user_permission((int) $user_id);
        return ($permission == 1 || $permission == 5);
    }

    /* user holds the Project Management right */
    public static function has_project_right($user_id) {
        $user_id = (int) $user_id;
        $sql = "SELECT t1.permission
                FROM user_access_matrix AS t1
                join lu_user_access_type AS t2 on t2.id = t1.user_activity_type
                where t1.user_id = {$user_id} and t2.internal_name = '" . self::RIGHT_INTERNAL_NAME . "' and t1.permission = 1
                LIMIT 1";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return !empty($result);
    }

    /* can create new projects: admin or regional tech support role, with the Project Management right */
    public static function can_create($user_id) {
        $user_id = (int) $user_id;
        $sql = "SELECT role_id FROM roles_users where user_id = {$user_id} LIMIT 1";
        $role = DB::query(Database::SELECT, $sql)->execute()->current();
        if (empty($role) || !in_array((int) $role['role_id'], self::$create_roles, TRUE)) {
            return FALSE;
        }
        return self::has_project_right($user_id);
    }

    /* user created or is assigned to at least one project */
    public static function has_projects($user_id) {
        $user_id = (int) $user_id;
        $sql = "SELECT ip.id FROM int_projects AS ip
                where ip.created_by = {$user_id} or ip.id in (SELECT pm.project_id FROM int_project_members AS pm where pm.user_id = {$user_id})
                LIMIT 1";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return !empty($result);
    }

    /* can open the Projects list page */
    public static function can_list($user_id, $role_id) {
        return Helpers_Utilities::chek_role_access($role_id, 29) == 1 || self::can_create($user_id) || self::has_projects($user_id);
    }

    /* SQL condition restricting int_projects (aliased as $alias) to projects the user can see */
    public static function access_condition($user_id, $alias = 'ip') {
        if (self::is_super_user($user_id)) {
            return ' 1 ';
        }
        $user_id = (int) $user_id;
        return " ({$alias}.created_by = {$user_id} or {$alias}.id in (SELECT pm.project_id FROM int_project_members AS pm where pm.user_id = {$user_id})) ";
    }

    /* user can see / use the project */
    public static function can_view($user_id, $project_id) {
        $project_id = (int) $project_id;
        $condition = self::access_condition($user_id, 'ip');
        $sql = "SELECT ip.id FROM int_projects AS ip where ip.id = {$project_id} and {$condition} LIMIT 1";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return !empty($result);
    }

    /* user can open the project's request details (Project Management right + creator or member) */
    public static function can_view_details($user_id, $project_id) {
        if (self::is_super_user($user_id)) {
            return TRUE;
        }
        return self::has_project_right($user_id) && self::can_view($user_id, $project_id);
    }

    /* user can edit the project and manage its members */
    public static function can_manage($user_id, $project_id) {
        if (self::is_super_user($user_id)) {
            return TRUE;
        }
        $project_id = (int) $project_id;
        $sql = "SELECT created_by FROM int_projects where id = {$project_id} LIMIT 1";
        $project = DB::query(Database::SELECT, $sql)->execute()->current();
        if (empty($project) || (int) $project['created_by'] !== (int) $user_id) {
            return FALSE;
        }
        return self::has_project_right($user_id);
    }

    /* project record with creator name */
    public static function get_project($project_id) {
        $project_id = (int) $project_id;
        $sql = "SELECT ip.*, t2.name as region_name, u1.username as creator_username,
                       concat(ifnull(up.first_name, ''), ' ', ifnull(up.last_name, '')) as creator_name
                FROM int_projects AS ip
                left join region as t2 on t2.region_id = ip.region_id
                left join users as u1 on u1.id = ip.created_by
                left join users_profile as up on up.user_id = ip.created_by
                where ip.id = {$project_id} LIMIT 1";
        return DB::query(Database::SELECT, $sql)->execute()->current();
    }

    /* assigned members of a project */
    public static function get_members($project_id) {
        $project_id = (int) $project_id;
        $sql = "SELECT pm.user_id, pm.assigned_at, u1.username, up.first_name, up.last_name, up.job_title,
                       concat(ifnull(ab.first_name, ''), ' ', ifnull(ab.last_name, '')) as assigned_by_name
                FROM int_project_members AS pm
                join users as u1 on u1.id = pm.user_id
                left join users_profile as up on up.user_id = pm.user_id
                left join users_profile as ab on ab.user_id = pm.assigned_by
                where pm.project_id = {$project_id}
                order by pm.assigned_at desc";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    /* active users that can still be added to the project (for the member search box) */
    public static function search_users($project_id, $term) {
        $project_id = (int) $project_id;
        $term = Database::instance()->escape('%' . trim($term) . '%');
        $sql = "SELECT u1.id, u1.username, up.first_name, up.last_name, up.job_title
                FROM users AS u1
                join users_profile as up on up.user_id = u1.id
                where u1.is_active = 1
                and u1.username not like '%::transferred%'
                and u1.id != (SELECT created_by FROM int_projects where id = {$project_id})
                and u1.id not in (SELECT user_id FROM int_project_members where project_id = {$project_id})
                and (u1.username like {$term} or concat(up.first_name, ' ', up.last_name) like {$term} or up.job_title like {$term})
                order by up.first_name
                LIMIT 20";
        return DB::query(Database::SELECT, $sql)->execute()->as_array();
    }

    public static function add_member($project_id, $user_id, $assigned_by) {
        $project_id = (int) $project_id;
        $user_id = (int) $user_id;
        $assigned_by = (int) $assigned_by;
        $date = date('Y-m-d H:i:s');
        $sql = "INSERT IGNORE INTO int_project_members (project_id, user_id, assigned_by, assigned_at)
                VALUES ({$project_id}, {$user_id}, {$assigned_by}, '{$date}')";
        return DB::query(Database::INSERT, $sql)->execute();
    }

    public static function remove_member($project_id, $user_id) {
        return DB::delete('int_project_members')
                        ->where('project_id', '=', (int) $project_id)
                        ->and_where('user_id', '=', (int) $user_id)
                        ->execute();
    }

}
