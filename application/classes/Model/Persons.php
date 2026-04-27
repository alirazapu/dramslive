<?php
defined('SYSPATH') or die('No direct script access.');

class Model_Persons
{
    /* for cell log summary */

    public static function cell_log_summary($data, $count, $pid)
    {
//        echo '<pre>';
//        print_r($data); exit;
        $searchsql = '';
        $searchsql_phone = '';
        $searchsql_ophone = '';
        $time_filter = '';
        $searchsql_duration = '';
        $searchsql_lat = '';
        $searchsql_long = '';

        if (!empty($data['st']) && !empty($data['et'])) {
            $start_time = date('H:i:s', strtotime($data['st']));
            $end_time = date('H:i:s', strtotime($data['et']));
            if ($end_time == '00:00:00') {
                $end_time = '24:00:00';
            }
            //  $time_filter= " and sms_at between '{$start_time}' and '{$end_time}' ";
            $time_filter = " and  DATE_FORMAT(call_at,'%H:%i:s') >= '{$start_time}'
            and DATE_FORMAT(call_at,'%H:%i:s') <= '{$end_time}' ";
        }
        if (!empty($data['duration'])) {
            $searchsql_duration = " and duration_in_seconds = {$data['duration']}";
        }
        if (!empty($data['lat'])) {
            $searchsql_lat = " and latitude = {$data['lat']}";
        }
        if (!empty($data['long'])) {
            $searchsql_long = " and longitude = {$data['long']}";
        }
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
        }
        if (!empty($data['type'])) {
            switch ($data['type']) {
                case 'location':
                    $searchsql = " and address like '%{$data['key']}%' ";
                    break;
                /*case 'date':                    
                    $start_date = date("Y-m-d H:i:s", strtotime($data['startdate']));
                    $end_date = date("Y-m-d H:i:s", strtotime($data['enddate']));
                    /*$start_date = date("Y-m-d", strtotime($data['startdate']));
                    $end_date = date("Y-m-d", strtotime($data['enddate']));
                    $start_date1 = $start_date . ' 00:00:00';
                   $end_date1 = $end_date . ' 23:59:59';
                    $searchsql = " and call_at between '{$start_date1}' and '{$end_date1}' ";
                    $searchsql = " and call_at between '{$start_date}' and '{$end_date}' ";
                    break;*/
                case 'all':
                    $searchsql = '';
                    break;
            }
        }

        if (!empty($data['startdate']) && !empty($data['enddate'])) {
            $data['startdate'] = $data['startdate'] . ':00';
            $data['enddate'] = $data['enddate'] . ':59';

            $start_date = date("Y-m-d H:i:s", strtotime($data['startdate']));
            $end_date = date("Y-m-d H:i:s", strtotime($data['enddate']));

            $searchsql .= " and call_at between '{$start_date}' and '{$end_date}' ";
        }

        /* Sorted Data */
        $order_by_param = "phone_number";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "2":
                    $order_by_param = "is_outgoing";
                    break;
                case "3":
                    $order_by_param = "duration_in_seconds";
                    break;
                case "4":
                    $order_by_param = "call_at";
                    break;
                case "5":
                    $order_by_param = "address";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\_\|\-\. ]/', '', $data['sSearch']);
            $search = "and (other_person_phone_number like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }
        //print_r($search); exit;
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count from person_call_log
                        where person_id = $pid
                        {$search}
                        {$searchsql_phone}
                        {$time_filter}   
                        {$searchsql_ophone}   
                        {$searchsql}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select * from person_call_log 
                            where person_id = $pid
                            {$search}
                            {$searchsql}
                            {$time_filter}  
                                {$searchsql_phone}   
                        {$searchsql_ophone}
                        {$searchsql_duration}
                        {$searchsql_lat}
                        {$searchsql_long}
                            {$order_by} 
                            {$limit}";
//             echo $sql;
//             print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* sms log summary */

    public static function sms_log_summary($data, $count, $pid)
    {
        //sms
        $searchsql = '';
        $searchsql_phone = '';
        $searchsql_ophone = '';
        $time_filter = '';

        if (!empty($data['st']) && !empty($data['et'])) {
            $start_time = date('H:i:s', strtotime($data['st']));
            $end_time = date('H:i:s', strtotime($data['et']));
            if ($end_time == '00:00:00') {
                $end_time = '24:00:00';
            }
            //  $time_filter= " and sms_at between '{$start_time}' and '{$end_time}' ";
            $time_filter = " and  DATE_FORMAT(sms_at,'%H:%i:s') >= '{$start_time}'
            and DATE_FORMAT(sms_at,'%H:%i:s') <= '{$end_time}' ";
        }
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
        }
        if (!empty($data['field'])) {
            switch ($data['field']) {
                case 'location':
                    $searchsql = " and address like '%{$data['key']}%' ";
                    break;
                case 'date':
                    $start_date = date("Y-m-d", strtotime($data['startdate']));
                    $end_date = date("Y-m-d", strtotime($data['enddate']));

                    $start_date = $start_date . ' 00:00:00';
                    $end_date = $end_date . ' 23:59:59';
                    $searchsql = " and sms_at between '{$start_date}' and '{$end_date}' ";
                    // print_r($searchsql); exit;
                    break;
                case 'all':
                    $searchsql = '';
                    break;
            }
        }

        /* Sorted Data */
        $order_by_param = "phone_number";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "2":
                    $order_by_param = "is_outgoing";
                    break;
                case "3":
                    $order_by_param = "sms_at";
                    break;
                case "4":
                    $order_by_param = "address";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        /* Starting and Ending Lenght (size) */
        $limit = '';
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\_\|\-\. ]/', '', $data['sSearch']);
            $search = "and ( other_person_phone_number like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count from person_sms_log
                        where person_id = $pid
                        {$search}
                        {$searchsql}
                        {$time_filter}
                        {$searchsql_phone}
                        {$searchsql_ophone}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select * from person_sms_log 
                            where person_id = $pid
                            {$search}
                            {$searchsql}
                            {$time_filter}                            
                            {$searchsql_phone}                            
                            {$searchsql_ophone}                            
                            {$order_by} 
                            {$limit}";
            //print_r($sql);exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* location log summary */

    public static function location_log_summary($data, $count, $pid)
    {

//        $id = (int)Helpers_Utilities::encrypted_key($data['id'], "decrypt");
//        echo '<pre>';
//        print_r($data);
//        exit();
        //sms
        $searchsql = '';
        $searchsql_phone = '';
        $searchsql_ophone = '';
        $time_filter = '';
        $where = 'where 1';

        if (!empty($data['st']) && !empty($data['et'])) {
            $start_time = date('H:i:s', strtotime($data['st']));
            $end_time = date('H:i:s', strtotime($data['et']));
            if ($end_time == '00:00:00') {
                $end_time = '24:00:00';
            }
            //  $time_filter= " and sms_at between '{$start_time}' and '{$end_time}' ";
            $time_filter_sms = " and  DATE_FORMAT(sms_at,'%H:%i:s') >= '{$start_time}'
            and DATE_FORMAT(sms_at,'%H:%i:s') <= '{$end_time}' ";
            $time_filter_call = " and  DATE_FORMAT(call_at,'%H:%i:s') >= '{$start_time}'
            and DATE_FORMAT(call_at,'%H:%i:s') <= '{$end_time}' ";
        }
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
        }
        if (!empty($data['add'])) {
            $address = " and address='{$data['add']}'";
        }
        if (!empty($data['add'])) {
            $address1 = " where address='{$data['add']}'";
        }
        $start_date = '';
        $end_date = '';
        if (!empty($data['startdate']) && !empty($data['enddate'])) {
            $start_date = date("Y-m-d", strtotime($data['startdate']));
            $end_date = date("Y-m-d", strtotime($data['enddate']));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $searchsql = "  and (t1.time_t between '{$start_date}' and '{$end_date}')  ";

        }
