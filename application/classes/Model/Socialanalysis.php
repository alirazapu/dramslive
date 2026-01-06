<?php

defined('SYSPATH') or die('No direct script access.');

class Model_Socialanalysis {
    /* for social analysis */

    public static function social_analysis_table($data, $count, $pid) {

        /* Sorted Data */
        $order_by_param = "t1.time_stamp";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.website_name";
                    break;
                case "1":
                    $order_by_param = "t1.person_sw_id";
                    break;
                case "2":
                    $order_by_param = "t1.sw_profile_link";
                    break;
                case "3":
                    $order_by_param = "t1.suggested_by";
                    break;
                case "4":
                    $order_by_param = "t1.authenticity";
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
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( t2.website_name like '%{$data['sSearch']}%'
                        or t1.person_sw_id like '%{$data['sSearch']}%'  
                        )";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select COUNT(*) AS count 
                from person_social_links as t1
                    inner join social_websites as t2 on t1.sw_type_id=t2.id
                            where person_id = {$pid} AND t1.is_deleted=0
                        {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "Select t1.id as record_id,t1.person_sw_id,t1.sw_profile_link as profile_link,t1.phone_number,t1.authenticity,t1.suggested_by,t1.time_stamp,t2.id as website_id,t2.website_name,t2.website_logo 
                from person_social_links as t1
                    inner join social_websites as t2 on t1.sw_type_id=t2.id
                            where t1.person_id = {$pid} AND t1.is_deleted=0
                            {$search}
                            {$order_by} 
                            {$limit}";
            // echo $sql;  
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

//Delete social analysis link
    public static function delete_record_link($rec, $uid) {
        date_default_timezone_set("Asia/Karachi");
        $newDate = date('Y-m-d H:i:s', time());
        $query = DB::update('person_social_links')
                ->set(array('is_deleted' => '1', 'updated_by' => $uid, 'time_stamp' => $newDate))
                ->where('id', '=', $rec)
                ->execute();
        
        $DB = Database::instance();        
        $query1 = "select person_id from person_social_links where id = $rec";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        
        $login_user_id = Auth::instance()->get_user();
        $uid = $login_user_id->id;
        Helpers_Profile::user_activity_log($uid, 67, NULL, NULL, $results->person_id);
        return $query;
    }

//approve social analysis link
    public static function approve_record($rec, $uid) {
        date_default_timezone_set("Asia/Karachi");
        $newDate = date('Y-m-d H:i:s', time());
        $query = DB::update('person_social_links')
                ->set(array('authenticity' => '1', 'updated_by' => $uid, 'time_stamp' => $newDate))
                ->where('id', '=', $rec)
                ->execute();
        
        $DB = Database::instance();        
        $query1 = "select person_id from person_social_links where id = $rec";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        
        $login_user_id = Auth::instance()->get_user();
        $uid = $login_user_id->id;
        Helpers_Profile::user_activity_log($uid, 68, NULL, NULL, $results->person_id);
        return $query;
    }

    /* get single record data */

    public static function getlinkrecord($recid) {
        $DB = Database::instance();
        $query = "Select*
                from person_social_links as t1
                            where t1.id = $recid AND t1.is_deleted=0";
        $sql = DB::query(Database::SELECT, $query);
        $results = $DB->query(Database::SELECT, $sql, FALSE)->current();
        return $results;
    }

    /* view social link details */

    public static function view_link_details($recid) {
        $DB = Database::instance();
        $query = "Select t1.id as record_id,t1.file_link,t1.information,t1.person_sw_id,t1.sw_profile_link as profile_link,t1.phone_number,t1.authenticity,t1.suggested_by,t1.time_stamp,t2.id as website_id,t2.website_name,t2.website_logo,t2.website_image 
                from person_social_links as t1
                    inner join social_websites as t2 on t1.sw_type_id=t2.id
                            where t1.id = $recid AND t1.is_deleted=0";
        $sql = DB::query(Database::SELECT, $query);
        $results = $DB->query(Database::SELECT, $sql, FALSE)->current();
        return $results;
    }

    /* get social websites name list */

    public static function get_social_website_list() {
        $query = "SELECT id,website_name,website_logo
                    FROM social_websites ";
        $sql = DB::query(Database::SELECT, $query);

        $result = $sql->execute();

        return $result;
    }

    /* inset new social link */

    public static function sociallink_insert($data) {
        date_default_timezone_set("Asia/Karachi");
        $newDate = date('Y-m-d H:i:s', time());
        if (!empty($data['phone_number'])) {
            $is_mobile = 1;
        } else {
            $is_mobile = 0;
        }
        $query = DB::insert('person_social_links', array('id', 'person_id', 'sw_type_id', 'person_sw_id', 'sw_profile_link', 'is_sw_id_against_mobile', 'phone_number', 'information', 'file_link', 'suggested_by', 'authenticity', 'is_deleted', 'updated_by', 'time_stamp'))
                ->values(array('', $data['person_id'], $data['socialwebsite'], $data['person_sw_id'], $data['sw_profile_link'], $is_mobile, $data['phone_number'], $data['information'], $data['file_link'], $data['user_id'], 0, 0, '', $newDate))
                ->execute();
        $login_user_id = Auth::instance()->get_user();
        $uid = $login_user_id->id;
        Helpers_Profile::user_activity_log($uid, 65, NULL, NULL, $data['person_id']);
    }

    /* update existing social link */

    public static function sociallink_update($data) {
        date_default_timezone_set("Asia/Karachi");
        $newDate = date('Y-m-d H:i:s', time());
        if (!empty($data['phone_number'])) {
            $is_mobile = 1;
        } else {
            $is_mobile = 0;
        }

        $query = DB::update('person_social_links');
        if (!empty($data['person_sw_id']))
            $query->set(array('person_sw_id' => $data['person_sw_id']));

        if (!empty($data['sw_profile_link']))
            $query->set(array('sw_profile_link' => $data['sw_profile_link']));

        if (!empty($data['phone_number']))
            $query->set(array('is_sw_id_against_mobile' => $is_mobile));

        if (!empty($data['phone_number']))
            $query->set(array('phone_number' => $data['phone_number']));

        if (!empty($data['information']))
            $query->set(array('information' => $data['information']));

        if (!empty($data['file_link']))
            $query->set(array('file_link' => $data['file_link']));

        if (!empty($data['user_id']))
            $query->set(array('updated_by' => $data['user_id']));

        if (!empty($newDate))
            $query->set(array('time_stamp' => $newDate));

        $query->where('id', '=', $data['record_id'])
                ->execute();
        $login_user_id = Auth::instance()->get_user();
        $uid = $login_user_id->id;
        Helpers_Profile::user_activity_log($uid, 66, NULL, NULL, $data['person_id']);
    }

}

?>