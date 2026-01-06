<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_Othernumber {

    public static function bulk_search($data) {

        $DB = Database::instance();
                $sql = "SELECT 
            t1.other_person_phone_number AS Aparty,
            t1.phone_number AS Bparty,
            CONCAT(p.first_name, ' ', p.last_name) AS PersonName,
            p.father_name AS FatherName,
            pi.cnic_number AS PersonCNIC,
            CASE 
                WHEN pc.category_id = 0 THEN 'White'
                WHEN pc.category_id = 1 THEN 'Grey'
                WHEN pc.category_id = 2 THEN 'Black'
                ELSE 'Unknown'
            END AS Category,
            GROUP_CONCAT(DISTINCT lt.tag_name ORDER BY lt.tag_name SEPARATOR ', ') AS Tags,
            SUM(t1.calls_made_count) AS CallMade,
            SUM(t1.calls_received_count) AS CallReceived,
            SUM(t1.sms_sent_count) AS SMSMade,
            SUM(t1.sms_received_count) AS SmsReceived,
            (
                SUM(t1.calls_made_count) +
                SUM(t1.calls_received_count) +
                SUM(t1.sms_sent_count) +
                SUM(t1.sms_received_count)
            ) AS TotalLogs,
            (
                SELECT pcl.call_at 
                FROM person_call_log pcl
                WHERE pcl.other_person_phone_number = t1.other_person_phone_number 
                  AND pcl.phone_number = t1.phone_number 
                ORDER BY pcl.call_at DESC 
                LIMIT 1
            ) AS LastCall,
            (
                SELECT psl.sms_at 
                FROM person_sms_log psl
                WHERE psl.other_person_phone_number = t1.other_person_phone_number 
                  AND psl.phone_number = t1.phone_number 
                ORDER BY psl.sms_at DESC 
                LIMIT 1
            ) AS SmsCall
        FROM 
            person_summary AS t1
        LEFT JOIN person AS p ON p.person_id = t1.person_id
        LEFT JOIN person_initiate AS pi ON pi.person_id = t1.person_id
        LEFT JOIN person_category AS pc ON pc.person_id = t1.person_id
        LEFT JOIN person_tags AS pt ON pt.person_id = t1.person_id
        LEFT JOIN lu_tags AS lt ON lt.id = pt.tag_id
        WHERE 
            t1.other_person_phone_number IN (
                {$data}
            )
        GROUP BY 
            t1.person_id, t1.other_person_phone_number, t1.phone_number
        ORDER BY 
            TotalLogs DESC" ;
            //$members = $DB->query(Database::SELECT, $sql, False);    
            
           $members = DB::query(Database::SELECT, $sql)->execute()->as_array();
            return $members;
        
    }
    public static function othernumber_search($data, $count) {
//        echo '<pre>';
//        print_r($data); exit;
        //advance search section
        $number_query = '';
        $requested_number = (!empty($data['ptclnumber']) ? $data['ptclnumber'] :
                        (!empty($data['internationalnumber']) ? $data['internationalnumber'] : ''));

        if (isset($requested_number)) {
            $number_query = " where t1.phone_number = {$requested_number}";
        } else {
            $number_query = '';
        }
        //Other Number Search activity log
        if ($count == 'false') {

            if ($data['number_type'] == 1) {
                $key = 'PTCL Number';
            } else {
                $key = 'International Number';
            }
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 83, $key, $requested_number);
        }
        /* Sorted Data */
        $order_by_param = "t1.firsst_name";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.first_name";
                    break;
                case "1":
                    $order_by_param = "t1.father_name";
                    break;
                case "3":
                    $order_by_param = "t2.phone_number";
                    break;
                case "4":
                    $order_by_param = "t3.imei_number";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
            $search = "";
        } else {
            $search = "";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.person_id";

        $DB = Database::instance();

        if ($count == 'true') {
            $sql = "Select count(*) from other_numbers as t1"
                    . "$number_query";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members->count();
        } else { /*  Fetch all Records */

            $sql = "Select * from other_numbers AS t1"
                    . "$number_query";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update entry into other numbers table
    public static function update_other_numbers($data) {

        $requested_value = (!empty($_POST['inputPTCLNO']) ? $_POST['inputPTCLNO'] :
                        (!empty($_POST['inputInternationalNo']) ? $_POST['inputInternationalNo'] : ''));

        $requested_company = (!empty($_POST['company_name_get'][0]) ? $_POST['company_name_get'][0] : '');
        $number_exist = Helpers_Othernumbers::check_number_exist($requested_value);
        //echo '<pre>'; print_r($_POST); exit;
        if ($number_exist == 0) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $login_user_id = $login_user->id;
            $query = 'INSERT INTO other_numbers (`phone_number`, `mnc`, `user_id`) VALUES (' . $requested_value . ',' . $requested_company . ',' . $login_user_id . ')';
            $sql = DB::query(Database::INSERT, $query)->execute();
        }
    }

    public static function affiliate_number($number, $person_id) {
        $DB = Database::instance();
        $query = DB::update('other_numbers')->set(array('person_id' => $person_id))
                ->where('phone_number', '=', $number)
                ->execute();
        return $query;
    }
    public static function add_other_number($number, $p_id, $mnc) {
        $DB = Database::instance();
        $query = DB::insert('other_numbers', array('person_id', 'phone_number', 'mnc'))
                ->values(array('person_id' => $p_id ,'phone_number' => $number ,'mnc' => $mnc ))                
                ->execute();
        return 1;
    }

}