//        if (!empty($data['field'])) {
//            switch ($data['field']) {
//                case 'location':
//                    $searchsql = " and address like '%{$data['key']}%' ";
//                    break;
//                case 'date':
//                    $start_date = date("Y-m-d", strtotime($data['startdate']));
//                    $end_date = date("Y-m-d", strtotime($data['enddate']));
//
//                    $start_date = $start_date . ' 00:00:00';
//                    $end_date = $end_date . ' 23:59:59';
//                    $searchsql = " where (t1.time_t between '{$start_date}' and '{$end_date}') ";
//                    // print_r($searchsql); exit;
//                    break;
//                case 'all':
//                    $searchsql = '';
//                    break;
//            }
//        }

        /* Sorted Data */
        $order_by_param = "phone_number";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "2":
                    $order_by_param = "is_outgoing";
                    break;
                case "3":
                    $order_by_param = "sms_at";
                    break;
                case "4":
                    $order_by_param = "address";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        /* Starting and Ending Lenght (size) */
        $limit = '';
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\_\|\-\. ]/', '', $data['sSearch']);
            $search = "and ( other_person_phone_number like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
//        GROUP by t1.address
//                        ORDER BY COUNT(t1.address) DESC
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = " select  count(t1.address) as count, t1.address from (
                        (SELECT person_id as person_id,  address
                        FROM aies.person_call_log
                        where person_id = $pid
                        {$time_filter_call}
                        )
                        UNION all
                        (SELECT  person_id as person_id,address 
                        FROM aies.person_sms_log
                        where person_id = $pid   
                        {$time_filter_sms})    ) as t1
                        {$address1}
                      
                         
                        ";
//            print_r($sql);exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "

select * from (
                        (SELECT person_id as person_id, phone_number , other_person_phone_number ,address,call_at as time_t , 'call'
                        FROM person_call_log 
                        where person_id = $pid
                        {$time_filter_call}
                        {$address})
                        UNION all
                        (SELECT person_id as person_id, phone_number , other_person_phone_number ,address,sms_at ,'sms'  
                        FROM person_sms_log 
                        where person_id = $pid   
                        {$time_filter_sms}
                        {$address})) as t1
                        {$where}
                            {$search}
                            {$searchsql}
                                                    
                            {$searchsql_phone}                            
                            {$searchsql_ophone}                            
                       
                            {$limit}";
