<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * module related user request  
 */
class Model_Userrequest {
    /* User request status Ajax Call */

    public static function user_request_status($data, $count) {
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


        $serach_datetime = ' ';
        //for email send filter
        if(!empty($data['e_status'])){
        if($data['e_status']==1) {
            if (empty($data['endtime'])) {
                $data['endtime'] = date("Y-m-d H:i");
            }
            if (!empty($data['starttime'])) {
                $start_datetime = date("Y-m-d H:i", strtotime($data['starttime']));
                $end_datetime = date("Y-m-d H:i", strtotime($data['endtime']));


                $start_datetime = $start_datetime . ':00';
                $end_datetime = $end_datetime . ':59';
                $serach_datetime = " and em.message_date between '{$start_datetime}' and '{$end_datetime}' ";
            }
        }
        //for email recieve filter
        if($data['e_status']==2) {
            if (empty($data['endtime'])) {
                $data['endtime'] = date("Y-m-d H:i");
            }
            if (!empty($data['starttime'])) {
                $start_datetime = date("Y-m-d H:i", strtotime($data['starttime']));
                $end_datetime = date("Y-m-d H:i", strtotime($data['endtime']));


                $start_datetime = $start_datetime . ':00';
                $end_datetime = $end_datetime . ':59';
                $serach_datetime = " and em.received_date between '{$start_datetime}' and '{$end_datetime}' ";
            }
        }}

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

        if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer support requests                       
           $where_clause = "where 1 ";
        }else{
           $where_clause = "where t1.user_id not IN (842, 137, 2031, 2603) "; 
        }
                           
        

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
        } else if ($permission == 4) {
//            if($result[0]=='h')
//                $where_clause = "where t1.user_id not IN (182,137,136,184) and t1.user_id not in (select user_id from users_profile as u1 where u1.region_id in (1,3,4))";
//            else
            $where_clause = "where t1.user_id = $login_user->id";
        }
        if(!empty($data['txtbd']))
        {
            $where_body = " and em.received_body like '%{$data['txtbd']}%'  ";
        }else{
            $where_body = " ";
        }

        if(!empty($data['user']))
        {
            $DB = Database::instance();
            $rsr = "SELECT user_id from users_profile up where CONCAT(first_name , ' ' , last_name) like '%{$data['user']}%'";
            //$rs=DB::query(Database::SELECT, $rsr, false)->execute();
            $rs=$DB->query(Database::SELECT, $rsr, FALSE)->as_array();
            $request_array = implode(',', array_column($rs, 'user_id'));

            if(!empty($request_array)) {
                $where_user = " and t1.user_id in($request_array) ";
            }else
            {
                 $where_user = " and t1.user_id= 'null' ";
            }
        }else{
            $where_user = " ";
        }
        if(!empty($data['person']))
        {

            $DB = Database::instance();
            $rsr = "SELECT person_id from person where CONCAT(first_name , ' ' , last_name) like '%{$data['person']}%'";
            $rs=$DB->query(Database::SELECT, $rsr, FALSE)->as_array();
            $request_array = implode(',', array_column($rs, 'person_id'));


         

            if(!empty($request_array)) {
                $where_person = " and t1.concerned_person_id in($request_array) ";
            }
            else{
                 $where_person = " and t1.concerned_person_id= '-1' ";
            }
        }else{
            $where_person = " ";
        }
        
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}
                                {$and_status}    
                                {$and_reply}    
                                {$and_processing}    
                                {$where_body}
                                {$where_user}
                                {$where_person}    
                                {$serach_date}
                                {$serach_datetime}    
                                {$data['field']}
                                {$data['r_category']}
                                {$data['mnc']}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reference_id, t1.reply,t1.company_name,request_id,user_request_type_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.received_file_path,em.received_body, em.message_subject, em.sender_id FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$and_status}    
                                {$and_reply}    
                                {$and_processing}
                                {$where_body}
                                {$where_user}
                                {$where_person} 
                                {$serach_date}
                                {$serach_datetime}    
                                {$data['field']}
                                {$data['mnc']}
                                {$data['r_category']}
                                {$search}
                                {$order_by}    
                                {$limit}";
//           print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //request status for resend request tab
    public static function user_request_status_resend($data, $count) {
        //echo '<pre>';
        // print_r($data); exit;
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        //Request type Advance Search
        if (!empty($data['request_type_new'])) {
            $requesttype = array_values($data['request_type_new']);
            $requesttype_value = implode(',', $requesttype);
            $data['field'] = "and t1.user_request_type_id in ({$requesttype_value})";
        } else {
            $data['field'] = '';
        }
        //Company Advance Search
        if (empty($data['mnc_new']))
            $data['mnc'] = '';
        else {
            $mnc_array = array_values($data['mnc_new']);
            $mnc_value = implode(',', $mnc_array);
            $data['mnc'] = " and t1.company_name in ({$mnc_value}) ";
        }



        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "created_at";
                    break;
                case "4":
                    $order_by_param = "sending_date";
                    break;
                case "5":
                    $order_by_param = "request_send_count";
                    break;
            }
        }

//        / Order By /
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 'sending_date';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

//        / Starting and Ending Lenght (size) /
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }

//        / Search via table /
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        //Where only Status  = send  and time 24 greater     
        $where_clause = "where t1.status = 1 and ( sending_date <= NOW() - INTERVAL 1 DAY)";
        
          if (in_array($login_user->id, [842, 137, 2031, 2603])) {  //developer technical support requests                       
                      
                 
             }else{
                $where_clause .= "  and t1.user_id not IN (842, 137, 2031, 2603) ";        
                }
                
