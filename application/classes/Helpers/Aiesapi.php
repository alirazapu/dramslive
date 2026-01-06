<?php

defined('SYSPATH') OR die('No direct script access.');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Helpers_Aiesapi {

    //authenticate aies key
    public static function authenticate_key($value) {
        $key = Helpers_Inneruse::get_aieskey();        
        //echo json_encode($key); 
        //echo json_encode($value); exit;
        if ($value == $key) {
            return 1; //key matched
        } else {
            return 0;
        }
    }
    //authenticate aies key for cctw
    public static function authenticate_cctw_key($value) {
        $key = Helpers_Inneruse::get_aieskey_cctw();
        //echo json_encode($key);
        //echo json_encode($value); exit;
//        $value= Helpers_Utilities::encrypted_key($value, "decrypt");

        if ($value == $key) {
            return 1; //key matched
        } else {
            return 0;
        }
    }
    //authenticate user create key
    public static function authenticate_update_cis_aies_permission_key($value) {
        $key = Helpers_Inneruse::get_updatecisaiespermissionkey();
        if ($value == $key) {
            return 1; //key matched
        } else {
            return 0;
        }
    }
    //authenticate key to access table data
    public static function authenticate_table_data_key($value) {
        $key = Helpers_Inneruse::get_tabledatakey();
        if ($value == $key) {
            return 1; //key matched
        } else {
            return 0;
        }
    }
    //authenticate user create key
    public static function authenticate_user_create_key($value) {
        $key = Helpers_Inneruse::get_usercreatekey();
        if ($value == $key) {
            return 1; //key matched
        } else {
            return 0;
        }
    }

    //check finger print exist
    public static function check_fingerprint_exist($fingerprint_type_id, $person_id,$table,$column) {
        $DB = Database::instance();
        $sql = "SELECT count(t1.person_id) as cnt
                FROM  {$table} AS t1
                WHERE t1.person_id={$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = !empty($results->cnt) ? $results->cnt : 0;
        if($chk!=0){
            $chk=1;
        }
        return $chk;
    }
  

    //this helper will provide finger print save data path with person_id
    public static function get_finger_print_data_path($pid = NULL, $name = NULL, $type = NULL) {
        //get folder range to uplaod data
        $folder_range = !empty($id) ? Helpers_Upload::get_folder_range($id) : '';
        //get server details to upload request data
        $serverdata = !empty($id) ? Helpers_Upload::server_details_for_request_data($id) : '';

        $request_subfolder_path = !empty($serverdata) ? $serverdata['request_save_data_path'] . $folder_range : '';
        // print_r($request_subfolder_path); exit;
        if ((!is_dir($request_subfolder_path)) && !empty($request_subfolder_path)) {
            mkdir("{$request_subfolder_path}", 0777);
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/.htaccess', $request_subfolder_path . '/.htaccess');
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/index.php', $request_subfolder_path . '/index.php');
        }


        if ($type == "save") {
            //request data folder path
            $request_save_data_path = $serverdata['request_save_data_path'] . $folder_range . '/';

            return $request_save_data_path;
        } else {
            //alias for download only
            $request_download_data_path = $serverdata['server_name'] . $serverdata['request_download_data_path'] . $folder_range . '/';

            return $request_download_data_path;
        }
    }

    /*
     * Error API Response 
     * 
     */

    public static function error_code($error_code = NULL) {
        $DB = Database::instance();
        $sql = "SELECT *
                         from lu_api_error_type AS T1";
        if (!empty($error_code)) {
            $sql .= " WHERE T1.error= $error_code";
            $error = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $error = !empty($error->message) ? $error->message : 'Unknown Error';

            return $error;
        }
        $error = $DB->query(Database::SELECT, $sql, TRUE);
        return $error;
    }

    /*
     * Get finger print type 
     */

    public static function finger_print_type($fp_name = NULL) {
        $DB = Database::instance();
        $sql = "SELECT T1.id as fp_type_id,T1.finger_print_type as fp_type,T1.fp_file_name,T2.id as fp_category_id,T2.fp_category,T2.fp_category_description as fp_category_desc
                         from lu_finger_print_types AS T1
                         inner join lu_finger_print_category as T2 on T1.fp_category_id=T2.id";
        if (!empty($fp_name)) {
            $sql .= " WHERE T1.fp_file_name='". $fp_name."' limit 1";
            $type = $DB->query(Database::SELECT, $sql, FALSE)->as_array();
            if(!empty($type)){
            return $type[0];
            }
        }else{
        $type = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        }
        return $type;
    }
    public static function get_finger_print_category($id = NULL) {
        $DB = Database::instance();
        $sql = "SELECT *
                         from  lu_finger_print_category as T2 ";
        if (!empty($id)) {
            $sql .= " WHERE T2.id='". $id."' limit 1";
            $type = $DB->query(Database::SELECT, $sql, FALSE)->as_array();
            if(!empty($type)){
            return $type[0];
            }
        }else{
        $type = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        }
        return $type;
    }

}

?>