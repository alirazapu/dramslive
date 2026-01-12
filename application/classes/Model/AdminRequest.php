<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template
 */
class Model_AdminRequest
{
    /* insert request type  */

    public static function admin_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $startDate, $endDate, $reason, $file_name, $rqtby)
    {

        $date = date('Y-m-d H:i:s');
        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        if ($permission == 1) {
            $request_priority = 3;
        } else {
            $request_priority = 1;
        }
        $DB = Database::instance();

        $sql = "INSERT INTO admin_request (reference_id, user_id, user_request_type_id, company_name,  status, requested_value, startDate, endDate, reason, request_priority, file_name, rqtbyname) 
        VALUES({$reference_id}, '{$user_id}', '{$request_type}' , '{$company_name}', '{$status}', '{$requested_value}', '{$startDate}', '{$endDate}', '{$reason}', '{$request_priority}', '{$file_name}', '{$rqtby}')";

        $members = $DB->query(Database::INSERT, $sql, FALSE);
        return $members[0];
    }

    //nadra request insertion
    public static function admin_nadra_request($user_id, $cnic, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name)
    {

        $query = DB::insert('admin_nadra_request', array('user_id', 'cnic', 'request_date', 'reason', 'file_name', 'region_id', 'district_id', 'status', 'rqtbyname'))
            ->values(array($user_id, $cnic, $date, $reason, $file_name, $rqtby_region_id, $rqtby_district_id, $status, $rqtby))
            ->execute();
        return $query;
    }

    public static function admin_family_tree_request($user_id, $cnic, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name)
    {

        $query = DB::insert('admin_familytree_request', array('user_id', 'cnic', 'request_date', 'reason', 'file_name', 'region_id', 'district_id', 'status', 'rqtbyname'))
            ->values(array($user_id, $cnic, $date, $reason, $file_name, $rqtby_region_id, $rqtby_district_id, $status, $rqtby))
            ->execute();
        return $query;
    }

    public static function admin_travel_request($user_id, $cnic, $passport, $date, $status, $rqtby, $rqtby_region_id, $rqtby_district_id, $reason, $file_name)
    {

        $query = DB::insert('admin_travel_request', array('user_id', 'cnic', 'passport', 'request_date', 'reason', 'file_name', 'region_id', 'district_id', 'status', 'rqtbyname'))
            ->values(array($user_id, $cnic, $passport, $date, $reason, $file_name, $rqtby_region_id, $rqtby_district_id, $status, $rqtby))
            ->execute();
        return $query;
    }

    /* email sended  */

    public static function admin_email_sended($to, $subject, $body, $reference_number, $process_status, $status, $startDate = NULL, $enddate = NULL)
    {
        $startDate = !empty($startDate) ? (date('Y-m-d', strtotime($startDate))) : $startDate;
        $enddate = !empty($enddate) ? (date('Y-m-d', strtotime($enddate))) : $enddate;

        $date = date('Y-m-d H:i:s');
        $query = DB::insert('admin_email_messages', array('sender_id', 'message_body', 'message_subject', 'message_date'))
            ->values(array($to, $body, $subject, $date))
            ->execute();

        $DB = Database::instance();

        $query = DB::update('admin_request')->set(array('status' => $status, 'processing_index' => $process_status, 'startDate' => $startDate, 'endDate' => $enddate, 'message_id' => $query[0]))
            ->where('request_id', '=', $reference_number)
            ->execute();

//        return $query[0]; //updated by yaser 28-07-20
        return $reference_number;
    }

    /* Admin request status Ajax Call */

    public static function admin_sent_request_status($data, $count)
    {
//        echo '<pre>';
//        print_r($data);
//        exit;
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
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
        if (!empty($data['request_type_new'])) {
            // $request_id=  Helpers_Utilities::encrypted_key($data['request_type_new'], 'decrypt');
            $requesttype = array_values($data['request_type_new']);
            $requesttype_value = implode(',', $requesttype);
            $data['field'] = "and t1.user_request_type_id in ({$requesttype_value})";
        } else {
            $data['field'] = '';
        }
        if (empty($data['mnc_new']))
            $data['mnc'] = '';
        else {
            $mnc_array = array_values($data['mnc_new']);
            $mnc_value = implode(',', $mnc_array);
            $data['mnc'] = " and t1.company_name in ({$mnc_value}) ";
        }
        //print_r($data['r_category']); exit;
        if (!empty($data['r_category']) && $data['r_category'] == 2)
            $data['r_category'] = " and t1.user_id = $login_user->id";
        else {
            $data['r_category'] = '';
        }


        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "user_id";
                    break;
                case "1":
                    $order_by_param = "user_request_type_id";
                    break;
                case "4":
                    $order_by_param = "created_at";
                    break;
                case "5":
                    $order_by_param = "status";
                    break;
                case "7":
                    $order_by_param = "processing_index";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'created_at';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $where = "WHERE username like '%{$data['sSearch']}%'";
            $DB = Database::instance();
            $sql = "SELECT  id FROM  users {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%' or t1.reference_id like '%{$data['sSearch']}%')";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        //    $where_clause = "where 1";
        if (!empty($login_user->id) && in_array($login_user->id, [842, 137, 2031, 2603])) {
            $where_clause = "where 1";

        } else {
            $where_clause = "where t1.user_id not IN (842, 137, 2031, 2603)";
        }
//        $where_clause = "where t1.user_id not IN (182,137,136)";

        $and_status = '';
        if (isset($data['e_status'])) {
            switch ($data['e_status']) {
                case 0;
                    $and_status = ' and  t1.status = 0 ';
                    break;
                case 1;
                    $and_status = ' and  t1.status = 1 ';
                    break;
                case 2;
                    $and_status = ' and  t1.status = 2 ';
                    break;
                case 3;
                    $and_status = '  ';
                    break;
            }
        }
        $and_reply = '';
        if (isset($data['r_status'])) {
            switch ($data['r_status']) {
                case 0;
                    $and_reply = ' and  t1.reply = 0 ';
                    break;
                case 1;
                    $and_reply = ' and  t1.reply = 1 ';
                    break;
                case 2;
                    $and_reply = '  ';
                    break;
            }
        }
        $and_processing = '';
        if (isset($data['p_status'])) {
            switch ($data['p_status']) {
                case 3;
                    $and_processing = ' and  t1.processing_index = 3 ';
                    break;
                case 4;
                    $and_processing = ' and  t1.processing_index = 4 ';
                    break;
                case 5;
                    $and_processing = ' and  t1.processing_index = 5 ';
                    break;
                default:
                    $and_processing = '  ';
            }
        }
        $result = explode('-', $posting);
        //request status
        if ($permission == 2 || $permission == 3) {
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                        $where_clause = "where 1";
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
        } else if ($permission == 4) {
//            if($result[0]=='h')
//                $where_clause = "where t1.user_id not IN (182,137,136,184) and t1.user_id not in (select user_id from users_profile as u1 where u1.region_id in (1,3,4))";
//            else
            $where_clause = "where t1.user_id = $login_user->id";
        }
        if (!empty($data['txtbd'])) {
            $where_body = " and em.received_body like '%{$data['txtbd']}%'  ";
        } else {
            $where_body = " ";
        }
        if (!empty($data['rqtbyname'])) {
            $where_rqt = " and t1.rqtbyname like '%{$data['rqtbyname']}%'  ";
        } else {
            $where_rqt = " ";
        }

        $DB = Database::instance();

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM admin_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join admin_email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}
                                {$and_status}    
                                {$and_reply}    
                                {$and_processing}    
                                {$where_body}    
                                {$where_rqt}    
                                {$serach_date}    
                                {$data['field']}
                                {$data['r_category']}
                                {$data['mnc']}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT t1.reference_id, t1.reply,t1.company_name, t1.reason, request_id,user_request_type_id, user_id, email_type_name, requested_value,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.received_file_path,em.received_body, em.message_subject, em.sender_id FROM 
                                admin_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join admin_email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$and_status}    
                                {$and_reply}    
                                {$and_processing}
                                {$where_body} 
                                {$where_rqt}        
                                {$serach_date}    
                                {$data['field']}
                                {$data['mnc']}
                                {$data['r_category']}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Admin request status Ajax Call */

    public static function admin_sent_request_single_user($data, $count)
    {

        $name = Helpers_Utilities::encrypted_key($data['name'], "decrypt");

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

//        $where_clause =  "where 1";
//        // request type
//        if(!empty($data['request_type'])){
//            $where_clause .= " where t1.user_request_type_id = {$data['request_type']}";
//        }


        $search_req_id = '';
        if (!empty($data['request_type'])) {
            $search_req_id .= " and t1.user_request_type_id = {$data['request_type']}";
        }
        //dates filter
        $serach_date = ' ';
        //if end date is not selected then user current date as end date
        if (empty($data['enddate'])) {
            $data['enddate'] = date("Y-m-d");
        }
        if (!empty($data['fr'])) {
            $start_date = date("Y-m-d", strtotime($data['fr']));
            $end_date = date("Y-m-d", strtotime($data['enddate']));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $serach_date = " and created_at between '{$start_date}' and '{$end_date}' ";
        }
        if (!empty($data['request_type_new'])) {
            // $request_id=  Helpers_Utilities::encrypted_key($data['request_type_new'], 'decrypt');
            $requesttype = array_values($data['request_type_new']);
            $requesttype_value = implode(',', $requesttype);
            $data['field'] = "and t1.user_request_type_id in ({$requesttype_value})";
        } else {
            $data['field'] = '';
        }
        if (empty($data['mnc_new']))
            $data['mnc'] = '';
        else {
            $mnc_array = array_values($data['mnc_new']);
            $mnc_value = implode(',', $mnc_array);
            $data['mnc'] = " and t1.company_name in ({$mnc_value}) ";
        }
        //print_r($data['r_category']); exit;
        if (!empty($data['r_category']) && $data['r_category'] == 2)
            $data['r_category'] = " and t1.user_id = $login_user->id";
        else {
            $data['r_category'] = '';
        }


        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "user_id";
                    break;
                case "1":
                    $order_by_param = "user_request_type_id";
                    break;
                case "4":
                    $order_by_param = "created_at";
                    break;
                case "5":
                    $order_by_param = "status";
                    break;
                case "7":
                    $order_by_param = "processing_index";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'created_at';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $where = "WHERE username like '%{$data['sSearch']}%'";
            $DB = Database::instance();
            $sql = "SELECT  id FROM  users {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%' or t1.reference_id like '%{$data['sSearch']}%' or t2.email_type_name like '%{$data['sSearch']}%')";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        //    $where_clause = "where 1";
        $where_clause = "where t1.user_id not IN (842, 137, 2031, 2603)";
        if (($name == 'NA'))
            $where_clause .= " and t1.rqtbyname is null ";
        elseif (empty($name))
            $where_clause .= " and t1.rqtbyname = '' ";
        else
            $where_clause .= " and t1.rqtbyname ='$name' ";


        //$where_clause .=" and where t1.rqtbyname IS NULL";
        // $where_clause .= "where t1.rqtbyname =''";


//        $where_clause = "where t1.user_id not IN (182,137,136)";

        $and_status = '';
        if (isset($data['e_status'])) {
            switch ($data['e_status']) {
                case 0;
                    $and_status = ' and  t1.status = 0 ';
                    break;
                case 1;
                    $and_status = ' and  t1.status = 1 ';
                    break;
                case 2;
                    $and_status = ' and  t1.status = 2 ';
                    break;
                case 3;
                    $and_status = '  ';
                    break;
            }
        }
        $and_reply = '';
        if (isset($data['r_status'])) {
            switch ($data['r_status']) {
                case 0;
                    $and_reply = ' and  t1.reply = 0 ';
                    break;
                case 1;
                    $and_reply = ' and  t1.reply = 1 ';
                    break;
                case 2;
                    $and_reply = '  ';
                    break;
            }
        }
        $and_processing = '';
        if (isset($data['p_status'])) {
            switch ($data['p_status']) {
                case 3;
                    $and_processing = ' and  t1.processing_index = 3 ';
                    break;
                case 4;
                    $and_processing = ' and  t1.processing_index = 4 ';
                    break;
                case 5;
                    $and_processing = ' and  t1.processing_index = 5 ';
                    break;
                default:
                    $and_processing = '  ';
            }
        }
        $result = explode('-', $posting);
        //request status
        if ($permission == 2 || $permission == 3) {
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause .= " and t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                        $where_clause .= " and 1";
                    }
                    break;
                case 'r':
                    $where_clause .= " and t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause .= " and t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause .= " and t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else if ($permission == 4) {
//            if($result[0]=='h')
//                $where_clause = "where t1.user_id not IN (182,137,136,184) and t1.user_id not in (select user_id from users_profile as u1 where u1.region_id in (1,3,4))";
//            else
            $where_clause .= " and t1.user_id = $login_user->id";
        }
        if (!empty($data['txtbd'])) {
            $where_body = " and em.received_body like '%{$data['txtbd']}%'  ";
        } else {
            $where_body = " ";
        }
        if (!empty($data['rqtbyname'])) {
            $where_rqt = " and t1.rqtbyname like '%{$data['rqtbyname']}%'  ";
        } else {
            $where_rqt = " ";
        }

        $DB = Database::instance();

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM admin_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join admin_email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}
                                {$search_req_id}
                                {$and_status}    
                                {$and_reply}    
                                {$and_processing}    
                                {$where_body}    
                                {$where_rqt}    
                                {$serach_date}    
                                {$data['field']}
                                {$data['r_category']}
                                {$data['mnc']}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();

            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT t1.user_request_type_id, t1.reference_id, t1.reply,t1.company_name, t1.reason, request_id,user_request_type_id, user_id, email_type_name, requested_value,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.received_file_path,em.received_body, em.message_subject, em.sender_id FROM 
                                admin_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join admin_email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$and_status}
                                {$search_req_id}    
                                {$and_reply}    
                                {$and_processing}
                                {$where_body} 
                                {$where_rqt}        
                                {$serach_date}    
                                {$data['field']}
                                {$data['mnc']}
                                {$data['r_category']}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Delete user request  */

    public static function delete_user_request($request_id, $loginid)
    {
        $sql = "DELETE t1, t2 
                FROM admin_request as t1 
                INNER JOIN admin_email_messages as t2 ON (t2.message_id=t1.message_id)
                WHERE t1.request_id ={$request_id}";
        $results = DB::query(Database::DELETE, $sql)->execute();
        //Helpers_Profile::user_activity_log($uid, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $results;
    }

    /* User request status Ajax Call */

    public static function user_sent_request_single_user($data, $count)
    {

        $id = Helpers_Utilities::encrypted_key($data['id'], "decrypt");

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

//        $where_clause =  "where 1";
//        // request type
//        if(!empty($data['request_type'])){
//            $where_clause .= " where t1.user_request_type_id = {$data['request_type']}";
//        }

        $search_req_id = '';
        if (!empty($data['request_type'])) {
            $search_req_id .= " and t1.user_request_type_id = {$data['request_type']}";
        }
        //dates filter
        $serach_date = ' ';
        //if end date is not selected then user current date as end date
        if (empty($data['enddate'])) {
            $data['enddate'] = date("Y-m-d");
        }
        if (!empty($data['fr'])) {
            $start_date = date("Y-m-d", strtotime($data['fr']));
            $end_date = date("Y-m-d", strtotime($data['enddate']));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $serach_date = " and created_at between '{$start_date}' and '{$end_date}' ";
        }
        if (!empty($data['request_type_new'])) {
            // $request_id=  Helpers_Utilities::encrypted_key($data['request_type_new'], 'decrypt');
            $requesttype = array_values($data['request_type_new']);
            $requesttype_value = implode(',', $requesttype);
            $data['field'] = "and t1.user_request_type_id in ({$requesttype_value})";
        } else {
            $data['field'] = '';
        }
        if (empty($data['mnc_new']))
            $data['mnc'] = '';
        else {
            $mnc_array = array_values($data['mnc_new']);
            $mnc_value = implode(',', $mnc_array);
            $data['mnc'] = " and t1.company_name in ({$mnc_value}) ";
        }
        //print_r($data['r_category']); exit;
        if (!empty($data['r_category']) && $data['r_category'] == 2)
            $data['r_category'] = " and t1.user_id = $login_user->id";
        else {
            $data['r_category'] = '';
        }


        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "user_id";
                    break;
                case "1":
                    $order_by_param = "user_request_type_id";
                    break;
                case "4":
                    $order_by_param = "created_at";
                    break;
                case "5":
                    $order_by_param = "status";
                    break;
                case "7":
                    $order_by_param = "processing_index";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'created_at';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $where = "WHERE username like '%{$data['sSearch']}%'";
            $DB = Database::instance();
            $sql = "SELECT  id FROM  users {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%' or t1.reference_id like '%{$data['sSearch']}%' or t2.email_type_name like '%{$data['sSearch']}%')";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        //    $where_clause = "where 1";
//        $where_clause = "where t1.user_id not IN (419)";
//        if(($name=='NA'))
//            $where_clause = "where t1.rqtbyname is null";
//        elseif(empty($name))
//            $where_clause = "where t1.rqtbyname = ''" ;
//         else
//            $where_clause = "where t1.rqtbyname ='$name'";
        $where_clause = "where t1.user_id ='$id'";


        //$where_clause .=" and where t1.rqtbyname IS NULL";
        // $where_clause .= "where t1.rqtbyname =''";


//        $where_clause = "where t1.user_id not IN (182,137,136)";

        $and_status = '';
        if (isset($data['e_status'])) {
            switch ($data['e_status']) {
                case 0;
                    $and_status = ' and  t1.status = 0 ';
                    break;
                case 1;
                    $and_status = ' and  t1.status = 1 ';
                    break;
                case 2;
                    $and_status = ' and  t1.status = 2 ';
                    break;
                case 3;
                    $and_status = '  ';
                    break;
            }
        }
        $and_reply = '';
        if (isset($data['r_status'])) {
            switch ($data['r_status']) {
                case 0;
                    $and_reply = ' and  t1.reply = 0 ';
                    break;
                case 1;
                    $and_reply = ' and  t1.reply = 1 ';
                    break;
                case 2;
                    $and_reply = '  ';
                    break;
            }
        }
        $and_processing = '';
        if (isset($data['p_status'])) {
            switch ($data['p_status']) {
                case 3;
                    $and_processing = ' and  t1.processing_index = 3 ';
                    break;
                case 4;
                    $and_processing = ' and  t1.processing_index = 4 ';
                    break;
                case 5;
                    $and_processing = ' and  t1.processing_index = 5 ';
                    break;
                default:
                    $and_processing = '  ';
            }
        }
        $result = explode('-', $posting);
        //request status
        if ($permission == 2 || $permission == 3) {
            switch ($result[0]) {
                case 'h':
                    if ($permission == 3) {
                        //H.Q Exective
                        $where_clause = "where t1.user_id IN (SELECT u1.user_id FROM users_profile as u1 join roles_users as u2 on u2.user_id = u1.user_id where (u2.role_id not in (1,2,3) || u2.user_id = {$login_user->id}) ) ";
                    } else {
                        $where_clause = "where 1";
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
        } else if ($permission == 4) {
//            if($result[0]=='h')
//                $where_clause = "where t1.user_id not IN (182,137,136,184) and t1.user_id not in (select user_id from users_profile as u1 where u1.region_id in (1,3,4))";
//            else
            $where_clause = "where t1.user_id = $login_user->id";
        }
        if (!empty($data['txtbd'])) {
            $where_body = " and em.received_body like '%{$data['txtbd']}%'  ";
        } else {
            $where_body = " ";
        }
        if (!empty($data['rqtbyname'])) {
            $where_rqt = " and t1.rqtbyname like '%{$data['rqtbyname']}%'  ";
        } else {
            $where_rqt = " ";
        }

        $DB = Database::instance();

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM admin_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join admin_email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}
                                {$search_req_id}
                                {$and_status}    
                                {$and_reply}    
                                {$and_processing}    
                                {$where_body}    
                                {$where_rqt}    
                                {$serach_date}    
                                {$data['field']}
                                {$data['r_category']}
                                {$data['mnc']}";
//            print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();

            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT t1.user_request_type_id, t1.reference_id, t1.reply,t1.company_name, t1.reason, request_id,user_request_type_id, user_id, email_type_name, requested_value,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.received_file_path,em.received_body, em.message_subject, em.sender_id FROM 
                                admin_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join admin_email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$and_status}
                                {$search_req_id}    
                                {$and_reply}    
                                {$and_processing}
                                {$where_body} 
                                {$where_rqt}        
                                {$serach_date}    
                                {$data['field']}
                                {$data['mnc']}
                                {$data['r_category']}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Admin request count Ajax Call */

    public static function admin_sent_request_count($data, $count)
    {

        $login_user = Auth::instance()->get_user();

        $where_clause = "where 1 and t1.user_id not IN (842, 137, 2031, 2603)";
        // request type
        if (!empty($data['request_type'])) {
            $where_clause .= " and t1.user_request_type_id = {$data['request_type']}";
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
            $serach_date = " and t1.created_at between '{$start_date}' and '{$end_date}' ";
        }

        /* Search via table */
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);

            $search = " and t1.rqtbyname like '%{$data['sSearch']}%'";
        }


        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {

                case "0":
                    $order_by_param = "t1.created_at";
                    break;

            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'desc';
        }
        $group_by = 'group by t1.rqtbyname';
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        if (!empty($data['txtbd'])) {
            $where_body = " and em.received_body like '%{$data['txtbd']}%'  ";
        } else {
            $where_body = " ";
        }


        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  count( DISTINCT(t1.rqtbyname)) AS count
                                FROM admin_request as t1 
                                  join admin_email_messages as em on em.message_id = t1.message_id 
                           ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } else {

            $DB = Database::instance();
            $sql = "Select  COUNT(request_id) as count, rqtbyname , created_at, user_request_type_id 
                    FROM admin_request as t1 
                      join admin_email_messages as em on em.message_id = t1.message_id   
                    {$where_clause}
                    {$where_body}                 
                    {$serach_date}
                    {$search}                                    
                    {$group_by}
                    {$order_by}
                    {$limit}
                      ";
            //    print_r($sql); exit;

//                      if($login_user->id==138){
//            echo '<pre>';
//            print_r($sql); exit;
//        }

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }

    } /* User request count Ajax Call */

    public static function user_request_count($data, $count)
    {

        $login_user = Auth::instance()->get_user();

        $where_clause = "where 1 and t1.user_id not IN (842, 137, 2031, 2603)";
        // request type
        if (!empty($data['request_type'])) {
            $where_clause .= " and t1.user_request_type_id = {$data['request_type']}";
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

        /* Search via table */
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " and rqtbyname like '%{$data}%'";
        }


        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {

                case "0":
                    $order_by_param = "created_at";
                    break;

            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'desc';
        }
        $group_by = 'group by user_id';
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
        if (!empty($data['txtbd'])) {
            $where_body = " and em.received_body like '%{$data['txtbd']}%'  ";
        } else {
            $where_body = " ";
        }

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  count( DISTINCT(user_id)) AS count
                                FROM admin_request as t1 
                                join admin_email_messages as em on em.message_id = t1.message_id  

                           ";
            //print_r($sql); exit();
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } else {

            $DB = Database::instance();
            $sql = "Select  COUNT(request_id) as count, user_id , created_at, user_request_type_id 
                    FROM admin_request as t1 
                    join admin_email_messages as em on em.message_id = t1.message_id  
                    {$where_clause} 
                    {$where_body}                
                    {$serach_date}
                    {$search}                                    
                    {$group_by}
                    {$order_by}
                      ";
            //    print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }

    }

    /* Admin nadra request status Ajax Call */

    public static function admin_nadra_request_sent_status($data, $count)
    {

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "created_at";
                    break;

            }
        }
        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'created_at';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {

            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\ ]/', '', $data['sSearch']);
            $search = "and (t1.cnic_number like '%{$data['sSearch']}%') ";

        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        $where_clause = "where created_at > '2023-01-01 00:00:00' ";
        //$where_clause = "where t1.user_id not IN (182,137,136)";


        $DB = Database::instance();

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM admin_nadra_request as t1
                                {$search}
                                {$where_clause}
                               ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT *
                                FROM  admin_nadra_request as t1 
                                {$where_clause}  
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    public static function admin_familtytree_request_sent_status($data, $count)
    {

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "created_at";
                    break;

            }
        }
        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'created_at';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {

            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\ ]/', '', $data['sSearch']);
            $search = "and (t1.cnic_number like '%{$data['sSearch']}%') ";

        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        $where_clause = "where created_at > '2023-01-01 00:00:00' ";
        //$where_clause = "where t1.user_id not IN (182,137,136)";


        $DB = Database::instance();

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM admin_familytree_request as t1
                                {$search}
                                {$where_clause}
                               ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT *
                                FROM  admin_familytree_request as t1 
                                {$where_clause}  
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    public static function admin_travel_request_sent_status($data, $count)
    {

        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "created_at";
                    break;

            }
        }
        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'created_at';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {

            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\ ]/', '', $data['sSearch']);
            $search = "and (t1.cnic_number like '%{$data['sSearch']}%') ";

        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        $where_clause = "where created_at > '2023-01-01 00:00:00' ";
        //$where_clause = "where t1.user_id not IN (182,137,136)";


        $DB = Database::instance();

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM admin_travel_request as t1
                                {$search}
                                {$where_clause}
                               ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT *
                                FROM  admin_travel_request as t1 
                                {$where_clause}  
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }


    public static function update_nadra_request_status($request_id, $processing_index)
    {
        $query = DB::update('admin_nadra_request')->set(array('status' => $processing_index))
            ->where('request_id', '=', $request_id)
            ->execute();
        return $query;
    }

    public static function update_familytree_request_status($request_id, $processing_index)
    {
        $query = DB::update('admin_familytree_request')->set(array('status' => $processing_index))
            ->where('request_id', '=', $request_id)
            ->execute();
        return $query;
    }

    public static function update_travel_request_status($request_id, $processing_index)
    {
        $query = DB::update('admin_travel_request')->set(array('status' => $processing_index))
            ->where('request_id', '=', $request_id)
            ->execute();
        return $query;
    }

}