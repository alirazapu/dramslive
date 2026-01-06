<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Adminreports {
    /* Verisy response Reports Ajax Call Data */

    public static function verisys_response_summary($data, $count) {
        /* Posted Data */
//        echo '<pre>';
//        print_r($data); exit;
        if (empty($data['startdate']) || empty($data['enddate'])) {
            $date_where_clause = "";
        } else {
            $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
            $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
            $start_date = $data['startdate'] . " 00:00:00";
            $end_date = $data['enddate'] . " 23:59:59";

            $date_where_clause = "and (t1.created_at >= '{$start_date}' AND  t1.created_at <= '{$end_date}' )";
        }
        /* Sorted Data */
        $order_by_param = "request_date";
//        if (isset($data['iSortCol_0'])) {
//            switch ($data['iSortCol_0']) {
//                case "0":
//                    $order_by_param = "u1.date";
//                    break;
//                case "1":
//                    $order_by_param = "u1.company_mnc";
//                    break;
//                case "5":
//                    $order_by_param = "u1.total_send";
//                    break;
//                default:
//                    $order_by_param = "u1.date";
//            }
//        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $where_clause = "where 0  ";
        if ($permission == 1) {
            $where_clause = "where t1.user_request_type_id =8  ";
        }
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id NOT IN (1,3,4,6,8,9) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type;
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( u2.company_name like '%{$data['sSearch']}%' or u1.total_send like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select sum(count) as count from (
                        SELECT count(distinct CAST(t1.created_at as DATE)) AS count
                        FROM user_request as t1
                        join users_profile as t2 on t2.user_id = t1.user_id
                         {$where_clause}
                         {$date_where_clause}
                         group by t2.region_id) as a";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {

            $sql = "SELECT count(request_id) as request_count, t2.region_id, CAST(t1.created_at as DATE) as request_date,
                    SUM(CASE WHEN t1.status = 2 THEN 1
                            ELSE 0
                                END) AS pending
                    FROM user_request as t1
                    join users_profile as t2 on t2.user_id = t1.user_id
                     {$where_clause}
                     {$date_where_clause}
                     group by t2.region_id,request_date
                     {$order_by}
                        {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* view all blocked ips   */

    public static function blocked_ip_list() {
        $query = "SELECT * FROM LoginAttempts";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute();
        return $result;
    }

    /* Delete record from block list   */

    public static function delete_ip_from_blocked_ip_list($id) {
        $query = DB::delete('LoginAttempts')
                ->where('id', '=', $id)
                ->execute();
        return $query;
    }

    /* view password reset requests */

    public static function password_reset_requests() {
        $query = "SELECT t1.id,CONCAT(t2.first_name, ' ', t2.last_name) as name, t1.username, t2.region_id, t2.posted, t2.mobile_number,t2.job_title, t1.reset_password_text 
                    FROM users as t1
                    join users_profile as t2 on t2.user_id = t1.id 
                    where t1.is_forget_reset = 1 && (t1.is_active=1 || t1.is_active_cis=1) && t1.is_deleted=0;";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute();
        return $result;
    }

    //set temp password as users original password
    public static function set_temp_password($id) {
        $query = "select reset_password_text from users where id=$id ";
        $result = DB::query(Database::SELECT, $query)->execute()->current();
        if (!empty($result['reset_password_text'])) {
            $password = Auth::instance()->hash_password($result['reset_password_text']);
            $query = DB::update('users')
                    ->set(array('password' => $password, 'is_forget_reset' => '0', 'reset_password_text' => '', 'is_password_changed' => '0'))
                    ->where('id', '=', $id)
                    ->execute();
            return $query;
        } else {
            return -2;
        }
    }

    public static function menu_update($menu_id, $role_id) {
        $user_obj = Auth::instance()->get_user();
        $login_user_id = $user_obj->id;
        $current_date = date('Y-m-d H:i:s');
        $check_menu_role = Helpers_Utilities::check_menu_ag_role($menu_id, $role_id);
        //if $check_menu_role is 0 insert new record else update same record
        if ($check_menu_role == 0) {
            $query = DB::insert('manu_management', array('manu_id', 'role_id', 'access_status', 'updated_by_user_id', 'timestamp'))
                    ->values(array($menu_id, $role_id, 1, $login_user_id, $current_date))
                    ->execute();
            return $query;
        } else {
            $db = Database::instance();
            $db->query(Database::UPDATE, "UPDATE manu_management t1 
                    SET access_status = CASE WHEN access_status = 1 THEN 0 WHEN access_status = 0 THEN 1 END, t1.updated_by_user_id = {$login_user_id} , t1.timestamp='{$current_date}' 
                    Where t1.manu_id= {$menu_id} and t1.role_id = {$role_id};");
            //$result = $db->execute();
            //return $result;
//            $query = DB::update('manu_management')
//                    ->set(array('access_status' => "CASE WHEN access_status = 1 THEN 0 WHEN access_status = 0 THEN 1 END", 'updated_by_user_id' => $login_user_id, 'timestamp' => $current_date))
//                    ->where('manu_id', '=', $menu_id)
//                    ->and_where('role_id', '=', $role_id)
//                    ->execute();
            return $db;
        }
    }

}
