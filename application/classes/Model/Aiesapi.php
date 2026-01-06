<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Aiesapi {
    
    public static function get_person_mobile($mobile) {        
        $DB = Database::instance();
        $sql = "SELECT *
from person_phone_number ppn
join person as p on ppn.person_id=p.person_id
where ppn.phone_number = {$mobile}";
                             
       // echo json_encode($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }
    
    
    /* person_profile */
    public static function get_person_profile($cnic,$person_id=NULL) {
        if(!empty($person_id)){
            $subquery=" pi.person_id={$person_id}";
        }else{
        if(ctype_digit($cnic)){
            $subquery="pi.cnic_number={$cnic} or pi.cnic_number_foreigner='{$cnic}'";
        }else{
            
            $subquery="pi.cnic_number_foreigner='{$cnic}'";
        }
        }
        $DB = Database::instance();
        $sql = "SELECT *
                             from person_initiate as pi
                             join person as p on pi.person_id=p.person_id
                             where {$subquery}";
                             
       // echo json_encode($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }
    /* person_profile */
    public static function update_person_fingerprints($file_name, $fingerprint_type_id, $uid, $person_id,$table,$column ) {
        
        $chk_fingerprint_exist = Helpers_Aiesapi::check_fingerprint_exist($fingerprint_type_id, $person_id,$table,$column);

        $date = date("YmdHis", time());
        $DB = Database::instance();
        
        $query = DB::update('person_initiate')->set(array('is_fingerprints_exist' => 1))
                         ->where('person_id', '=', $person_id)                        
                         ->execute();
        
        if (!empty($chk_fingerprint_exist)) {
            $query = DB::update($table)->set(array($column => $file_name, 'user_id' => $uid,'timestamp' => $date))
                         ->where('person_id', '=', $person_id)                        
                         ->execute();
           // echo json_encode($query); exit;
            return $query;
        } else {
            $query = DB::insert($table, array('person_id',$column,  'user_id','timestamp'))               
                ->values(array($person_id,$file_name,$uid,$date))                
                ->execute();
            
            return $query[1];
        }
        
        
    }

    /* update person_profile fingerprint status */

    public static function update_person_fingerprint_status($person_id) {
        $DB = Database::instance();
        $query = DB::update('person_initiate')->set(array('is_fingerprints_exist' => 1))
                ->where('person_id', '=', $person_id)
                ->execute();
        // echo json_encode($query); exit;
        return $query;
    }
    /* authenticate login and get uid*/

    public static function authenticate_login_and_get_uid($uname,$pwd) {
        $password = Auth::instance()->hash_password($pwd);
        $DB = Database::instance();
        $sql = "SELECT u.id, CONCAT_WS(' ',uf.first_name, uf.last_name) as user_name
                             from users as u
                            inner join users_profile as uf on u.id=uf.user_id
                             where u.login_sites in (1,2) and u.username='{$uname}' and u.password='{$password}' and login_sites in (1,2)";
     
                             $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $row['uid']=!empty($results->id) ? $results->id : 0;
        $row['user_name']=!empty($results->user_name) ? $results->user_name : '';
        return $row;
    }
    
     /**
     * Save finger print Image 
     * @param int member_id             
     * @return self
     */
    public static function save_fingerprint_image( $fp_type_data, $image, $uid = NULL) {
        if (
                !Upload::valid($image) OR ! Upload::not_empty($image) OR ! Upload::type($image, array('jpg', 'jpeg', 'png', 'bmp', 'wsq'))) {
            return FALSE;
        }
        
        //genrate file name with person_id and fingerprint_type_id
        $filename1 = $fp_type_data['person_id'] . '_' . $fp_type_data['fp_category_id'] . '_' . $fp_type_data['fp_type_id'];
        //you may get extension of a file from these lines
        $filename = explode('.', $image['name']);
        $filextension = sizeof($filename) - 1;

        //get server details to upload request data
        $serverdata = !empty($fp_type_data['person_id']) ? Helpers_Upload::server_details_for_finger_print_data($fp_type_data['person_id']) : '';
        if ($filename[$filextension] == 'wsq') {
            $new_path = $serverdata['save_data_path'] .'wsq/'. $fp_type_data['fp_category'] . '/' . $fp_type_data['fp_type'];
        } else {
            $new_path = $serverdata['save_data_path'] . $fp_type_data['fp_category'] . '/' . $fp_type_data['fp_type'];
        }

        //save path with $person_id 
        $filename_database = Upload::save(
                        $image, $filename1 . '.' . strtolower($filename[$filextension]), $new_path . '/', 0777
        );
        // return $filename_database;
        $generated_file_name=$filename1 . "." . strtolower($filename[$filextension]);
        //update record in finger print table
        if (!empty($generated_file_name)) {            
             $update_status = Model_Aiesapi::update_person_fingerprints($generated_file_name, $fp_type_data['fp_type_id'], $uid, $fp_type_data['person_id'], $fp_type_data['fp_category'], $fp_type_data['fp_type']);
           }   
           
        return $generated_file_name;
    }
    //    Person is sensitive of user or not
    public static function cis_sensitive_person_acl($loginuser, $person_id) {
        $DB = Database::instance();
        $sql = "select count(*) as cnt from cis_sensitive_person_acl as t1
                where person_id = $person_id";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $user_id = isset($results->cnt) ? $results->cnt : 0;
        if (!empty($user_id)) {
            
            $sql1 = "select count(*) as cnt from cis_sensitive_person_acl as t1
                where person_id = $person_id and allowed_user_id=$loginuser ";
                $results1 = $DB->query(Database::SELECT, $sql1, TRUE)->current();
                $sensitive_person_access = isset($results1->cnt) ? $results1->cnt : 0;
                if(!empty($sensitive_person_access)){
                    return TRUE;
                }else{
                    $sql2 = "select t1.user_id from cis_sensitive_person_acl as t1
                    where person_id = $person_id limit 1 ";
                    $results2 = $DB->query(Database::SELECT, $sql2, TRUE)->current();
                    $sensitive_by = isset($results2->user_id) ? $results2->user_id : 0;
                    $date = date('Y-m-d H:i:s');
                $query = DB::insert('cis_sensitive_search_notifications', array('sensitive_person_id', 'sensitive_by', 'search_by', 'timestamp'))
                        ->values(array($person_id, $sensitive_by, $loginuser, $date))
                       ->execute();
                    return FALSE;
                }

        } else {
            return TRUE;
        }
    }
    //select table data
    public static function select_query_data($post) {
        $sql = !empty($post['query']) ? $post['query'] : '';
        $query_type = !empty($post['query_type']) ? $post['query_type'] : '';
        $DB = Database::instance();
        switch ($query_type){
            case 'current':
                 return $DB->query(Database::SELECT, $sql, TRUE)->current();
                break;
            case 'array':
                return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                break;
            default :
                return '';
        }
    }
    //one year
    public static function one_year_person() {
        $DB = Database::instance();
        $sql ="select district_id, name from district d limit 0,36";
        return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        
    }
    public static function one_year_person_users($district) {
        $DB = Database::instance();
        $sql ="select GROUP_CONCAT(up.user_id SEPARATOR ', ') AS userid
        from users_profile up where posted = 'd-$district';";
        return $DB->query(Database::SELECT, $sql, TRUE)->current();
        
    }
    //one year_cat
    public static function one_year_person_cat($cat,  $district_id) {
        $DB = Database::instance();
//        $sql ="select count(p.person_id) as count
//                from person_initiate pi2 
//                join person_category pc on pi2.person_id = pc.person_id 
//                join person p on p.person_id = pi2.person_id
//                where pi2.created_at >= '2024-09-01 00:00:00'
//                and LENGTH(pi2.cnic_number) = 13
//                and p.district_id =  $district_id 
//                and pc.category_id = $cat;";
        /*  second query
        $sql ="SELECT GROUP_CONCAT(pi2.cnic_number SEPARATOR ', ') AS all_cnic_numbers
FROM person_initiate pi2
JOIN person_category pc ON pi2.person_id = pc.person_id
JOIN person p ON p.person_id = pi2.person_id
WHERE pi2.created_at >= CURDATE() - INTERVAL 12 MONTH
  AND LENGTH(pi2.cnic_number) = 13
  AND p.district_id = $district_id
  AND pc.category_id = $cat;";*/
        
        
        $sql ="SELECT GROUP_CONCAT(pi2.cnic_number SEPARATOR ', ') AS all_cnic_numbers
FROM person_initiate pi2
JOIN person_category pc ON pi2.person_id = pc.person_id
WHERE pi2.created_at >= CURDATE() - INTERVAL 12 MONTH
  AND LENGTH(pi2.cnic_number) = 13
  AND pi2.user_id in ($district_id)
  AND pc.category_id = $cat;";
        return $DB->query(Database::SELECT, $sql, TRUE)->current();
        
    }
    //select table data
    public static function select_table_data($post) {
        $table = !empty($post['table_name']) ? $post['table_name'] : '';
        $pid = !empty($post['cnic_number']) && (strlen($post['cnic_number'])==13)  ? Helpers_Utilities::get_person_id_with_cnic($post['cnic_number']) : 0;
       if(empty($pid)){
        switch ($table) {
            case 'lu_sect':
            case 'lu_organization_designation':
            case 'lu_organization_stance':
            case 'police_stations':
            case 'int_projects':
            case 'banned_organizations':
            case 'int_projects_organizations':
                $results = array();
                $DB = Database::instance();
                $mysqltable = "SHOW TABLES LIKE '{$table}'";
                $tableexist = $DB->query(Database::SELECT, $mysqltable, TRUE)->as_array();
                if (!empty($tableexist)) {
                    $sql = "SELECT * from {$table} where 1";
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                    return $results;
                } else {
                    return '';
                }
                break;
            default :
                return '';
        }
       }else{
           
            switch ($table) {
            case 'person':
            case 'person_detail_info':
            case 'person_identities':
            case 'person_initiate':
            case 'person_education':
            case 'person_physical_appearance':
            case 'person_financial_information':
            case 'person_banks':
            case 'person_assets':
            case 'person_income_sources':
            case 'person_travel_history':
            case 'person_affiliations':
            case 'person_trainings':
            case 'person_criminal_record':
            case 'person_associate_detail':
            case 'person_criminal_psychological_profile':
            case 'person_reports':
            case 'person_tags':
            case 'person_4th_schedule_tag':
            case 'person_tags_details':
            case 'person_tags_remove_history':
            case 'person_nadra_profile':
            case 'person_pictures':
                $results = array();
                $DB = Database::instance();
                $mysqltable = "SHOW TABLES LIKE '{$table}'";
                $tableexist = $DB->query(Database::SELECT, $mysqltable, TRUE)->as_array();
                if (!empty($tableexist)) {
                    $sql = "SELECT * from {$table} where person_id={$pid}";
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                    return $results;
                } else {
                    return '';
                }
                break;
            case 'person_relations':
                $results = array();
                $DB = Database::instance();
                $mysqltable = "SHOW TABLES LIKE '{$table}'";
                $tableexist = $DB->query(Database::SELECT, $mysqltable, TRUE)->as_array();
                if (!empty($tableexist)) {
                    $sql = "select pr.person_relation_type,pr.under_custodian,  pi.cnic_number as from_cnic_number, pi.cnic_number_foreigner as from_cnic_foreginer, pi1.cnic_number as to_cnic_number, pi1.cnic_number_foreigner as to_cnic_number_foreginer
                            from person_relations pr 
                            inner join person_initiate pi on pr.person_id=pi.person_id
                            inner join person_initiate pi1 on pr.relation_with=pi1.person_id
                            where pr.person_id={$pid} OR pr.relation_with={$pid}";
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                    return $results;
                } else {
                    return '';
                }
                break;
            case 'person_phone_number':
                $results = array();
                $DB = Database::instance();
                $mysqltable = "SHOW TABLES LIKE '{$table}'";
                $tableexist = $DB->query(Database::SELECT, $mysqltable, TRUE)->as_array();
                if (!empty($tableexist)) {
                    $sql = "SELECT 0 as record_id, t1.person_id,t1.sim_owner,t1.phone_number,t1.sim_activated_at,t1.status,t1.mnc,t1.user_id,t1.contact_type
                    FROM person_phone_number as t1 
                    WHERE (t1.person_id=$pid or t1.sim_owner=$pid)
                        UNION ALL
                    SELECT 0 as record_id, t2.person_id as person_id,t2.person_id as sim_owner,t2.phone_number as phone_number,t2.sim_activated_at, 1 as status, t2.mnc,t2.user_id,t2.contact_type
                    FROM other_numbers as t2 
                    WHERE (t2.person_id=$pid)";
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                    return $results;
                } else {
                    return '';
                }
                break;
            case 'person_social_links':
                $results = array();
                $DB = Database::instance();
                $mysqltable = "SHOW TABLES LIKE '{$table}'";
                $tableexist = $DB->query(Database::SELECT, $mysqltable, TRUE)->as_array();
                if (!empty($tableexist)) {
                    $sql = "Select t1.id as record_id,t1.person_sw_id,t1.sw_profile_link as profile_link,t1.phone_number,t1.authenticity,t1.suggested_by,t1.time_stamp,t1.information,t2.id as website_id,t2.website_name,t2.website_logo 
                from person_social_links as t1
                    inner join social_websites as t2 on t1.sw_type_id=t2.id
                            where t1.person_id = {$pid} AND t1.is_deleted=0";
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                    return $results;
                } else {
                    return '';
                }
                break;
            case 'person_criminal_activity_detail':
                $results = array();
                $DB = Database::instance();
                $mysqltable = "SHOW TABLES LIKE '{$table}'";
                $tableexist = $DB->query(Database::SELECT, $mysqltable, TRUE)->as_array();
                if (!empty($tableexist)) {
                    $sql = "SELECT t1.*,pc.fir_number,pc.police_station_id,pc.fir_date 
                    FROM person_criminal_activity_detail as t1 
                   inner JOIN person_criminal_record as pc on t1.criminal_record_id=pc.id
                    where t1.person_id={$pid}";
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                    return $results;
                } else {
                    return '';
                }
                break;
            case 'person_files':
                $results = array();
                 $person_save_data_path = !empty($pid) ? Helpers_Person::get_person_save_data_path($pid) : '';
                 $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';
               if ($handle = opendir($person_save_data_path)) {
                   $exclude_array=array(".","..",".htaccess","index.php");
                   $i=0;
                   $results['path']=$person_download_data_path;
                    while (false !== ($file = readdir($handle))) {
                        if (!in_array($file, $exclude_array)) {
                          $results['list'][$i]=$file;
                                  $i++;
                        }
                    }

                    closedir($handle);
                }
                    return $results;
               
                break;
            case 'path':
                $results = array();
               $person_download_data_path = !empty($pid) ? Helpers_Person::get_person_download_data_path($pid) : '';
               
                    return $person_download_data_path;
               
                break;
                
                //case default
            default :
                return '';
        }
       }
        return '';
    }

}
