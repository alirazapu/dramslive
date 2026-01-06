<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Userreport {
    /* User List Ajax Call Data */

    public static function user_list($data, $count) {
        /* Posted Data */
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(u2.first_name), ' ', TRIM(u2.last_name))  like '%{$data['key']}%' )";
                    break;

                case "username":
                    $data['field'] = "and u1.username  like '%{$data['key']}%' ";
                    break;

                case "mobile_number":
                    $data['field'] = "and u2.mobile_number  like '%{$data['key']}%' ";
                    break;

                case "cnic_number":
                    $data['field'] = "and u2.cnic_number  like '%{$data['key']}%' ";
                    break;

                case "designation":
                    $data['field'] = "and u2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and u2.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
                case "utype":
                    $userstype = implode("','", $data['utype']);
                    $data['field'] = "and u3.role_id in ( '{$userstype}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
            }
        }
            $is_active = '';
            if(!empty($data['is_active']))
            {                
                if($data['is_active']==1)
                    $is_active = ' and u1.is_active = 1 and u1.is_approved = 1 ';
                elseif($data['is_active']==2) 
                    $is_active = ' and u1.is_active = 0 and u1.is_approved = 1 ';
                elseif($data['is_active']==3)  
                    $is_active = ' and u1.is_active = 0 and u1.is_approved = 0 ';
                
            }
        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
//                case "0":
//                    $order_by_param = "user_id";
//                    break;
                case "0":
                    $order_by_param = "first_name";
                    break;
                case "1":
                    $order_by_param = "job_title";
                    break;
                case "2":
                    $order_by_param = "job_title";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' OR u2.mobile_number like '%{$data['sSearch']}%' OR u1.username like '%{$data['sSearch']}%'  OR u2.cnic_number like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.id";
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;


        $where_clause = "where u1.username NOT like '%::transferred%' ";
        if ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    //H.Q Exective
                    //$where_clause = "where 1";
                    $where_clause = "where u1.id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where u1.id  = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                        from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id 
                        left join roles_users u3 on u3.user_id = u1.id                        
                        {$where_clause}
                        and u1.is_deleted = 0                         
                        AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                        and u1.id  != {$login_user->id}
                        {$search} 
                        {$is_active} 
                        {$data['field']}
                        ";
             //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select u1.*,u2.* from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id 
                        left join roles_users u3 on u3.user_id = u1.id                        
                        {$where_clause}
                        and u1.is_deleted = 0
                        AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)                         
                        and u1.id  != {$login_user->id}
                        {$search}
                        {$is_active}
                        {$data['field']}
                        {$groupby} 
                        {$order_by}    
                        {$limit}";