//             print_r($sql);exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }


    /* call summary */

    public static function call_summary($data, $count, $pid, $phone = NULL, $phone2 = NULL)
    {
        // echo "<pre>"; print_r($data) ; echo "</pre>"; exit;
        $searchsql_phone = '';
        $searchsql_ophone = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
        }
        /* Sorted Data */
        $order_by_param = "calls";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "2":
                    $order_by_param = "icalls";
                    break;
                case "3":
                    $order_by_param = "ocalls";
                    break;
                case "4":
                    $order_by_param = "calls";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and other_person_phone_number like '%{$data['sSearch']}%' ";
        } else {
            $search = "";
        }

        //Search by Party A and Party B
        if (!empty($phone) && !empty($phone2)) {
            $search_by_persons = " and phone_number= $phone AND other_person_phone_number = '$phone2' ";
        } else {
            $search_by_persons = '';
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_summary
                    WHERE person_id=$pid 
                    {$search_by_persons}
                    {$search}  
                    {$searchsql_ophone}
                    {$searchsql_phone}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT * , (calls_made_count + calls_received_count) as calls,
                    calls_received_count as icalls,
                    calls_made_count as ocalls
                    FROM person_summary
                    WHERE person_id=$pid 
                    {$search_by_persons}
                    {$search}                         
                    {$searchsql_phone}
                    {$searchsql_ophone}
                    {$order_by}
                    {$limit}";
            // print_r($sql); exit; 
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* for call summary Detail */

    public static function call_summary_detail($data, $count, $pid, $phone, $phone2)
    {
        $searchsql = '';
        if (empty($data['key'])) {
            $searchsql = '';
        } else {
            //if (isset($data['searchkey'])) {
            $searchsql = " and address like '%{$data['key']}%' ";
            //}
            if (!empty($data['startdate']) && !empty($data['enddate'])) {
                $searchsql .= " and call_at between '{$data['startdate']}' and '{$data['enddate']}' ";
            }
        }
        /* Sorted Data */
        $order_by_param = "phone_number";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "2":
                    $order_by_param = "is_outgoing";
                    break;
                case "3":
                    $order_by_param = "duration_in_seconds";
                    break;
                case "4":
                    $order_by_param = "call_at";
                    break;
                case "5":
                    $order_by_param = "address";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\_\|\-\:\. ]/', '', $data['sSearch']);
            $search = "and (call_at like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count from person_call_log
                        where person_id = $pid AND phone_number = $phone AND other_person_phone_number = $phone2 
                        {$search}
                        {$searchsql}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select * from person_call_log 
                            where person_id = $pid AND phone_number = $phone AND other_person_phone_number = $phone2
                            {$search}
                            {$searchsql}                            
                            {$order_by} 
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* SMS summary */

    public static function sms_summary($data, $count, $pid, $phone = NULL, $phone2 = NULL)
    {
        //echo "<pre>"; print_r($data) ; echo "</pre>"; exit;
        $searchsql_phone = '';
        $searchsql_ophone = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
        }

        /* Sorted Data */
        $order_by_param = "sms";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "2":
                    $order_by_param = "isms";
                    break;
                case "3":
                    $order_by_param = "osms";
                    break;
                case "4":
                    $order_by_param = "sms";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\- ]/', '', $data['sSearch']);
            $search = "and other_person_phone_number like '%{$data['sSearch']}%' ";
        } else {
            $search = "";
        }

        //Search by Party A and Party B
        if (!empty($phone) && !empty($phone2)) {
            $search_by_persons = " and phone_number= $phone AND other_person_phone_number = '$phone2'";
        } else {
            $search_by_persons = '';
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_summary
                    WHERE person_id=$pid 
                    and (sms_sent_count > 0 OR sms_received_count > 0)        
                    {$search_by_persons}    
                    {$search}             
                    {$searchsql_phone}
                    {$searchsql_ophone}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT * , (sms_received_count + sms_sent_count) as sms,
                    sms_received_count as isms,
                    sms_sent_count as osms
                    FROM person_summary                    
                    WHERE person_id=$pid
                    and (sms_sent_count > 0 OR sms_received_count > 0)    
                    {$search_by_persons}
                    {$search}                         
                    {$searchsql_phone}
                    {$searchsql_ophone}
                    {$order_by}
                    {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* for sms summary Detail */

    public static function sms_summary_detail($data, $count, $pid, $phone, $phone2)
    {
        $searchsql = '';
        if (empty($data['key'])) {
            $searchsql = '';
        } else {
            //if (isset($data['searchkey'])) {
            $searchsql = " and address like '%{$data['key']}%' ";
            //}
            if (!empty($data['startdate']) && !empty($data['enddate'])) {
                $searchsql .= " and call_at between '{$data['startdate']}' and '{$data['enddate']}' ";
            }
        }
        /* Sorted Data */
        $order_by_param = "phone_number";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "2":
                    $order_by_param = "is_outgoing";
                    break;
                case "3":
                    $order_by_param = "sms_at";
                    break;
                case "4":
                    $order_by_param = "address";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\_\|\-\,\:\. ]/', '', $data['sSearch']);
            $search = "and ( sms_at like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count from person_sms_log
                        where person_id = $pid
                        AND phone_number = $phone
                        AND other_person_phone_number = '$phone2'
                        {$search}
                        {$searchsql}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select * from person_sms_log 
                            where person_id = $pid AND phone_number = $phone AND other_person_phone_number = '$phone2' 
                            {$search}
                            {$searchsql}                            
                            {$order_by} 
                            {$limit}";
            // echo $sql;                
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* cdr summary */

    public static function cdr_summary($data, $count, $pid, $valid_bparty_only = false)
    {
        //echo "<pre>"; print_r($data['phone_number']) ; echo "</pre>"; exit;
        $searchsql_phone = '';
        $searchsql_ophone = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            //now this
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
            // print_r($searchsql_ophone); exit;
        }
        // Restrict to valid Pakistani mobile MSISDNs only (drops empty, NULL, short codes, landlines).
        // Accepts: 3XXXXXXXXX (10), 03XXXXXXXXX (11), 923XXXXXXXXX (12).
        // Folded into $searchsql_ophone so the SQL templates below don't change
        // (identical templates exist in the sibling b_party() method, which we don't touch).
        if ($valid_bparty_only) {
            $searchsql_ophone .= " and other_person_phone_number IS NOT NULL"
                . " and other_person_phone_number != ''"
                . " and other_person_phone_number REGEXP '^(3[0-9]{9}|03[0-9]{9}|923[0-9]{9})$' ";
        }
        /* Sorted Data */
        $order_by_param = "tcalls";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "2":
                    $order_by_param = "tsms";
                    break;
                case "3":
                    $order_by_param = "tcalls";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and other_person_phone_number like '%{$data['sSearch']}%' ";
        } else {
            $search = "";
        }

        /* Group By */
//        $groupby = "group by person_id";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_summary
                    WHERE person_id=$pid
                    {$search}             
                    {$searchsql_phone}
                    {$searchsql_ophone}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT * , (sms_received_count + sms_sent_count) as tsms,
                             (calls_received_count + calls_made_count) as tcalls
                    FROM person_summary
                    WHERE person_id=$pid
                    {$search}                         
                    {$searchsql_phone}
                    {$searchsql_ophone}
                    {$order_by}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* short code analysis */

    public static function short_code_analysis($data, $count, $pid)
    {
       
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            //  $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and ps.other_person_phone_number like '%{$data['sSearch']}%' ";
        } else {
            $search = "";
        }
   
        $DB = Database::instance();
        $sc = "select code from telco_short_code";
        $results = $DB->query(Database::SELECT, $sc, false)->as_array();
        $s_code = implode(', ', array_values(array_column($results, 'code')));

        $where = "where person_id= '{$pid}' ";
        $where1 = '';
        if (!empty($s_code)) {
            $where1 = "and ps.other_person_phone_number in($s_code)";
        }

        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(tsc.code) as count 
                    FROM telco_short_code tsc
                    left join person_summary ps on tsc.code=ps.other_person_phone_number
                    {$where}              
                    {$search}                                
                   ";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
		else {
            $sql = "SELECT ps.* , (sms_received_count + sms_sent_count) as tsms,
                          (calls_received_count + calls_made_count) as tcalls, tsc.company_name
                         FROM person_summary ps
                         left join telco_short_code tsc on tsc.code=ps.other_person_phone_number
                    {$where}
                    {$where1}
                    {$search}
                    {$limit}                         
                    ";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* b party */

    public static function b_party($data, $count, $pid)
    {
        //echo "<pre>"; print_r($data['phone_number']) ; echo "</pre>"; exit;
        $searchsql_phone = '';
        $searchsql_ophone = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            //now this
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
            // print_r($searchsql_ophone); exit;
        }
        /* Sorted Data */
        $order_by_param = "tcalls";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "2":
                    $order_by_param = "tsms";
                    break;
                case "3":
                    $order_by_param = "tcalls";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and other_person_phone_number like '%{$data['sSearch']}%' ";
        } else {
            $search = "";
        }

        /* Group By */
//        $groupby = "group by person_id";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_summary
                    WHERE person_id=$pid
                    {$search}             
                    {$searchsql_phone}
                    {$searchsql_ophone}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT * , (sms_received_count + sms_sent_count) as tsms,
                             (calls_received_count + calls_made_count) as tcalls
                    FROM person_summary
                    WHERE person_id=$pid
                    {$search}                         
                    {$searchsql_phone}
                    {$searchsql_ophone}
                    {$order_by}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Person Favourite Person */

    public static function person_favourite_person($data, $count, $pid)
    {
        //echo "<pre>"; print_r($data) ; echo "</pre>"; exit;
        $searchsql_phone = '';
        $searchsql_ophone = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        if (!empty($data['otherphone'])) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            $searchsql_ophone = " and other_person_phone_number IN ('{$o_person_phone}')";
        }
        /* Sorted Data */
        $order_by_param = "calls";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "4":
                    $order_by_param = "calls";
                    break;
                case "5":
                    $order_by_param = "sms";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and ps.other_person_phone_number like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by ps.other_person_phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_summary ps
                    WHERE person_id=$pid
                    {$searchsql_phone}
                    {$searchsql_ophone}
                    {$search}             
                    ";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT 
                (select person_id from person_phone_number as ppn where ppn.phone_number = ps.other_person_phone_number LIMIT 1) as other_id,
                ps.other_person_phone_number, 
                (SUM(ps.calls_made_count) + SUM(ps.calls_received_count)) as calls , 
                (SUM(ps.sms_sent_count) + SUM(ps.sms_received_count)) as sms 
                 FROM person_summary ps 
                 WHERE ps.person_id=$pid
                 {$searchsql_phone}
                 {$searchsql_ophone}
                    {$search}                                             
                    {$groupby}
                    {$order_by}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Person DB Match */
    public static function person_db_match($data, $count, $pid)
    {
        //this
        $searchsql_phone = '';
        $searchsql_date = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone = {$data['phone_number']}";
        }
        if (!empty($data['startdate'])) {
            $start_date = date("Y-m-d", strtotime($data['startdate']));
            if (empty($data['enddate'])) {
                //current date 
                $end_date = date("Y-m-d");
            } else {
                $end_date = date("Y-m-d", strtotime($data['enddate']));
            }
            $start_date1 = $start_date . ' 00:00:00';
            $end_date1 = $end_date . ' 23:59:59';
            $searchsql_date = " and calldate between '{$start_date1}' and '{$end_date1}' ";
        }
        /* Sorted Data */
        $order_by_param = "incoming_calls";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone";
                    break;
                case "5":
                    $order_by_param = "incoming_calls";
                    break;
                case "6":
                    $order_by_param = "outgoing_calls";
                    break;
                case "7":
                    $order_by_param = "incoming_sms";
                    break;
                case "8":
                    $order_by_param = "outgoing_sms";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = " and ( CONCAT(TRIM(pt.first_name), ' ', TRIM(pt.last_name))  like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }
        $category = '';
        if (isset($data['category']) && !empty($data['category']) && $data['category'] !=4) {
            if($data['category']==3)                
                $category = ' and pt.person_id in (select person_id from person_category as pc where pc.category_id in (1,2)) ';
            else 
                $category = ' and pt.person_id in (select person_id from person_category as pc where pc.category_id = '.$data['category'].') ';
        }
        /* Group By */
        $groupby = "group by ppn.person_id,table1.phone,ppn.phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        //if ($count == 'true') {
            $sql = "select count(distinct ppn.person_id,table1.phone,ppn.phone_number) as count from (
                        (SELECT 
                            person_id                 as person_id, 
                            phone_number              as phone, 
                            other_person_phone_number as otherphone,
                            is_outgoing               as calltype,
                            call_at                   as calldate,
                            1 as is_call
                        FROM person_call_log
                        where person_id = $pid)
                        UNION all
                        (SELECT 
                                person_id                 as person_id, 
                                phone_number              as phone, 
                                other_person_phone_number as otherphone,
                                is_outgoing               as calltype, 
                                sms_at                    as calldate,
                                0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as table1
                        join person_phone_number as ppn on ppn.phone_number = table1.otherphone 
                        join person as pt on ppn.person_id = pt.person_id
                        where 1 
                        {$category}
                    {$searchsql_phone}
                    {$searchsql_date}
                    {$search}             
                    ";
//            print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            $result = array();
            $result['count'] = $members['count'];
        //} /*  Fetch all Records */ else {
            $sql = "select pt.first_name, ppn.person_id as other_id,ppn.phone_number as bparty,pt.address,
                           table1.person_id,table1.phone,
                           sum(CASE WHEN (calltype = 1 and is_call = 1) THEN 1 ELSE 0 END) as outgoing_calls,
                           sum(CASE WHEN (calltype = 0 and is_call = 1)THEN 1 ELSE 0 END) as incoming_calls,
                           sum(CASE WHEN (calltype = 1 and is_call = 0) THEN 1 ELSE 0 END) as outgoing_sms,
                           sum(CASE WHEN (calltype = 0 and is_call = 0)THEN 1 ELSE 0 END) as incoming_sms 
                   from (
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype,
                                    call_at                   as calldate,
                                    1 as is_call
                            FROM person_call_log
                            where person_id = $pid)
                    UNION all
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype, 
                                    sms_at                    as calldate,
                                    0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as table1
                        join person_phone_number as ppn on ppn.phone_number = table1.otherphone
                        join person as pt on ppn.person_id = pt.person_id
                        where 1 
                        {$category}
                 {$searchsql_phone}
                 {$searchsql_date}
                    {$search}                                             
                    {$groupby}
                    {$order_by}
                    {$limit}";
//                    if(Auth::instance()->get_user()->id==419){
                    //print_r($sql); exit;                    
//                    }
            $members = $DB->query(Database::SELECT, $sql, FALSE);
          //  return $members;
            
          $result['result'] = $members;
          return $result;
        //}
    }

    /* Person Physical Location summary */

    public static function person_physical_location($data, $count, $pid)
    {

        $searchsql_phone = '';
        $searchsql_date = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone = {$data['phone_number']}";
        }
        if (!empty($data['startdate'])) {
            $start_date = date("Y-m-d", strtotime($data['startdate']));
            if (empty($data['enddate'])) {
                //current date 
                $end_date = date("Y-m-d");
            } else {
                $end_date = date("Y-m-d", strtotime($data['enddate']));
            }
            $start_date1 = $start_date . ' 00:00:00';
            $end_date1 = $end_date . ' 23:59:59';
            $searchsql_date = " and calldate between '{$start_date1}' and '{$end_date1}' ";
        }
        /* Sorted Data */
        $order_by_param = "loc_count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "loc_count";
                    break;
                case "2":
                    $order_by_param = "loc_count";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.address like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }

        /* Group By */
        $groupby = "group by t1.phone, t1.address";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "select count(distinct t1.phone,t1.address) as count from (
                        (SELECT 
                            person_id                 as person_id, 
                            phone_number              as phone, 
                            other_person_phone_number as otherphone,
                            is_outgoing               as calltype,
                            call_at                   as calldate,
                            address                   as address,
                            1 as is_call
                        FROM person_call_log
                        where person_id = $pid)
                        UNION all
                        (SELECT 
                                person_id                 as person_id, 
                                phone_number              as phone, 
                                other_person_phone_number as otherphone,
                                is_outgoing               as calltype, 
                                sms_at                    as calldate,
                                address                   as address,
                                0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as t1                        
                        where 1 
                    {$searchsql_phone}
                    {$searchsql_date}
                    {$search}             
                    ";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "select t1.person_id, t1.longitude,t1.latitude, t1.phone, t1.calldate,t1.address, count(t1.address) as loc_count from (
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype,
                                    call_at                   as calldate,
                                    address                   as address,
                                    longitude,
                                    latitude,
                                    1 as is_call
                            FROM person_call_log
                            where person_id = $pid)
                    UNION all
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype, 
                                    sms_at                    as calldate,
                                    address                   as address,
                                    longitude,
                                    latitude,
                                    0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as t1
                        where 1 
                 {$searchsql_phone}
                 {$searchsql_date}
                    {$search}                                             
                    {$groupby}
                    {$order_by}
                    {$limit}";
//            echo '<pre>';
//            print_r($sql);
//            exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Person Affiliations */

    public static function person_affiliation($data, $count, $pid)
    {
        //echo "<pre>"; print_r($data) ; echo "</pre>"; exit;
        $searchsql_phone = '';
        $searchsql_project = '';
        $searchsql_organization = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and ps.phone_number = {$data['phone_number']}";
        }
        if (!empty($data['affproject'])) {
            $project_id = implode(',', $data['affproject']);
            $searchsql_project = " and pa.project_id IN ( {$project_id})";
        }
        if (!empty($data['afforg'])) {
            $organization_id = implode(',', $data['afforg']);
            $searchsql_organization = " and pa.organization_id IN ( {$organization_id})";
        }
        /* Sorted Data */
        $order_by_param = "other_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "3":
                    $order_by_param = "other_id";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $s_value = $data['sSearch'];
            $search = "and ps.other_person_phone_number like '%$s_value%'";
        } else {
            $search = "";
        }
        /* Group By */
        $groupby = "group by ps.other_person_phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(DISTINCT ps.other_person_phone_number) as count                
                 FROM person_summary ps 
                 Inner join person_summary as pa1 on pa1.phone_number = ps.other_person_phone_number
                 Inner join person_affiliations as pa on pa.person_id = pa1.person_id
                 WHERE ps.person_id=$pid                 
                    {$searchsql_phone}
                    {$searchsql_project}
                    {$searchsql_organization}
                    {$search}             
                    ";