//        / For Total Record Count /
        if ($count == 'true') {
            $sql = "Select  COUNT(*)
 AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id                                                                
                                {$where_clause}            
                                {$search}                                
                                and t1.user_request_type_id != 8
                                {$data['field']}
                                {$data['mnc']}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
//        /  Fetch all Records / 
        else {
            $sql = "SELECT t1.reference_id, t1.reply,t1.sending_date,t1.request_send_count, t1.company_name,request_id,user_request_type_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.received_file_path,em.received_body, em.message_subject, em.sender_id FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}            
                                {$search}                                
                                and t1.user_request_type_id != 8 
                                {$data['field']}
                                {$data['mnc']}
                                {$order_by}    
                                {$limit}";
            //print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User request status Ajax Call */

    public static function user_request_status_telenor($data, $count) {
//        echo '<pre>';
//        print_r($data); exit;
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
            $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%')";
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

        if ($login_user->id == 136){
        //$where_clause = " where (t1.company_name = 1 and t1.status = 0)"; ,3,5
            $where_clause = " where ((t1.company_name = 6) and user_request_type_id in (1,2) and t1.status = 0 and DATEDIFF(endDate ,startDate )<170)";
        //$where_clause = " where (t1.company_name = 7 and t1.status = 0)";
        }else{
        //$where_clause = " where (t1.company_name = 1 and t1.status = 0)";  //,3,5
            $where_clause = " where ((t1.company_name = 6) and user_request_type_id in (1,2) and t1.status = 0)";
        }
        
        if (in_array($login_user->id, [842, 137, 2031, 2603])) {  //developer technical support requests                       
                      
                 
             }else{
                $where_clause .= "  and t1.user_id not IN (842, 137, 2031, 2603) ";        
                }
                
                
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reply,t1.company_name,request_id,user_request_type_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.message_body,em.received_file_path,em.received_body, em.message_subject, em.sender_id, 
                                DATEDIFF(endDate ,startDate ) 
                                AS days
                                FROM user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;    
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    public static function user_request_status_ufone($data, $count) {
//        echo '<pre>';
//        print_r($data); exit;
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
            $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%')";
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

        if ($login_user->id == 136){
        //$where_clause = " where (t1.company_name = 1 and t1.status = 0)"; ,3,5
            $where_clause = " where ((t1.company_name = 3) and user_request_type_id in (5) and t1.status = 0)";
        //$where_clause = " where (t1.company_name = 7 and t1.status = 0)";
        }else{
        //$where_clause = " where (t1.company_name = 1 and t1.status = 0)";  //,3,5
            $where_clause = " where ((t1.company_name = 3) and user_request_type_id in (5) and t1.status = 0)";
        }
        
           if (in_array($login_user->id, [842, 137, 2031, 2603])) {  //developer technical support requests                       
                      
                 
             }else{
                $where_clause .= "  and t1.user_id not IN (842, 137, 2031, 2603) ";        
                }
                
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reply,t1.company_name,request_id,user_request_type_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.message_body,em.received_file_path,em.received_body, em.message_subject, em.sender_id
                                FROM user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;    
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User request status more than 6 months Ajax Call */

    public static function user_request_status_telenor_sixmonths($data, $count) {
//        echo '<pre>';
//        print_r($data); exit;


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
            $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%')";
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



        if ($login_user->id == 136){
            //$where_clause = " where (t1.company_name = 1 and t1.status = 0)";
            $where_clause = " where ((t1.company_name = 6) and user_request_type_id in (1,2) and t1.status = 0 and DATEDIFF(endDate ,startDate )>=170) ";

      // $where_clause="where (em.message_body==Data can\'t be fetched for more than 180 days");
        //$where_clause = " where (t1.company_name = 7 and t1.status = 0)";
        }else{
            //$where_clause = " where (t1.company_name = 1 and t1.status = 0)";
            $where_clause = " where ((t1.company_name = 6) and user_request_type_id in (1,2) and t1.status = 0)";
        }
        
                
        if (in_array($login_user->id, [842, 137, 2031, 2603])) {  //developer technical support requests                       
                      
                 
             }else{
                $where_clause .= "  and t1.user_id not IN (842, 137, 2031, 2603) ";        
                }
                
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reply,t1.company_name,request_id,user_request_type_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.message_body,em.received_file_path,em.received_body, em.message_subject, em.sender_id,
                                DATEDIFF(endDate ,startDate ) 
                                AS days
                                FROM  user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User request status Ajax Call */

    public static function user_request_status_familytree($data, $count) {
//        echo '<pre>';
//        print_r($data); exit;
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
            $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%')";
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

        if ($login_user->id == 136)
        //$where_clause = " where (t1.company_name = 1 and t1.status = 0)";
            $where_clause = " where (user_request_type_id = 10 and t1.status = 0)";
        //$where_clause = " where (t1.company_name = 7 and t1.status = 0)";
        else
        //$where_clause = " where (t1.company_name = 1 and t1.status = 0)";
            $where_clause = " where (user_request_type_id = 10 and t1.status = 0)";
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id
                                {$search}
                                {$where_clause}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reference_id, t1.reply,t1.company_name,request_id,user_request_type_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id,em.message_body,em.received_file_path,em.received_body, em.message_subject, em.sender_id FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User request status Ajax Call */

    public static function blocked_number($data, $count) {
//        echo '<pre>';
//        print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "t1.time_stamp";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "5":
                    $order_by_param = "t1.time_stamp";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and t1.blocked_value like '%{$data['sSearch']}%'";
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
        //       $where_clause = "";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM blocked_numbers as t1  
                                where 1 
                                {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "select * 
                        from blocked_numbers as t1
                        where 1 
                        {$search}
                        {$order_by}    
                        {$limit}";
            // print_r($sql); exit;    
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Delete CDR with error  */

    public static function deletecdr_with_error($request_id, $loginid) {
        $date = date('Y-m-d H:i:s');
        $DB = Database::instance();
        $sql = "SELECT file 
                        FROM files
                         where id = {$request_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $file_name = !empty($results->file) ? $results->file : 0;
        if (!empty($file_name)) {
            //$dir='uploads/cdr/manual/';
            $file_path = !empty($request_id) ? Helpers_Upload::get_request_data_path($request_id, 'save') . $file_name : '';
            unlink($file_path);
        }
        $query = DB::delete('files')
                ->where('id', '=', $request_id)
                ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        //Helpers_Profile::user_activity_log($login_user->id, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $query;
    }

    public static function user_uploaded_cdrs($data, $count) {
        /* Sorted Data */
        $order_by_param = "t1.created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.created_by";
                    break;
                case "1":
                    $order_by_param = "t1.request_type";
                    break;
                case "2":
                    $order_by_param = "t1.no_of_record";
                    break;
                case "3":
                    $order_by_param = "t1.company_name";
                    break;
                case "4":
                    $order_by_param = "t1.created_on";
                    break;
                case "5":
                    $order_by_param = "t1.upload_status";
                    break;
                case "6":
                    $order_by_param = "t1.error_type";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 't1.created_on';
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
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";
            $DB = Database::instance();
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.created_by in ({$request_array}) or t1.imei like '%{$data['sSearch']}%' or t1.phone_number like '%{$data['sSearch']}%')";
            else {
                $search = "and ( t1.imei like '%{$data['sSearch']}%' or t1.phone_number like '%{$data['sSearch']}%')";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by t1.created_by";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where t1.is_manual=1 ";
        //manula cdr upload status
        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.is_manual=1 AND t1.is_deleted !=1";
                    break;
                case 'r':
                    $where_clause = "where t1.is_manual=1 AND t1.is_deleted !=1 AND t1.created_by IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.is_manual=1 AND t1.is_deleted !=1 AND t1.created_by IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.is_manual=1 AND t1.is_deleted !=1 AND t1.created_by IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else if ($permission == 4) {
            $where_clause = "where t1.is_manual=1 AND t1.is_deleted !=1 AND t1.created_by = $login_user->id";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM files as t1 
                                {$where_clause}
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *
                    FROM files as t1 
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            //  print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    public static function user_nadra_requests($data, $count) {
        if (empty($data['region']))
            $data['region'] = '';
        else {
            $regions_list = implode(",", $data['region']);
            //$regions_list =  str_replace('11','0',$regions_list) ;
            $data['region'] = "and t3.region_id in ({$regions_list})";
        }
        if (empty($data['user']))
            $data['user'] = '';
        else {
            $users_list = implode(",", $data['user']);
            $data['user'] = "and t1.user_id in ({$users_list})";
        }
        if (empty($data['e_status']))
            $data['e_status'] = '';
        else {
            if ($data['e_status'] == 1) {
                $data['e_status'] = "and t1.status=1";
            } elseif ($data['e_status'] == 2) {
                $data['e_status'] = "and t1.status=2";
            } else {
                $data['e_status'] = "and t1.status in (1,2)";
            }
        }
        /* Sorted Data */
        //$order_by_param = "t1.created_at";
        $order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "1":
                    $order_by_param = "t3.region_id";
                    break;
                case "3":
                    $order_by_param = "t1.concerned_person_id";
                    break;
                case "4":
                    $order_by_param = "t1.created_at";
                    break;
                case "5":
                    $order_by_param = "t1.status";
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
        $limit = "";
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
            $sql = "SELECT  user_id FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%'  or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%'  or t1.request_id like '%{$data['sSearch']}%')";
            }
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

        $where_clause = "where t1.user_request_type_id =8  ";
        //nadra verisys reponded by H.Q technical support and regional focal persons
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    //is line k last main not in main se 3 remove kia hai wo add krna hai
                    //is line k last main not in main se 1 remove kia hai wo add krna hai
                    //is line k last main not in main se 9 remove kia hai wo add krna hai
//                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id NOT IN (1,3,4,6,8,9) ) ";
                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id NOT IN (1,3,4,6,8,9) ) ";
                    break;
                case 'r':
                    $sub_query = " WHERE u1.region_id = $result[1] ";

                    /* switch ($result[1])
                      {

                      case 4: //Rawalpindi
                      $sub_query = " WHERE u1.region_id in (4) ";
                      break;
                      case 3: //Gujrawala
                      $sub_query = " WHERE u1.region_id in (3,5) ";
                      break;
                      case 8: //Multan
                      $sub_query = " WHERE u1.region_id in (8,10) ";
                      break;
                      case 6: //Sargoda
                      $sub_query = " WHERE u1.region_id in (6,7) ";
                      break;
                      case 9: //Bahawalpur
                      $sub_query = " WHERE u1.region_id in (9,11,2) ";
                      break;
                      default:
                      $sub_query = " WHERE u1.region_id = $result[1] ";

                      } */

                    $where_clause = "where t1.user_request_type_id =8 "
                            . "AND t1.user_id IN (SELECT user_id FROM users_profile as u1 $sub_query ) ";
                    break;
                //$where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                //break;
                case 'd':
                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        }
//        else if ($permission == 4) {
//            $where_clause = "where t1.user_request_type_id =8 AND t1.user_id = $login_user->id";
//        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                join users_profile as t3
                                on t1.user_id=t3.user_id
                                {$where_clause}
                                {$data['e_status']}
                                {$data['user']}
                                {$data['region']}
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.request_id, t1.user_id, t2.email_type_name, t1.requested_value, t1.concerned_person_id,
                    t1.created_at, t1.status,t1.project_id,CONCAT_WS(' ',t3.first_name, t3.last_name) as name,t3.region_id
                    FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                join users_profile as t3
                                on t1.user_id=t3.user_id
                                {$where_clause}
                                {$data['e_status']}
                                {$data['user']}
                                {$data['region']}
                                {$search}
                                {$order_by}    
                                {$limit}";
            //echo $sql; exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    public static function user_familytree_requests($data, $count) {
        if (empty($data['region']))
            $data['region'] = '';
        else {
            $regions_list = implode(",", $data['region']);
            //$regions_list =  str_replace('11','0',$regions_list) ;
            $data['region'] = "and t3.region_id in ({$regions_list})";
        }
        if (empty($data['user']))
            $data['user'] = '';
        else {
            $users_list = implode(",", $data['user']);
            $data['user'] = "and t1.user_id in ({$users_list})";
        }
        if (empty($data['e_status']))
            $data['e_status'] = '';
        else {
            if ($data['e_status'] == 1) {
                $data['e_status'] = "and t1.status=1";
            } elseif ($data['e_status'] == 2) {
                $data['e_status'] = "and t1.status=2";
            } else {
                $data['e_status'] = "and t1.status in (1,2)";
            }
        }
        /* Sorted Data */
        //$order_by_param = "t1.created_at";
        $order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "1":
                    $order_by_param = "t3.region_id";
                    break;
                case "3":
                    $order_by_param = "t1.concerned_person_id";
                    break;
                case "4":
                    $order_by_param = "t1.created_at";
                    break;
                case "5":
                    $order_by_param = "t1.status";
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
        $limit = "";
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
            $sql = "SELECT  user_id FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%'  or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%'  or t1.request_id like '%{$data['sSearch']}%')";
            }
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

        $where_clause = "where t1.user_request_type_id =10  ";
        //nadra verisys reponded by H.Q technical support and regional focal persons
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    //is line k last main not in main se 3 remove kia hai wo add krna hai
                    //is line k last main not in main se 1 remove kia hai wo add krna hai
                    //is line k last main not in main se 9 remove kia hai wo add krna hai
//                    $where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id NOT IN (1,3,4,6,8,9) ) ";
                    $where_clause = "where t1.user_request_type_id =10 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id NOT IN (4) ) ";
                    break;
                case 'r':
                    $sub_query = " WHERE u1.region_id = $result[1] ";

                    /* switch ($result[1])
                      {

                      case 4: //Rawalpindi
                      $sub_query = " WHERE u1.region_id in (4) ";
                      break;
                      case 3: //Gujrawala
                      $sub_query = " WHERE u1.region_id in (3,5) ";
                      break;
                      case 8: //Multan
                      $sub_query = " WHERE u1.region_id in (8,10) ";
                      break;
                      case 6: //Sargoda
                      $sub_query = " WHERE u1.region_id in (6,7) ";
                      break;
                      case 9: //Bahawalpur
                      $sub_query = " WHERE u1.region_id in (9,11,2) ";
                      break;
                      default:
                      $sub_query = " WHERE u1.region_id = $result[1] ";

                      } */

                    $where_clause = "where t1.user_request_type_id =10 "
                            . "AND t1.user_id IN (SELECT user_id FROM users_profile as u1 $sub_query ) ";
                    break;
                //$where_clause = "where t1.user_request_type_id =8 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                //break;
                case 'd':
                    $where_clause = "where t1.user_request_type_id =10 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.user_request_type_id =10 AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        }
//        else if ($permission == 4) {
//            $where_clause = "where t1.user_request_type_id =8 AND t1.user_id = $login_user->id";
//        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                join users_profile as t3
                                on t1.user_id=t3.user_id
                                {$where_clause}
                                {$data['e_status']}
                                {$data['user']}
                                {$data['region']}
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.request_id, t1.user_id, t2.email_type_name, t1.requested_value, t1.concerned_person_id,
                    t1.created_at, t1.status,t1.project_id,CONCAT_WS(' ',t3.first_name, t3.last_name) as name,t3.region_id
                    FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                join users_profile as t3
                                on t1.user_id=t3.user_id
                                {$where_clause}
                                {$data['e_status']}
                                {$data['user']}
                                {$data['region']}
                                {$search}
                                {$order_by}    
                                {$limit}";
         //   echo $sql; exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    public static function user_travelhistory_requests($data, $count) {
        if (empty($data['region']))
            $data['region'] = '';
        else {
            $regions_list = implode(",", $data['region']);
            //$regions_list =  str_replace('11','0',$regions_list) ;
            $data['region'] = "and t3.region_id in ({$regions_list})";
        }
        if (empty($data['user']))
            $data['user'] = '';
        else {
            $users_list = implode(",", $data['user']);
            $data['user'] = "and t1.user_id in ({$users_list})";
        }
        if (empty($data['e_status']))
            $data['e_status'] = '';
        else {
            if ($data['e_status'] == 1) {
                $data['e_status'] = "and t1.status=1";
            } elseif ($data['e_status'] == 2) {
                $data['e_status'] = "and t1.status=2";
            } else {
                $data['e_status'] = "and t1.status in (1,2)";
            }
        }
        /* Sorted Data */
        //$order_by_param = "t1.created_at";
        $order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "1":
                    $order_by_param = "t3.region_id";
                    break;
                case "3":
                    $order_by_param = "t1.concerned_person_id";
                    break;
                case "4":
                    $order_by_param = "t1.created_at";
                    break;
                case "5":
                    $order_by_param = "t1.status";
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
        $limit = "";
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
            $sql = "SELECT  user_id FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%'  or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%'  or t1.request_id like '%{$data['sSearch']}%')";
            }
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
        $where_clause = "where t1.user_request_type_id = 12 ";
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                join users_profile as t3
                                on t1.user_id=t3.user_id
                                {$where_clause}
                                {$data['e_status']}
                                {$data['user']}
                                {$data['region']}
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.request_id, t1.user_id, t2.email_type_name, t1.requested_value, t1.concerned_person_id,
                    t1.created_at, t1.status,t1.project_id,CONCAT_WS(' ',t3.first_name, t3.last_name) as name,t3.region_id
                    FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                join users_profile as t3
                                on t1.user_id=t3.user_id
                                {$where_clause}
                                {$data['e_status']}
                                {$data['user']}
                                {$data['region']}
                                {$search}
                                {$order_by}    
                                {$limit}";
            //echo $sql; exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //uploaded images
    public static function travelhistory_bulk_upload($data, $count) {
        /* Sorted Data */
        $order_by_param = "t1.upload_date";
        //$order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "t1.upload_date";
                    break;
                case "4":
                    $order_by_param = "t1.attachment_status";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 't1.upload_date';
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.cnic_number like '%{$data['sSearch']}%' ";
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
        //nadra verisys reponded by H.Q technical support and regional focal persons
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.uploaded_by_user IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id IN ($login_user_region) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.uploaded_by_user = $login_user->id";
                    break;
                case 'd':
                    $where_clause = "where 0";
                    break;
                case 'p':
                    $where_clause = "where 0";
                    break;
            }
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM travelhistory_temp_files as t1                                 
                                join users_profile as t3
                                on t1.uploaded_by_user=t3.user_id
                                {$where_clause}                                
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *,t1.cnic_number as vcnic   FROM 
                                travelhistory_temp_files as t1                                 
                                join users_profile as t3
                                on t1.uploaded_by_user=t3.user_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    //uploaded images
    public static function nadra_verisys_bulk($data, $count) {
        /* Sorted Data */
        $order_by_param = "t1.upload_date";
        //$order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "t1.upload_date";
                    break;
                case "4":
                    $order_by_param = "t1.attachment_status";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 't1.upload_date';
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.cnic_number like '%{$data['sSearch']}%' ";
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
        //nadra verisys reponded by H.Q technical support and regional focal persons
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.uploaded_by_user IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id IN ($login_user_region) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.uploaded_by_user = $login_user->id";
                    break;
                case 'd':
                    $where_clause = "where 0";
                    break;
                case 'p':
                    $where_clause = "where 0";
                    break;
            }
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM verisys_temp_files as t1                                 
                                join users_profile as t3
                                on t1.uploaded_by_user=t3.user_id
                                {$where_clause}                                
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *,t1.cnic_number as vcnic   FROM 
                                verisys_temp_files as t1                                 
                                join users_profile as t3
                                on t1.uploaded_by_user=t3.user_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    //uploaded images
    public static function nadra_familytree_bulk($data, $count) {
        /* Sorted Data */
        $order_by_param = "t1.upload_date";
        //$order_by_param = " ";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "t1.upload_date";
                    break;
                case "4":
                    $order_by_param = "t1.attachment_status";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 't1.upload_date';
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.cnic_number like '%{$data['sSearch']}%' ";
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
        //nadra verisys reponded by H.Q technical support and regional focal persons
        if ($permission == 2 || $permission == 3 || $permission == 4) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.uploaded_by_user IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id IN ($login_user_region) ) ";
                    break;
                case 'r':
                    $where_clause = "where t1.uploaded_by_user = $login_user->id";
                    break;
                case 'd':
                    $where_clause = "where 0";
                    break;
                case 'p':
                    $where_clause = "where 0";
                    break;
            }
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM familytree_temp_files as t1                                 
                                join users_profile as t3
                                on t1.uploaded_by_user=t3.user_id
                                {$where_clause}                                
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *,t1.cnic_number as vcnic   FROM 
                                familytree_temp_files as t1                                 
                                join users_profile as t3
                                on t1.uploaded_by_user=t3.user_id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
                                
                                //echo $sql; exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    //uploaded images
    public static function nadra_verisys_databank_bulk($data, $count) {


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
            $search = "and (t1.cnic_number like '%{$data['sSearch']}%' or t1.cnic_number_foreigner like '%{$data['sSearch']}%') ";
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

        //nadra verisys reponded by H.Q technical support and regional focal persons
      $where_clause .= " and t1.user_id='{$data['uid']}' and t1.created_from=2";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM person_initiate as t1 
                                                         
                           
                                {$where_clause}                                
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
        // print_r($sql); exit;
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *,t1.cnic_number as vcnic
                                FROM  person_initiate as t1
                                                              
                               
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
    //uploaded msisdn data
    public static function msisdn_old_data_upload($data, $count) {
//        echo '<pre>';
//        print_r($data);
//        exit();


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
            $search = "and (t1.cnic_number like '%{$data['sSearch']}%' ) ";
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

      $where_clause .= " and t1.user_id='{$data['uid']}'";

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
    //uploaded images
    public static function nadra_verisys_databank_reports($data, $count) {


        $where_clause =  "where 1";
        // user name
//        if(!empty($data['user_id'])){
//            $where_clause .= " and t1.user_id = {$data['user_id']}";
//        }
        if (isset($data['user_name']) && !empty($data['user_name'])) {
            $data['user_name'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['user_name']);
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['user_name']}%'";
            $DB = Database::instance();
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));

            $search_name = "and ( t1.user_id in ({$request_array}))";

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
            $search = "and (t1.cnic_number like '%{$data['sSearch']}%' or t1.cnic_number_foreigner like '%{$data['sSearch']}%') ";
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
        $result = explode('-', $posting);
      //  $where_clause = "where t1.user_id not IN (419)";
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
    //    $where_clause = "where 1";

        //nadra verisys reponded by H.Q technical support and regional focal persons
      $where_clause .= " and  t1.created_from=2";

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM person_initiate as t1 
                                                         
                           
                                {$where_clause}
                                {$search_name}
                                {$serach_date}                                
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
        // print_r($sql); exit;
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT  *,t1.cnic_number as vcnic
                                FROM  person_initiate as t1
                                                              
                               
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

    public static function user_rejected_requests($data, $count) {
        /* Sorted Data */
        $order_by_param = "t1.created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "3":
                    $order_by_param = "t1.concerned_person_id";
                    break;
                case "4":
                    $order_by_param = "t1.created_at";
                    break;
                case "5":
                    $order_by_param = "t1.status";
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
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";
            $DB = Database::instance();
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%')";
            }
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

        $where_clause = "where t1.status =4  ";

        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where t1.status =4 ";
                    break;
                case 'r':
                    $where_clause = "where t1.status =4  AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where t1.status =4  AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where t1.status =4  AND t1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else if ($permission == 4) {
            $where_clause = "where t1.status =4  AND t1.user_id = $login_user->id";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                {$where_clause}
                                 {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reference_id, t1.request_id, t1.user_id, t2.email_type_name, t1.requested_value, t1.concerned_person_id,
                    t1.created_at, t1.status
                    FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            //  print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* single request status view  */

    public static function viewad($id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                                admin_request as t1 
                                join admin_email_messages as t2 
                                on t2.message_id = t1.message_id
                                where t1.request_id = {$id}";
        $members = $DB->query(Database::SELECT, $sql, FALSE)->current();
        return $members;
    }
    public static function view($id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                                user_request as t1 
                                join email_messages as t2 
                                on t2.message_id = t1.message_id
                                where t1.request_id = {$id}";
                                
        $members = $DB->query(Database::SELECT, $sql, FALSE)->current();
        return $members;
    }

    public static function update_request_priority($request_id, $request_priority) {

        $added_on = date("Y-m-d H:i:s");
        $query = DB::update('user_request')->set(array('request_priority' => $request_priority))
                ->where('request_id', '=', $request_id)
                ->execute();
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 63);
        return $query;
    }

    /* User request status for Schedular Call */

    public static function request_status($data, $count) {
        // print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "t1.request_priority";

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        } else {
            $order_by_type = 't1.request_priority';
        }
        // if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        //print_r($order_by); exit;
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
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%' or t1.request_id like '%{$data['sSearch']}%'  or t1.reference_id like '%{$data['sSearch']}%')";
            }
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

       
         if ((in_array($login_user->id, [842, 137, 2031, 2603]))) {  //developer technical support requests                       
                              $where_clause = "where t1.status = 0 and t1.user_request_type_id !=8 ";        
                        }else{
                           $where_clause = "where t1.status = 0 and t1.user_request_type_id !=8 and t1.user_id not IN (842, 137, 2031, 2603) ";        
                        }
                        
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM user_request as t1 
                                join email_templates_type as t2 
                                on t1.user_request_type_id = t2.id
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}
                                    and t1.user_request_type_id <> 10   
                                {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.reference_id,t1.company_name,request_id, user_id, email_type_name, requested_value, concerned_person_id,
                    created_at, status, processing_index,t1.request_priority, em.message_id, em.message_subject, em.sender_id FROM 
                                user_request as t1 
                                join email_templates_type as t2                                 
                                on t1.user_request_type_id = t2.id                       
                                join email_messages as em on em.message_id = t1.message_id
                                {$where_clause}                                 
                                {$search}
                                and t1.user_request_type_id <> 10       
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User request status for Schedular Call */

    public static function request_parsing_status($data, $count) {
        /* Sorted Data */
        $order_by_param = "created_at";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "user_id";
                    break;
                case "2":
                    $order_by_param = "user_request_type_id";
                    break;
                case "4":
                    $order_by_param = "created_at";
                    break;
                case "5":
                    $order_by_param = "status";
                    break;
                case "6":
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
            $where = "WHERE CONCAT(first_name, ' ', TRIM(last_name)) like '%{$data['sSearch']}%'";
            $DB = Database::instance();
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$where}";
            $users_array = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $request_array = implode(', ', array_values(array_column($users_array, 'user_id')));
            if (!empty($request_array))
                $search = "and ( t1.user_id in ({$request_array}) or t1.requested_value like '%{$data['sSearch']}%')";
            else {
                $search = "and (t1.user_id = null or t1.requested_value like '%{$data['sSearch']}%')";
            }
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";
        $login_user = Auth::instance()->get_user();
        $where_clause='';
             if (in_array($login_user->id, [842, 137, 2031, 2603])) {  //developer technical support requests                       
                      
                 
             }else{
                $where_clause .= "  and t1.user_id not IN (842, 137, 2031, 2603) ";        
                }
                        
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(request_id) as count
                    FROM user_request as t1
                    join email_messages as t2 on t2.message_id = t1.message_id
                    where t1.status = 2 
                    and t1.user_request_type_id != 8 
                    and t1.processing_index = 4 
                    {$where_clause}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.company_name,t1.reference_id, t1.request_id, t1.user_id, t1.user_request_type_id,t1.sending_date, t1.requested_value,t2.message_date,t1.processing_index
                    FROM user_request as t1
                    join email_messages as t2 on t2.message_id = t1.message_id
                    where t1.status = 2 
                    and t1.user_request_type_id != 8 
                    and t1.processing_index = 4                                
                                {$where_clause}
                                {$search}
                                {$order_by}    
                                {$limit}";
            // print_r($sql); exit;

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Delete Blocked Number  */

    public static function delete_blocked_number($blocked_id, $loginid) {
        $date = date('Y-m-d H:i:s');
        $query = DB::delete('blocked_numbers')
                ->where('blocked_id', '=', $blocked_id)
                ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        //Helpers_Profile::user_activity_log($uid, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $query;
    }

    /* Add Number in blocked number list  */

    public static function add_blocked_number($data, $loginid) {
        $number_type = $data['numbertype'];
        $number_value = $data['numbervalue'];
        $block_reason = $data['blockreason'];
        $block_details = $data['blockdetails'];
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('blocked_numbers', array('blocked_number_type', 'blocked_value', 'blocked_reason', 'blocked_details', 'blocked_by', 'time_stamp'))
                ->values(array($number_type, $number_value, $block_reason, $block_details, $loginid, $date))
                ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        //Helpers_Profile::user_activity_log($uid, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $query;
    }

    /* Search Subscriber in local databases  */
public static function subscriber_external_search_results($data, $uid) {
    $i = 1;
    $cnic_body = '';

    // Normalize data to always be an array of records
    if (!empty($data['data'])) {
        if (isset($data['data']['id'])) {
            $records = array($data['data']); // single record
        } else {
            $records = $data['data']; // multiple records
        }
    } else {
        $records = array();
    }

    $html = '';

    foreach ($records as $result) {
        if (!empty($result)) {
            // Extract only the fields we need
            $cnic = !empty($result['cnic']) ? $result['cnic'] : 'NA';
            $name = !empty($result['name']) ? $result['name'] : 'NA';
            $address = !empty($result['address']) ? $result['address'] : '';
            $msisdn = !empty($result['msisdn']) ? $result['msisdn'] : '';


            //$msisdn = !empty($result['msisdn']) ? $result['msisdn'] : '';
            $msisdn = preg_replace('/\D/', '', $msisdn); // remove any non-digit characters
            if (substr($msisdn, 0, 3) === '923') {
                $msisdn = substr($msisdn, 1); // remove leading '9' to get '03100910677'
                $msisdn = substr($msisdn, 1); // remove leading '0' -> '3100910677'
            } elseif (substr($msisdn, 0, 2) === '03') {
                $msisdn = substr($msisdn, 1); // remove leading '0' -> '3100910677'
            } elseif (substr($msisdn, 0, 1) === '0') {
                $msisdn = substr($msisdn, 1); // remove leading '0' -> '3100910677'
            }

            $bvs_stat = !empty($result['bvs_status']) ? $result['bvs_status'] : 'Not Verified';
            $status = !empty($result['status']) ? $result['status'] : '';
            //$save_body_array = array('Name'=>$name,'CNIC'=>$cnic,'ADDRESS'=>$address,'MOBILE'=>$msisdn,'STATUS'=> $status,'BVS_STATUS'=>$bvs_stat);
            //$save_body_new = json_encode($save_body_array);
            $save_body_array = array(
                    'name'       => $name,
                    'cnic'       => $cnic,
                    'address'    => $address,
                    'mobile'     => $msisdn,
                    'status'     => $status,
                    'bvs_status' => $bvs_stat
                );
                //print_r($save_body_array);
                $save_body_new = json_encode($save_body_array);
                $save_body_safe = htmlspecialchars($save_body_new, ENT_QUOTES, 'UTF-8');


            $save_body = "NAME => $name , CNIC => $cnic , ADDRESS => $address , MOBILE => $msisdn , STATUS => $status , BVS_STATUS => $bvs_stat";
            //$cnic_body .= " , " . $save_body;

            // Build HTML safely
            $html .= '<div>';
            $html .= '<p style="color: #00a7d0;font-weight: bold"><u>RECORD-' . $i . '</u></p>';
            $html .= '<span><b>NAME: </b><i>' . $name . '</i></span><br>';
            $html .= '<span><b>CNIC: </b><i>' . $cnic . '</i></span><br>';
            $html .= '<span><b>ADDRESS: </b><i>' . $address . '</i></span><br>';
            $html .= '<span><b>MOBILE: </b><i style="color: #3c8dbc;font-weight: bold">' . $msisdn . '</i></span><br>';
            $html .= '<span><b>STATUS: </b><i>' . $status . ' (' . $bvs_stat . ')</i></span><br>';

            $html .= '<form target="_blank" name="fixerror-' . $i . '" id="fixerror-' . $i . '" action="' . URL::base() . 'user/upload_against_msisdn" method="post">';
            $html .= '<input type="hidden" name="requestid" value="0">';
            $html .= '<input type="hidden" name="receivedfilepath" value="No Attached File">';
            $html .= '<input type="hidden" name="receivedbody" value="' . $save_body . '">';
            $html .= '<input type="hidden" name="requesttype" value="3">';
            $html .= '<input type="hidden" name="requestvalue" value="' . $msisdn . '">';
            $html .= '<span>';
            $html .= '<button style="margin-top: -22px; margin-right: 5px" type="submit" title="Save in AIES" class="pull-right btn-success">Save Subscriber</button>';
            $html .= '<button style="margin-top: -22px; margin-right: 5px" type="button" title="Request From Company" class="pull-right btn-primary" onclick="requestsub(' . $msisdn . ')">Request Subscriber</button>';
            $html .= '</span>';
            $html .= '<input type="hidden" name="receivedbodynew" value="' . $save_body_safe . '">';

            $html .= '</form>';

            $html .= '<hr class="style14">';
            $html .= '</div>';

            $i++;
        }
    }

    // Single CNIC form at the bottom
    if (!empty($cnic)) {
        $html .= '<form target="_blank" name="fixerror-' . $i . '" id="fixerror-' . $i . '" action="' . URL::base() . 'user/upload_against_cnic" method="post">';
        $html .= '<input type="hidden" name="requestid" value="0">';
        $html .= '<input type="hidden" name="receivedfilepath" value="No Attached File">';
        $html .= '<input type="hidden" name="receivedbody" value="' . $save_body . '">';
        $html .= '<input type="hidden" name="requesttype" value="5">';
        $html .= '<input type="hidden" name="requestvalue" value="' . $cnic . '">';
        $html .= '<span>';
        $html .= '<button style="margin-top: -7px;margin-bottom: 5px; margin-right: 5px" type="submit" title="Save in AIES" class="pull-right btn-success">Save CNIC SIMs</button>';
        $html .= '<button style="margin-top: -7px;margin-bottom: 5px; margin-right: 5px" onclick="requestcnicsims(' . $cnic . ')" type="button" title="Request From Company" class="pull-right btn-primary">Request CNIC SIMs</button>';
        $html .= '</span>';
        $html .= '</form>';
    }

    return $html; // return HTML as string
}




    /* subscriber results */

    public static function subscriber_external_results($data, $uid) {
        $sub_data = '';
        foreach ($data['data'] as $result) {
            if (!empty($result)) {
                //basic info
                $sub_data['cnic'] = !empty($result['CNIC']) ? $result['CNIC'] : '';
                $sub_data['name'] = !empty($result['FIRSTNAME']) ? $result['FIRSTNAME'] : '';
                $address1 = !empty($result['ADDRESS1']) ? $result['ADDRESS1'] : '';
                $address2 = !empty($result['ADDRESS2']) ? $result['ADDRESS2'] : '';
                $address3 = !empty($result['ADDRESS3']) ? $result['ADDRESS3'] : '';
                $address4 = !empty($result['ADDRESS4']) ? $result['ADDRESS4'] : '';
                $resident_contact = !empty($result['RESCONTACT']) ? $result['RESCONTACT'] : '';
                $phone_office = !empty($result['PHONE_OFFICE']) ? $result['PHONE_OFFICE'] : '';
                $sub_data['address'] = $address1 . " " . $address2 . " " . $address3 . " " . $address4 . ", Home#" . $resident_contact . ", Office#" . $phone_office;
                //sim detail
                $sub_data['msisdn'] = !empty($result['MSISDN']) ? $result['MSISDN'] : '';
                $bvs = !empty($result['BVS']) ? $result['BVS'] : 0;
                if (!empty($bvs)) {
                    $bvs_stat = "Biometic Verified";
                } else {
                    $bvs_stat = "Biometic Not Verified";
                }
                $sub_data['bvs'] = $bvs_stat;
                $status_flag = !empty($result['STATUS']) ? $result['STATUS'] : '';
                $sub_data['status'] = !empty($status_flag) ? Helpers_Utilities::get_subscriber_status($status_flag) : 'Unknown';
                $sub_data['imsi'] = !empty($result['IMSI']) ? $result['IMSI'] : '';
                $connection_type = !empty($result['CONNECTION_TYPE']) ? $result['CONNECTION_TYPE'] : 0;
                if (empty($connection_type)) {
                    $connection_type_name = "Prepaid";
                } else {
                    $connection_type_name = "Postpaid";
                }
                $sub_data['connection_type'] = $connection_type_name;
                $mnc = !empty($result['NETWORK']) ? $result['NETWORK'] : 0;
                $company_name = Helpers_Utilities::get_companies_data($mnc);
                $company_name = $company_name->company_name;
                $sub_data['company_name'] = $company_name;
                $sub_data['act_date'] = !empty($result['ACTDATE']) ? $result['ACTDATE'] : '';
            }
        }
        return $sub_data;
    }

    /* Search Subscriber in local databases  */

    public static function foreigner_external_search_results($data, $uid) {
        $i = 1;
        $cnic_body = '';
        foreach ($data['data'] as $result) {
            if (!empty($result)) {
                //basic info
                $cnic = !empty($result['cnic_number']) ? $result['cnic_number'] : '';
                $name = !empty($result['person_name']) ? $result['person_name'] : 'NA';
                $fname = !empty($result['person_g_name']) ? $result['person_g_name'] : '';
                $gender = !empty($result['person_gender']) ? $result['person_gender'] : '';
                if ($gender == 1) {
                    $person_gender = 'Male';
                } elseif ($gender == 2) {
                    $person_gender = 'Female';
                } else {
                    $person_gender = 'Other';
                }
                $maritalstatus = !empty($result['martial_status']) ? Helpers_Utilities::get_marital_status($result['martial_status']) : '';

                $dob = !empty($result['person_dob']) ? $result['person_dob'] : '';
                $present_Add = !empty($result['person_present_add']) ? $result['person_present_add'] : '';
                $permanent_Add = !empty($result['person_permanent_add']) ? $result['person_permanent_add'] : '';
                $family_id = !empty($result['family_id']) ? $result['family_id'] : '';
                $pak_district = !empty($result['pak_district']) ? $result['pak_district'] : '';
                $pak_tehsil = !empty($result['pak_tehsil']) ? $result['pak_tehsil'] : '';
                $home_country = !empty($result['home_country']) ? $result['home_country'] : '';
                $ethnicity = !empty($result['ethnicity']) ? $result['ethnicity'] : '';
                $address = $present_Add . " " . $pak_tehsil . " " . $pak_district;
                ?>
                <div>
                    <p style="color: #00a7d0;font-weight: bold"><u>RECORD-<?php echo $i; ?></u></p>
                    <span><b >NAME: </b><i><?php echo $name ?></i></span><br>
                    <span><b >GARDIAN NAME: </b><i><?php echo $fname ?></i></span><br>
                    <span><b >CNIC: </b><i><?php echo $cnic ?></i></span><br>
                    <span><b >DOB: </b><i><?php echo $dob ?></i></span><br>
                    <span><b >GENDER: </b><i><?php echo $person_gender ?></i></span><br>
                    <span><b >MARITAL STATUS: </b><i><?php echo $maritalstatus ?></i></span><br>
                    <span><b >PRESENT ADDRESS: </b><i><?php echo $address; ?></i></span><br>
                    <span><b >PERMANENT ADDRESS: </b><i><?php echo $permanent_Add; ?></i></span><br>
                    <span><b >HOME COUNTRY: </b><i><?php echo $home_country; ?></i></span><br>
                    <span><b >ETHNICITY: </b><i><?php echo $ethnicity; ?></i></span><br>

                    <form  name="fixerror-<?php echo $i ?>" id="fixerror-<?php echo $i ?>" action="<?php echo URL::base() . 'userreports/create_foreigner_person'; ?>"  method="post"  >

                        <input type="hidden" name="cnic_number" value="<?php echo $cnic; ?>">
                        <input type="hidden" name="is_foreigner" value="1">
                        <button  style="margin-top: -22px; margin-right: 5px" type="submit" title="Save in AIES" class="pull-right btn-primary" >Save in Database</button>

                    </form>



                    <hr class="style14 ">
                </div>
                <?php
            }
            $i++;
        }
    }

//function to update user request status
    public static function update_user_request_status($request_id, $request_status, $processing_index) {
        $query = DB::update('user_request')->set(array('status' => $request_status, 'processing_index' => $processing_index))
                ->where('request_id', '=', $request_id)
                ->execute();
        return $query;
    }

    /* Delete user request  */

    public static function delete_user_request($request_id, $loginid) {
        $sql = "DELETE t1, t2 
                FROM user_request as t1 
                INNER JOIN email_messages as t2 ON (t2.message_id=t1.message_id)
                WHERE t1.request_id ={$request_id}";
        $results = DB::query(Database::DELETE, $sql)->execute();
        //Helpers_Profile::user_activity_log($uid, 4 ,NULL ,NULL ,NULL ,NULL ,$id);
        return $results;
    }

    /* delete_temp_verisys_record  */

    public static function delete_temp_verisys_record($row_id) {
        $sql = "Select *  FROM verisys_temp_files where row_id  ={$row_id}";
        $results_select = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($results_select)) {
            $file_name = isset($results_select['image_name']) ? $results_select['image_name'] : '';
            //Unlink file form folder
            $file_with_path = getcwd() . '/uploads/verisys_temp_images/' . $file_name;
            $re = unlink($file_with_path);
        }
        $sql = "DELETE FROM verisys_temp_files where row_id  ={$row_id}";
        $results = DB::query(Database::DELETE, $sql)->execute();
        return $results;
    }
    /* delete_temp_familytree_record  */

    public static function delete_temp_familytree_record($row_id) {
        $sql = "Select *  FROM familytree_temp_files where row_id  ={$row_id}";
        $results_select = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($results_select)) {
            $file_name = isset($results_select['image_name']) ? $results_select['image_name'] : '';
            //Unlink file form folder
            $file_with_path = getcwd() . '/uploads/familytree_temp_images/' . $file_name;
            $re = unlink($file_with_path);
        }
        $sql = "DELETE FROM familytree_temp_files where row_id  ={$row_id}";
        $results = DB::query(Database::DELETE, $sql)->execute();
        return $results;
    }
    public static function delete_temp_travelhistory_record($row_id) {
        $sql = "Select *  FROM travelhistory_temp_files where row_id  ={$row_id}";
        $results_select = DB::query(Database::SELECT, $sql)->execute()->current();
        if (!empty($results_select)) {
            $file_name = isset($results_select['image_name']) ? $results_select['image_name'] : '';
            //Unlink file form folder
            $file_with_path = getcwd() . '/uploads/travelhistory_temp_images/' . $file_name;
            $re = unlink($file_with_path);
        }
        $sql = "DELETE FROM travelhistory_temp_files where row_id  ={$row_id}";
        $results = DB::query(Database::DELETE, $sql)->execute();
        return $results;
    }

    /* Branchless Banking User request status Ajax Call */

    public static function branchlessbanking_request_data($data, $count) {
//        echo '<pre>';
//        print_r($data);
//        exit;
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        if (empty($data['bank_id']))
            $data['bank_id'] = '';
        else {
            $bank_array = array_values($data['bank_id']);
            $bank_value = implode(',', $bank_array);
            $data['bank_id'] = " and t1.bank_id in ({$bank_value}) ";
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
                case "4":
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
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $search_string = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and (t1.requested_value like '%{$search_string}%' or t1.request_id like '%{$search_string}%' or t1.reference_id like '%{$search_string}%' or t1.dispatch_id like '%{$search_string}%')";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by u1.user_id";

        $where_clause = " where 1 ";
        //$where_clause = "where t1.user_id not IN (182,137,136)";

        $and_status = '';
        if (isset($data['r_status'])) {
            switch ($data['r_status']) {
                case 1;
                    $and_status = ' and  t1.request_status = 1 ';
                    break;
                case 2;
                    $and_status = ' and  t1.request_status = 2 ';
                    break;
                case 3;
                    $and_status = ' and  t1.request_status = 3 ';
                    break;
                case 4;
                    $and_status = '  ';
                    break;
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
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                                FROM ctfu_user_request as t1 
                                join email_templates_type as t2  on t1.user_request_type_id = t2.id                                
                                {$search}
                                {$where_clause}
                                {$and_status}     
                                {$data['r_category']}
                                {$data['bank_id']}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * FROM 
                                ctfu_user_request as t1 
                                join email_templates_type as t2 on t1.user_request_type_id = t2.id                                
                                {$where_clause}
                                {$and_status}      
                                {$data['bank_id']}
                                {$data['r_category']}
                                {$search}
                                {$order_by}    
                                {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* single request status view  */

    public static function request_detail_ctfu($id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM  ctfu_user_request as t1  where t1.request_id = {$id}";
        $members = $DB->query(Database::SELECT, $sql, FALSE)->current();
        return $members;
    }

}