//                        if(Auth::instance()->get_user()->id==419){
//            print_r($sql); exit;
//                        }
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User Transferred List Ajax Call Data */

    public static function users_transferred_list($data, $count) {
        /* Posted Data */
        
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(u2.first_name), ' ', TRIM(u2.last_name))  like '%{$data['key']}%' )";
                    break;

                case "username":
                    $data['field'] = "and u1.username  like '%{$data['key']}%' ";
                    break;

                case "mobile_number":
                    $data['field'] = "and u2.mobile_number  like '%{$data['key']}%' ";
                    break;

                case "cnic_number":
                    $data['field'] = "and u2.cnic_number  like '%{$data['key']}%' ";
                    break;

                case "designation":
                    $data['field'] = "and u2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and u2.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
                case "utype":
                    $userstype = implode("','", $data['utype']);
                    $data['field'] = "and u3.role_id in ( '{$userstype}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
            }
        }

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
//                case "0":
//                    $order_by_param = "user_id";
//                    break;
                case "0":
                    $order_by_param = "first_name";
                    break;
                case "1":
                    $order_by_param = "job_title";
                    break;
                case "2":
                    $order_by_param = "job_title";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' OR u2.mobile_number like '%{$data['sSearch']}%' OR u1.username like '%{$data['sSearch']}%'  OR u2.cnic_number like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.id";
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;


        $where_clause = "where u1.username like '%::transferred%' ";
        if ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    //H.Q Exective
                    //$where_clause = "where 1";
                    $where_clause = "where u1.id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where u1.id  = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                        from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id 
                        left join roles_users u3 on u3.user_id = u1.id                        
                        {$where_clause}
                        and u1.is_deleted = 0 
                        and u1.is_approved = 1
                        AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                        and u1.id  != {$login_user->id}
                        {$search}
                        {$data['field']}
                        ";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select u1.*,u2.* from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id 
                        left join roles_users u3 on u3.user_id = u1.id                        
                        {$where_clause}
                        and u1.is_deleted = 0
                        AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                        and u1.is_approved = 1
                        and u1.id  != {$login_user->id}
                        {$search}
                        {$data['field']}
                        {$groupby} 
                        {$order_by}    
                        {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Blocked User List Ajax Call Data */

    public static function user_list_blocked($data, $count) {
        /* Posted Data */
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(u2.first_name), ' ', TRIM(u2.last_name))  like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and u2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and u2.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
            }
        }
        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "first_name";
                    break;
                case "1":
                    $order_by_param = "job_title";
                    break;
                case "2":
                    $order_by_param = "district_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' OR u2.mobile_number like '%{$data['sSearch']}%' )";
            //$search = "and ( u2.first_name like '%{$data['sSearch']}%' or u2.last_name like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.id";
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $where_clause = "where 1";
        if ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    //H.Q Exective
                    //$where_clause = "where 1";
                    $where_clause = "where u1.id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where u1.id  = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                        from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id   
                        {$where_clause}
                        and u1.is_deleted = 1           
                        AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                        and u1.is_active = 0                        
                        {$search} 
                        {$data['field']}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select *
                         from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id                         
                        {$where_clause}
                        and u1.is_deleted = 1
                        AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                        and u1.is_active = 0
                        {$search} 
                        {$data['field']}
                        {$groupby} 
                        {$order_by}    
                        {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* New User List Ajax Call Data */

    public static function user_list_new($data, $count) {

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "id";
                    break;
                case "7":
                    $order_by_param = "created_at";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' "
                    . "or u2.job_title like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.id";
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                        from users as u1
                        join users_profile u2 on u2.user_id=u1.id   
                        where u1.is_approved = 0  AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)                       
                        {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select *
                         from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id                         
                        where u1.is_approved = 0 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                        {$search}                        
                        {$groupby} 
                        {$order_by}    
                        {$limit}";
        //    print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Users Favourite User Ajax Call Data */

    public static function user_favourite_user($data, $count) {
        $limit = "";
        //this is
        /* Posted Data */
//        echo '<pre>';
//        print_r($data);
//        exit; 
        if (!empty($data['field'])) {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and user_id IN (SELECT user_id FROM users_profile as u1 WHERE  CONCAT(TRIM(u1.first_name), ' ', TRIM(u1.last_name))  like '%{$data['key']}%' )";
                    //print_r($data['field']); exit;
                    break;
                case "designation":
                    $data['field'] = "and user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.job_title like '%{$data['key']}%') ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted in ( '{$posting}' )) ";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
            }
        } else {
            $data['field'] = '';
        }

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "first_name";
                    break;
                case "1":
                    $order_by_param = "job_title";
                    break;
                case "2":
                    $order_by_param = "district_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        //  $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $DB = Database::instance();
            $sql = "SELECT  user_id FROM  users_profile as u2
                                    where ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' )";
            $user_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $users_array = implode(', ', array_values(array_column($user_id, 'user_id')));
            if (!empty($users_array))
                $search = "and  t1.user_id in ({$users_array})";
            else {
                $search = 'and t1.user_id = null';
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by t1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;


        $where_clause = "where 1";
        if ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where user_id  = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(DISTINCT user_id) AS count
                            from user_favourite_user as t1 
                            {$where_clause}
                            {$search} 
                            {$data['field']}
                            ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select  user_id,COUNT(t1.favourite_user_id) AS count
                            from user_favourite_user as t1              
                            {$where_clause}
                            {$search}
                            {$data['field']}
                            {$groupby}
                             {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* No Of Login Ajax Call Data */

    public static function no_of_login($data, $count) {
        /* Posted Data */
        if (!empty($data['key']) || !empty($data['designation'])) {
            switch ($data['field']) {
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(t2.first_name), ' ', TRIM(t2.last_name)) like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and t2.job_title like '%{$data['designation']}%' ";
                    break;
            }
        } else {
            $data['field'] = '';
        }
        // print_r($data['field']); exit;

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "id";
                    break;
                case "1":
                    $order_by_param = "first_name";
                    break;
                case "5":
                    $order_by_param = "last_login";
                    break;
                case "6":
                    $order_by_param = "logins";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $DB = Database::instance();
            $sql = "SELECT  user_id FROM  users_profile as u2
                                    where ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' )";
            $user_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $users_array = implode(', ', array_values(array_column($user_id, 'user_id')));
            if (!empty($users_array))
                $search = "and  t1.id in ({$users_array})";
            else {
                $search = 'and t1.id = null';
            }
        } else {
            $search = "";
        }

        /* Group By */
        // $groupby = "group by t1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";

        if ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause = "where t1.id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                        $where_clause = "where 1";
                    }
                    break;
                case 'r':
                    $where_clause = "where t1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else if ($permission == 4) {
            $where_clause = "where t1.id = {$login_user->id}";
        }
        //herenow
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                            from users as t1 
                            JOIN users_profile as t2 
                            ON   t2.user_id = t1.id
                            {$where_clause}
                            AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)     
                            {$search}
                            {$data['field']}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                            from users as t1 
                            JOIN users_profile as t2 
                            ON   t2.user_id = t1.id              
                            {$where_clause}
                                AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) 
                            {$search}
                            {$data['field']}
                            {$order_by}    
                            {$limit} ";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Audit Report Ajax Call Data */

    public static function audit_report_basic($data, $count) {
        //advance search of date
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */
        $search = "";
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $DB = Database::instance();
            $sql = "SELECT distinct(t1.region_id) as region_id
                    FROM district as t1 join region as t2 on t2.region_id=t1.region_id
                    where t1.name or t2.name like '%{$data['sSearch']}%'";
            $data_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $district_region = implode(', ', array_values(array_column($data_array, 'region_id')));
            if (!empty($district_region)) {
                $search = " and t2.region_id in ({$district_region})";
            } else {
                $search = " and t2.region_id = null";
            }
        } else {
            $search = "";
        }
        /* Group By */
        $groupby = "GROUP by t1.request_id";
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        if ($permission == 1) {
            $where_clause = "where 1";
        } elseif ($permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where 1";
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                      if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                              $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                        }else{
                              $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) and t1.user_id not IN (842, 137, 2031, 2603) ";
                        }
                   
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else {
            $where_clause = "where t1.user_id = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(distinct t2.posted) AS count
                            from user_request as t1 
                            JOIN users_profile as t2  on t2.user_id =  t1.user_id 
                            {$where_clause}
                            {$search}
                            {$where_date}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * , count(request_id) as count
                            from user_request as t1 
                            JOIN users_profile as t2 on t2.user_id= t1.user_id                                                         
                            {$where_clause}
                            {$search}
                            {$where_date}
                            group by t2.posted
                            {$order_by}
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Breakup Report Ajax Call Data */

    public static function breakup_report_basic($data, $count) {
        //advance search of date
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t3.created_at >= '{$start_date}' and t3.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */
        $search = "";
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {

            $DB = Database::instance();
            $sql = "SELECT distinct(t1.region_id) as region_id
                    FROM district as t1 join region as t2 on t2.region_id=t1.region_id
                    where t1.name or t2.name like '%{$data['sSearch']}%'";
            $data_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $district_region = implode(', ', array_values(array_column($data_array, 'region_id')));
            if (!empty($district_region)) {
                $search = " and t2.region_id in ({$district_region})";
            } else {

                $DB = Database::instance();
                $sql = "SELECT distinct(region_id) as region_id
                    FROM region 
                    where name like '%{$data['sSearch']}%'";
                $data_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
                $district_region = implode(', ', array_values(array_column($data_array, 'region_id')));
                if (!empty($district_region)) {
                    $search = " and t2.region_id in ({$district_region})";
                } else {
                    $search = " and t2.region_id = null";
                }
            }
        } else {
            $search = "";
        }
        /* Group By */
        $groupby = "GROUP by t1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        if ($permission == 1) {
            $where_clause = "where 1";
        } elseif ($permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                             
                              $where_clause = "where 1";
                        }else{
                              $where_clause = "where t1.user_id not IN (842, 137, 2031, 2603) ";
                        }
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else {
            $where_clause = " t3.created_from=2";
        }
        $where_clause = " and t3.created_from=2";
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(distinct t2.posted) AS count
                            from users as t1 
                            JOIN users_profile as t2  on t2.user_id =  t1.id 
                            join person_initiate as t3 on t1.id=t3.user_id
                            {$where_clause}
                           
                            {$where_date}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * , count(id) as count
                            from users as t1 
                            JOIN users_profile as t2 on t2.user_id= t1.id  
                                join person_initiate as t3 on t1.id=t3.user_id                                                       
                            {$where_clause}
                           {$search}
                            {$where_date}
                            group by t2.posted
                            {$order_by}
                            {$limit}";
           // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    /* MSISDN Breakup Report Ajax Call Data */

    public static function msisdn_breakup_report_basic($data, $count) {
        //advance search of date
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */
        $search = "";
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {

            $DB = Database::instance();
            $sql = "SELECT distinct(t1.region_id) as region_id
                    FROM district as t1 join region as t2 on t2.region_id=t1.region_id
                    where t1.name or t2.name like '%{$data['sSearch']}%'";
            $data_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $district_region = implode(', ', array_values(array_column($data_array, 'region_id')));
            if (!empty($district_region)) {
                $search = " and t2.region_id in ({$district_region})";
            } else {

                $DB = Database::instance();
                $sql = "SELECT distinct(region_id) as region_id
                    FROM region 
                    where name like '%{$data['sSearch']}%'";
                $data_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
                $district_region = implode(', ', array_values(array_column($data_array, 'region_id')));
                if (!empty($district_region)) {
                    $search = " and t2.region_id in ({$district_region})";
                } else {
                    $search = " and t2.region_id = null";
                }
            }
        } else {
            $search = "";
        }
        /* Group By */
        $groupby = "GROUP by t1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        if ($permission == 1) {
            $where_clause = "where 1";
        } elseif ($permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                             
                              $where_clause = "where 1";
                        }else{
                              $where_clause = "where t1.user_id not IN (842, 137, 2031, 2603) ";
                        }
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        }
// else {
//            $where_clause = " t3.created_from=2";
//        }
//        $where_clause = " and t3.created_from=2";
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(distinct t2.posted) AS count
                            from old_data as t1 
                            JOIN aies.users_profile as t2  on t2.user_id =  t1.user_id 
                            
                            {$where_clause}
                           
                            {$where_date}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * , count(t1.user_id) as count
                            from old_data as t1 
                            JOIN aies.users_profile as t2 on t2.user_id= t1.user_id  
                                                                                
                            {$where_clause}
                           {$search}
                            {$where_date}
                            group by t2.posted
                            {$order_by}
                            {$limit}";
          //  print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Audit Report Ajax Call Data */

    public static function audit_report($data, $count, $posted) {
        // print_r($posted); exit;
                //advance search of date
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        //$where_date = " and (t1.created_at >= '2018-11-01 00:00:00' and t1.created_at <='2019-03-31 23:59:59') ";
        
        /* Posted Data */
        if (!empty($data['field'])) {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "username":
                    $data['field'] = "and ( t2.first_name like '%{$data['key']}%' or t2.last_name like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and t2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
                case "requesttype":
                    $DB = Database::instance();
                    $sql = "SELECT  id
                                    FROM  email_templates_type
                                    WHERE email_type_name like '%{$data['key']}%'";
                    $request_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
                    $request_array = implode(', ', array_values(array_column($request_id, 'id')));
                    if (!empty($request_array))
                        $data['field'] = "and t1.user_request_type_id in ({$request_array})";
                    else {
                        $data['field'] = 'and t1.user_request_type_id = null';
                    }
            }
        } else {
            $data['field'] = '';
        }

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.user_id";
                    break;
                case "5":
                    $order_by_param = "count";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        // $groupby = "GROUP by t1.request_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

    

          if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer support requests                       
              $where_clause = "where t2.posted  = '$posted'";
        }else{
           $where_clause = "where t2.posted  = '$posted' and t1.user_id not IN (842, 137, 2031, 2603) "; 
        }
        
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(distinct t1.user_id) AS count
                            from user_request as t1 
                            JOIN users_profile as t2 
                            on t2.user_id =  t1.user_id 
                            {$where_clause}
                            {$where_date}    
                            {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *,count(distinct t1.request_id) as count
                            from user_request as t1 
                            JOIN users_profile as t2 
                            on t2.user_id= t1.user_id 
                            {$where_clause}
                           {$where_date}    
                            {$search}                                                         
                            group by t2.user_id
                            {$order_by}
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Audit Report Ajax Call Data */

    public static function breakup_report($data, $count, $posted) {
        // print_r($posted); exit;
                //advance search of date
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t3.created_at >= '{$start_date}' and t3.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        //$where_date = " and (t1.created_at >= '2018-11-01 00:00:00' and t1.created_at <='2019-03-31 23:59:59') ";

        /* Posted Data */
        if (!empty($data['field'])) {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "username":
                    $data['field'] = "and ( t2.first_name like '%{$data['key']}%' or t2.last_name like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and t2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
//                    break;
//                case "requesttype":
//                    $DB = Database::instance();
//                    $sql = "SELECT  id
//                                    FROM  email_templates_type
//                                    WHERE email_type_name like '%{$data['key']}%'";
//                    $request_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
//                    $request_array = implode(', ', array_values(array_column($request_id, 'id')));
//                    if (!empty($request_array))
//                        $data['field'] = "and t1.user_request_type_id in ({$request_array})";
//                    else {
//                        $data['field'] = 'and t1.user_request_type_id = null';
//                    }
            }
        } else {
            $data['field'] = '';
        }

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.user_id";
                    break;
                case "5":
                    $order_by_param = "count";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        // $groupby = "GROUP by t1.request_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where t2.posted  = '$posted'";
        $where_clause .= " and t3.created_from =2 ";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT( distinct (t3.user_id)) AS count
                            from users as t1 
                            JOIN users_profile as t2 on t2.user_id =  t1.id
                            join person_initiate as t3 on t1.id=t3.user_id 
                            {$where_clause}
                            {$where_date}    
                            {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *,count( t3.user_id) as count
                            from users as t1 
                            JOIN users_profile as t2 on t2.user_id= t1.id
                             join person_initiate as t3 on t3.user_id=t1.id  
                            {$where_clause}
                            {$where_date}    
                            {$search}                                                         
                            group by t2.user_id
                            {$order_by}
                            {$limit}";
           // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    /* MSISDN Audit Report Ajax Call Data */

    public static function msisdn_breakup_report($data, $count, $posted) {
        // print_r($posted); exit;
                //advance search of date
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        //$where_date = " and (t1.created_at >= '2018-11-01 00:00:00' and t1.created_at <='2019-03-31 23:59:59') ";

        /* Posted Data */
        if (!empty($data['field'])) {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "username":
                    $data['field'] = "and ( t2.first_name like '%{$data['key']}%' or t2.last_name like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and t2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
//                    break;
//                case "requesttype":
//                    $DB = Database::instance();
//                    $sql = "SELECT  id
//                                    FROM  email_templates_type
//                                    WHERE email_type_name like '%{$data['key']}%'";
//                    $request_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
//                    $request_array = implode(', ', array_values(array_column($request_id, 'id')));
//                    if (!empty($request_array))
//                        $data['field'] = "and t1.user_request_type_id in ({$request_array})";
//                    else {
//                        $data['field'] = 'and t1.user_request_type_id = null';
//                    }
            }
        } else {
            $data['field'] = '';
        }

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.user_id";
                    break;
                case "5":
                    $order_by_param = "count";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        // $groupby = "GROUP by t1.request_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where t2.posted  = '$posted'";
      //  $where_clause .= " and t3.created_from =2 ";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT( distinct (t1.user_id)) AS count
                            from old_data as t1 
                            JOIN users_profile as t2 on t2.user_id =  t1.user_id
                           
                            {$where_clause}
                            {$where_date}    
                            {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *,count( t1.user_id) as count
                            from old_data as t1 
                            JOIN users_profile as t2 on t2.user_id= t1.user_id
                             
                            {$where_clause}
                            {$where_date}    
                            {$search}                                                         
                            group by t1.user_id
                            {$order_by}
                            {$limit}";
           // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Number of Record Searched Ajax Call Data */

    public static function no_record_searched($data, $count) {
        /* Posted Data */
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "searched":
                    $data['field'] = "and t2.key_name like '%{$data['key']}%' ";
                    break;
                case "searchkey":
                    $data['field'] = "and t2.key_value like '%{$data['key']}%' ";
                    break;
                case "username":
                    $DB = Database::instance();
                    $sql = "SELECT  *
                                    FROM  users_profile
                                    WHERE first_name like '%{$data['key']}%' or last_name like '%{$data['key']}%'";
                    $userid = DB::query(Database::SELECT, $sql)->execute()->current();
                    $data['field'] = "and t1.user_id = '{$userid['user_id']}' ";
                    break;
            }
        }

        /* Sorted Data */
        $order_by_param = "t1.activity_time ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.activity_time";
                    break;
                case "1":
                    $order_by_param = "t1.activity_time";
                    break;
                case "2":
                    $order_by_param = "t1.activity_time";
                    break;
                case "3":
                    $order_by_param = "t1.activity_time";
                    break;
                case "4":
                    $order_by_param = "t1.activity_time";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $DB = Database::instance();
            $sql = "SELECT  user_id FROM  users_profile as u2
                            where ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' )";
            $user_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $users_array = implode(', ', array_values(array_column($user_id, 'user_id')));
            if (!empty($users_array))
                $search = "and ( t2.key_name like '%{$data['sSearch']}%' or t2.key_value like '%{$data['sSearch']}%' ) or t1.user_id in ({$users_array})";
            else {
                $search = "and ( t2.key_name like '%{$data['sSearch']}%' or t2.key_value like '%{$data['sSearch']}%' )";
            }
        } else {
            $search = "";
        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";

        if ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where t1.user_id  = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {

            $sql = "Select COUNT(*) AS count
                            from user_activity_timeline as t1
                            JOIN  user_activity_timeline_detail as t2 on t2.timeline_id = t1.timeline_id
                            {$where_clause}
                            {$data['field']}
                            {$search}
                            and t1.user_activity_type_id = 8";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *,t1.user_id as tuser FROM  user_activity_timeline as t1
                            JOIN  user_activity_timeline_detail as t2 on t2.timeline_id = t1.timeline_id                                          
                            {$where_clause}
                            and t1.user_activity_type_id = 8
                            {$search}                             
                            {$data['field']} 
                            {$order_by}
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* users_favourite_agent Ajax Call Data */

    public static function users_favourite_agent($data, $count, $id = null) {
        $order_by = "";
        $order_by = "";
        /* Sorted Data */
        $order_by_param = "t1.user_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "1":
                    $order_by_param = "t1.user_id";
                    break;
                case "2":
                    $order_by_param = "t1.user_id";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            //$search = "and ( t2.first_name like '%{$data['sSearch']}%' or t2.last_name like '%{$data['sSearch']}%' )";
            $search = "and ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }
        /* Group By */
        // $groupby = "GROUP by userid";        
        $user_obj = Auth::instance()->get_user();
        $role = Helpers_Utilities::get_user_role_id($user_obj->id);
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count
                            FROM user_favourite_user as t1
                            JOIN users_profile as t2 on t1.user_id = t2.user_id
                            where t1.user_id = $id
                            {$search}                               
                            ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.favourite_user_id as userid,
                            t2.first_name as fname,
                            t2.last_name as lname
                            FROM user_favourite_user as t1
                            JOIN users_profile as t2 on t2.user_id = t1.favourite_user_id
                            where t1.user_id = $id
                            {$search}                             
                            {$order_by}
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* users_favourite_person_list Ajax Call Data */

    public static function user_favourite_person_list($data, $count, $id = null) {
        /* Sorted Data */
        $order_by_param = "t1.user_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.first_name";
                    break;
                case "1":
                    $order_by_param = "t2.father_name";
                    break;
                case "2":
                    $order_by_param = "t2.cnic_number";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' or t3.cnic_number like '%{$data['sSearch']}%' or t3.cnic_number_foreigner like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        /* Group By */
        $groupby = "GROUP by t3.cnic_number";

        $user_obj = Auth::instance()->get_user();
        $role = Helpers_Utilities::get_user_role_id($user_obj->id);
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(DISTINCT t1.person_id) AS count
                            FROM user_favorite_person as t1
                            JOIN person as t2 on t1.person_id = t2.person_id
                            JOIN person_initiate as t3 on  t3.person_id = t1.person_id                            
                            where t1.user_id = {$id}
                            {$search}
                            ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.user_id, t2.first_name, t2.last_name,t2.father_name,t3.cnic_number,t3.cnic_number_foreigner,t3.is_foreigner,
                            t2.address, t2.person_id
                            FROM user_favorite_person as t1
                            JOIN person as t2 on t2.person_id = t1.person_id
                            JOIN person_initiate as t3 on  t3.person_id = t1.person_id
                            where t1.user_id = $id
                            {$search}                             
                            {$groupby}
                            {$order_by}
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    /* users_favourite_person_list Ajax Call Data */

    public static function user_person_list($data, $count, $id = null) {
        /* Sorted Data */
        $order_by_param = "t1.user_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.first_name";
                    break;
                case "1":
                    $order_by_param = "t2.father_name";
                    break;

            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' or t3.phone_number like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        /* Group By */
        $groupby = "GROUP by t2.person_id";

        $user_obj = Auth::instance()->get_user();
        $role = Helpers_Utilities::get_user_role_id($user_obj->id);
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(DISTINCT t1.person_id) AS count
                            FROM person_category as t1                   
                            where t1.user_id = {$id}
                          
                            ";
//            echo '<pre>';
//            print_r($sql);
//            exit();
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.person_id, t1.category_id, t2.first_name, t2.last_name,t2.father_name, t2.address,GROUP_CONCAT(DISTINCT(t3.phone_number)) AS phone_number
                          
                            FROM person_category as t1
                            left JOIN person as t2 on t2.person_id = t1.person_id
                            left JOIN person_phone_number as t3 on  t3.person_id = t1.person_id
                            where t1.user_id = $id
                            {$search}                             
                            {$groupby}
                            {$order_by}
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Performance Report Ajax Call Data */

    public static function performance_report($data, $count) {



        /* Sorted Data */
        $order_by_param = "t1.created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }


        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(t1.first_name, ' ', TRIM(t1.last_name)) like '%{$data['sSearch']}%')";
            //$search = "and ( t1.first_name like '%{$data['sSearch']}%' or t1.last_name like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "GROUP by t1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;


        $where_clause = "where 1";
        if ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where t1.user_id  = {$login_user->id}";
        }
        
        
          if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer support requests                       
              
        }else{
           $where_clause .= " and t1.user_id not IN (842, 137, 2031, 2603) "; 
        }
        /* Posted Data */
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;


                case "username":
                    $data['field'] = " and ( CONCAT(TRIM(t1.first_name), ' ', TRIM(t1.last_name)))  like '%{$data['key']}%' ";
                    break;
                /*case "username":
                    $data['field'] = "and u1.username  like '%{$data['key']}%' ";
                    break;*/





                case "designation":
                    $data['field'] = "and t1.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t1.posted in ( '{$posting}' )";
                    // $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;

            }
        }
        $user_obj = Auth::instance()->get_user();
        $role = Helpers_Utilities::get_user_role_id($user_obj->id);
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count                            
                        FROM users_profile as t1                                                        
                        {$where_clause}     
                        {$data['field']}                       
                        {$search}
                        ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                        FROM users_profile as t1
                        {$where_clause}
                        {$data['field']}
                        {$search}                             
                        $groupby
                        {$order_by}
                        {$limit}";
//            echo '<pre>';
//            print_r($sql);
//            exit();
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User Favourite person Ajax Call Data */

    public static function user_favourite_person($data, $count) {
        /* Posted Data */
        //fav
        if (!empty($data['field'])) {

            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(t2.first_name), ' ', TRIM(t2.last_name)) like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and t2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
            }
        } else {
            $data['field'] = '';
        }

        /* Sorted Data */
        $order_by_param = "user_id";
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and  ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";

        if ($permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(DISTINCT t1.user_id) AS count
                        from user_favorite_person as t1
                            JOIN users_profile as t2 
                            on t1.user_id = t2.user_id              
                            {$where_clause}
                            {$search}                             
                            {$data['field']}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select  t1.user_id,COUNT(DISTINCT person_id) AS count , 
                            t2.first_name as fname, t2.last_name as lname,
                            t2.job_title as designaion, t2.district_id as dist_id
                            from user_favorite_person as t1
                            JOIN users_profile as t2 
                            on t1.user_id = t2.user_id              
                            {$where_clause}
                            {$search}                             
                            {$data['field']}                                
                            GROUP by t1.user_id
                            {$order_by}
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Number of Request Send Ajax Call Data */

    public static function no_request_send($data, $count) {
        $where_date = " ";
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }        
        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "6":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $DB = Database::instance();
            $sql = "SELECT  user_id FROM  users_profile as t2
                                    WHERE ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
            $user_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $users_array = implode(', ', array_values(array_column($user_id, 'user_id')));
            if (!empty($users_array))
                $search = "and  t1.user_id in ({$users_array})";
            else {
                $search = "and  t1.user_id = null";
            }
        } else {
            $search = "";
        }


        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";
        

        if ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                          if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                             $where_clause = " where 1 ";
                        }else{
                             $where_clause = " where t1.user_id not IN (842, 137, 2031, 2603) "; 
                        }
                    }
                    break;
                case 'r':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where t1.user_id  = {$login_user->id}";
        }

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(DISTINCT t1.user_id) as count   
                               FROM user_request as t1
                               join users_profile as t2 on t2.user_id = t1.user_id
                               {$where_clause}
                               {$where_date}
                               {$search}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  t1.user_id, t2.region_id , t2.posted ,                              
                               COUNT(*) as count   
                               FROM user_request as t1 
                               join users_profile as t2 on t2.user_id = t1.user_id
                                {$where_clause}
                                {$where_date}
                                {$search}
                                GROUP BY t1.user_id
                                {$order_by}    
                                {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Number of Request Send Ajax Call Data */

    public function no_request_send_detail($data, $count, $userid, $request_type) {
        //where clause for date temporaty hardcoded
        //this
        $where_date = " ";
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }                        
        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "5":
                    $order_by_param = "created_at";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        //print_r($limit); exit;
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and t1.requested_value like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();

        $where_clause = "where t1.user_id = {$userid} and t1.user_request_type_id = {$request_type}";        

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(*) as count   
                               FROM user_request as t1
                               {$where_clause}
                               {$where_date}    
                               {$search}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  user_id, requested_value, reason, concerned_person_id,request_id,
                               user_request_type_id,status,created_at
                               FROM user_request as t1 
                               {$where_clause}
                               {$where_date}    
                               {$search}                           
                                {$order_by}    
                                {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    /* Number of Request Send Ajax Call Data */

    public function no_request_send_detail_reports($data, $count, $userid, $request_type) {
        //where clause for date temporaty hardcoded
        //this
        $where_date = " ";
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        /* Sorted Data */
        $order_by_param = "t1.created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "5":
                    $order_by_param = "t1.created_at";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        //print_r($limit); exit;
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and t1.cnic_number like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();

        $where_clause = "where t1.user_id = {$userid} ";
        $where_clause .= " and t1.created_from=2";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(*) as count   
                               FROM person_initiate as t1
                               join person_category as t2 on  t1.person_id =t2.person_id 
                               
                               {$where_clause}
                               {$where_date}    
                               {$search}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  t1.user_id, t1.cnic_number, t1.cnic_number_foreigner, t2.reason, t1.person_id, t1.created_at
                               FROM person_initiate as t1
                               join person_category as t2 on t1.person_id =t2.person_id 
                               {$where_clause}
                               {$where_date}    
                               {$search}                           
                                {$order_by}    
                                {$limit}";
           // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    /* MSISDN Number of Request Send Ajax Call Data */

    public function msisdn_no_request_send_detail_reports($data, $count, $userid) {
        /* Sorted Data */
        $order_by_param = "t1.created_at";
        //$order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "t1.created_at";
                    break;

            }
        }

        /* Order By */
        $order_by_type = "desc";

        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and (t1.phone_number like '%{$data['sSearch']}%' ) ";
        } else {
            $search = "";
        }

        /* Group By */
        //$groupby = "group by u1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $login_user_region = $login_user_profile->region_id;

        $where_clause = "where 1";

        $where_clause .= " and t1.user_id='{$userid}'";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM old_data as t1 
                                                         
                           
                                {$where_clause}                                
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            // print_r($sql); exit;
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *  
                                FROM  old_data as t1
                                                              
                               
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);

