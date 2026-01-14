<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Profile helper class. It will contain all the Helper Functions realted to Profile
 *
 * @package    Profile Helper
 * @category   Helpers
 */
abstract class Helpers_Watchlist {
        //    get all tags data
    public static function get_tags_data($id=null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM lu_tags ";
        if(!empty($id)){
            $sql .= " Where id={$id}";
             $results = $DB->query(Database::SELECT, $sql, TRUE)->current();          
        $results = isset($results->tag_name) ? $results->tag_name : 'Unknown';
        }else{
        $results = $DB->query(Database::SELECT, $sql, TRUE);  
        }              
        //print_r($results); exit;
        return $results;
    }
    //    get person tags data
    public static function get_person_tags($person_id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM person_tags where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, FALSE)->as_array();        
        //print_r($results); exit;
        return $results;
    }
    /* get user all data */
     public static function get_user_data($user_ud) {
        $DB = Database::instance('default');
        $sql = "SELECT CONCAT_WS(' ',first_name, last_name) as name, job_title, district_id, posted
                         from users_profile
                         where user_id = '".$user_ud."'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(!empty($results))
        {    
            $results->name = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";
            $results->job_title = isset($results->job_title) && !empty($results->job_title) ? $results->job_title : "Unknown";
        }else{
            $results = new stdClass;
            $results->name = "Unknown";
            $results->district_id = "";
            $results->job_title = "Unknown";
        }
        return $results;
    }
    
    //    get person tags data
    public static function in_watchlist($person_id, $district_id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM person_tags where person_id = '".$person_id."' and tag_district_id ='".$district_id."' and in_watchlist=1 Group By tag_district_id";
        $results = $DB->query(Database::SELECT, $sql, FALSE)->as_array();
        return $results;
    }
    //    get tag name by id
    public static function get_tag_name($tag_id) {
        $DB = Database::instance();
        $sql = "SELECT tag_name FROM lu_tags where id = {$tag_id}";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();        
        $results = isset($results->tag_name) && !empty($results->tag_name) ? $results->tag_name : "Unknown";
        //print_r($results); exit;
        return $results;
    }
    //    get tag name by id in one query
    public static function get_tag_name_all($tag_id) {
        //print_r($tag_id); exit;
        $DB = Database::instance();
        $sql = "SELECT GROUP_CONCAT(tag_name) as tag_name FROM lu_tags where id in ({$tag_id})";
       // print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();        
        $results = isset($results->tag_name) && !empty($results->tag_name) ? $results->tag_name : "---";
        //print_r($results); exit;
        return $results;
    }
    //    get person watchlist status 
    public static function get_watchlist_status($person_id) {
        $DB = Database::instance();
        $sql = "SELECT count(person_id) as count FROM person_tags where in_watchlist = 1 and person_id = {$person_id}";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();          
        $results = isset($results->count) ? $results->count : 0;
        //print_r($results); exit;
        return $results;
    }
    //get person tags by id with comma seperated
    public static function get_person_tags_data_comma($person_id) {
        $tags = '';
        $DB = Database::instance();
        $sql = "SELECT tag_name,tag_description FROM lu_tags as t1 "
                . "join person_tags as t2 on t2.tag_id = t1.id "
                . "where t2.person_id = {$person_id}";
                
        $results = DB::query(Database::SELECT, $sql, FALSE)->execute();                        
        foreach ($results as $data){
            $tag_name = !empty($data['tag_name']) ? $data['tag_name'] : '-';
            $tag_description = !empty($data['tag_description']) ? $data['tag_description'] : '-';
            $tags .= '<span title="'.$tag_description.'" class="text-black">'.$tag_name.'</span>';
            $tags .= ', ';            
        }
        return $tags;
    }

}

?>
