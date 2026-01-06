<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related user request  
 */
class Model_Watchlist {
    
    
    public static function tag_person_list($data, $count) {
        //print_r($data); exit;
        //posted data from advance search
        $category_where = '';
        $watchlist_status_where = '';
        if (isset($data['category_type'])) {  
            $keys = array_keys($data['category_type']);
            $values = implode(',', $keys);            
            $category_where = "and t1.tag_id in ({$values})";
        }
        if (isset($data['watchlist_status'])) {           
            if ($data['watchlist_status'] == 1) {
             $watchlist_status_where = 'and t1.in_watchlist = 1';   
            }            
            else if ($data['watchlist_status'] == 0) {
             $watchlist_status_where = 'and t1.in_watchlist = 0';   
            }            
        }
        //print_r($watchlist_status_where); exit;

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";
            
            $DB = Database::instance();
            $sql = "SELECT  person_id FROM  person {$where}";

            $person_array = DB::query(Database::SELECT, $sql)->execute()->as_array();

            $request_array = implode(', ', array_values(array_column($person_array, 'person_id')));

            if (!empty($person_array))
                $search = "and ( t1.person_id in ({$request_array}))";
            else {
                $search = "";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by t1.person_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 0";
        //$where_clause = "where t1.user_request_type_id =8  ";

        if ($permission == 1 || $permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'd':
                    $where_clause = "where t1.tag_district_id ='$result[1]' ";
                    break;
            }
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(DISTINCT person_id) as count
                                FROM person_tags as t1
                                {$where_clause}
                                {$watchlist_status_where}
                                {$category_where}
                                 {$search}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select t1.*, Group_concat(a.tag_name) as tagname
                                FROM person_tags as t1
                                inner join lu_tags a on FIND_IN_SET(a.id, t1.tag_id)
                                {$where_clause}  
                                {$watchlist_status_where}                                      
                                {$category_where}
                                {$search}    
                                {$groupby}    
                                {$limit}";
            //  print_r($sql); exit; and t1.in_watchlist = 0

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    
        /* Add Person to Watch List   */
    public static function add_to_watchlist($person_id, $user_district) {
        //code here
        $date = date('Y-m-d H:i:s');
        $query = DB::update('person_tags')->set(array('in_watchlist' => '1'))
                ->where('person_id', '=', $person_id)
                ->and_where('tag_district_id', '=', $user_district)
                ->execute();
//        //to add activity detail in user activity time line
//        $login_user = Auth::instance()->get_user();
//        $uid = $login_user->id;
//        Helpers_Profile::user_activity_log($uid, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $query;      
    }
        /* Add Person to Watch List   */
    public static function remove_from_watchlist($person_id, $user_district) {
        //code here
        $date = date('Y-m-d H:i:s');
        $query = DB::update('person_tags')->set(array('in_watchlist' => '0'))
                ->where('person_id', '=', $person_id)
                ->and_where('tag_district_id', '=', $user_district)
                ->execute();
//        //to add activity detail in user activity time line
//        $login_user = Auth::instance()->get_user();
//        $uid = $login_user->id;
//        Helpers_Profile::user_activity_log($uid, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $query;      
    }
    //wwwwwwwwwwwwwwww
    public static function watchlist_persons($data, $count) {
        //print_r($data); exit;
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t2.name like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by t1.tag_district_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
       // print_r($posting); exit;
        $result = explode('-', $posting);
        $where_clause = "where 0";
        
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            switch ($result[0]) {
                case 'd':
                    $where_clause = "where t1.tag_district_id ='$result[1]' ";
                    break;
                case 'r':
                    $where_clause = "where t1.tag_district_id IN (SELECT t4.district_id from district as t4 where t4.region_id = $result[1] ) ";
                    break;
            }
        } else if ($permission == 1 || $permission == 5) {
            $where_clause = "where 1";
        } else if ($permission == 4 && $result[0] == 'd') {
            $where_clause = "where t1.tag_district_id ='$result[1]' ";
        }
        //print_r($where_clause); exit;
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(DISTINCT t1.tag_district_id) as count
                                FROM person_tags as t1
                                join district as t2 on t2.district_id = t1.tag_district_id                                
                                {$where_clause}
                                and t1.in_watchlist = 1
                                 {$search}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select COUNT(DISTINCT t1.id) as count,t1.tag_district_id, t2.name                                
                                FROM person_tags as t1
                                join district as t2 on t2.district_id = t1.tag_district_id                                
                                {$where_clause}
                                and t1.in_watchlist = 1    
                                {$search}    
                                {$groupby}    
                                {$limit}";
             // print_r($sql); exit; //and t1.in_watchlist = 0

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    //watch person list details
    public static function watchlist_persons_details($data, $count) {
        //print_r($data); exit;
        //posted data from advance search
        
        $and_where_clause = "and t1.tag_district_id = 0";
        if (isset($data['district_id'])) { 
            $district_id=  Helpers_Utilities::encrypted_key($data['district_id'], 'decrypt');
            $and_where_clause = "and t1.tag_district_id = {$district_id}";
        }
        $category_where = '';
        if (isset($data['category_type'])) {  
            $keys = array_keys($data['category_type']);
            $values = implode(',', $keys);            
            $category_where = "and t1.tag_id in ({$values})";
        }        
        //print_r($category_where); exit;
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";            
            $DB = Database::instance();
            $sql = "SELECT  person_id FROM  person {$where}";

            $person_array = DB::query(Database::SELECT, $sql)->execute()->as_array();

            $request_array = implode(', ', array_values(array_column($person_array, 'person_id')));

            if (!empty($person_array))
                $search = "and  t1.person_id in ({$request_array})";
            else {
                $search = "";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by t1.id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where t1.in_watchlist = 1";
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(DISTINCT t1.id) as count
                                FROM person_tags as t1
                                inner join lu_tags a on a.id = t1.tag_id
                                {$where_clause}
                                {$and_where_clause}
                                {$category_where}
                                 {$search}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select t1.*, a.tag_name as tagname, a.tag_description
                                FROM person_tags as t1
                                inner join lu_tags a on a.id = t1.tag_id
                                {$where_clause}  
                                {$and_where_clause}                                     
                                {$category_where}
                                {$search}    
                                {$groupby}    
                                {$limit}";
             // print_r($sql); exit; //and t1.in_watchlist = 0

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    //watch person list details
    public static function user_watchlist_persons_details($data, $count) {
      //  print_r($data); exit;
        //posted data from advance search
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        // print_r($posting); exit;
        $result = explode('-', $posting);
        $and_where_clause = "where 0";

        if ($permission == 2 || $permission == 3 || $permission == 4) {
            switch ($result[0]) {
                case 'd':
                    $and_where_clause = "where t1.tag_district_id ='$result[1]' ";
                    break;
                case 'r':
                    $and_where_clause = "where t1.tag_district_id IN (SELECT t4.district_id from district as t4 where t4.region_id = $result[1] ) ";
                    break;
            }
        } else if ($permission == 1 || $permission == 5) {
            $and_where_clause = "where 1";
        } else if ($permission == 4 && $result[0] == 'd') {
            $and_where_clause = "where t1.tag_district_id ='$result[1]' ";
        }

        $category_where = '';
        if (isset($data['category_type'])) {
            $keys = array_keys($data['category_type']);
            $values = implode(',', $keys);
            $category_where = "and t1.tag_id in ({$values})";
        }
        //print_r($category_where); exit;
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
//        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
//            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
//            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";
//            $DB = Database::instance();
//            $sql = "SELECT  person_id FROM  person {$where}";
//
//            $person_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
//
//            $request_array = implode(', ', array_values(array_column($person_array, 'person_id')));
//
//            if (!empty($person_array))
//                $search = "and  t1.person_id in ({$request_array})";
//            else {
//                $search = "";
//            }
//        } else {
//            $search = "";
//        }

        /* Group By */
        $groupby = "group by t1.user_id";



        $where_clause = "and t1.in_watchlist = 1";
//        {$search}
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(DISTINCT t1.user_id) as count
                                FROM person_tags as t1
                                {$and_where_clause}
                                {$where_clause}
                                {$category_where}
                                ";
           // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select count(DISTINCT t1.person_id) as total, t1.user_id
                                FROM person_tags as t1
                                {$and_where_clause} 
                                 {$where_clause}                                    
                                {$category_where}
                                   
                                {$groupby}    
                                {$limit}";
//              print_r($sql); exit; //and t1.in_watchlist = 0

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    //watch person list details
    public static function user_wl_persons_info($data, $count,$uid) {

        //posted data from advance search
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        // print_r($posting); exit;
        $result = explode('-', $posting);
        $and_where_clause = "where t1.user_id={$uid} ";

//        if ($permission == 2 || $permission == 3 || $permission == 4) {
//            switch ($result[0]) {
//                case 'd':
//                    $and_where_clause = "where t1.tag_district_id ='$result[1]' ";
//                    break;
//                case 'r':
//                    $and_where_clause = "where t1.tag_district_id IN (SELECT t4.district_id from district as t4 where t4.region_id = $result[1] ) ";
//                    break;
//            }
//        } else if ($permission == 1 || $permission == 5) {
//            $and_where_clause = "where 1";
//        } else if ($permission == 4 && $result[0] == 'd') {
//            $and_where_clause = "where t1.tag_district_id ='$result[1]' ";
//        }

        $category_where = '';
        if (isset($data['category_type'])) {
            $keys = array_keys($data['category_type']);
            $values = implode(',', $keys);
            $category_where = "and t1.tag_id in ({$values})";
        }
        //print_r($category_where); exit;
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
//        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
//            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
//            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";
//            $DB = Database::instance();
//            $sql = "SELECT  person_id FROM  person {$where}";
//
//            $person_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
//
//            $request_array = implode(', ', array_values(array_column($person_array, 'person_id')));
//
//            if (!empty($person_array))
//                $search = "and  t1.person_id in ({$request_array})";
//            else {
//                $search = "";
//            }
//        } else {
//            $search = "";
//        }

        /* Group By */
        $groupby = "group by t1.person_id";



        $where_clause = "and t1.in_watchlist = 1";
        $order_by= " order by t2.sending_date desc";
//        {$search}
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(DISTINCT t1.person_id) as count
                                FROM person_tags as t1
                                
                                {$and_where_clause}
                                {$where_clause}
                                {$category_where}
                                ";
          //  print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select count(DISTINCT t2.request_id) as total, max(t2.sending_date) as sending_date,t2.request_id ,t2.user_id, t1.person_id 
                                FROM person_tags as t1
                                right join user_request as t2 on t1.person_id= t2.concerned_person_id and t1.user_id =t2.user_id
                                {$and_where_clause} 
                                 {$where_clause}                                    
                                {$category_where}
                                   
                                {$groupby}
                                {$order_by}    
                                {$limit}";
//              print_r($sql); exit; //and t1.in_watchlist = 0

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    
}
