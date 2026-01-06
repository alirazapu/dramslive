<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Profile helper class. It will contain all the Helper Functions realted to Profile
 *
 * @package    Profile Helper
 * @category   Helpers
 */
abstract class Helpers_Adminreports {
    public static function get_identity_count($identity_id) {
        $DB = Database::instance();
        $sql = "SELECT count(*) as count FROM person_identities as t1 where t1.identity_id = {$identity_id}";
        $results = DB::query(Database::SELECT, $sql)->execute()->current();
        //$results = $DB->query(Database::SELECT, $sql, TRUE);
        //print_r($results); exit;
        return $results['count'];
    }
    //    Identity Breakup report, Foreigner Person Count
    public static function get_foreigner_cnic_count() {
        $DB = Database::instance();
        $sql = "SELECT count(*) as count FROM person_initiate as t1 where t1.is_foreigner = 1";
        $results = DB::query(Database::SELECT, $sql)->execute()->current();
        //$results = $DB->query(Database::SELECT, $sql, TRUE);        
        //print_r($results); exit;
        return $results['count'];
    }
    //    Identity Breakup report, Foreigner Person Count
    public static function get_total_person_count() {
        $DB = Database::instance();
        $sql = "SELECT count(*) as count FROM person_initiate as t1";
        $results = DB::query(Database::SELECT, $sql)->execute()->current();
        //$results = $DB->query(Database::SELECT, $sql, TRUE);        
        //print_r($results); exit;
        return $results['count'];
    }
    
        /*    Helper to get data of pending NADRA verisys requests */
    public static function get_pending_nadravarisys_data() {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.request_id) as requests, t3.region_id as region_id
                FROM user_request as t1  
                join users_profile as t3 on t1.user_id=t3.user_id 
                where t1.user_request_type_id =8 
                and t1.status=1  
                GROUP by t3.region_id";          
        //print_r($sql); exit;
        $results = DB::query(Database::SELECT, $sql, FALSE)->execute()->as_array();
        //$results = $DB->query(Database::SELECT, $sql, TRUE)->as_object();
        return $results;
    }
        /*    Helper to get data of pending NADRA verisys requests */
    //$response_status    0 = All requests  1 = responded requests
    //$type   =  0 = region 1 = district 2 = police station
    public static function get_verisys_response_breakup($type, $value, $date,$response_status) { 
        $date1 = $date . " 00:00:00";
        $date2 = $date . " 23:59:59";              
        $DB = Database::instance();
        $status_clause = " ";
        $posting = " ";
        switch ($type){
            case 0:
                $posting = "r-".$value;
                break;
            case 1:
                $posting = "d-".$value;
                break;
            case 2:
                $posting = "2-".$value;
                break;
        }
        if ($response_status == 1) {
            $status_clause = " and t1.status = 2";
        }
        $sql = "SELECT COUNT(t1.request_id) as count
                FROM user_request as t1  
                join users_profile as t3 on t1.user_id=t3.user_id 
                where t1.user_request_type_id =8 
                {$status_clause}
                and t3.posted = '{$posting}'
                and (t1.created_at >= '{$date1}' and t1.created_at <= '{$date2}' )";          
       // print_r($sql); exit;
        $results = DB::query(Database::SELECT, $sql, FALSE)->execute()->current();
        $value = isset($results['count']) ? $results['count'] : 0;
        return $value;
    }   

}

?>