//            print_r($sql);
//            exit();
            return $members;
        }
    }

    /* MSISDN Request Report Ajax Call Data */

    public function msisdn_reports($data, $count) {
        $where_clause= "where 1";
        if (isset($data['user_name']) && !empty($data['user_name'])) {
            $data['user_name'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['user_name']);
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['user_name']}%'";
            $DB = Database::instance();
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            $search_name = " and (t1.user_id in ({$request_array}))";

        } else {
            $search_name = "";
        }
        //dates filter
        $serach_date = ' ';
        //if end date is not selected then user current date as end date
        if (empty($data['enddate'])) {
            $data['enddate'] = date("Y-m-d");
        }


        if (!empty($data['startdate'])) {
            $start_date = date("Y-m-d", strtotime($data['startdate']));
            $end_date = date("Y-m-d", strtotime($data['enddate']));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $serach_date = " and created_at between '{$start_date}' and '{$end_date}' ";
        }
      //  $userid= $data['uid'];
        /* Sorted Data */
        $order_by_param = "t1.created_at";
        //$order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "t1.created_at";
                    break;

            }
        }

        /* Order By */
        $order_by_type = "desc";

        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and (t1.phone_number like '%{$data['sSearch']}%' ) ";
        } else {
            $search = "";
        }

        /* Group By */
        //$groupby = "group by u1.user_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $login_user_region = $login_user_profile->region_id;

       // $where_clause .= " and t1.user_id='{$userid}'";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM old_data as t1 
                                    {$where_clause}                        
                                {$search_name}
                                {$serach_date}
                                                             
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            // print_r($sql); exit;
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *  
                                FROM  old_data as t1
                                    {$where_clause}                            
                                 {$search_name}
                                {$serach_date}
                              
                                {$search}
                                {$order_by}    
                                {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);

