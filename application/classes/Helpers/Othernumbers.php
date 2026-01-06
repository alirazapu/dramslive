<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Profile helper class. It will contain all the Helper Functions realted to Profile
 *
 * @package    Profile Helper
 * @category   Helpers
 */
abstract class Helpers_Othernumbers {
    //    Number Exist in other_numbers table
    public static function check_number_exist($number) {
        $DB = Database::instance();
        $sql = "SELECT count(*) as count FROM other_numbers as t1 where t1.phone_number = {$number}";        
        $results = DB::query(Database::SELECT, $sql)->execute()->current();
        return $results['count'];
    }
    //    check request against number
    public static function check_request_against_number($number) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as count from user_request as t1 WHERE t1.requested_value = {$number}";
        $results = DB::query(Database::SELECT, $sql)->execute()->current();
        return $results['count'];
    }
        //Request data against requested value 
    public static function request_details($requested_value) {
        $DB = Database::instance();
        $sql = "Select * from user_request AS t1 where t1.requested_value = {$requested_value}";
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
    }
}

?>