//            print_r($sql);exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "SELECT ps.phone_number, ps.other_person_phone_number,ps.person_id as pid,
                    pa.person_id as other_id,pa.organization_id as org_id
                 FROM person_summary ps 
                 Inner join person_summary as pa1 on pa1.phone_number = ps.other_person_phone_number
                 Inner join person_affiliations as pa on pa.person_id = pa1.person_id
                 WHERE ps.person_id=$pid                 
                 {$searchsql_phone}
                 {$searchsql_project}
                 {$searchsql_organization}                     
                    {$search}                                             
                    {$groupby}
                    {$order_by}
                    {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* User's feed back about person */

    public static function users_feedback($count, $pid)
    {

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count from users_feedback 
                            where person_id = $pid";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select * from users_feedback 
                            where person_id = $pid order by added_on desc";
            // echo $sql;                
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Add Favourite Person   */

    public static function add_favouriteperson($id, $loginid)
    {
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('user_favorite_person', array('user_id', 'person_id', 'added_on'))
            ->values(array($loginid, $id, $date))
            ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 6, NULL, NULL, $id);
        return $query;
    }

    /* Delete Favourite Person   */

    public static function delete_favouriteperson($id, $loginid)
    {
        $date = date('Y-m-d H:i:s');
        $query = DB::delete('user_favorite_person')
            ->where('user_id', '=', $loginid)
            ->and_where('person_id', '=', $id)
            ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 7, NULL, NULL, $id);
        return $query;
    }

    /* Add Sensitive Person   */

    public static function add_sensitiveperson($id, $loginid)
    {
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('user_sensitive_person', array('user_id', 'person_id', 'added_on'))
            ->values(array($loginid, $id, $date))
            ->execute();

        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 17, NULL, NULL, $id);
        return $query;
    }

    /* Delete Sensitive Person   */

    public static function delete_sensitiveperson($id, $loginid)
    {
        $date = date('Y-m-d H:i:s');
        $query = DB::delete('user_sensitive_person')
            ->where('user_id', '=', $loginid)
            ->and_where('person_id', '=', $id)
            ->execute();
        $query1 = DB::delete('sensitive_person_acl')
            ->where('user_id', '=', $loginid)
            ->and_where('person_id', '=', $id)
            ->execute();
        //to add activity detail in user activity time line
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 18, NULL, NULL, $id);
        return $query;
    }

    public static function get_person_acl_users()
    {

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        if ($permission == 1 || $permission == 2 || $permission == 3) {
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
            $sql = "Select * from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id                         
                        {$where_clause}
                        and u1.is_deleted = 0 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) ";
            // print_r($sql);
            $members = $DB->query(Database::SELECT, $sql, TRUE);
            return $members;
        }
    }

    public static function sensitiveperson_acl_save($data)
    {
//       echo '<pre>';
//        print_r($data);
//        exit;
        $date = date('Y-m-d H:i:s');
        $login_user = Auth::instance()->get_user();
        for ($i = 0; $i < sizeof($data['user-acl']); $i++) {
            $DB = Database::instance();
            $sql = "SELECT allowed_user_id
                                    FROM  sensitive_person_acl
                                    WHERE allowed_user_id = '{$data['user-acl'][$i]}' and person_id = {$data['person-acl']} Limit 1";
            $acl_check = DB::query(Database::SELECT, $sql)->execute()->current();
            if (!empty($acl_check)) {
                $check = 1;
            } else {
                $check = 0;
            }
            //print_r($acl_check);                    
            if (isset($data['user-acl-val'][$i])) {
                if ($data['user-acl-val'][$i] == 'on' && $check == 0) {
                    $query = DB::insert('sensitive_person_acl', array('user_id', 'person_id', 'allowed_user_id', 'allowed_at'))
                        ->values(array($login_user->id, $data['person-acl'], $data['user-acl'][$i], $date))
                        ->execute();
                }
            } else {
                $DB = Database::instance();
                $query = DB::delete('sensitive_person_acl')
                    ->where('user_id', '=', $login_user->id)
                    ->and_where('person_id', '=', $data['person-acl'])
                    ->and_where('allowed_user_id', '=', $data['user-acl'][$i])
                    ->execute();
            }
        }
    }

    /* get person calls and sms summary   */

    public static function get_person_calls_sms_summary($pid)
    {
        $DB = Database::instance();
        $query = "SELECT MONTHNAME(reported_month) as month, (calls_received_count+calls_made_count) as calls,(sms_sent_count+sms_received_count) as sms
                FROM person_monthly_summary 
                where person_id = {$pid} 
                GROUP BY reported_month  
                order by reported_month DESC Limit 7";
        $results = $DB->query(Database::SELECT, $query, TRUE);
        //$sql = DB::query(Database::SELECT, $query)->execute();  
        return $results;
    }

    /* Data for page load of Location Call Log */

    public static function get_person_last_five_calls($pid)
    {

        $DB = Database::instance();
        $query_current_location = "SELECT address as location,t1.phone_number, t1.moved_in_at as time,t1.latitude,t1.longitude
                FROM person_location_history AS t1
                join person_phone_number as t2
                on t1.phone_number=t2.phone_number
                WHERE t2.person_id= $pid 
                and t1.latitude>0 and t1.longitude>0
                ORDER BY t1.moved_in_at DESC
                Limit 5";
        $results_current_location = $DB->query(Database::SELECT, $query_current_location, TRUE);

        //echo '<pre>'; print_r($results_current_location); exit;
        if (!empty($results_current_location)) {
            $var = array();
            foreach ($results_current_location as $row) {
                $var['location'][] = 'Number = ' . $row->phone_number . ' Time=' . $row->time . ' Address = ' . $row->location;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            return $var;
        } else {
            $query = "SELECT t1.person_id, t1.phone_number, t1.latitude, t1.longitude, t1.address 
                 FROM person_call_log as t1 
                 WHERE t1.person_id = {$pid} 
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.call_at DESC
                 LIMIT 5";
            $results = $DB->query(Database::SELECT, $query, TRUE);
            $var = array();
            foreach ($results as $row) {
                $var['location'][] = 'Number = ' . $row->phone_number . ' Address = ' . $row->address;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            return $var;
        }
        //$sql = DB::query(Database::SELECT, $query)->execute(); 
    }

    /* Data for advance search of Location Call Log */

    public static function get_person_call_sms_location($data, $pid)
    {
        $start_time = '';
        $end_time = '';
        $type = $data['type'];
        $person_phone = !empty($data['phonenumber']) ? $data['phonenumber'] : '';
        $other_person_phone = !empty($data['otherphone']) ? $data['otherphone'] : '';
        // $other_person_phone = $data['ophone'];

        if (!empty($data['limit'])) {
            if ($data['limit'] == 1) {
                $limit = '';
            } else {
                $limit = "LIMIT " . $data['limit'];
            }
        } else {
            $limit = '';
        }
        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $start_date = "'" . date("Y-m-d", strtotime($data['sdate'])) . ' 00:00:00' . "'";
            $enddate = "'" . date("Y-m-d", strtotime($data['edate'])) . ' 23:59:59' . "'";
        }
        if (!empty($data['starttime']) && !empty($data['endtime'])) {
            $start_time = "'" . date("H:i", strtotime($data['starttime'])) . "'";
            $end_time = "'" . date("H:i", strtotime($data['endtime'])) . "'";
        }
        $where_person_phone = "";
        $where_other_person_phone = "";
        $where_date_between = "";
        $where_time_between = "";

        if (!empty($person_phone)) {
            $where_person_phone = "and t1.phone_number = $person_phone";
        }
        if (!empty($other_person_phone)) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            //$searchsql_ophone = " and other_person_phone_number IN ( {$o_person_phone})";
            $where_other_person_phone = "and t1.other_person_phone_number IN ('{$o_person_phone}')";
        }

        $DB = Database::instance();
        if ($type == 'call') {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (call_at >= $start_date AND call_at <= $enddate)";
            }
            if (!empty($data['starttime']) && !empty($data['endtime'])) {
                $where_time_between = "and (DATE_FORMAT(call_at,'%H:%i:%s') >= $start_time AND DATE_FORMAT(call_at,'%H:%i:%s') <= $end_time)";
            }
            $query = "SELECT t1.person_id, t1.phone_number, t1.latitude, t1.longitude, t1.address 
                 FROM person_call_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 {$where_time_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.call_at DESC
                 {$limit}";
//            echo '<pre>';
//            print_r($query);
//            exit();
            $results = $DB->query(Database::SELECT, $query, TRUE);
            $var = array();
            foreach ($results as $row) {
                $var['location'][] = $row->phone_number . " | " . $row->address;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            return $var;
        } elseif ($type == 'sms') {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (sms_at >= $start_date AND sms_at <= $enddate)";
            }
            if (!empty($data['starttime']) && !empty($data['endtime'])) {
                $where_time_between = "and (DATE_FORMAT(sms_at,'%H:%i:%s') >= $start_time AND DATE_FORMAT(sms_at,'%H:%i:%s') <= $end_time)";
            }
            $query = "SELECT t1.person_id, t1.phone_number,  t1.latitude, t1.longitude, t1.address 
                 FROM person_sms_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 {$where_time_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.sms_at DESC
                 {$limit}";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, TRUE);
            $var = array();
            foreach ($results as $row) {
                $var['location'][] = $row->phone_number . " | " . $row->address;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            return $var;
        } elseif ($type == 'curloc') {

            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = " and (moved_in_at >= $start_date and moved_in_at <= $enddate) ";
            }

            $query_current_location = "SELECT address as location,t1.phone_number, t1.moved_in_at as time,t1.latitude,t1.longitude
                FROM person_location_history AS t1
                join person_phone_number as t2
                on t1.phone_number=t2.phone_number
                WHERE t2.person_id= {$pid }
                {$where_person_phone}
                {$where_date_between}    
                and t1.latitude>0 and t1.longitude>0
                ORDER BY t1.moved_in_at DESC
                {$limit}";
            // print_r($query_current_location); exit;
            $results = $DB->query(Database::SELECT, $query_current_location, TRUE);
            $var = array();
            foreach ($results as $row) {
                $var['location'][] = 'Phone Number = ' . $row->phone_number . ' Time = ' . $row->time . ' Address = ' . $row->location;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            return $var;
        } elseif ($type == 'favfive') {

            $query = "SELECT phone_number, address, latitude,longitude, count(*) as CNT 
                FROM person_call_log as t1
                WHERE t1.person_id = {$pid} 
                {$where_person_phone}
                AND t1.latitude > 0                 
                group by latitude  
                HAVING COUNT(*) > 1
                ORDER by COUNT(*) DESC
                LIMIT 5";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, TRUE);
            $var = array();
            foreach ($results as $row) {
                $var['location'][] = $row->phone_number . " | " . " Location Count = " . $row->CNT . " Address = " . $row->address;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            return $var;
        } else {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (call_at >= $start_date AND call_at <= $enddate)";
            }
            if (!empty($data['starttime']) && !empty($data['endtime'])) {
                $where_time_between = "and (DATE_FORMAT(call_at,'%H:%i:%s') >= $start_time AND DATE_FORMAT(call_at,'%H:%i:%s') <= $end_time)";
            }
            $query1 = "SELECT t1.person_id, t1.phone_number, t1.latitude, t1.longitude, t1.address 
                 FROM person_call_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 {$where_time_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.call_at DESC 
                 {$limit}";
            //print_r($query1); exit;
            $results1 = $DB->query(Database::SELECT, $query1, TRUE);
            $var = array();
            foreach ($results1 as $row) {
                $var['location'][] = $row->phone_number . " | " . $row->address;
                $var['latitude'][] = $row->latitude;
                $var['longitude'][] = $row->longitude;
            }
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (sms_at >= $start_date AND sms_at <= $enddate)";
            }
            if (!empty($data['starttime']) && !empty($data['endtime'])) {
                $where_time_between = "and (DATE_FORMAT(sms_at,'%H:%i:%s') >= $start_time AND DATE_FORMAT(sms_at,'%H:%i:%s') <= $end_time)";
            }
            $query2 = "SELECT t1.person_id, t1.phone_number,  t1.latitude, t1.longitude, t1.address 
                 FROM person_sms_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 {$where_time_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.sms_at DESC
                 {$limit}";
            $results2 = $DB->query(Database::SELECT, $query2, TRUE);
            $var1 = array();
            foreach ($results2 as $row) {
                $var1['location'][] = $row->phone_number . " | " . $row->address;
                $var1['latitude'][] = $row->latitude;
                $var1['longitude'][] = $row->longitude;
            }
            $results = array_merge($var, $var1);
            return $results;
        }
    }

    /* Data for page load of call log, export to excel */

    public static function get_person_last_five_calls_export($pid)
    {

        $DB = Database::instance();
        $sql = "SELECT *
                FROM person_location_history AS t1
                join person_phone_number as t2
                on t1.phone_number=t2.phone_number
                WHERE t2.person_id= $pid 
                and t1.latitude>0 and t1.longitude>0
                ORDER BY t1.moved_in_at DESC
                Limit 5";
        $results = DB::query(Database::SELECT, $sql)->execute()->as_array();
        if (!empty($results)) {
            return $results;
        } else {
            $query = "SELECT * 
                 FROM person_call_log as t1 
                 WHERE t1.person_id = {$pid} 
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.call_at DESC
                 LIMIT 5";
            // print_r($query); exit;
            $results = DB::query(Database::SELECT, $sql)->as_object()->execute();
            return $results;
        }
    }

    /* Data for advance search of Location Call Log */

    public static function get_person_call_sms_location_export($data, $pid)
    {

        $type = $data['type'];
        $person_phone = !empty($data['phonenumber']) ? $data['phonenumber'] : '';
        $other_person_phone = !empty($data['otherphone']) ? $data['otherphone'] : '';
        // $other_person_phone = $data['ophone'];

        if (!empty($data['limit'])) {
            $limit = "LIMIT " . $data['limit'];
        } else {
            $limit = '';
        }

        if (!empty($data['sdate']) && !empty($data['edate'])) {
            $start_date = "'" . date("Y-m-d H:i:s", strtotime($data['sdate'])) . "'";
            $enddate = "'" . date("Y-m-d H:i:s", strtotime($data['edate'])) . "'";
        }
        //rule
        $where_person_phone = "";
        $where_other_person_phone = "";
        $where_date_between = "";

        if (!empty($person_phone)) {
            $where_person_phone = "and t1.phone_number = $person_phone";
        }
        if (!empty($other_person_phone)) {
            $o_person_phone = implode("' , '", $data['otherphone']);
            //$searchsql_ophone = " and other_person_phone_number IN ( {$o_person_phone})";
            $where_other_person_phone = "and t1.other_person_phone_number IN ('{$o_person_phone}')";
        }

        $DB = Database::instance();
        if ($type == 'call') {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (call_at >= $start_date AND call_at <= $enddate)";
            }
            $query = "SELECT *
                 FROM person_call_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.call_at DESC
                 {$limit}";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, FALSE);
            return $results;
        } elseif ($type == 'sms') {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (sms_at >= $start_date AND sms_at <= $enddate)";
            }
            $query = "SELECT *
                 FROM person_sms_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.sms_at DESC
                 {$limit}";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, FALSE);
            return $results;
        } elseif ($type == 'favfive') {

            $query = "SELECT *,count(*) as CNT
                FROM person_call_log as t1
                WHERE t1.person_id = {$pid} 
                {$where_person_phone}
                AND t1.latitude > 0                 
                group by latitude  
                HAVING COUNT(*) > 1
                ORDER by COUNT(*) DESC
                LIMIT 5";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, FALSE);
            return $results;
        } elseif ($type == 'curloc') {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = " and (moved_in_at >= $start_date and moved_in_at <= $enddate) ";
            }

            $query = "SELECT *
                FROM person_location_history as t1
                WHERE t1.person_id = {$pid} 
                {$where_person_phone}
                {$where_date_between}
                AND t1.latitude > 0                 
                group by latitude  
                ORDER by latitude DESC
                {$limit}";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, FALSE);
            return $results;
        } else {
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (call_at >= $start_date AND call_at <= $enddate)";
            }
            $query1 = "SELECT *
                 FROM person_call_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.call_at DESC 
                 {$limit}";
            //print_r($query1); exit;
            $results1 = $DB->query(Database::SELECT, $query1, FALSE)->as_array();
            if (!empty($data['sdate']) && !empty($data['edate'])) {
                $where_date_between = "and (sms_at >= $start_date AND sms_at <= $enddate)";
            }
            $query2 = "SELECT *
                 FROM person_sms_log as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_person_phone}
                 {$where_other_person_phone}
                 {$where_date_between}
                 AND t1.latitude > 0
                 GROUP BY latitude
                 ORDER by t1.sms_at DESC
                 {$limit}";
            $results2 = $DB->query(Database::SELECT, $query2, FALSE)->as_array();
            $results = array_merge($results1, $results2);
            return $results;
        }
    }


    /* Person DB Match for One page performa */

    public static function get_person_dbmatch($pid)
    {
        $DB = Database::instance();
        $query = "SELECT 
            (select person_id from person_phone_number as ppn where ppn.phone_number = ps.other_person_phone_number  limit 1) as other_id,
                ps.other_person_phone_number, 
                (SUM(ps.calls_made_count) + SUM(ps.calls_received_count)) as calls , 
                (SUM(ps.sms_sent_count) + SUM(ps.sms_received_count)) as sms 
                 FROM person_summary ps 
                 WHERE ps.person_id={$pid}
                 and (select person_id from person_phone_number as ppn where ppn.phone_number = ps.other_person_phone_number limit 1) is NOT NULL 
                 group by ps.other_person_phone_number;";
        // print_r($query); exit;
        //$results = $DB->query(Database::SELECT, $query, TRUE);

        $results = DB::query(Database::SELECT, $query)->execute();
        return $results;
    }

    /* CDR GRAPHIC VIEW DATA ON PAGE LOAD */

    public static function get_person_recent_five_calls($pid)
    {

        $DB = Database::instance();
        $query = "SELECT *
                 FROM person_summary as t1 
                 WHERE t1.person_id = {$pid}
                 AND t1.calls_made_count > 0
                 ORDER by t1.last_update DESC
                 LIMIT 5";
        // print_r($query); exit;
        $results = $DB->query(Database::SELECT, $query, TRUE);
        //$sql = DB::query(Database::SELECT, $query)->execute(); 
        $var = array();
        foreach ($results as $row) {
            $var['phone'][] = $row->phone_number;
            $var['ophone'][] = $row->other_person_phone_number;
            $var['calls_made'][] = $row->calls_made_count;
            $var['calls_received'][] = $row->calls_received_count;
            $var['sms_sent'][] = $row->sms_sent_count;
            $var['sms_received'][] = $row->sms_received_count;
        }
        // echo '<pre>';
        // print_r($var); exit;
        return $var;
    }

    /* CDR GRAPHIC VIEW DATA ON Advance Search */

    public static function get_person_cdr_graphic_calls($data, $pid)
    {
        $where_phone_clause = "";
        $where_ophone_clause = "";
        $person_phone = $data['phone'];
        if (!empty($data['ophone'])) {
            $o_person_phone = implode("' , '", $data['ophone']);
        }
        if (!empty($person_phone)) {
            $where_phone_clause = " AND t1.phone_number = $person_phone";
        }
        $var = array();
        if (!empty($o_person_phone)) {
            $where_ophone_clause = " AND t1.other_person_phone_number IN ('{$o_person_phone}')";
            //print_r($where_ophone_clause); exit;
        }
        if ($data['type'] == 'favfive') {
            $DB = Database::instance();
            $query = "SELECT * , (calls_made_count + calls_received_count) as calls
                 FROM person_summary as t1 
                 WHERE t1.person_id = {$pid}
                 {$where_phone_clause}
                 order by calls DESC,t1.last_update DESC
                 limit 5";
            // print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, TRUE);
        } else if ($data['type'] == 'linked1') {
            $DB = Database::instance();
            $query = "select * from person_summary as t1
                      where 1
                      {$where_phone_clause}";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, TRUE);
            $link = array();
            $query1 = "select distinct(phone_number) from person_summary";
            $phone_numbers = $DB->query(Database::SELECT, $query1, TRUE)->as_array();
            foreach ($results as $r) {
                for ($i = 0; $i < sizeof($phone_numbers); $i++) {
                    //print_r($phone_numbers[$i]->phone_number);                    echo '<br>';
                    if ($r->other_person_phone_number == $phone_numbers[$i]->phone_number) {
                        $link[] = $r;
                        foreach ($link as $l) {
                            $query = "select * from person_summary as t1 where t1.phone_number = $l->other_person_phone_number";
                            $results1 = $DB->query(Database::SELECT, $query, TRUE);
                            foreach ($results1 as $r1) {
                                for ($i = 0; $i < sizeof($phone_numbers); $i++) {
                                    if ($r1->other_person_phone_number == $phone_numbers[$i]->phone_number) {
                                        if ($l->phone_number == $r1->other_person_phone_number) {

                                        } else {
                                            $link[] = $r1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $results = $link;
        } else if ($data['type'] == 'linked') {
            $DB = Database::instance();
            $query = "select * from person_summary as t1
                      where 1
                      {$where_phone_clause}";
            //print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, TRUE);
            $link = array();
            foreach ($results as $r) {
                $query = "select * from person_summary as t1
                      where t1.phone_number = '$r->other_person_phone_number'";
                $first_record = $DB->query(Database::SELECT, $query, TRUE)->as_array();
                if (!empty($first_record)) {
                    if (in_array($r->phone_number, array_column($first_record, 'phone_number')) && in_array($r->other_person_phone_number, array_column($first_record, 'other_person_phone_number'))) {

                    } else {
                        $link[] = $r;
                    }
                    foreach ($first_record as $r1) {
                        $query = "select * from person_summary as t1
                                  where t1.phone_number = '$r1->other_person_phone_number'";
                        $second_record = $DB->query(Database::SELECT, $query, TRUE)->as_array();
                        //mil gayaprint_r($second_record); exit;
                        if (!empty($second_record)) {
                            if ((in_array($r->phone_number, array_column($second_record, 'phone_number')) && in_array($r->other_person_phone_number, array_column($second_record, 'other_person_phone_number'))) || in_array($r1->phone_number, array_column($second_record, 'phone_number')) && in_array($r1->other_person_phone_number, array_column($second_record, 'other_person_phone_number'))) {

                            } else {
                                $link[] = $r1;
                            }

                            foreach ($second_record as $r2) {
                                $query = "select * from person_summary as t1
                                            where t1.phone_number = '$r2->other_person_phone_number'";
                                $third_record = $DB->query(Database::SELECT, $query, TRUE)->as_array();
                                if (!empty($third_record)) {
                                    if ((in_array($r->phone_number, array_column($third_record, 'phone_number')) && in_array($r->other_person_phone_number, array_column($third_record, 'other_person_phone_number'))) || (in_array($r1->phone_number, array_column($third_record, 'phone_number')) && in_array($r1->other_person_phone_number, array_column($third_record, 'other_person_phone_number'))) || in_array($r2->phone_number, array_column($third_record, 'phone_number')) && in_array($r2->other_person_phone_number, array_column($third_record, 'other_person_phone_number'))) {

                                    } else {
                                        $link[] = $r2;
                                    }
                                    foreach ($third_record as $r3) {
                                        $query = "select * from person_summary as t1
                                            where t1.phone_number = '$r3->other_person_phone_number'";
                                        $forth_record = $DB->query(Database::SELECT, $query, TRUE)->as_array();
                                        if (!empty($forth_record)) {
                                            if ((in_array($r->phone_number, array_column($forth_record, 'phone_number')) && in_array($r->other_person_phone_number, array_column($forth_record, 'other_person_phone_number'))) || (in_array($r1->phone_number, array_column($forth_record, 'phone_number')) && in_array($r1->other_person_phone_number, array_column($forth_record, 'other_person_phone_number'))) || (in_array($r2->phone_number, array_column($forth_record, 'phone_number')) && in_array($r2->other_person_phone_number, array_column($forth_record, 'other_person_phone_number'))) || (in_array($r2->phone_number, array_column($forth_record, 'phone_number')) && in_array($r2->other_person_phone_number, array_column($forth_record, 'other_person_phone_number'))) || in_array($r3->phone_number, array_column($forth_record, 'phone_number')) && in_array($r3->other_person_phone_number, array_column($forth_record, 'other_person_phone_number'))) {

                                            } else {
                                                $link[] = $r3;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $results = array_map("unserialize", array_unique(array_map("serialize", $link)));
        } else {
            $DB = Database::instance();
            $query = "SELECT *
                 FROM person_summary as t1 
                 WHERE t1.person_id = {$pid} 
                 {$where_phone_clause}
                 {$where_ophone_clause}
                 ORDER by t1.last_update DESC";
            // print_r($query); exit;
            $results = $DB->query(Database::SELECT, $query, TRUE);
        }
        $var = array();
        foreach ($results as $row) {
            $var['phone'][] = $row->phone_number;
            $var['ophone'][] = $row->other_person_phone_number;
            $var['calls_made'][] = $row->calls_made_count;
            $var['calls_received'][] = $row->calls_received_count;
            $var['sms_sent'][] = $row->sms_sent_count;
            $var['sms_received'][] = $row->sms_received_count;
        }
//         echo '<pre>';
//         print_r($var); exit;
        return $var;
    }

    /* CDR GRAPHIC VIEW DATA ON PAGE LOAD */

    public static function person_current_location_history($person_id)
    {

        $location = Helpers_Utilities::get_person_location_history($person_id);
        if (!empty($location)) {
            foreach ($location as $loc) {
                ?>
                <div class="col-md-12">
                    <span><strong>Mobile Number: </strong></span><span><?php echo $loc->phone_number; ?></span>
                </div>
                <div class="col-md-12">
                    <span><strong>Company Name: </strong></span><span><?php if (!empty($loc->mnc)) {
                            $comname = Helpers_Utilities::get_companies_data($loc->mnc);
                            echo $comname->company_name;
                        } else {
                            echo "Unknown";
                        } ?></span>
                    <span><strong>Network: </strong></span><span><?php echo Helpers_Utilities::get_network_status($loc->network); ?></span>
                </div>
                <div class="col-md-12">
                    <span><strong>LAC ID: </strong></span><span><?php echo !empty($loc->lac_id) ? (int)$loc->lac_id : 'N/A'; ?></span>

                    <span><strong>CELL ID: </strong></span><span><?php echo !empty($loc->cell_id) ? (int)$loc->cell_id : 'N/A'; ?></span>
                </div>
                <div class="col-md-12">
                    <span><strong>LAT: </strong></span><span><?php echo $loc->latitude; ?></span>

                    <span><strong>LONG: </strong></span><span><?php echo $loc->longitude; ?></span>
                </div>
                <div class="col-md-12">
                    <span><strong>Address: </strong></span><span><?php echo $loc->address; ?></span>
                </div>
                <div class="col-md-12">
                    <span><strong>Location Time: </strong></span><span><?php echo $loc->moved_in_at; ?></span>
                </div>
                <div class="col-md-12">
                    <span><strong>Current Status: </strong></span><span><?php echo Helpers_Utilities::get_connection_status($loc->status); ?></span>
                </div>
                <div class="col-md-12">
                    <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px">
                </div>
                <?php
            }
        } else {
            ?>
            <img id="nodata" style="width: 534px; margin: auto" class="img-responsive"
                 src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
            <?php
        }
    }

    /* Add person tags data   */

    public static function add_person_tags($data)
    {
        $user_obj = Auth::instance()->get_user();
        $login_user_id = $user_obj->id;
        $person_id = $_POST['person_id'];
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user_id);
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
        $user_district_id = $result[1];
        $date = date('Y-m-d H:i:s');

        $person_tags = Helpers_Watchlist::get_person_tags($person_id);
        $active_tags = array_column($person_tags, 'tag_id');
        // if ($result[0] == 'd') {
        //print_r($data); 
        if (isset($data['category_type'])) {
            $temp_tags_data = '(';
            $count = 0;
            foreach ($data['category_type'] as $key => $value) {
                if (in_array($key, $active_tags)) {

                } else {
                    $query = DB::insert('person_tags', array('person_id', 'tag_id', 'tag_district_id', 'user_id', 'added_on'))
                        ->values(array($person_id, $key, $user_district_id, $login_user_id, $date))
                        ->execute();
                }
                if ($count == 0) {
                    $temp_tags_data = $temp_tags_data . $key;
                    $count = $count + 1;
                } else {
                    $temp_tags_data = $temp_tags_data . ',' . $key;
                }
            }
            $temp_tags_data = $temp_tags_data . ')';
        }
        //print_r($temp_tags_data); exit;
        $DB = Database::instance();
        $query = "Delete from person_tags where person_id = {$person_id} ";
        if (isset($data['category_type'])) {
            $query .= "and tag_id not in {$temp_tags_data}";
        }
        //print_r($query); exit;
        $results = DB::query(Database::DELETE, $query)->execute();
        if (isset($data['category_type'])) {
            $temp_tags_data = str_replace(")", ' ', $temp_tags_data);
            $temp_tags_data = str_replace("(", ' ', $temp_tags_data);
        } else {
            $temp_tags_data = '';
        }
        $active_tags = implode(",", $active_tags);
        //Tag change Activity Log
        $uid = $user_obj->id;
        Helpers_Profile::user_activity_log($uid, 75, $active_tags, $temp_tags_data, $person_id);
        //}
    }

    /* sms log summary */

    public static function user_activity_log($data, $count, $pid)
    {
        //print_r($data); exit;       
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
        $query_clause='';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }

        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);

            $DB = Database::instance();
            $sql = "SELECT  id FROM  lu_user_activity_type
                                    WHERE label like '%{$data['sSearch']}%'";
            $activity_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $activity_array = implode(', ', array_values(array_column($activity_id, 'id')));
            $query_clause = " join user_activity_timeline_detail as uat on uat.timeline_id=t1.timeline_id " ;
                      
            if (!empty($activity_array))
                $search = "and ( uat.key_value like '%{$data['sSearch']}%' and (t1.person_id = {$pid} or t1.person_id =null))";
            else {
                $search = "and (uat.key_value like '%{$data['sSearch']}%' and (t1.person_id = {$pid} or t1.person_id =null) )";
            }
            
//            
//              if (!empty($activity_array))
//                $search = "and t1.user_activity_type_id in ({$activity_array})";
//            else {
//                $search = "and t1.user_activity_type_id = null";
//            }
         
        } else {
            $search = "    and (t1.person_id = {$pid}) ";
        }

        /* Group By */
        $groupby = "group by u1.timeline_id";

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = "where 0";
        if ($permission == 1 || $permission == 2 || $permission == 5) {
            $where_clause = "where 1";
        }
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select  COUNT(*) AS count
                    from user_activity_timeline as t1 
                    join users_profile as t2 on t2.user_id=t1.user_id                    
                    {$query_clause}
                    {$where_clause}
                    and t1.user_activity_type_id NOT IN (9,27,28)
                
                    {$search}";

            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select t2.user_id, t2.region_id, t2.posted, t2.job_title, t1.user_activity_type_id, t1.person_id, t1.activity_time,t1.timeline_id 
                    from user_activity_timeline as t1 
                    join users_profile as t2 on t2.user_id=t1.user_id 
                    {$query_clause}
                    {$where_clause}
                    and t1.user_activity_type_id NOT IN (9,27,28) 
                              
                    {$search}
                    {$order_by}
                    {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }


    /*branchless_transactions*/

    public static function branchless_transactions($data, $count, $pid)
    {
        $searchsql = '';
        $searchsql_phone = '';
        $searchsql_ophone = '';
        if (!empty($data['phone_number'])) {
            $searchsql_phone = " and phone_number = {$data['phone_number']}";
        }
        $searchsql_companiesID = '';
        if (!empty($data['branchlesstransaction'])) {
            $companies_ids = implode("' , '", $data['branchlesstransaction']);
            $searchsql_companiesID = " and t2.id IN ('{$companies_ids}')";
        }
        if (empty($data['enddate'])) {
            $data['enddate'] = date("Y-m-d");
        }
        if (!empty($data['startdate'])) {
            $start_date = date("Y-m-d", strtotime($data['startdate']));
            $end_date = date("Y-m-d", strtotime($data['enddate']));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $searchsql = " and sms_at between '{$start_date}' and '{$end_date}' ";
        }

        /* Sorted Data */
        $order_by_param = "phone_number";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "other_person_phone_number";
                    break;
                case "2":
                    $order_by_param = "is_outgoing";
                    break;
                case "3":
                    $order_by_param = "sms_at";
                    break;
                case "4":
                    $order_by_param = "address";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        /* Starting and Ending Lenght (size) */
        $limit = '';
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\_\|\-\. ]/', '', $data['sSearch']);
            $search = "and ( other_person_phone_number like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count from person_sms_log as t1
                        join lu_branchless_transactions as t2 on ( t2.transaction_code = t1.other_person_phone_number or t2.transaction_code_countrycode = t1.other_person_phone_number)
                        where person_id = $pid
                        {$search}
                        {$searchsql}
                        {$searchsql_phone}
                        {$searchsql_companiesID}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        } /*  Fetch all Records */ else {
            $sql = "Select * from person_sms_log as t1
                            join lu_branchless_transactions as t2 on ( t2.transaction_code = t1.other_person_phone_number or t2.transaction_code_countrycode = t1.other_person_phone_number)
                            where person_id = $pid
                            {$search}
                            {$searchsql}                            
                            {$searchsql_phone}                            
                            {$searchsql_companiesID}                            
                            {$order_by} 
                            {$limit}";
            //print_r($sql);exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

}

?>