//            print_r($sql);
//            exit();
            return $members;
        }
    }

    /* Number of Request Send Ajax Call Data */

    public static function no_request_send_type($data, $count, $userid) {
        //now this
        /* Posted Data */
        $where_date = " ";
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t1.created_at >= '{$start_date}' and t1.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        /* Sorted Data */
        $order_by_param = "t1.user_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "4":
                    $order_by_param = "request_id";
                    break;
                case "5":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        /* Search via table */
        //no need for table search
        $DB = Database::instance();
        $where_clause = "where t1.user_id = {$userid}";        

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(DISTINCT t1.user_request_type_id) as count   
                               FROM user_request as t1
                               join users_profile as t2 on t2.user_id = t1.user_id
                               {$where_clause}"
                    . "{$where_date}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  t1.user_id,t1.user_request_type_id, t2.region_id , t2.posted ,                              
                               COUNT(*) as count   
                               FROM user_request as t1 
                               join users_profile as t2 on t2.user_id = t1.user_id 
                               {$where_clause}
                               {$where_date}    
                            GROUP BY t1.user_request_type_id, t1.user_id
                            {$order_by}    
                            {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Number of Request Send Reports Ajax Call Data */

    public static function no_request_send_type_reports($data, $count, $userid) {
        //now this
        /* Posted Data */
        $where_date = " ";
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $data['sdate'] = date('Y-m-d', strtotime($data['sdate']));
            $data['edate'] = date('Y-m-d', strtotime($data['edate']));
            $start_date = $data['sdate'] . " 00:00:00";
            $end_date = $data['edate'] . " 23:59:59";
            $where_date = " and (t4.created_at >= '{$start_date}' and t4.created_at <= '{$end_date}') ";
        } else {
            $where_date = " ";
        }
        /* Sorted Data */
        $order_by_param = "t1.user_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {

                case "5":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        /* Search via table */
        //no need for table search
        $DB = Database::instance();
        $where_clause = "where t1.id = {$userid}";
        $where_clause .= " and t4.created_from = 2";
       // $where_clause .= " and t3.user_request_type_id = 8";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(DISTINCT t1.id) as count   
                               FROM users as t1
                               join users_profile as t2 on t2.user_id = t1.id
                             
                               join person_initiate as t4 on t4.user_id=t1.id
                               {$where_clause}"
                    . "{$where_date}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  t1.id, t2.region_id , t2.posted ,                              
                               COUNT(*) as count   
                               FROM users as t1 
                               join users_profile as t2 on t2.user_id = t1.id
                              
                               join person_initiate as t4 on t4.user_id=t1.id 
                               {$where_clause}
                               {$where_date}    
                            GROUP BY  t1.id
                            {$order_by}    
                            {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
           // print_r($sql); exit;
            return $members;
        }
    }

    /* User List Ajax Call Data */

    public static function user_panel_log($data, $count) {
        // print_r($data); exit;
        /* Posted Data */
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(t2.first_name), ' ', TRIM(t2.last_name))  like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and ( t2.job_title = '{$data['designation']}')";
                    //print_r($data['field']); exit;
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted  in ( '{$posting}') ";
                    break;
                case "usertype":
                    $user_type = implode("','", $data['usertype']);
                    $data['field'] = "and t3.role_id  in ( '{$user_type}') ";
                    break;
                case "activity":
                    $activity = implode("','", $data['activity']);
                    $DB = Database::instance();
                    $sql = "SELECT  id
                                    FROM  lu_user_activity_type
                                    WHERE id in ( '{$activity}') ";
                    $activity = DB::query(Database::SELECT, $sql)->execute()->as_array();
                    $activity_array = implode(', ', array_values(array_column($activity, 'id')));
                    if (!empty($activity_array)) {
                        $data['field'] = "and t1.user_activity_type_id in ({$activity_array}) ";
                    } else {
                        $data['field'] = "and t1.user_activity_type_id = null";
                    }
                    // print_r($data['field']); exit;
                    break;
            }
        }

        /* Sorted Data */
        $order_by_param = "t1.activity_time";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "5":
                    $order_by_param = "t1.activity_time";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and  ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.timeline_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";
        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause = "where t2.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                          if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                             $where_clause = " where 1 ";
                        }else{
                             $where_clause = " where t2.user_id not IN (842, 137, 2031, 2603) "; 
                        }
                    }
                    break;
                case 'r':
                    $where_clause = "where t2.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t2.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t2.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where t2.user_id  = {$login_user->id}";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                    from user_activity_timeline as t1 
                    join users_profile as t2 on t2.user_id=t1.user_id    
                    JOIN roles_users as t3 on   t3.user_id = t1.user_id
                    {$where_clause}
                    and t1.user_activity_type_id NOT IN (0)
                    {$data['field']}
                    {$search}
                    ";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select t3.role_id, t2.user_id, t2.region_id, t2.posted, t2.job_title, t1.user_activity_type_id, t1.person_id, t1.activity_time,t1.timeline_id 
                    from user_activity_timeline as t1 
                    join users_profile as t2 on t2.user_id = t1.user_id  
                    JOIN roles_users as t3 on   t3.user_id = t1.user_id
                    {$where_clause}
                    and t1.user_activity_type_id NOT IN (0)                   
                    {$data['field']}                    
                    {$search}
                    {$order_by}
                    {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    
    /* User List Ajax Call Data */

    public static function user_panel_log_officewise($data, $count) {
        // print_r($data); exit;
        /* Posted Data */
         $start_date = '';
        $enddate = '';
  
         if (!empty($data['sdate']) ) {
            $start_date = "'" . date("Y-m-d H:i", strtotime($data['sdate'])) . "'";
        }
         if ( !empty($data['edate'])) {
          $enddate = "'" . date("Y-m-d H:i", strtotime($data['edate']))  . "'";
        }
        
        
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(t2.first_name), ' ', TRIM(t2.last_name))  like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and ( t2.job_title = '{$data['designation']}')";
                    //print_r($data['field']); exit;
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted  in ( '{$posting}') ";
                    break;
                case "usertype":
                    $user_type = implode("','", $data['usertype']);
                    $data['field'] = "and t3.role_id  in ( '{$user_type}') ";
                    break;
                case "activity":
                    $activity = implode("','", $data['activity']);
                    $DB = Database::instance();
                    $sql = "SELECT  id
                                    FROM  lu_user_activity_type
                                    WHERE id in ( '{$activity}') ";
                    $activity = DB::query(Database::SELECT, $sql)->execute()->as_array();
                    $activity_array = implode(', ', array_values(array_column($activity, 'id')));
                    if (!empty($activity_array)) {
                        $data['field'] = "and t1.user_activity_type_id in ({$activity_array}) ";
                    } else {
                        $data['field'] = "and t1.user_activity_type_id = null";
                    }
                    // print_r($data['field']); exit;
                    break;
            }
        }

        /* Sorted Data */
        $order_by_param = "t1.activity_time";
        if (isset($data['iSortCol_0'])) {
            
                    $order_by_param = "t2.posted";
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and  ( CONCAT(t2.first_name, ' ', TRIM(t2.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = " group by t2.posted ";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";
        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause = "where t2.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                          if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                             $where_clause = " where 1 ";
                        }else{
                             $where_clause = " where t2.user_id not IN (842, 137, 2031, 2603) "; 
                        }
                    }
                    break;
                case 'r':
                    $where_clause = "where t2.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t2.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t2.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where t2.user_id  = {$login_user->id}";
        }
        
         if (!empty($data['sdate'])) {
                $where_clause .= " and t1.activity_time >= $start_date ";
            }
            
             if (!empty($data['edate'])) {
                $where_clause .= " and t1.activity_time <= $enddate ";
            }
            
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                    from user_activity_timeline as t1 
                    join users_profile as t2 on t2.user_id=t1.user_id    
                    JOIN roles_users as t3 on   t3.user_id = t1.user_id
                    {$where_clause}
                    and t1.user_activity_type_id NOT IN (0)
                    {$data['field']}
                    {$search}                        
                    {$groupby}
                    {$order_by}
                    ";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select t2.posted,count(t1.timeline_id) as total
                    from user_activity_timeline as t1 
                    join users_profile as t2 on t2.user_id = t1.user_id  
                    LEFT JOIN roles_users as t3 on   t3.user_id = t1.user_id
                    {$where_clause}
                    and t1.user_activity_type_id NOT IN (0)                   
                    {$data['field']}                    
                    {$search}
                    {$groupby}
                    {$order_by}
                    {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User url hits log */

    public static function user_url_hits_log($data, $count) {
        // print_r($data); exit;
        /* Posted Data */
        if (empty($data['field']))
            $data['field'] = '';
        else {
            switch ($data['field']) {
                case "def":
                    $data['field'] = '';
                    break;
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(t2.first_name), ' ', TRIM(t2.last_name))  like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and ( t2.job_title = '{$data['designation']}')";
                    //print_r($data['field']); exit;
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and t2.posted  in ( '{$posting}') ";
                    break;
                case "usertype":
                    $user_type = implode("','", $data['usertype']);
                    $data['field'] = "and t3.role_id  in ( '{$user_type}') ";
                    break;
                case "activity":
                    $activity = implode("','", $data['activity']);
                    $DB = Database::instance();
                    $sql = "SELECT  id
                                    FROM  lu_user_activity_type
                                    WHERE id in ( '{$activity}') ";
                    $activity = DB::query(Database::SELECT, $sql)->execute()->as_array();
                    $activity_array = implode(', ', array_values(array_column($activity, 'id')));
                    if (!empty($activity_array)) {
                        $data['field'] = "and t1.user_activity_type_id in ({$activity_array}) ";
                    } else {
                        $data['field'] = "and t1.user_activity_type_id = null";
                    }
                    // print_r($data['field']); exit;
                    break;
            }
        }

        /* Sorted Data */
        $order_by_param = "t1.id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "t1.id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and  (TRIM(t1.accessed_url) like '%{$data['sSearch']}%' OR TRIM(t1.user_agent) like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }


        $where_clause = "where 1";
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                    from url_hits_log as t1 
                    join users_profile as t2 on t2.user_id=t1.user_id    
                    JOIN roles_users as t3 on   t3.user_id = t1.user_id
                    {$where_clause}
                    {$data['field']}
                    {$search}
                    ";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select t3.role_id, t2.user_id, t2.region_id, t2.posted, t2.job_title, t1.user_id, t1.user_agent, t1.accessed_url,t1.id,t1.accessed_url_status_code,t1.timestamp, t1.user_ip
                    from url_hits_log as t1 
                    join users_profile as t2 on t2.user_id=t1.user_id    
                    JOIN roles_users as t3 on   t3.user_id = t1.user_id
                    {$where_clause}                  
                    {$data['field']}                    
                    {$search}
                    {$order_by}
                    {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Access Control List Ajax Call Data */

    public static function access_control_list($data, $count) {
        /* Posted Data */
//        echo '<pre>';
//        print_r($data); exit;
        if (!empty($data['field'])) {
            switch ($data['field']) {
                case "name":
                    $data['field'] = "and ( CONCAT(TRIM(u2.first_name), ' ', TRIM(u2.last_name))  like '%{$data['key']}%' )";
                    break;
                case "designation":
                    $data['field'] = "and u2.job_title like '%{$data['key']}%' ";
                    break;
                case "posting":
                    $posting = implode("','", $data['posting']);
                    $data['field'] = "and u2.posted in ( '{$posting}') ";
                    //print_r($data['field']); exit;
                    break;
            }
        } else {
            $data['field'] = '';
        }


        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "first_name";
                    break;
                case "1":
                    $order_by_param = "job_title";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and  ( CONCAT(u2.first_name, ' ', TRIM(u2.last_name)) like '%{$data['sSearch']}%' OR u1.username like '%{$data['sSearch']}%')";
            //$search = "and ( u2.first_name like '%{$data['sSearch']}%' or u2.last_name like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 1";
        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where 1";
                    break;
                case 'r':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } elseif ($permission == 4) {
            $where_clause = "where u1.id  = {$login_user->id}";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                            from users as u1
                            join users_profile u2
                            on u1.id = u2.user_id
                            {$where_clause}
                            and u1.is_deleted = 0 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)                                                                                   
                            {$search}
                            {$data['field']}                            
                            ";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select *
                             from users as u1
                            join users_profile u2
                            on u1.id = u2.user_id  
                            {$where_clause}
                            and u1.is_deleted = 0 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)                              
                            {$search}
                            {$data['field']}
                            {$groupby} 
                            {$order_by}    
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    public static function update_password($user, $user_id) {

        $result = DB::update("users")->set($user)->where('id', '=', $user_id)->execute();
        return $result;
    }

    /* user inactive */

    public static function useractive($id) {
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        $date = date("Y-m-d H:i:s");
        $query = DB::update('users')->set(array('is_active' => 0, 'is_deleted' => 1, 'deactivated_at' => $date))
                ->where('id', '=', $id)
                ->execute();
        Helpers_Profile::user_activity_log($uid, 20);
    }

    /* user Block */

    public static function userblock($id) {
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        $date = date("Y-m-d H:i:s");
        $query = DB::update('users')->set(array('is_active' => 0, 'is_deleted' => 1, 'is_approved' => 2, 'deactivated_at' => $date))
                ->where('id', '=', $id)
                ->execute();
        Helpers_Profile::user_activity_log($uid, 20, NULL, NULL, NULL, NULL, $id);
    }

    /* user Un-Block */

    public static function userUnBlock($id) {
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        $date = date("Y-m-d H:i:s");
        $query = DB::update('users')->set(array('is_active' => 1, 'is_deleted' => 0, 'is_approved' => 0, 'deactivated_at' => $date))
                ->where('id', '=', $id)
                ->execute();
        Helpers_Profile::user_activity_log($uid, 21, NULL, NULL, NULL, NULL, $id);
    }

    /* user Approve */

    public static function userApprove($id) {
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        $date = date("Y-m-d H:i:s");
        $query = DB::update('users')->set(array('is_approved' => 1, 'approved_by' => $uid, 'approved_at' => $date))
                ->where('id', '=', $id)
                ->execute();
        //to add activity detail in user activity time line
        Helpers_Profile::user_activity_log($uid, 19, NULL, NULL, NULL, NULL, $id);
    }

    /* Delete Favourite user   */

    public static function delete_favouriteuser($id, $loginid) {
        $query = DB::delete('user_favourite_user')
                ->where('user_id', '=', $loginid)
                ->and_where('favourite_user_id', '=', $id)
                ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 5, NULL, NULL, NULL, NULL, $id);
        return $query;
    }

    /* Add Favourite User   */

    public static function add_favouriteuser($id, $loginid) {
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('user_favourite_user', array('user_id', 'favourite_user_id', 'added_on'))
                ->values(array($loginid, $id, $date))
                ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 4, NULL, NULL, NULL, NULL, $id);
        return $query;
    }

    /* Access Control List Ajax Call Data */

    public static function access_control_save($data) {
        $date = date('Y-m-d H:i:s');
        $login_user = Auth::instance()->get_user();
        foreach ($data['user-acl'] as $key => $value) {
            $DB = Database::instance();
            $sql = "SELECT user_id
                                    FROM  user_access_matrix
                                    WHERE user_id = '{$data['user-id']}' and user_activity_type = {$data['user-acl'][$key]} Limit 1";
            $acl_check = DB::query(Database::SELECT, $sql)->execute()->current();
            if (isset($data['user-acl-val'][$key]) && $data['user-acl-val'][$key] == 'on')
                $value = 1;
            else
                $value = 0;
            if (!empty($acl_check)) {
                $query = DB::update('user_access_matrix')
                        ->set(array('permission' => $value, 'modified_at' => $date))
                        ->where('user_id', '=', $data['user-id'])
                        ->and_where('user_activity_type', '=', $data['user-acl'][$key])
                        ->execute();
            } else {

                $query = DB::insert('user_access_matrix', array('user_id', 'user_activity_type', 'permission', 'created_by', 'created_at'))
                        ->values(array($data['user-id'], $data['user-acl'][$key], $value, $login_user->id, $date))
                        ->execute();
            }
        }
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 29, NULL, NULL, $data['user-id']);
    }

    /* user Report Moduel */
    /* Telco Reports Ajax Call Data */

    public static function telco_request_summary($data, $count) {
        /* Posted Data */

        if (empty($data['companyname'])) {
            $where_clause = "where 1";
            $data['companyname'] = '';
            if (!empty($data['startdate']) && !empty($data['enddate'])) {
                $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
                $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
                $where_clause .= " and (u1.date >= '{$data['startdate']}' AND  u1.date <= '{$data['enddate']}' )";
            }else{
                $data['startdate'] = '';
                $data['enddate'] = '';
            }

            $companymnc = '';
        } else {// $companymnc = array_filter(explode(',', $data['companyname']));               
            $companymnc = implode(',', $data['companyname']);

            if (!empty($data['startdate']) && !empty($data['enddate'])) {
                $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
                $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
                $where_clause = "and ( u1.company_mnc IN ({$companymnc}) and (u1.date >= '{$data['startdate']}' AND  u1.date <= '{$data['enddate']}' ))";
            } else {
                $where_clause = "and ( u1.company_mnc IN ({$companymnc}) )";
            }
        }

        /* Sorted Data */
        $order_by_param = "u1.date";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "u1.date";
                    break;
                case "1":
                    $order_by_param = "u1.company_mnc";
                    break;
                case "5":
                    $order_by_param = "u1.total_send";
                    break;
                default:
                    $order_by_param = "u1.date";
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
            $sql = "Select  COUNT(*) AS count
                        from telco_request_summary as u1
                        join mobile_companies u2
                        on u2.mnc=u1.company_mnc  
                        {$where_clause}
                        {$search}
                        ";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }

        /*  Fetch all Records */ else {

            $sql = "Select * 
                        from telco_request_summary as u1
                        join mobile_companies u2
                        on u2.mnc=u1.company_mnc 
                        {$where_clause}
                        {$search}
                        {$order_by}    
                        {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    public static function telco_request_total_summary($data, $count) {
        /* Posted Data */

//        if (empty($data['companyname'])) {
//            $where_clause = "where 1";
//            $data['companyname'] = '';
//            $data['startdate'] = '';
//            $data['enddate'] = '';
//            $companymnc = '';

//        } else {// $companymnc = array_filter(explode(',', $data['companyname']));
//            $companymnc = implode(',', $data['companyname']);
//            `if (!empty($data['startdate']) && !empty($data['enddate'])) {
//                $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
//                $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
//                $where_clause = "and ( u1.company_mnc IN ({$companymnc}) and (u1.date >= '{$data['startdate']}' AND  u1.date <= '{$data['enddate']}' ))";
//            } else {
//                $where_clause = "and ( u1.company_mnc IN ({$companymnc}) )";
//            }
//        }
        //to adjust start and end dates
        $where_clause = "where 1";
        $current_date = date("Y-m-d");
        if (!empty($data['startdate']) && !empty($data['enddate'])) {
            $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
            $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
            $where_clause .= " and (u1.date >= '{$data['startdate']}' AND  u1.date <= '{$data['enddate']}' )";
        } else {
          //  $where_clause .= " and u1.date='{$current_date}' ";
        }

        /* Sorted Data */
        $order_by_param = "u1.date";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "u1.date";
                    break;
                case "1":
                    $order_by_param = "u1.company_mnc";
                    break;
                case "5":
                    $order_by_param = "u1.total_send";
                    break;
                default:
                    $order_by_param = "u1.date";
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
        //group by clause
        $group_by = ' GROUP by u1.company_mnc';

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "  select COUNT(distinct (u2.mnc)) as count from telco_request_summary u1
                        inner join mobile_companies as u2 on u2.mnc =u1.company_mnc 
                        ";

            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
       //

        /*  Fetch all Records */
        else  {

            $sql = "SELECT date, u2.company_name, sum(total_received) as total_received, sum(total_send) as total_send
                FROM telco_request_summary u1 
                inner join mobile_companies as u2 on u2.mnc =u1.company_mnc 
                {$where_clause} 
                {$search}
                {$group_by}
                {$order_by} ";

          //  print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    public static function request_breakup_region($data, $count) {
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
        $order_by_param = "t2.region_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.region_id";
                    break;
            }
        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $where_clause = "where 0  ";
        if ($permission == 1 || $permission == 2) {
            $where_clause = "where 1  ";
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
            $sql = "SELECT  count(distinct region_id) as count FROM user_request as t1
                    join users_profile as t2 on t2.user_id = t1.user_id
                         {$where_clause}
                         {$date_where_clause}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {

            $sql = "SELECT t2.region_id, count(request_id) as total_request FROM user_request as t1
                    join users_profile as t2 on t2.user_id = t1.user_id
                     {$where_clause}
                     {$date_where_clause}
                     group by t2.region_id
                     {$order_by}
                        {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //breakup district
    public static function request_breakup_district($data, $count) {
        /* Posted Data */
//        echo '<pre>';
//        print_r($data); exit;
        if (empty($data['startdate']) || empty($data['enddate'])) {
            $date_where_clause = "";
        } else {
            if (DateTime::createFromFormat('Y-m-d', $data['startdate']) == FALSE) {
                $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
                $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
            } else {
                $data['startdate'] = date('Y-m-d', ($data['startdate']));
                $data['enddate'] = date('Y-m-d', ($data['enddate']));
            }
            $start_date = $data['startdate'] . " 00:00:00";
            $end_date = $data['enddate'] . " 23:59:59";

            $date_where_clause = " and (ur.created_at >= '{$start_date}' AND  ur.created_at <= '{$end_date}' ) ";
        }
        /* Sorted Data */
        $order_by_param = "up.region_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "up.region_id";
                    break;
                case "1":
                    $order_by_param = "ur.posted";
                    break;
            }
        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $where_clause = !empty($data['region_id']) ? "where up.region_id={$data['region_id']} " : 'where 1 ';

        //  if ($permission == 1) {
        //  $where_clause = " ";
        // }
        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            $where_clause .= ($result[0] != 'h') ? "AND up.posted = '$posting' ) " : '';
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
            $sql = "select COUNT(DISTINCT up.posted) as count from user_request ur 
                    JOIN users_profile up on up.user_id = ur.user_id
                         {$where_clause}
                         {$date_where_clause}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {

            $sql = "select COUNT(up.posted) as total_request,up.region_id , up.posted from user_request ur 
                    JOIN users_profile up on up.user_id = ur.user_id
                   {$where_clause}
                     {$date_where_clause}
                    GROUP by up.posted
                     {$order_by}
                        {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //request type breakup district
    public static function request_type_breakup_district($data, $count) {
        /* Posted Data */
//        echo '<pre>';
//        print_r($data); exit;
        if (empty($data['startdate']) || empty($data['enddate'])) {
            $date_where_clause = "";
        } else {
            if (DateTime::createFromFormat('Y-m-d', $data['startdate']) == FALSE) {
                $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
                $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
            } else {
                $data['startdate'] = date('Y-m-d', ($data['startdate']));
                $data['enddate'] = date('Y-m-d', ($data['enddate']));
            }
            $start_date = $data['startdate'] . " 00:00:00";
            $end_date = $data['enddate'] . " 23:59:59";

            $date_where_clause = " and (ur.created_at >= '{$start_date}' AND  ur.created_at <= '{$end_date}' ) ";
        }
        /* Sorted Data */
        $order_by_param = "up.region_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "up.region_id";
                    break;
                case "1":
                    $order_by_param = "ur.posted";
                    break;
            }
        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = !empty($data['posted']) ? "where up.posted='{$data['posted']}' " : 'where 0 ';

        //  if ($permission == 1) {
        //  $where_clause = " ";
        // }
        /*   if ($permission == 2 || $permission == 3 ) {
          $result = explode('-', $posting);
          $where_clause=($result[0] != 'h') ? "AND up.posted = $result[1] ) " : '';
          } */
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
            $sql = "select COUNT(DISTINCT ur.user_request_type_id) as count from user_request ur 
                    JOIN users_profile up on up.user_id = ur.user_id
                         {$where_clause}
                         {$date_where_clause}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {

            $sql = "select COUNT(ur.user_request_type_id) as total_request,up.region_id , up.posted,ur.user_request_type_id 
                    from user_request ur 
                    JOIN users_profile up on up.user_id = ur.user_id
                   {$where_clause}
                     {$date_where_clause}
                    GROUP by ur.user_request_type_id
                     {$order_by}
                        {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Number of Request Send Ajax Call Data */

    public static function project_request_type($data, $count, $project_id) {


        if (!empty($data['sdate'])) {
            $sdate =  Helpers_Utilities::encrypted_key($data['sdate'], 'decrypt');
        } else {
            $sdate = '';
        }
        if (!empty($data['edate'])) {
            $edate =  Helpers_Utilities::encrypted_key($data['edate'], 'decrypt');
        } else {
            $edate = '';
        }


        /* Sorted Data */
        $order_by_param = "t1.user_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "user_id";
                    break;
                case "5":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        $serach_date='';
        if (empty($edate)) {
            $edate = date("Y-m-d");
        }
        if (!empty($sdate)) {
            $start_date = date("Y-m-d", strtotime($sdate));
            $end_date = date("Y-m-d", strtotime($edate));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $serach_date = " and t1.created_at between '{$start_date}' and '{$end_date}' ";
        }

        /* Search via table */
        //no need for table search
        $DB = Database::instance();

        $where_clause = "where t1.project_id = {$project_id}";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(DISTINCT t1.user_request_type_id, t1.user_id) as count   
                               FROM user_request as t1
                               join users_profile as t2 on t2.user_id = t1.user_id
                               {$where_clause}
                               {$serach_date}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  t1.user_id,t1.user_request_type_id,t1.project_id, t2.region_id , t2.posted ,                              
                               COUNT(*) as count   
                               FROM user_request as t1 
                               join users_profile as t2 on t2.user_id = t1.user_id 
                               {$where_clause}
                               {$serach_date}
                            GROUP BY t1.user_request_type_id, t1.user_id
                            {$order_by}    
                            {$limit}";
//            echo '<pre>';
//            print_r($sql);
//            exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Number of Request Send Ajax Call Data */

    public function project_request_send_detail($data, $count, $userid, $request_type, $project_id) {
        //print_r($data); exit;        
        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "5":
                    $order_by_param = "created_at";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = "";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        //print_r($limit); exit;
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and t1.requested_value like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }


        if (!empty($data['sdate'])) {
            $sdate =  Helpers_Utilities::encrypted_key($data['sdate'], 'decrypt');
        } else {
            $sdate = '';
        }
        if (!empty($data['edate'])) {
            $edate =  Helpers_Utilities::encrypted_key($data['edate'], 'decrypt');
        } else {
            $edate = '';
        }
        $serach_date='';

        if (empty($edate)) {
            $edate = date("Y-m-d");
        }
        if (!empty($sdate)) {
            $start_date = date("Y-m-d", strtotime($sdate));
            $end_date = date("Y-m-d", strtotime($edate));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $serach_date = " and t1.created_at between '{$start_date}' and '{$end_date}' ";
        }


        $DB = Database::instance();

        $where_clause = "where t1.user_id = {$userid} and t1.user_request_type_id = {$request_type} and t1.project_id ={$project_id} ";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  COUNT(*) as count   
                               FROM user_request as t1
                               {$where_clause}
                               {$serach_date}
                               {$search}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  user_id, requested_value, reason, concerned_person_id,request_id,
                               user_request_type_id,status,created_at
                               FROM user_request as t1 
                               {$where_clause}
                               {$serach_date}
                               {$search}                          
                                {$order_by}    
                                {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

}
