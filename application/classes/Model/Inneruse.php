<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Inneruse {
     //get tokens by id
    public static function get_inner_tokens($token_id) {
        $DB = Database::instance();
        $sql = "SELECT key_value
                             from inner_token as t1
                             where t1.is_active=1 and t1.key_id ={$token_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $key = !empty($results->key_value) ? ($results->key_value) : '';
        return $key;
    }
    public static function get_inner_value_2($token_id) {
        $DB = Database::instance();
        $sql = "SELECT key_value_2
                             from inner_token as t1
                             where t1.is_active=1 and t1.key_id ={$token_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $key = !empty($results->key_value) ? ($results->key_value) : '';
        return $key;
    }

}
