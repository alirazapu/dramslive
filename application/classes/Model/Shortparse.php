<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with Parseing 
 */
class Model_Shortparse {
    /*
     * Get Person id from phone number 
     */
    public static function updateorinsert_person_phone_number_withlastuse($party_a, $imsi_number, $from, $mnc, $status, $user_id){
        $DB = Database::instance();
        $sql = "select * from person_phone_number WHERE phone_number = {$party_a} sim_last_used_at < '{$from}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(!empty($results))
        {
            $query = DB::update('person_phone_number')->set(array('sim_last_used_at' => $from))                    
                    ->where('sim_owner', '=', $results->sim_owner)
                    ->where('person_id', '=', $results->person_id)
                    ->where('phone_number', '=', $party_a)
                    ->execute();  
        }
    }
    public static function updateorinsert_person_phone_number_withfirstuse($party_a, $imsi_number, $from, $mnc, $status, $user_id){
        $DB = Database::instance();
        $sql = "select * from person_phone_number WHERE phone_number = {$party_a}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        
        if(!empty($results))
        {
            $sql = "select * from person_phone_number WHERE phone_number = {$party_a} and (sim_last_used_at < '{$from}' or sim_last_used_at IS NULL)";
            $results_1 = DB::query(Database::SELECT, $sql)->as_object()->execute()->current();
           if(isset($results_1->sim_owner))
            { 
            $query = DB::update('person_phone_number')->set(array('status' => $status, 'mnc' => $mnc, 'sim_last_used_at'=>$from))
                    ->where('sim_owner', '=', $results_1->sim_owner)
                    ->and_where('person_id', '=', $results_1->person_id)
                    ->and_where('phone_number', '=', $party_a)
                    ->execute();   
            }
            if(!empty($imsi_number))
            {
                $query = DB::update('person_phone_number')->set(array('imsi_number' => $imsi_number))                        
                        ->where('sim_owner', '=', $results->sim_owner)
                        ->and_where('person_id', '=', $results->person_id)
                        ->and_where('phone_number', '=', $party_a)
                        ->execute();   
            }            
            /*else{
            $query = DB::update('person_phone_number')->set(array('imsi_number' => $imsi_number, 'status' => $status, 'mnc' => $mnc, 'user_id'=>$user_id, 'sim_last_used_at' =>$from))
                    ->where('sim_owner', '=', $results->sim_owner)
                    ->where('person_id', '=', $results->person_id)
                    ->where('phone_number', '=', $party_a)
                    ->execute();                   
            }*/
        }else{
            if($party_a>20) {
                $query = DB::insert('person_phone_number', array('imsi_number', 'status', 'mnc', 'phone_number', 'sim_activated_at'))
                    ->values(array($imsi_number, $status, $mnc, $party_a, $from))
                    ->execute();
            }else{
                $query = DB::insert('debugging_insertion', array('details'))
                    ->values(array('Model/Shortparse/updateorinsert_person_phone_number_withfirstuse -- '.$imsi_number.' -- '. $status.' -- '. $mnc.' -- '. $party_a.' -- '. $from))
                    ->execute();
            }
            return $query[0];
        }    
    }
    public static function updateorinsert_person_device_number_withlastuse($device_id, $party_a, $from) {
        
        $DB = Database::instance();
        $sql = "select device_id from person_device_numbers WHERE phone_number = {$party_a} and device_id = {$device_id} and last_use < '{$from}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(!empty($results->device_id))
        {
            $query = DB::update('person_device_numbers')->set(array('last_use' => $from))                    
                    ->where('phone_number', '=', $party_a)
                    ->where('device_id', '=', $device_id)
                    ->execute(); 
        }
        
    }
    public static function updateorinsert_person_device_number_withfirstuse($device_id, $party_a, $from, $to) {
        $DB = Database::instance();
        $sql = "select device_id from person_device_numbers WHERE phone_number = {$party_a} and device_id = {$device_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(!empty($results->device_id))
        {
            $query = DB::update('person_device_numbers')->set(array('is_active' => 1))                    
                    ->where('phone_number', '=', $party_a)
                    ->where('device_id', '=', $device_id)
                    ->execute(); 
            /* check first date */
            $sql = "select device_id from person_device_numbers WHERE phone_number = {$party_a} and device_id = {$device_id} and first_use > '{$from}'";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            if(!empty($results->device_id))
            {
                $query = DB::update('person_device_numbers')->set(array('first_use' => $from))                    
                    ->where('phone_number', '=', $party_a)
                    ->where('device_id', '=', $device_id)
                    ->execute(); 
            }
            /* check last date */
            $sql = "select device_id from person_device_numbers WHERE phone_number = {$party_a} and device_id = {$device_id} and last_use < '{$to}'";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            if(!empty($results->device_id))
            {
                $query = DB::update('person_device_numbers')->set(array('last_use' => $to))                    
                    ->where('phone_number', '=', $party_a)
                    ->where('device_id', '=', $device_id)
                    ->execute(); 
            }
        
        }else{
            $query = DB::insert('person_device_numbers', array('device_id', 'phone_number', 'is_active', 'first_use', 'last_use'))
                    ->values(array($device_id, $party_a, 1,$from, $to))
                    ->execute();
            return $query[0];
        }
    }
    public static function update_person_device_number_change_status($party_a) {
        $query = DB::update('person_device_numbers')->set(array('is_active' => 0))                    
                    ->where('phone_number', '=', $party_a)
                    ->execute();  
    }
    public static function insert_person_phone_device($imei_number,$date_right, $user_id) {
        $query = DB::insert('person_phone_device', array('imei_number', 'in_use_since', 'user_id'))
                    ->values(array($imei_number, $date_right,$user_id))
                    ->execute();
            return $query[0];
    }
    public static function get_person_id($phone_number) {
        $DB = Database::instance();
        $sql = "select person_id from person_phone_number WHERE phone_number = {$phone_number}";        
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results->person_id;
    }
    

}
