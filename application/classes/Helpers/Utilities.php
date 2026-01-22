<?php

abstract class Helpers_Utilities {

    //put your code here

    /* Change char to number     * 
     */
    public static function toNumber($char) {
        if ($char)
            return ord(trim($char));
        //return ord(strtolower($char)) - 96;
        else
            return 0;
    }

    /* Change char to number     * 
     */

    public static function find_imei_last_digit($imei) {
        $imei = substr($imei, 0, 14);
        // echo $imei; exit;
        // $number = 35145120840121;
        $number = $imei;
//(5x2, 4x2, 1x2, 0x2, 4x2, 1x2, 1x2) = (10, 8, 2, 0, 8, 2, 2)  //getting digits at even positions and multiply by 2
        $array_first = array_map('intval', str_split($number));



        array_unshift($array_first, "phoney");
        unset($array_first[0]);
        $array_second = array();
        foreach ($array_first as $k => $v) {
            if ($k % 2 == 0) {
                $array_second[] = $v * 2;
            } else {
                $array_second[] = $v;
            }
        }

        $value_third = '';
        foreach ($array_second as $second) {
            $value_third .= $second;
        }
        // print_r($value_third);
        //(1+0+8+2+0+8+2+2) + (3+1+5+2+8+0+2 ) = 44  //separating digits and adding all results with existing odd position digits
        $array_third = array_map('intval', str_split($value_third));
        $fourth = 0;
        foreach ($array_third as $third)
            $fourth += $third;


        $result = '';
        for ($i = 0; $i <= 10; $i++) {
            $check_point = $fourth + $i;
            if ($check_point % 10 == 0) {
                $result = $i;
                $break;
            }
        }
        // echo $result;
        if ($result == 10)
            $result = 0;

        return $imei . $result;
    }

    /*  get array of each string
     */

    public static function check_digit($cnic) {

        if (ctype_digit(trim($cnic))) {
            return trim($cnic);
        } else {
            $helper = trim($cnic);

            if (!ctype_digit(($helper[0]))) {
                $helper[0] = 9; // Helpers_Utilities::toNumber($helper[0]);
            }
            if (!ctype_digit(($helper[1]))) {
                $helper[1] = 9;
            }
            if (!ctype_digit(($helper[2]))) {
                $helper[2] = 9;
            }
            if (!ctype_digit(($helper[3]))) {
                $helper[3] = 9;
            }
            if (!ctype_digit(($helper[4]))) {
                $helper[4] = 9;
            }
            if (!ctype_digit(($helper[5]))) {
                $helper[5] = 9;
            }
            if (!ctype_digit(($helper[6]))) {
                $helper[6] = 9;
            }
            if (!ctype_digit(($helper[7]))) {
                $helper[7] = 9;
            }
            if (!ctype_digit(($helper[8]))) {
                $helper[8] = 9;
            }
            if (!ctype_digit(($helper[9]))) {
                $helper[9] = 9;
            }
            if (!ctype_digit(($helper[10]))) {
                $helper[10] = 9;
            }
            if (!ctype_digit(($helper[11]))) {
                $helper[11] = 9;
            }
            if (!ctype_digit(($helper[12]))) {
                $helper[12] = 9;
            }
            $cnic = $helper;
            return $helper;
        }
    }

    public static function remvoe_science($x) {
        $f = sprintf('%0.08f', $x);
        $f = rtrim($f, '0');
        $f = rtrim($f, '.');
        return $f;
    }
    public static function date_extended_with_second($dateStr, $secondsToAdd) {
        //$dateStr = '2025-07-01 14:24:51';
        //$secondsToAdd = 75; // change this to how many seconds you want to add
        // Create a DateTime object
        $date = new DateTime($dateStr);
        // Add seconds using a DateInterval
        $date->add(new DateInterval('PT' . $secondsToAdd . 'S'));
        // Get the new date-time string
        $newDateStr = $date->format('Y-m-d H:i:s');
        return $newDateStr; // Output: 2025-07-01 14:26:06
    }
    public static function date_checking($input) {
        if (Helpers_Utilities::isValidDateTime($input, 'm/d/Y h:i:s a')) {
            $normalized = strtolower($input);
            $date = DateTime::createFromFormat('m/d/Y h:i:s a', $normalized);
            $result = $date->format('m/d/Y H:i:s'); // → 07/01/2025 14:34:19

        } elseif (Helpers_Utilities::isValidDateTime($input, 'm/d/Y H:i:s')) {
            $result = $input;
        }else{
            $dt = new DateTime();
            $dt->setTimestamp($input);
            $result = $dt->format('Y-m-d H:i:s'); // Output: 2025-08-04 06:58:49
        }
        //echo $result;
        //echo '<br>';
        if($result <= '2001-01-00 00:00:00')
        {
            $unixTimestamp = ($input - 25569) * 86400;
            $result = gmdate("Y-m-d H:i:s", $unixTimestamp);
            //echo $dateTime;
        }    
        
        return $result;
    }
    public static function isValidDateTime($dateStr, $format) {
        $dt = DateTime::createFromFormat($format, $dateStr);
        return $dt && $dt->format($format) === strtolower($dateStr);
    }
    public static function convertExcelDate($value) {
      if (is_numeric($value)) {
        // Convert Excel serial to Unix timestamp
        $timestamp = ($value - 25569) * 86400;
        return gmdate('Y-m-d H:i:s', round($timestamp));
    } else {
        // For string-based dates
        $ts = strtotime($value);
        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    }
    

    public static function remvoe_science_fifteen($x) {
        $f = sprintf('%0.15f', $x);
        $f = rtrim($f, '0');
        $f = rtrim($f, '.');
        return $f;
    }

    public static function full_url() {
        $s = (empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on")) ? "s" : "";
        $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }

    public static function full_site_url() {
        $s = (empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on")) ? "s" : "";
        $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
        return $protocol . "://" . $_SERVER['SERVER_NAME'] . URL::site();
    }

    public static function get_random_code($characters = 8) {

        $possible = '2aq345p67bm89rAcBCDduEFsnGHlJtKLMNPQRSzTUVWXYykZ';
        $possible = $possible . $possible . '23v4567f8g923h45i6w7j89x';
        $code = '';
        $i = 0;

        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }

        return $code;
    }

    //Random password that consists on character and number only
    public static function get_random_password($characters) {

        $possible = 'abcdefghijklmnpqrstuvwxyz123456789';
        $code = '';
        $i = 0;

        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }

        return $code;
    }

    /* Random colour generator */

    public static function rand_color() {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    /* Random shape generator */

    public static function rand_shape() {
        $array = array("triangle", "ellipse", "octagon", "rectangle", "diamond");
        return $array[rand(0, count($array) - 1)];
    }

    /*
     *  Get the Country name of the given ID
     */

    public static function inactive_user($id = Null) {
        $DB = Database::instance();
        /* $sql = "SELECT u.id
          FROM `user_activity_timeline` as uat
          join users as u on uat.user_id = u.id
          where uat.activity_time > DATE_SUB( NOW(), INTERVAL 4 HOUR)
          and u.is_login = 1
          and FROM_UNIXTIME(u.last_login) < DATE_SUB( NOW(), INTERVAL 4 HOUR)
          group by u.id";
          $results = $DB->query(Database::SELECT, $sql, TRUE);
          foreach($results as $r)
          {
          $result = DB::update("users")->set(array('is_login' =>0))->where('id', '=', $r->id)->execute();
          }
         */

        if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active']) > 5000) {
            $result = DB::update("users")->set(array('is_login' => 0))->where('id', '=', $id)->execute();
            session_unset();
            session_destroy();
            session_start();
            $sql = "SELECT id
                FROM users
                where is_login = 0
                and id = {$id}";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            if (!empty($results->id)) {
                Auth::instance()->logout(TRUE, TRUE);
                HTTP::redirect();
                //$this->redirect();
            }
        }
        //return $results;
    }

    /*  Get the Country name of the given ID
     */

    public static function get_roles_data($role = Null) {
        $DB = Database::instance();
        $sql = "SELECT *
                     FROM roles";
        if (!empty($role)) {
            $sql .= " WHERE id = {$role} LIMIT 1";
        }
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    public static function chek_role_access($role_id, $menu_id) {

        $DB = Database::instance();
        $sql = "SELECT access_status AS status
                FROM  manu_management AS t1
                WHERE t1.role_id = {$role_id} AND t1.manu_id = {$menu_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $status = isset($results->status) && $results->status ? $results->status : 0;
        return $status;
    }

    public static function chek_role_array_access($role_id, $menu_array) {
        $DB = Database::instance();
        foreach ($menu_array as $value) {
            $sql = "SELECT access_status AS status
                FROM  manu_management AS t1
                WHERE t1.role_id = {$role_id} AND t1.manu_id = {$value}";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $status = isset($results->status) && $results->status ? $results->status : 0;
            if ($status == 1) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function get_manu_data($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM lu_manu";
        if (!empty($role)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
        }
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    // get user role name form id
    public static function get_user_role_name($userid) {
        $DB = Database::instance();
        $sql = "SELECT t1.label as lbl FROM roles as t1
                join roles_users as t2 on t1.id = t2.role_id
                where t2.user_id = $userid";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->lbl) && !empty($results->lbl) ? $results->lbl : "Unknown";
        return $results;
    }

    /*
     *  Get the Country name of the given ID
     */

    public static function get_request_type_name($type_id) {
        $DB = Database::instance();
        $sql = "select  email_type_name as name from email_templates_type WHERE id = {$type_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";
        return $results;
    }

    /*
     *  Get the state name of the given ID
     */

    public static function get_user_type_name($user_type_id) {
        $user_type_id = (int) $user_type_id;
        $DB = Database::instance();
        $sql = "SELECT *
                     FROM roles
                     WHERE id = {$user_type_id}                     
                     LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        return !empty($results->label) ? $results->label : 'Not Define';
    }

    /*
     *  Get the state name of the given ID
     */

    public static function get_user_type() {
        $DB = Database::instance();
        $sql = "SELECT * FROM roles";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     *  Get the state name of request
     */

    public static function get_request_details($requestid) {
        $DB = Database::instance();
        $sql = "SELECT *
                     FROM user_request
                     WHERE request_id = {$requestid}                     
                     LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     *  Get the user name of the given id
     *
     * @param int $user_ud
     * 
     * return self
     */


    public static function get_user_name($user_ud) {
        $DB = Database::instance('default');
        $sql = "SELECT CONCAT_WS(' ',first_name, last_name) as name
                         from users_profile
                         where user_id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";
        return $name;
    }
    public static function get_user_pid($user_ud) {
        $DB = Database::instance('default');
        $sql = "SELECT person_id
                         from person_tags
                         where id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $pid = isset($results->person_id) && !empty($results->person_id) ? $results->person_id : 0;
        return $pid;
    }
    public static function get_username($user_ud) {
        $DB = Database::instance('default');
        $sql = "SELECT * from users where id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = isset($results->username) && !empty($results->username) ? $results->username : "--";
        return $name;
    }

    /*
     *  Get the user list
     *
     * 
     */

    public static function get_users_list_with_posting($posting = NULL) {

        $DB = Database::instance('default');
        $sql = "SELECT CONCAT_WS(' ',first_name, last_name) as name, user_id, district_id,posted,region_id,job_title,belt
                         from users_profile";
        if (!empty($posting)) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $sql .= " where 1";
                    break;
                case 'r':
                    $sql .= " where region_id=$result[1]";
                    break;
                case 'd':
                    $sql .= " where district_id=$result[1]";
                    break;
                case 'p':
                    $sql .= " where posted='p-$result[1]'";
                    break;
            }
        }

        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }  /*
     *  Get the user list
     *
     *
     */

    public static function get_users_list_against_posting_id($posting = NULL) {

        $DB = Database::instance('default');
        $sql = "SELECT CONCAT_WS(' ',first_name, last_name) as name, user_id, district_id,posted,region_id,job_title,belt
                         from users_profile
                         where posted= '{$posting}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     *  Get the user_role_id of the given id
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_user_role_id($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT role_id 
                        FROM roles_users 
                         where user_id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $role_id = isset($results->role_id) && !empty($results->role_id) ? $results->role_id : 0;
        return $role_id;
    }

    //
    public static function get_user_permission($user_ud) {
        $user_ud = Helpers_Utilities::remove_injection($user_ud);
        $DB = Database::instance();
        $sql = "SELECT role_id 
                        FROM roles_users 
                         where user_id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if (!empty($results->role_id)) {
            switch ($results->role_id) {
                case 1:
                    return 1; //administrator
                    break;
                case 2:
                case 4:
                case 6:
                    return 2; //technical support
                    break;
                case 3:
                case 5:
                case 7:
                    return 3; //exectives ,do, ro, ssp
                    break;
                case 8:
                    return 4; //field officer
                    break;
                case 9:
                    return 5; //Development Tech Support
                    break;
                default :
                    return 0;
                    break;
            }
        } else {
            return 0;
        }
    }

    //Increment Check comapny counter
    public static function increament_check_company_count($mnc) {
        $DB = Database::instance();
        //$result = DB::update("person")->set(array('view_count' =>'view_count' + 1))->where('person_id', '=', $person_id)->execute();
        $sql = "UPDATE mobile_companies set check_counter = check_counter + 1
                where mnc  = $mnc";
        $result = $DB->query(Database::UPDATE, $sql, TRUE);
        return $result;
    }

    /* Search Person details */

    public static function search_person_details($id) {
        $DB = Database::instance();
        $sql = "SELECT * from user_activity_timeline_detail
                where timeline_id = {$id}";
        $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $members;
    }

    /* Search MNC against phone */

    public static function search_mnc_ofmobile($id) {
        $DB = Database::instance();
        $sql = "SELECT mnc from person_phone_number where phone_number = {$id}";
        $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return !empty($members->mnc) ? $members->mnc : '';
    }
    /* Search PID against phone */

    public static function search_pid_of_mobile($ph_num) {
//        echo '<pre>';
//        print_r($ph_num[0]);
//        exit();
        $DB = Database::instance();
        $sql = "SELECT person_id from person_phone_number where phone_number = '{$ph_num}'";
        $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $pid= !empty($members->person_id) ? $members->person_id : 0;
        return $pid;
    }
    /* Search PID against cnic */

    public static function search_pid_of_cnic($cnic) {
//        echo '<pre>';
//        print_r($ph_num[0]);
//        exit();
        $DB = Database::instance();
        $sql = "SELECT person_id from person_initiate where cnic_number = '{$cnic}'";
        $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $pid= !empty($members->person_id) ? $members->person_id : 0;
        return $pid;
    }

    /* Time Line detail */

    public static function timeline_details($id) {
        $DB = Database::instance();
        $sql = "SELECT * from user_activity_timeline
                where timeline_id = {$id}";
        $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $members;
    }

    /*
     *  Get the job_title of the given id
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_user_job_title($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT job_title
                         from users_profile
                         where user_id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $job_title = isset($results->job_title) && !empty($results->job_title) ? $results->job_title : "Unknown";
        return $job_title;
    }

    /*
     *  Get the user_district_name of the given id
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_user_district_name($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT t2.name FROM
                        users_profile as t1 
                        JOIN district as t2 
                        on 
                        t1.district_id = t2.district_id 
                         where user_id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $dist_name = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";
        return $dist_name;
    }

    /*
     *  get person_id with foreign cnic number
     */

    public static function get_person_id_with_cnic($cnic) {
        $DB = Database::instance();
        if (ctype_digit(trim($cnic))) {
            $sql = "SELECT t1.person_id FROM
                        person_initiate as t1 
                         where t1.cnic_number = {$cnic} or t1.cnic_number_foreigner = {$cnic}";
        } else {
            $sql = "SELECT t1.person_id FROM
                        person_initiate as t1 
                         where t1.cnic_number_foreigner = '{$cnic}'";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $person_id = isset($results->person_id) && !empty($results->person_id) ? $results->person_id : 0;
        return $person_id;
    }
    /*
     *  get person_id with foreign cnic number
     */

    public static function get_person_id_with_phone_number($phone_number) {
        $DB = Database::instance();

            $sql = "SELECT t1.person_id FROM
                        person_phone_number as t1 
                         where t1.phone_number = '{$phone_number}'";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $person_id = isset($results->person_id) && !empty($results->person_id) ? $results->person_id : 0;
        return $person_id;
    }
    /*
     *  get person nationality withy pid
     */

    public static function get_person_nationality_with_pid($pid) {
        $DB = Database::instance();

            $sql = "SELECT t1.is_foreigner FROM
                        person_initiate as t1 
                         where t1.person_id = {$pid} ";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $is_foreigner = isset($results->is_foreigner) && !empty($results->is_foreigner) ? $results->is_foreigner : 0;
        return $is_foreigner;
    }

    /*
     *  helper to generate new id
     * person_id
     */

    public static function current_id($idtype) {
        $DB = Database::instance();
        $sql = "SELECT t1.last_id FROM
                        id_generator as t1 
                         where t1.id_type ='{$idtype}' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $existing_id = isset($results->last_id) && !empty($results->last_id) ? $results->last_id : 0;

        $new_id = $existing_id + 1;        
        return $new_id;
        
    }
    public static function id_generator($idtype) {
        if (empty($idtype)) {
            return 0;
        }
        $DB = Database::instance();
        $sql = "SELECT t1.last_id FROM
                        id_generator as t1 
                         where t1.id_type ='{$idtype}' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $existing_id = isset($results->last_id) && !empty($results->last_id) ? $results->last_id : 0;

        $new_id = $existing_id + 1;
        $query = DB::update('id_generator')->set(array('last_id' => $new_id))
                ->where('id_type', '=', $idtype)
                ->execute();
        $query_status = $query;

        if (empty($new_id) || empty($query_status)) {
            $query = DB::update('id_generator')->set(array('last_id' => $new_id))
                    ->where('id_type', '=', $idtype)
                    ->execute();
        }
        return $new_id;
    }

    /*
     *  Get the user type id of a given user
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_user_region_name($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT t2.name FROM
                        users_profile as t1 
                        JOIN region as t2 
                        on 
                        t1.region_id = t2.region_id 
                         where user_id = {$user_ud}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $region_name = isset($results->name) && !empty($results->name) ? $results->name : "Head Quarters";
        return $region_name;
    }

    /*
     *  Get the  id of a given activity
     *
     * 
     * 
     * return self
     */

    public static function get_user_activity_value($user_id, $id) {
        $DB = Database::instance();
        $sql = "SELECT permission
                        FROM  user_access_matrix
                        WHERE user_id = '{$user_id}' and user_activity_type = {$id} Limit 1";
        $acl_check = DB::query(Database::SELECT, $sql)->execute()->current();

        if (isset($acl_check['permission']) && $acl_check['permission'] == 1)
            return ' checked ';
        else
            return '';
    }

    public static function get_user_activity_name($id = null) {
        $DB = Database::instance();
        $sql = "SELECT id, label, internal_name FROM lu_user_activity_type";

        if (!empty($id)) {
            $sql .= " where id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $name = isset($results->label) && !empty($results->label) ? $results->label : "Unknown";
            return $name;
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
            return $results;
        }
    }

    public static function get_user_access_name($id = null) {
        $DB = Database::instance();
        $sql = "SELECT id, label, internal_name FROM lu_user_access_type";

        if (!empty($id)) {
            $sql .= " where id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $name = isset($results->label) && !empty($results->label) ? $results->label : "Unknown";
            return $name;
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
            return $results;
        }
    }

    // get project list---------
    public static function get_projects_list($id = null) {

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting_region = $login_user_profile->region_id;
        
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
        
        $where_clause = 'where 1';
        if ($posting_region == 11) {
            if ($permission == 1 || $permission == 2 || $permission == 5)
                $where_clause = " where 1 ";
            else
                $where_clause = "where ip.region_id = 11";
        } else {
            if ($result[0] == 'd') {
                $where_clause = " where ( ip.region_id = {$posting_region} and ip.district_id = {$result[1]})";
            } elseif ($result[0] == 'p') {
//                $distict = 0;
//                switch ($result[1]) {
//                    case 901:
//                        $distict = 901;
//                        break;
//                    case 3:
//                        $distict = 902;
//                        break;
//                    case 4:
//                        $distict = 903;
//                        break;
//                    case 5:
//                        $distict = 904;
//                        break;
//                    case 8:
//                        $distict = 905;
//                        break;
//                }
                //$where_clause = " where ( ip.region_id = {$posting_region} and ip.district_id = {$result[1]})";
                $where_clause = " where ( ip.region_id = {$posting_region})";
            } else {
                $where_clause = " where ( ip.region_id = {$posting_region})";
            }
        }
        
        // print_r($where_clause); exit;
        $DB = Database::instance();
        $sql = "SELECT * 
                FROM int_projects AS ip
                join region as t2 on t2.region_id = ip.region_id
                {$where_clause}";

        /* if(Auth::instance()->get_user()->id==2301){
            echo '<pre>';
            print_r($sql);
            exit();
        } */
                
        if (!empty($id)) {
            $sql .= " and ip.id={$id} and project_status = 0 LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            return $results;
        } else {
            
            $results = $DB->query(Database::SELECT, $sql, TRUE);
            return $results;
        }
        
    }
    // get project list---------
    public static function get_projects_dist_reg($id = null) {

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting_region = $login_user_profile->region_id;
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
        $where_clause = 'where 1';
        if ($posting_region == 11) {
            if ($permission == 1 || $permission == 2 || $permission == 5)
                $where_clause = " where 1 ";
            else
                $where_clause = "where ip.region_id = 11";
        } else {
            if ($result[0] == 'd') {
                $where_clause = " where ( ip.region_id = {$posting_region} and ip.district_id = {$result[1]})";
            } elseif ($result[0] == 'p') {

                $where_clause = " where ( ip.region_id = {$posting_region} and ip.district_id = {$result[1]})";
            } else {
                $where_clause = " where ( ip.region_id = {$posting_region})";
            }
        }
        // print_r($where_clause); exit;
        $DB = Database::instance();
        $sql = "SELECT * 
                FROM int_projects AS ip
                join region as t2 on t2.region_id = ip.region_id
                {$where_clause}";

        if (!empty($id)) {
            $sql .= " and ip.id={$id} and project_status = 0 LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            return $results;
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
            return $results;
        }

    }

    // get project id with organization id
    public static function get_project_id($id) {
        $DB = Database::instance();
        $sql = "SELECT ip.id 
                FROM int_projects AS ip
                inner join  banned_organizations AS bo on ip.org_id = bo.org_id where ip.org_id={$id} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $org_id = !empty($results->id) ? $results->id : 1;
        return $org_id;
    }

    // get person location History
    public static function get_person_location_history($person_id) {
        $DB = Database::instance();
        $sql = "SELECT * 
                FROM person_location_history where person_id = {$person_id} order by moved_in_at desc LIMIT 4";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }

    // get projects names against multiple values
    public static function array_to_comm($arr) {
        return join(",", $arr);
    }

    public static function get_projects_names($proj_ids) {
        //  $projectids = explode(',', $proj_ids);

        $projectids = array_filter(explode(',', $proj_ids));
        $countids = 1;
        $project = Helpers_Utilities::array_to_comm($projectids);
        $DB = Database::instance();
        $sql = "SELECT ip.project_name 
                FROM int_projects AS ip                
                where ip.id IN ({$project}) ";
        $names = $DB->query(Database::SELECT, $sql, TRUE);
        $results = '';
        foreach ($names as $name) {
            $nam = !empty($name->project_name) ? $name->project_name : '';
            $results = $results . "[" . $nam . "], ";
        }

        return $results;
    }

    //get project name form id
    public static function get_project_names($proj_id) {

        $DB = Database::instance();
        $sql = "SELECT ip.project_name 
                FROM int_projects AS ip                
                where ip.id = ({$proj_id}) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = !empty($results->project_name) ? $results->project_name : 'Un-Known';
        //print_r($name); exit;
        return $name;
    }

    //get project region name form project id
    public static function get_project_region_name($proj_id) {

        $DB = Database::instance();
        $sql = "SELECT *
                FROM int_projects AS ip
                join region as t2 on t2.region_id = ip.region_id
                where ip.id = ({$proj_id}) ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = !empty($results->name) ? $results->name : '';
        //print_r($name); exit;
        return $name;
    }

    //get person by category id and person id
    public static function get_project_persons($proj_id, $category_id) {

        $DB = Database::instance();
        $sql = "SELECT count(person_id) as count FROM person_category as t1                
                where t1.project_id = ({$proj_id}) and t1.category_id = {$category_id} ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $persons = !empty($results->count) ? $results->count : 0;
        //print_r($name); exit;
        return $persons;
    }

    /*
     *  Get the user type id of a given user
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_user_type_id($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT user_type_id 
                         from users
                         where user_ud = {$user_ud} AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get local time object on the basis of User IP address 
     *
     * @param int $ip_address
     * 
     * return self
     */

    public static function get_localtime($ip_address) {
        $timezone_json = file_get_contents("http://smart-ip.net/geoip-json/{$ip_address}");
        $timezone_json = json_decode($timezone_json);
        if ((isset($timezone_json->latitude) && !empty($timezone_json->latitude)) && (isset($timezone_json->longitude) && !empty($timezone_json->longitude))) {
            $localtimeobj = file_get_contents("http://www.earthtools.org/timezone/{$timezone_json->latitude}/{$timezone_json->longitude}");
            $localtimeobj = simplexml_load_string($localtimeobj);
        }
        return $localtimeobj;
    }

    /*
     *  Get time difference between two dates provided 
     *
     * @param int $date_first the date from which the difference need to be calculated
     * @param int $date_second the date to which the difference need to be calculated
     * 
     * return interval object
     */

    public static function get_time_difference($date_first, $date_second) {
        $date_first = new DateTime($date_first);
        $date_second = new DateTime($date_second);

        $interval = $date_first->diff($date_second);
        return $interval;
    }

    /* list of email template type   */

    public static function emailtemplatetype($id = null) {

        $data = new Model_Email;
        $data1 = $data->typeselection($id);
        return $data1;
    }

//    get company Name by id
    public static function get_companies_data($mnc = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    mobile_companies";
        if (!empty($mnc)) {
            $sql .= " WHERE mnc = {$mnc} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
             $sql .= " WHERE is_active=1 ";
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        //print_r($results); exit;
        return $results;
    }
//    get company Name by id
    public static function get_shortcode_data($record) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    telco_short_code";
        if (!empty($record)) {
        $sql .= " WHERE id = {$record} LIMIT 1";


            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }

        //print_r($results); exit;
        return $results;
    }

//    get company Name by id
    public static function get_banks_list($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM  lu_banks";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        //print_r($results); exit;
        return $results;
    }

//    get contact type
    public static function get_contact_type($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_contact_type";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = !empty($results) ? $results->contact_type : 'Unknown';
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        }
        //print_r($results); exit;
        return $results;
    }

//    get company Name by id
    public static function get_subscriber_status($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT status FROM 
                    lu_subscriber_flags";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $data = !empty($results->status) ? $results->status : 'Unknown';
        } else {
            $data = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $data;
    }

//    get banned orginization by id
    public static function get_banned_organizations($org_id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    banned_organizations";
        if (!empty($org_id)) {
            $sql .= " WHERE org_id = {$org_id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

//    get banned orginization designations by id
    public static function get_organization_designation($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_organization_designation";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

//    get banned orginization designations training type by id
    public static function get_organization_training_type($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_training_type";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

//    get banned orginization designations training camp by id
    public static function get_organization_training_camp($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_training_camp";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

//    get banned orginization by id
    public static function get_banned_organizations_name($org_id) {
        //print_r($org_id);        exit();
        $DB = Database::instance();
        $sql = "SELECT org_name FROM 
                    banned_organizations WHERE org_id = {$org_id} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = !empty($results->org_name) ? $results->org_name : 'Unknown';

        return $name;
    }

//    get person project from category table
    public static function get_person_project_from_category($person_id) {
        //print_r($org_id);        exit();
        $DB = Database::instance();
        $sql = "SELECT project_id FROM 
                    person_category WHERE person_id = {$person_id} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $project_id = !empty($results->project_id) ? $results->project_id : 'Unknown';
        return $project_id;
    }

//    get project  orginization by project  id
    public static function get_project_organizations($project_id) {
        //print_r($project_id);        exit();
        $DB = Database::instance();
        $sql = "SELECT * FROM int_projects_organizations as t1
                    join banned_organizations as t2 on t2.org_id = t1.org_id
                    where project_id = {$project_id}";
        $results = $DB->query(Database::SELECT, $sql, FALSE)->as_array();
        return $results;
    }

    //    get Tokens by id
    public static function get_organizations_stance($id = Null) {
        //print_r($id);
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_organization_stance";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = !empty($results->organization_stance) ? $results->organization_stance : '';
            return $results;
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    get Tokens by id
    public static function get_token_name($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    email_tokens";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     *  Get the category name of the given id
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_category_name($cat_id) {
        $DB = Database::instance();
        $sql = "SELECT name 
                     FROM lu_category
                     WHERE category_id= {$cat_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";

        return $name;
    }

    /*
     *  get mobile company name by phone number
     */

    public static function get_company_name_by_mobile($mobile) {
        $DB = Database::instance();
        $sql = "SELECT company_name 
                     FROM mobile_companies as t1
                     inner join person_phone_number as t2 on t1.mnc=t2.mnc
                     WHERE phone_number= {$mobile}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = isset($results->company_name) && !empty($results->company_name) ? $results->company_name : "NA";

        return $name;
    }

    /*
     *  get mobile company name by phone number
     */

    public static function get_company_mnc_by_mobile($mobile) {
        $DB = Database::instance();
        $sql = "SELECT mnc 
                     FROM person_phone_number as t1 
                     WHERE phone_number= {$mobile}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $mnc = isset($results->mnc) && !empty($results->mnc) ? $results->mnc : '';
        return $mnc;
    }

    /* to get cdr upload status */

    //please update helper in utilities. // to get person Report Types /

    public static function get_person_report_types($id = NULL) {
        $reporttype[1] = "Interrogation Report";
        $reporttype[2] = "Investigation Report";
        $reporttype[3] = "Special Report";
        $reporttype[4] = "Intelligence Report";
        $reporttype[5] = "Ground Check Report";
        $reporttype[6] = "FIR Report";
        $reporttype[7] = "Recommendations/Remarks";
        $reporttype[8] = "Other";
        $reporttype[9] = "Mobile Report";
        $reporttype[10] = "PEDs Forensics";
        $reporttype[11] = "Other Evidence";
        $reporttype[12] = "Detension Orders";
        $reporttype[13] = "Interview Report";
        $reporttype[14] = "Death Certificate";
        $reporttype[15] = "DO Remarks";
        $reporttype[16] = "RO Remarks";

        if (!empty($id) && $id <= sizeof($reporttype)) {
            return $reporttype[$id];
        } else {
            return $reporttype;
        }
    }

    /* to get cdr upload status */

    public static function get_cdr_upload_status($id = NULL) {
        $cdr_upload_status[0] = "Saved But Not Parsed";
        // $cdr_upload_status[1] = "File Saved";
        $cdr_upload_status[1] = "Partially Parsed";
        $cdr_upload_status[2] = "Fully Parsed";
        $cdr_upload_status[3] = "Parsing Error";
        if ($id != NULL) {
            return $cdr_upload_status[$id];
        } else {
            return $cdr_upload_status;
        }
    }

    /* to get cdr upload error type */

    public static function get_cdr_upload_error_type($id = NULL) {
        $cdr_upload_error[0] = "No Error";
        $cdr_upload_error[1] = "Company Format Not Matched";
        $cdr_upload_error[2] = "A Party Not Matched";
        $cdr_upload_error[3] = "Multiple A Parties";
        $cdr_upload_error[4] = "IMEI Not Matched";
        $cdr_upload_error[5] = "Data Already Exist";
        $cdr_upload_error[6] = "Wrong Company Selected";
        $cdr_upload_error[7] = "Multiple Imei's";
        if ($id != NULL) {
            return $cdr_upload_error[$id];
        } else {
            return $cdr_upload_error;
        }
    }

    /* to get Network status */

    public static function get_network_status($newtork) {
        $network_status = 'Unknown';
        switch ($newtork) {
            case 1:
                $network_status = '2G';
                break;
            case 2:
                $network_status = '3G';
                break;
            case 3:
                $network_status = '4G/LTE';
                break;
        }
        return $network_status;
    }

    /* to get Connection status */

    public static function get_connection_status($status) {
        $connection_status = 'Unknown';
        switch ($status) {
            case 0:
                $connection_status = 'Attached';
                break;
            case 1:
                $connection_status = 'De-Attached';
                break;
            case 2:
                $connection_status = 'Purged';
                break;
        }
        return $connection_status;
    }

    /* to get identity name */

//    public static function get_identity_name($cat_id) {
//        $identity_name = '';
//        switch ($cat_id){
//            case 0:
//                $identity_name = 'Passport No';
//                break;
//            case 1:
//                $identity_name = 'Armed License No';
//                break;
//            case 2:
//                $identity_name = 'Driving License No';
//                break;
//            case 3:
//                $identity_name = 'NTN No';
//                break;
//            case 4:
//                $identity_name = 'CNIC No';
//                break;
//            case 5:
//                $identity_name = 'Afghan Refugee Card No';
//                break;
//        }
//        return $identity_name;
//    }
    /* to get case position name */
    public static function get_case_position_name($caseposition) {
        $identity_name = '';
        switch ($caseposition) {
            case 1:
                $identity_name = 'Under Investigation';
                break;
            case 2:
                $identity_name = 'Under Trial';
                break;
            case 3:
                $identity_name = 'Convicted';
                break;
            case 4:
                $identity_name = 'Discharged';
                break;
        }
        return $identity_name;
    }

    /* to get Accused position name */

    public static function get_accused_position_name($caseposition) {
        $identity_name = '';
        switch ($caseposition) {
            case 1:
                $identity_name = 'Under Investigation';
                break;
            case 2:
                $identity_name = 'Under Trial';
                break;
            case 3:
                $identity_name = 'Convicted';
                break;
            case 4:
                $identity_name = 'Discharged';
                break;
        }
        return $identity_name;
    }

    /* to get Report Type */

    public static function get_report_type_name($caseposition) {
        $identity_name = '';
        switch ($caseposition) {
            case 2:
                $identity_name = 'Investigation Report';
                break;
            case 3:
                $identity_name = 'Special Report';
                break;
            case 4:
                $identity_name = 'Intelligance Report';
                break;
            case 5:
                $identity_name = 'Ground Check Report';
                break;
            case 5:
                $identity_name = 'FIR Report';
                break;
            case 5:
                $identity_name = 'Recommendations/Remarks';
                break;
            case 5:
                $identity_name = 'Other';
                break;
        }
        return $identity_name;
    }

    /*    get District records */

    public static function get_district($id = Null) {
        $DB = Database::instance();
        if($id>=237 and $id<=240)
        {    
            $sql = "SELECT t1.* , t1.name as d_name FROM  district as t1"
                    . " JOIN region as t2 on t1.region_id = t2.region_id"
                    . " Where 1 ";
        }else{ 
            $sql = "SELECT t1.* , t1.name as d_name FROM  district as t1"
                    . " JOIN region as t2 on t1.region_id = t2.region_id"
                    . " Where t2.province_id = 1 ";
        }
        if (!empty($id)) {
            $sql .= " AND t1.district_id = {$id} LIMIT 1";
          //   print_r($sql); exit;
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->d_name) && !empty($results->d_name) ? $results->d_name : "Unknown";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
            //print_r($results); exit;
        }
        return $results;
    }

    /*    get District records by region id */

    public static function get_region_district($region_id) {
        $DB = Database::instance();
        // print_r($region_id); exit;
        if (!empty($region_id)) {
            if ($region_id != 11) {
                $sql = "SELECT * FROM 
                    district where region_id = {$region_id}";
            } else {
                $sql = "SELECT * FROM 
                    district";
            }
            $results = $DB->query(Database::SELECT, $sql, False)->as_array();
            return $results;
        }
    }

    /*    get police records by region id */

    public static function get_region_police($region_id) {
        $DB = Database::instance();
        // print_r($region_id); exit;
        if (!empty($region_id)) {
            if ($region_id != 11) {
                $sql = "SELECT * FROM 
                    ctd_police_station where region_id = {$region_id}";
            } else {
                $sql = "SELECT * FROM 
                    ctd_police_station";
            }
            $results = $DB->query(Database::SELECT, $sql, False)->as_array();
            return $results;
        }
    }

    /*    get District records */

    public static function get_user_district($id)
    {
        $DB = Database::instance();
        $sql = "SELECT  name as d_name FROM 
                    district WHERE district_id = {$id} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->d_name) && !empty($results->d_name) ? $results->d_name : "Unknown";
    }
         /*    get District records */

    public static function get_user_place_of_posting($user_id) {
        $DB = Database::instance();
        $sql = "SELECT  posted  FROM 
                    users_profile WHERE user_id = {$user_id} ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->posted) && !empty($results->posted) ? $results->posted : 0;
        return $results;
    }

    /*    get sect records */

    public static function get_sect($id = Null, $religion_id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_sect";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->sect) && !empty($results->sect) ? $results->sect : "Unknown";
        } elseif (!empty($religion_id)) {
            $sql .= " WHERE religion_id = {$religion_id} ";
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get religion records */

    public static function get_religion($id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_religion";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->religion) && !empty($results->religion) ? $results->religion : "Unknown";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get marital_status records */

    public static function get_marital_status($id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_marital_status";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->marital_status) && !empty($results->marital_status) ? $results->marital_status : "Unknown";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get sensitive dept list records */

    public static function get_sensitive_dept($id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_sensitive_departments";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->department_name) && !empty($results->department_name) ? $results->department_name : "";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get caste records */

    public static function get_caste($id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_caste";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->caste) && !empty($results->caste) ? $results->caste : "Unknown";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get District records */

    public static function get_punjab_police_station($id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    police_stations";
        if (!empty($id)) {
            $sql .= " WHERE ps_id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->ps_name) && !empty($results->ps_name) ? $results->ps_name : "Unknown";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get USER LOGIN COUNT records */

    public static function get_login_count($user_id) {
        $DB = Database::instance();
        $sql = "SELECT logins FROM users WHERE id= {$user_id}";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->logins) && !empty($results->logins) ? $results->logins : 0;
        return $count;
    }

    /*    get USER LOGIN COUNT records */

    public static function get_nadra_profile_stat($month = Null) {
        $DB = Database::instance();
        $sql = "SELECT sum(t1.count) as total_request ,t1.region_id , t2.name FROM nadra_profile_stats as t1 "
                . "join region as t2 on t2.region_id = t1.region_id ";
        if (!empty($month)) {
            $sql .= " WHERE t1.date = '{$month}'";
        }
        $sql .= " group by t1.region_id";

        //print_r($sql); exit;
        $results = DB::query(Database::SELECT, $sql, FALSE)->execute()->as_array();
        //$results = $DB->query(Database::SELECT, $sql, TRUE)->as_object();
        return $results;
    }

    /*    get USER REQUEST COUNT records */

    public static function get_request_count($user_id) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as cnt FROM user_request
                WHERE user_id= {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countrequest = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        return $countrequest;
    }

    /*    get USER Favourite Person COUNT records */

    public static function get_favourite_person_count($user_id) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as cnt FROM user_favorite_person
                WHERE user_id= {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countperson = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        return $countperson;
    }

    /*    get USER Favourite user COUNT records */

    public static function get_favourite_user_count($user_id) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as cnt FROM user_favourite_user
                WHERE user_id= {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countuser = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        return $countuser;
    }

    /*    get user's white person records */

    public static function get_users_white_person($user_id = NULL ,$posting_filter=Null) {
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        if(!empty($posting_filter)) {
            $posting=$posting_filter;
        }
        else{
            $posting = $login_user_profile->posted;
        }

        $where_clause = 'where 1';
        if ($user_id == NULL) {
            if ($permission == 3 || $permission == 2|| $permission == 1) {
                $result = explode('-', $posting);
                switch ($result[0]) {
                    case 'h':
                        $where_clause = "where 1";
                        break;
                    case 'r':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                        break;
                    case 'd':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                        break;
                    case 'p':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                        break;
                }
            } elseif ($permission == 4) {
                $where_clause = "where user_id = {$login_user->id}";
            }
        } else {
            $where_clause = "where user_id = {$user_id}";
        }
        $sql = "SELECT COUNT(category_id) as COUNT 
                FROM person_category
                {$where_clause}
                and category_id=0";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countWhite = isset($results->COUNT) && !empty($results->COUNT) ? $results->COUNT : 0;
        //return 0;
        return $countWhite;
    }

    /*    get user's grey person records */

    public static function get_users_grey_person($user_id = NULL, $posting_filter=Null) {
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        if(!empty($posting_filter)) {
            $posting=$posting_filter;
        }
        else{
            $posting = $login_user_profile->posted;
        }

        $where_clause = 'where 1';
        if ($user_id == Null) {
            if ($permission == 3 || $permission == 2|| $permission == 1) {
                $result = explode('-', $posting);
                switch ($result[0]) {
                    case 'h':
                        $where_clause = "where 1";
                        break;
                    case 'r':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                        break;
                    case 'd':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                        break;
                    case 'p':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                        break;
                }
            } elseif ($permission == 4) {

                $where_clause = "where user_id = {$login_user->id}";
            }
        } else {
            $where_clause = "where user_id = {$user_id}";
        }

        $sql = "SELECT COUNT(category_id) as COUNT 
                FROM person_category 
               {$where_clause}
               and category_id=1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countGrey = isset($results->COUNT) && !empty($results->COUNT) ? $results->COUNT : 0;
        return $countGrey;
    }

    /*    get user's Black person records */

    public static function get_users_black_person($user_id = Null, $posting_filter=Null) {
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
//        echo '<pre>';
//        print_r($user_id);
//        print_r($posting_filter);
//        exit;
        if(!empty($posting_filter)) {
            $posting=$posting_filter;
        }
        else{
        $posting = $login_user_profile->posted;
        }


        $where_clause = 'where 1';
        if ($user_id == Null) {

            if ($permission == 2 || $permission == 3|| $permission == 1) {

                $result = explode('-', $posting);

                switch ($result[0]) {
                    case 'h':
                        $where_clause = "where 1";
                        break;
                    case 'r':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                        break;
                    case 'd':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                        break;
                    case 'p':
                        $where_clause = "where user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                        break;
                }
            } elseif ($permission == 4) {



                $where_clause = "where user_id = {$login_user->id}";
            }
        } else {
            $where_clause = "where user_id = {$user_id}";
        }


        $DB = Database::instance();
        $sql = "SELECT COUNT(category_id) as COUNT 
                FROM person_category 
                {$where_clause} and 
                category_id=2";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countBlack = isset($results->COUNT) && !empty($results->COUNT) ? $results->COUNT : 0;
        return $countBlack;
    }

    //    get Request Type
    public static function get_request_type($id = Null) {

        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    email_templates_type";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->email_type_name) && !empty($results->email_type_name) ? $results->email_type_name : "Unknown";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        //print_r($results); exit;
        return $results;
    }

    //    get nadra request status
    public static function get_nadra_request_status($cnic) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    user_request where requested_value = '{$cnic}' and user_request_type_id=8 && status <> 2 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->request_id) && !empty($results->request_id) ? 1 : 0;
        // print_r($results); exit;
        return $results;
    }
    //    get family tree request status
    public static function get_famlytree_request_status($cnic) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    user_request where requested_value = '{$cnic}' and user_request_type_id=10";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->request_id) && !empty($results->request_id) ? 1 : 0;
        // print_r($results); exit;
        return $results;
    }

    /*    get region records */

    public static function get_region($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    region where province_id = 1 ";
        if (!empty($id)) {
            $sql .= " AND region_id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->name) && !empty($results->name) ? $results->name : "Un-Known";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get district names by id  */

    public static function get_district_name_by_id($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    district ";
        if (!empty($id)) {
            $sql .= " where district_id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->name) && !empty($results->name) ? $results->name : "Un-Known";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get region records */

    public static function get_province($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_province";
        if (!empty($id)) {
            $sql .= " WHERE province_id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->name) && !empty($results->name) ? $results->name : "Un-Known";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get education level */

    public static function get_education_level($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    lu_education_level";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->education_level) && !empty($results->education_level) ? $results->education_level : "Un-Known";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    get police stations records */

    public static function get_police_station($id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    ctd_police_station";
        if (!empty($id)) {
            $sql .= " WHERE region_id = {$id} LIMIT 1";
        }
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*    get police stations records */

    public static function get_district_police_station($district_id = Null) {
        //print_r($id); exit;
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    police_stations ";
        if (!empty($district_id)) {
            $sql .= " WHERE district_id = {$district_id} ";
        }
        // print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*    get police stations records */

    public static function get_ps_name($id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    ctd_police_station WHERE id = {$id} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*    get head quarter records */

    public static function get_headquarter($id = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                    headquarter";
        if (!empty($id)) {
            $sql .= " WHERE id = {$id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->name) && !empty($results->name) ? $results->name : "Un-Known";
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*
     *  Get the user right level in percentage by user id
     */

    public static function get_user_right_level($user_id) {
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($user_id);
        if ($permission == 1) {
            $where = "where id not in (1,2,17,18)";
        } else {
            $where = "where id not in (1,2,14,15,16,17,18,19)";
        }
        $sql = "SELECT count(*) as tp
                     FROM  lu_user_access_type";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $total_permissions = $results->tp;
        $sql1 = "SELECT COUNT(*) as ap 
             FROM user_access_matrix
                WHERE permission=1 AND user_id= {$user_id}";
        $results1 = $DB->query(Database::SELECT, $sql1, TRUE)->current();
        $active_permissions = $results1->ap;
        $right_level = round(($active_permissions / $total_permissions) * 100, 2);

        return $right_level;
    }

    /*    get request status name */

    public static function get_request_status_name($id = Null) {
        $status = array();
        $status[0] = "Request in Queue";
        $status[1] = "Request Send";
        $status[2] = "Email Received";
        $status[3] = "E-Mail Sending Error";
        $status[4] = "Request Rejected";
        if (isset($id) && $id <= 4) {
            return $status[$id];
        } elseif (!empty($id)) {
            return "Unknown";
        } else {
            return $status;
        }
    }

    public static function get_request_status_name_ctfu($id = Null) {
        $status = array();
        $status[1] = "Request Not-Dispatched";
        $status[2] = "Request Dispatched";
        $status[3] = "Request Completed";
        if (isset($id) && $id <= 3) {
            return $status[$id];
        } elseif (!empty($id)) {
            return "Unknown";
        } else {
            return $status;
        }
    }

    /*    get request status name */

    public static function get_ctfu_request_status_name($id = Null) {
        $status = array();
        $status[1] = "Request Send";
        $status[2] = "Email Dispatched";
        $status[3] = "Request Rejected";
        if (isset($id) && $id <= 3) {
            return $status[$id];
        } elseif (!empty($id)) {
            return "Unknown";
        } else {
            return $status;
        }
    }

    /*    get nadra request status name */

    public static function get_nadra_request_status_name($id = Null) {
        if ($id == 1) {
            $result = "Request Send";
        } elseif ($id == 2) {
            $result = "Processed";
        } else {
            $result = "Unknown";
        }
        return $result;
    }

    /*    get request status name */

    public static function get_parsing_status_name($id = Null) {
        $status = array();
        $status[0] = "Response waiting";
        $status[1] = "E-Mail Format Error";
        $status[2] = "No Data found";
        $status[3] = "Parsing Error";
        $status[4] = "Waiting for Parsing";
        $status[5] = "Parsing Completed";
        $status[6] = "Partially Parsed";
        $status[7] = "Marked Complete";
        if (isset($id) && $id <= 7) {
            return $status[$id];
        } elseif (!empty($id)) {
            return "Unknown";
        } else {
            return $status;
        }
    }

    public static function get_parsing_error_details($request_id) {
        $DB = Database::instance();
        $sql = "SELECT (t1.error_type) AS error_type
                FROM  files AS t1
                WHERE t1.request_id = $request_id";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $error_type = isset($results->error_type) && $results->error_type ? $results->error_type : 0;
        $error_message = '';
        switch ($error_type) {
            case 0:
                $error_message = 'No Error';
                break;
            case 1:
                $error_message = 'Company Format Not Matched';
                break;
            case 2:
                $error_message = 'A Party Not Matched';
                break;
            case 3:
                $error_message = 'Multiple A parties';
                break;
            case 4:
                $error_message = 'IMEI Not Matched';
                break;
            case 5:
                $error_message = 'Data Already Exist';
                break;
        }
        return $error_message;
    }

    //Check imei number exist
    public static function check_imei_exist($imei) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.id) AS chk
                FROM  person_phone_device AS t1
                WHERE t1.imei_number= $imei";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

//    Check device number exist
    public static function check_device_number_exist($deviceid, $num) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.device_id) AS chk
                FROM  person_device_numbers AS t1
                WHERE t1.device_id= $deviceid AND t1.phone_number=$num";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

//    Check sim user exist
    public static function check_person_nadra_profile_exist($num) {
        $DB = Database::instance();
        $sql = "SELECT person_id AS chk
                FROM  person_nadra_profile AS t1
                WHERE t1.cnic_number=$num";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

//    Check sim user exist
    public static function check_sim_user_exist($num) {
        $DB = Database::instance();
        $sql = "SELECT person_id AS chk
                FROM  person_phone_number AS t1
                WHERE t1.phone_number=$num";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

//    Check device id against imei
    public static function get_device_id($imei) {
        $DB = Database::instance();
        $sql = "SELECT t1.id
                FROM  person_phone_device AS t1
                WHERE t1.imei_number= $imei";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->id) && $results->id ? $results->id : 0;
        return $chk;
    }

//    get device information
    public static function get_device_information($imei) {
        $DB = Database::instance();
        $sql = "SELECT *
                FROM  person_phone_device AS t1
                WHERE t1.imei_number= $imei";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

//    get Request sending failed
    public static function get_send_failed_request() {
        $DB = Database::instance();
        $sql = "SELECT *
                FROM  user_request AS t1
                WHERE t1.status= 3";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        //print_r($results); exit;
        return $results;
    }

//    get Request Processing error
    public static function get_processing_error_request() {
        $DB = Database::instance();
        $sql = "SELECT t1.request_id as req_id,t2.received_date as rec_date , t1.user_request_type_id as req_type_id
                FROM  user_request AS t1
                join email_messages as t2 on t2.message_id = t1.message_id
                WHERE t1.status=2 and t1.user_request_type_id !=8 and t1.processing_index in ( 1,2,3)";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

//    get subscriber against mobile number
    public static function get_subscribers_info($mobile) {
        $DB = Database::instance();
        $sql = "SELECT t1.phone_number,t1.imsi_number,t1.sim_activated_at,t1.status,t1.mnc,t1.connection_type,t2.first_name,t2.last_name,t2.address,t2.person_id,t3.cnic_number,t3.cnic_number_foreigner,t3.is_fingerprints_exist,t3.is_foreigner,t3.user_id
                FROM  person_phone_number AS t1
                inner join person as t2 on t1.sim_owner=t2.person_id         
                inner join person_initiate as t3 on t2.person_id=t3.person_id         
                WHERE t1.phone_number= $mobile";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        // echo "<pre>";     print_r($results); exit; 
        if (empty($results->cnic_number) && !empty($results->cnic_number_foreigner)) {

            $results->cnic_number = $results->cnic_number_foreigner;
            // $results->cnic_number=!empty($results->person_id) ?  Helpers_Person::get_person_cnic($results->person_id) : 0;
        }
        $act_date = isset($results->sim_activated_at) && $results->sim_activated_at ? $results->sim_activated_at : '00/00/0000';
        if ($act_date == '00/00/0000') {
            return $results;
        } else {
            $actdate = date("m/d/20y", strtotime($act_date));
            $results->actdate = $actdate;
            return $results;
        }
    }

//    get subscriber against mobile number
    public static function get_sim_last_used_imei($mobile) {
        $DB = Database::instance();
        $sql = "SELECT t2.imei_number,t2.phone_name
                FROM  person_device_numbers AS t1
               inner join person_phone_device as t2 on t1.device_id=t2.id               
                WHERE t1.phone_number= $mobile AND t1.is_active=1 ORDER BY t1.first_use ASC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = !empty($results->imei_number) ? $results->imei_number : -1;
        $results = !empty($results->phone_name) ? $results->phone_name : -1;
        return $results;
    }

    //    get cdr last date with msisdn
    public static function get_cdr_last_date_with_msisdn($mobile) {
        $DB = Database::instance();
        $sql = "SELECT t1.call_at
                FROM  person_call_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.call_at <> '') ORDER BY t1.call_at DESC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $enddatecall_1 = !empty($results->call_at) ? $results->call_at : "";
        $enddatecall = date("m/d/Y", strtotime($enddatecall_1));

        $DB1 = Database::instance();
        $sql1 = "SELECT t1.sms_at
                FROM  person_sms_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.sms_at <> '') ORDER BY t1.sms_at DESC LIMIT 1";
        $results1 = $DB1->query(Database::SELECT, $sql1, TRUE)->current();
        $enddatesms_1 = !empty($results1->sms_at) ? $results1->sms_at : '';
        $enddatesms = date("m/d/Y", strtotime($enddatesms_1));
        if (!empty($enddatecall_1)) {
            if (!empty($enddatesms_1)) {
                if (strtotime($enddatecall) > strtotime($enddatesms)) {
                    $enddate = $enddatecall;
                } else {
                    $enddate = $enddatesms;
                }
            } else {
                $enddate = $enddatecall;
            }
        } else {
            if (!empty($enddatesms_1)) {
                $enddate = $enddatesms;
            } else {
                $enddate = '';
            }
        }
        return $enddate;
    }

    //    get cdr last date with msisdn for sms details
    public static function get_sms_details_last_date_with_msisdn($mobile) {
        $DB1 = Database::instance();
        $sql1 = "SELECT t1.sms_at
                FROM  person_sms_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.sms_at <> '') ORDER BY t1.sms_at DESC LIMIT 1";
        $results1 = $DB1->query(Database::SELECT, $sql1, TRUE)->current();
        $enddatesms_1 = !empty($results1->sms_at) ? $results1->sms_at : '';
        $enddatesms = date("m/d/Y", strtotime($enddatesms_1));
        if (empty($enddatesms_1)) {
            return $enddatesms_1;
        } else {
            return $enddatesms;
        }
    }

    //    get cdr last date with imei
    public static function get_cdr_last_date_with_imei($imei) {
        $DB = Database::instance();
        $sql = "SELECT t1.call_at
                FROM  person_call_log AS t1             
                WHERE t1.imei_number= $imei AND (t1.call_at <> '') ORDER BY t1.call_at DESC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $enddatecall_1 = !empty($results->call_at) ? $results->call_at : "";
        $enddatecall = date("m/d/Y", strtotime($enddatecall_1));

        $DB1 = Database::instance();
        $sql1 = "SELECT t1.sms_at
                FROM  person_sms_log AS t1             
                WHERE t1.imei_number= $imei AND (t1.sms_at <> '') ORDER BY t1.sms_at DESC LIMIT 1";
        $results1 = $DB1->query(Database::SELECT, $sql1, TRUE)->current();
        $enddatesms_1 = !empty($results1->sms_at) ? $results1->sms_at : '';
        $enddatesms = date("m/d/Y", strtotime($enddatesms_1));

        if (!empty($enddatecall_1)) {
            if (!empty($enddatesms_1)) {
                if ($enddatecall > $enddatesms) {
                    $enddate = $enddatecall;
                } else {
                    $enddate = $enddatesms;
                }
            } else {
                $enddate = $enddatecall;
            }
        } else {
            if (!empty($enddatesms_1)) {
                $enddate = $enddatesms;
            } else {
                $enddate = '';
            }
        }



        return $enddate;
    }

    //    get cdr duration date with msisdn
    public static function get_cdr_duration_with_msisdn($mobile,$start_date,$end_date) {
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = date("Y-m-d", strtotime($end_date));
        
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';        
       
                
        $DB = Database::instance();
        $sql = "SELECT count(t1.call_at) total
                FROM  person_call_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.call_at <> '') AND t1.call_at > '$start_date' AND t1.call_at < '$end_date' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = !empty($results->total) ? $results->total : "";
        
        
                
        if(empty($results)){
             $sql = "SELECT count(t1.sms_at) total
                FROM  person_sms_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.sms_at <> '') AND t1.sms_at > '$start_date' AND t1.sms_at < '$end_date' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = !empty($results->total) ? $results->total : "";
        
        }
   
        return $results;
    }
    //    get cdr first date with msisdn
    public static function get_cdr_first_date_with_msisdn($mobile) {
        $DB = Database::instance();
        $sql = "SELECT t1.call_at
                FROM  person_call_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.call_at <> '') ORDER BY t1.call_at ASC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $firstdatecall_1 = !empty($results->call_at) ? $results->call_at : "";
        $firstdatecall = date("m/d/Y", strtotime($firstdatecall_1));

        $DB1 = Database::instance();
        $sql1 = "SELECT t1.sms_at
                FROM  person_sms_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.sms_at <> '') ORDER BY t1.sms_at ASC LIMIT 1";
        $results1 = $DB1->query(Database::SELECT, $sql1, TRUE)->current();
        $firstdatesms_1 = !empty($results1->sms_at) ? $results1->sms_at : '';
        $firstdatesms = date("m/d/Y", strtotime($firstdatesms_1));

        if (!empty($firstdatecall_1)) {
            if (!empty($firstdatesms_1)) {
                if ($firstdatecall < $firstdatesms) {
                    $firstdate = $firstdatecall;
                } else {
                    $firstdate = $firstdatesms;
                }
            } else {
                $firstdate = $firstdatecall;
            }
        } else {
            if (!empty($firstdatesms_1)) {
                $firstdate = $firstdatesms;
            } else {
                $firstdate = '';
            }
        }
        return $firstdate;
    }

    //    get cdr first date with msisdn
    public static function get_sms_details_first_date_with_msisdn($mobile) {
        $DB1 = Database::instance();
        $sql1 = "SELECT t1.sms_at
                FROM  person_sms_log AS t1             
                WHERE t1.phone_number= $mobile AND (t1.sms_at <> '') ORDER BY t1.sms_at ASC LIMIT 1";
        $results1 = $DB1->query(Database::SELECT, $sql1, TRUE)->current();
        $firstdatesms_1 = !empty($results1->sms_at) ? $results1->sms_at : '';
        $firstdatesms = date("m/d/Y", strtotime($firstdatesms_1));
        if (empty($firstdatesms_1)) {
            return $firstdatesms_1;
        } else {
            return $firstdatesms;
        }
    }

    //    get cdr first date with msisdn
    public static function get_cdr_first_date_with_imei($imei) {
        $DB = Database::instance();
        $sql = "SELECT t1.call_at
                FROM  person_call_log AS t1             
                WHERE t1.imei_number= $imei AND (t1.call_at <> '') ORDER BY t1.call_at ASC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $firstdatecall_1 = !empty($results->call_at) ? $results->call_at : "";
        $firstdatecall = date("m/d/Y", strtotime($firstdatecall_1));
        $DB1 = Database::instance();
        $sql1 = "SELECT t1.sms_at
                FROM  person_sms_log AS t1             
                WHERE t1.imei_number= $imei AND (t1.sms_at <> '') ORDER BY t1.sms_at ASC LIMIT 1";
        $results1 = $DB1->query(Database::SELECT, $sql1, TRUE)->current();
        $firstdatesms_1 = !empty($results1->sms_at) ? $results1->sms_at : '';
        $firstdatesms = date("m/d/Y", strtotime($firstdatesms_1));

        if (!empty($firstdatecall_1)) {
            if (!empty($firstdatesms_1)) {
                if ($firstdatecall < $firstdatesms) {
                    $firstdate = $firstdatecall;
                } else {
                    $firstdate = $firstdatesms;
                }
            } else {
                $firstdate = $firstdatecall;
            }
        } else {
            if (!empty($firstdatesms_1)) {
                $firstdate = $firstdatesms;
            } else {
                $firstdate = '';
            }
        }



        return $firstdate;
    }

////    get subscriber against mobile number
//    public static function get_subscribers_info($mobile) {
//        $DB = Database::instance();
//        $sql = "SELECT t1.phone_number,t1.imsi_number,t1.sim_activated_at,t1.status,t1.mnc,t1.connection_type,t2.first_name,t2.last_name,t2.cnic_number,t2.address,t3.phone_name,t3.imei_number
//                FROM  person_phone_number AS t1
//               inner join person as t2 on t1.sim_owner=t2.person_id
//               inner join person_phone_device as t3 on t2.person_id=t3.person_id
//               inner join person_device_numbers as t4 on t3.id=t4.device_id
//                WHERE t1.phone_number= $mobile && t4.phone_number=$mobile && t4.is_active=1";
//        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
//        $act_date = isset($results->sim_activated_at) && $results->sim_activated_at ? $results->sim_activated_at : '00/00/0000';
//        if ($act_date == '00/00/0000') {
//            return $results;
//        } else {
//            $actdate = date("m/d/20y", strtotime($act_date));
//            $results->actdate = $actdate;
//            return $results;
//        }
//    }
    //Daily request data
    public static function get_request_comparison() {
        $DB = Database::instance();
        $date2 = date("Y-m-d");
        $date = $date2 . ' 00:00:00';
        $date1 = $date2 . ' 23:59:59';
        //Today Total Request
        $query1 = "select count(request_id) today_total from user_request where created_at >= '$date' && created_at <= '$date1' and user_request_type_id != 8";
        //print_r($query1); exit;
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['today_total'] = $results->today_total;
        //today Send Request
        $query1 = "select count(request_id) today_send from user_request as t1 where t1.status in (1,2) and created_at >= '$date' && created_at <= '$date1' and user_request_type_id != 8";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['today_send'] = $results->today_send;
        //today Error Request
        $query1 = "select count(request_id) today_error from user_request as t1 where t1.status = 3 and  created_at >= '$date' && created_at <= '$date1' and user_request_type_id != 8";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['today_error'] = $results->today_error;
        //Today Pendin request
        $data['today_pending'] = $data['today_total'] - $data['today_send'];

        return $data;
    }

    //Total request data
    public static function get_total_request_comparison() {
        $DB = Database::instance();
        //Today Total Request
        $query1 = "select count(request_id) total from user_request where user_request_type_id != 8";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['total'] = $results->total;
        //today Send Request
        $query2 = "select count(request_id) send from user_request as t1 where t1.status in (1,2) and t1.user_request_type_id != 8";
        $results2 = $DB->query(Database::SELECT, $query2, TRUE)->current();
        $data['send'] = $results2->send;
        //today Error Request
        $query3 = "select count(request_id) error from user_request as t1 where t1.status = 3  and t1.user_request_type_id != 8";
        $results3 = $DB->query(Database::SELECT, $query3, TRUE)->current();
        $data['error'] = $results3->error;
        //Today Pendin request
        $data['pending'] = $data['total'] - $data['send'];

        return $data;
    }

    //Total request data
    public static function get_total_received_comparison() {
        $DB = Database::instance();
        //Today Total Request
        $query1 = "select count(request_id) total from user_request as t1 join email_messages as t2 on t2.message_id = t1.message_id where t1.status in (1,2) and user_request_type_id != 8";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['total'] = $results->total;
        //today Send Request
        $query2 = "select count(request_id) send from user_request as t1 join email_messages as t2 on t2.message_id = t1.message_id where t1.status = 2 and t1.user_request_type_id != 8";
        $results2 = $DB->query(Database::SELECT, $query2, TRUE)->current();
        $data['received'] = $results2->send;
        //Today Pending request
        $data['pending'] = $data['total'] - $data['received'];

        return $data;
    }

    public static function custom_echo($x, $length) {
        if (strlen($x) <= $length) {
            echo $x;
        } else {
            $y = substr($x, 0, $length) . ' ...';
            echo $y;
        }
    }

    //Daily request data
    public static function get_parsing_comparison() {
        $DB = Database::instance();
        $date2 = date("Y-m-d");
        $date = $date2 . ' 00:00:00';
        $date1 = $date2 . ' 23:59:59';
        //Today Total Recieved
        $query1 = "SELECT count(t1.message_id) as today_total 
                      FROM  email_messages as t1
                      join user_request as t2 on t2.message_id = t1.message_id 
                      where t2.user_request_type_id != 8 and t2.status=2 
                      and  t1.received_date >= '$date' && t1.received_date <= '$date1'";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['today_total'] = $results->today_total;
        // print_r($query1); exit;
        //today parsing completed
        $query2 = "SELECT count(t1.message_id) as today_complete  
                      FROM  email_messages as t1
                      join user_request as t2 on t2.message_id = t1.message_id 
                      where t2.user_request_type_id != 8 and t2.status=2 
                      and t1.received_date >= '$date' && t1.received_date <= '$date1'
                      and t2.processing_index in (5,6,7)";    // 5=fully parsed,6=partially parsed, 7=mark completed
        $results2 = $DB->query(Database::SELECT, $query2, TRUE)->current();
        $data['today_complete'] = $results2->today_complete;
        //today parsign Errors 
        $query3 = "SELECT count(t1.message_id) as today_error  
                      FROM  email_messages as t1
                      join user_request as t2 on t2.message_id = t1.message_id 
                      where t2.user_request_type_id != 8 and t2.status=2 
                      and t1.received_date >= '$date' && t1.received_date <= '$date1'
                      and t2.processing_index in (1,2,3)";    // 1=email format error,2=no data record, 3=parsing error
        $results3 = $DB->query(Database::SELECT, $query3, TRUE)->current();
        $data['today_error'] = $results3->today_error;
        //Today Pendin request
        $data['today_pending'] = $data['today_total'] - $data['today_complete'] - $data['today_error'];

        return $data;
    }

    public static function get_total_parsing_comparison() {
        $DB = Database::instance();
        //Today Total Request
        $query1 = "SELECT count(t1.message_id) as total 
                   FROM  email_messages as t1
                      join user_request as t2 on t2.message_id = t1.message_id 
                      where t2.user_request_type_id != 8 and t2.status=2 ";
        $results = $DB->query(Database::SELECT, $query1, TRUE)->current();
        $data['total'] = $results->total;
        // print_r($query1); exit;
        //today Send Request
        $query2 = "SELECT count(t1.message_id) as total_complete  
                      FROM  email_messages as t1
                      join user_request as t2 on t2.message_id = t1.message_id 
                      where t2.user_request_type_id != 8 and t2.status=2  
                      and t2.processing_index in (5,6,7)";
        $results2 = $DB->query(Database::SELECT, $query2, TRUE)->current();
        $data['total_complete'] = $results2->total_complete;
        //today Error Request
        $query3 = "SELECT count(t1.message_id) as total_error  
                      FROM  email_messages as t1
                      join user_request as t2 on t2.message_id = t1.message_id 
                      where t2.user_request_type_id != 8 and t2.status=2 
                      and t2.processing_index in (1,2,3)";
        $results3 = $DB->query(Database::SELECT, $query3, TRUE)->current();
        $data['total_error'] = $results3->total_error;
        //Today Pendin request
        $data['total_pending'] = $data['total'] - $data['total_complete'] - $data['total_error'];

        return $data;
    }

    /* irfan New code */

    public static function current_password($passowrd) {
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();
        $login_user_id = $login_user->id;
        $query = "select count(password) as count from users where id = {$login_user_id} and  password = '$passowrd' AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        //print_r($query); exit;
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        return $results->count;
    }

    //E-Mail duplicate check
    public static function email_duplicate($email) {
        $DB = Database::instance();
        $query = "select count(email) as count from users where email = '$email' AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        return $results->count;
    }

    //User Name duplicate check
    public static function username_duplicate($user) {
        $DB = Database::instance();
        $query = "select count(username) as count from users where username = '$user' AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        return $results->count;
    }

    //Request Re-Send
    public static function request_resend($request_id) {
        $DB = Database::instance();
        //$result = DB::update("person")->set(array('view_count' =>'view_count' + 1))->where('person_id', '=', $person_id)->execute();
        $sql = "UPDATE user_request as t1 set t1.status = 0, t1.processing_index = 0
                where t1.request_id = {$request_id}";
        $result = $DB->query(Database::UPDATE, $sql, TRUE);
        return $result;
    }

    //Request Re-Queue Verisys
    public static function request_requeue_verisys($request_id) {
        $DB = Database::instance();
        //$result = DB::update("person")->set(array('view_count' =>'view_count' + 1))->where('person_id', '=', $person_id)->execute();
        $sql = "UPDATE user_request as t1 set t1.status = 1, t1.processing_index = 0
                where t1.request_id = {$request_id}";
        $result = $DB->query(Database::UPDATE, $sql, TRUE);
        return $result;
    }

    //Request Reply sent
    public static function request_reply_sent($request_id) {
        $DB = Database::instance();
        //$result = DB::update("person")->set(array('view_count' =>'view_count' + 1))->where('person_id', '=', $person_id)->execute();
        $sql = "UPDATE user_request as t1 set t1.reply = 1
                where t1.request_id = {$request_id}";
        $result = $DB->query(Database::UPDATE, $sql, TRUE);
        return $result;
    }

    //To Delete user request
    public static function request_delete($request_id) {
        $DB = Database::instance();

        $sql = "select message_id from  user_request as t1
                where t1.request_id = {$request_id}";
        $result = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $message_id = (isset($result->message_id) && !empty($result->message_id)) ? $result->message_id : '0';
        // echo $message_id; exit;

        $sql1 = "delete from  user_request where request_id = {$request_id}";
        $result1 = $DB->query(Database::DELETE, $sql1, TRUE);

        $sql2 = "delete from  email_messages  where message_id = {$message_id}";
        $result2 = $DB->query(Database::DELETE, $sql2, TRUE);
        return $result2;
    }

    //total today
    public static function get_today_request_count() {
        $DB = Database::instance();
        $query = "select sum(total) as total from request_send_today where DATE(created_at)= CURDATE();";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data = $results->total;
        return $data;
    }

    //total today
    public static function get_family_tree_sent_requests() {
        $DB = Database::instance();
        $query = "select count(request_id) as total from user_request as t1 where t1.status = 1 and t1.user_request_type_id = 10;";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data = $results->total;
        return $data;
    }

    //Daily Request send count
    public static function get_request_send_today() {
        $DB = Database::instance();
        $date2 = date("Y-m-d");
        //$date2 = '2018-04-30 ';//date("Y-m-d");
        $date = $date2 . ' 00:00:00';
        $date1 = $date2 . ' 23:59:59';
        //Today Mobilink Normal Request Count
        $query_mobilink_normal = "select total as mobilink_normal from request_send_today where company_name = 1 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_mobilink_normal = $DB->query(Database::SELECT, $query_mobilink_normal, TRUE)->current();

        $data['mobilink_normal'] = !empty($results_mobilink_normal) ? $results_mobilink_normal->mobilink_normal : 0;
        //Today Mobilink Medium Request Count
        $query_mobilink_medium = "select total as mobilink_medium from request_send_today where company_name = 1 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_mobilink_medium = $DB->query(Database::SELECT, $query_mobilink_medium, TRUE)->current();
        $data['mobilink_medium'] = !empty($results_mobilink_medium) ? $results_mobilink_medium->mobilink_medium : 0;
        //Today Mobilink High Request Count
        $query_mobilink_high = "select total as mobilink_high from request_send_today where company_name = 1 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_mobilink_high = $DB->query(Database::SELECT, $query_mobilink_high, TRUE)->current();
        $data['mobilink_high'] = !empty($results_mobilink_high) ? $results_mobilink_high->mobilink_high : 0;

        //Today warid Normal Request Count
        $query_warid_normal = "select total as warid_normal from request_send_today where company_name = 7 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_warid_normal = $DB->query(Database::SELECT, $query_warid_normal, TRUE)->current();
        $data['warid_normal'] = !empty($results_warid_normal) ? $results_warid_normal->warid_normal : 0;
        //Today warid Medium Request Count
        $query_warid_medium = "select total as warid_medium from request_send_today where company_name = 7 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_warid_medium = $DB->query(Database::SELECT, $query_warid_medium, TRUE)->current();
        $data['warid_medium'] = !empty($results_warid_medium) ? $results_warid_medium->warid_medium : 0;
        //Today warid High Request Count
        $query_warid_high = "select total as warid_high from request_send_today where company_name = 7 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_warid_high = $DB->query(Database::SELECT, $query_warid_high, TRUE)->current();
        $data['warid_high'] = !empty($results_warid_high) ? $results_warid_high->warid_high : 0;

        //Today ufone Normal Request Count
        $query7 = "select total as ufone_normal from request_send_today where company_name = 3 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results7 = $DB->query(Database::SELECT, $query7, TRUE)->current();
        $data['ufone_normal'] = !empty($results7) ? $results7->ufone_normal : 0;
        //Today ufone Medium Request Count
        $query8 = "select total as ufone_medium from request_send_today where company_name = 3 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results8 = $DB->query(Database::SELECT, $query8, TRUE)->current();
        $data['ufone_medium'] = !empty($results8) ? $results8->ufone_medium : 0;
        //Today ufone High Request Count
        $query9 = "select total as ufone_high from request_send_today where company_name = 3 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results9 = $DB->query(Database::SELECT, $query9, TRUE)->current();
        $data['ufone_high'] = !empty($results9) ? $results9->ufone_high : 0;

        //Today telenor Normal Request Count
        $query10 = "select total as telenor_normal from request_send_today where company_name = 6 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results10 = $DB->query(Database::SELECT, $query10, TRUE)->current();
        $data['telenor_normal'] = !empty($results10) ? $results10->telenor_normal : 0;
        //Today telenor Medium Request Count
        $query11 = "select total as telenor_medium from request_send_today where company_name = 6 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results11 = $DB->query(Database::SELECT, $query11, TRUE)->current();
        $data['telenor_medium'] = !empty($results11) ? $results11->telenor_medium : 0;
        //Today telenor High Request Count
        $query12 = "select total as telenor_high from request_send_today where company_name = 6 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results12 = $DB->query(Database::SELECT, $query12, TRUE)->current();
        $data['telenor_high'] = !empty($results12) ? $results12->telenor_high : 0;

        //Today zong Normal Request Count
        $query_zong_normal = "select total as zong_normal from request_send_today where company_name = 4 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_zong_normal = $DB->query(Database::SELECT, $query_zong_normal, TRUE)->current();
        $data['zong_normal'] = !empty($results_zong_normal) ? $results_zong_normal->zong_normal : 0;
        //Today zong Medium Request Count
        $query_zong_medium = "select total as zong_medium from request_send_today where company_name = 4 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_zong_medium = $DB->query(Database::SELECT, $query_zong_medium, TRUE)->current();
        $data['zong_medium'] = !empty($results_zong_medium) ? $results_zong_medium->zong_medium : 0;
        //Today zong High Request Count
        $query_zong_high = "select total as zong_high from request_send_today where company_name = 4 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_zong_high = $DB->query(Database::SELECT, $query_zong_high, TRUE)->current();
        $data['zong_high'] = !empty($results_zong_high) ? $results_zong_high->zong_high : 0;
        //Today PTCL Normal Request Count
        $query_ptcl_normal = "select total as ptcl_normal from request_send_today where company_name = 11 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_ptcl_normal = $DB->query(Database::SELECT, $query_ptcl_normal, TRUE)->current();
        $data['ptcl_normal'] = !empty($results_ptcl_normal) ? $results_ptcl_normal->ptcl_normal : 0;
        //Today ptcl Medium Request Count
        $query_ptcl_medium = "select total as ptcl_medium from request_send_today where company_name = 11 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_ptcl_medium = $DB->query(Database::SELECT, $query_ptcl_medium, TRUE)->current();
        $data['ptcl_medium'] = !empty($results_ptcl_medium) ? $results_ptcl_medium->ptcl_medium : 0;
        //Today ptcl High Request Count
        $query_ptcl_high = "select total as ptcl_high from request_send_today where company_name = 11 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_ptcl_high = $DB->query(Database::SELECT, $query_ptcl_high, TRUE)->current();
        $data['ptcl_high'] = !empty($results_ptcl_high) ? $results_ptcl_high->ptcl_high : 0;
        //Today international Normal Request Count
        $query_international_normal = "select total as international_normal from request_send_today where company_name = 12 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_international_normal = $DB->query(Database::SELECT, $query_international_normal, TRUE)->current();
        $data['international_normal'] = !empty($results_international_normal) ? $results_international_normal->international_normal : 0;
        //Today international Medium Request Count
        $query_international_medium = "select total as international_medium from request_send_today where company_name = 12 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_international_medium = $DB->query(Database::SELECT, $query_international_medium, TRUE)->current();
        $data['international_medium'] = !empty($results_international_medium) ? $results_international_medium->international_medium : 0;
        //Today international High Request Count
        $query_international_high = "select total as international_high from request_send_today where company_name = 12 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_international_high = $DB->query(Database::SELECT, $query_international_high, TRUE)->current();
        $data['international_high'] = !empty($results_international_high) ? $results_international_high->international_high : 0;
        //Today nadra Normal Request Count
        $query_nadra_normal = "select total as nadra_normal from request_send_today where company_name = 13 and request_priority = 1 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_nadra_normal = $DB->query(Database::SELECT, $query_nadra_normal, TRUE)->current();
        $data['nadra_normal'] = !empty($results_nadra_normal) ? $results_nadra_normal->nadra_normal : 0;
        //Today nadra Medium Request Count
        $query_nadra_medium = "select total as nadra_medium from request_send_today where company_name = 13 and request_priority = 2 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_nadra_medium = $DB->query(Database::SELECT, $query_nadra_medium, TRUE)->current();
        $data['nadra_medium'] = !empty($results_nadra_medium) ? $results_nadra_medium->nadra_medium : 0;
        //Today nadra High Request Count
        $query_nadra_high = "select total as nadra_high from request_send_today where company_name = 13 and request_priority = 3 and (created_at > '{$date}' and created_at <= '{$date1}')";
        $results_nadra_high = $DB->query(Database::SELECT, $query_nadra_high, TRUE)->current();
        $data['nadra_high'] = !empty($results_nadra_high) ? $results_nadra_high->nadra_high : 0;
        //print_r($data); exit;
        return $data;
    }

    //Daily Request send count
    public static function get_company_last_response_date($mnc) {
        $DB = Database::instance();
        $query = "SELECT t2.received_date as response_date FROM user_request as t1
                           join email_messages as t2 on t2.message_id = t1.message_id 
                           where t1.company_name = {$mnc}
                           order by t2.received_date
                           desc limit 1";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $date = isset($results->response_date) ? $results->response_date : 0;
        return $date;
    }

    //password reset request Cancle
    public static function password_reset_request_cancel($user_id) {
        $DB = Database::instance();
        $sql = "UPDATE users set is_forget_reset = 0
                where id = {$user_id}";
        $result = $DB->query(Database::UPDATE, $sql, TRUE);
        return $result;
    }

    //Check imei number exist
    public static function check_number_exit_in_block_list($number) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.blocked_value) AS chk
                FROM  blocked_numbers AS t1
                WHERE t1.blocked_value= $number";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    /*    check is_foreign */

    public static function check_is_foreigner($pid = Null) {
        $DB = Database::instance();
        $sql = "SELECT is_foreigner
                FROM 
                    person_initiate";
        if (!empty($pid)) {
            $sql .= " WHERE person_id = {$pid} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->is_foreigner) && !empty($results->is_foreigner) ? $results->is_foreigner : '';
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*    check is_foreign */

    public static function check_is_foreigner_owner_with_mobile($mobile = Null) {
        $DB = Database::instance();
        $sql = "SELECT is_foreigner
                from  person_initiate AS pi 
                inner join person_phone_number AS pn on pn.sim_owner=pi.person_id";
        if (!empty($mobile)) {
            $sql .= " WHERE pn.phone_number = {$mobile} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $results = isset($results->is_foreigner) && !empty($results->is_foreigner) ? $results->is_foreigner : '';
        } else {
            $results = $DB->query(Database::SELECT, $sql, TRUE);
        }
        return $results;
    }

    /*   check company mnc */

    public static function check_mnc($post) {
        $company_name = 0;
        //https://numverify.com/signup?plan=17
        //test@gmail.com       nadema@temaba345  // only check valid number
        //http://www.numberportabilitylookup.com/register?s=
        //test@gmail.com       nadema@temaba345   myaser
        /////  yas.ctd nadema@temaba345
        //https://market.mashape.com/nixinfo/mobile-operator-and-circle-finder-api-mnp-supported-free
        //mirfan15ms  nadema@temaba345
        //$URL = 'https://www.hlr-lookups.com/api/?action=submitSyncLookupRequest&msisdn=+92' . $post['number'] . '&username=mirfan15ms&password=nadema@temaba345';
   /*     $URL = 'https://www.hlr-lookups.com/api/?action=submitSyncLookupRequest&msisdn=+92' . $post['number'] . '&username=naveedtech786&password=n@aveedtech786';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);
        $json = json_decode($result, true);

        if ($json['success'] == 1) {
            if (!empty($json['results'][0]['mnc'])) {
                switch ($json['results'][0]['mnc']) {
                    case 04:
                        $company_name = 4;
                        break;
                    case 01:
                        $company_name = 1;
                        break;
                    case 06:
                        $company_name = 6;
                        break;
                    case 03:
                        $company_name = 3;
                        break;
                    case 07:
                        $company_name = 7;
                        break;
                }
            } else {
                $company_name = '';
            }
        } else {
            $json = array();
        }

        //=> Second Check 
        if (empty($company_name)) {
            /////  test@temaba345
            //test@gmail.com    nadema@temaba345
            // test@gmail.com     chukar@12345678         
            // $username='[REDACTED]';
            //$password='39dd359c89562c020868b8d27e76028c';
            // $username='[REDACTED]';
            //$password='7d4d96b0229e9f2b4227cff012ade6bf';
            //$username='[REDACTED]';
            //$password='d88b6818e39e89421e0d71f4d6114c62';
            $username = '[REDACTED]';
            $password = '192910478edefd7ba137bea15aa542b9';
            $URL = 'https://lookups.twilio.com/v1/PhoneNumbers/%2B92' . $post['number'] . '?Type=carrier&Type=caller-name';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            $result = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
            curl_close($ch);
            $json = json_decode($result, true);

            if (!empty($json['carrier']['mobile_network_code'])) {
                $company_name = $json['carrier']['mobile_network_code'];

                switch ($company_name) {
                    case 04:
                        $company_name = 4;
                        break;
                    case 01:
                        $company_name = 1;
                        break;
                    case 06:
                        $company_name = 6;
                        break;
                    case 03:
                        $company_name = 3;
                        break;
                    case 07:
                        $company_name = 7;
                        break;
                }
            } else {
                $company_name = '';
            }
            //print_r($json);
        //
   }
        //// => Third Check 
*/ //close by me yaser
        if (empty($company_name)) {
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );

            //$url = file_get_contents("https://www.mybecharge.com/topup/ajax?cmd=get_operators&phoneNumber=+92".$post['number']."&pinNumber=&operatorid=", false, stream_context_create($arrContextOptions));
            try {
                $url = file_get_contents("https://www.mybecharge.com/topup/ajax?cmd=get_operators&phoneNumber=+92" . $post['number'], false, stream_context_create($arrContextOptions));
            } catch (Exception $e) {
                Model_ErrorLog::log(
                    'check_mnc',
                    'Failed to fetch operator info from mybecharge: ' . $e->getMessage(),
                    [
                        'phone_number' => strlen($post['number'] ?? '') > 4 ? substr($post['number'], -4) : '****'
                    ],
                    $e->getTraceAsString(),
                    'api_request_error',
                    'operator_lookup',
                    'warning'
                );
                $url = '';
            }
            //$url = "https://www.mybecharge.com/topup/ajax?cmd=get_operators&phoneNumber=+92".$post['number']."&pinNumber=&operatorid=";
            //echo $url;
            $content = $url; //file_get_contents($url);
            $json = json_decode($content, true);
            if (!empty($json['operator']))
                foreach ($json['operator'] as $item) {

                    if (isset($item['selected'])) {
                        switch ($item['name']) {
                            case 'Zong Pakistan':
                                $company_name = 4;
                                break;
                            case 'Jazz Pakistan':
                                $company_name = 1;
                                break;
                            case 'Telenor Pakistan':
                                $company_name = 6;
                                break;
                            case 'Ufone Pakistan':
                                $company_name = 3;
                                break;
                            case 'Warid Pakistan':
                                $company_name = 7;
                                break;
                        }
                    }
                }
        }       ////
        
        
        if (!empty($company_name)) {
            Helpers_Utilities::increament_check_company_count($company_name);
        }
        return $company_name;
    }

    //If Both CNIC have person profile in System
  /*  public static function replace_cnic_and_delete_profile($first_cnic, $second_cnic, $person_id_first_cnic, $person_id_second_cnic) {
        $DB = Database::instance();
        $query = "select * from person_phone_number where sim_owner = {$person_id_second_cnic} or person_id = {$person_id_first_cnic}";
        $results = DB::query(Database::SELECT, $query)->execute();
        foreach ($results as $data) {
            $sim_owner = $data['sim_owner'];
            $sim_user = $data['person_id'];
            if ($sim_owner == $sim_user) {
                
            }
            echo '<pre>';
            print_r($data);
        }
        exit;
        //update cnic by replacing with new one
        DB::update('person_initiate')->set(array('cnic_number' => $second_cnic))
                ->where('person_id', '=', $person_id_first_cnic)
                ->execute();
        //update cnic by replacing with new one
        DB::update('person_nadra_profile')->set(array('cnic_number' => $second_cnic))
                ->where('person_id', '=', $person_id_first_cnic)
                ->execute();
        //delete all data of second profile
        $sql = "DELETE FROM person WHERE person_id = {$person_id_second_cnic}";
        $sql1 = "DELETE FROM person_affiliations WHERE person_id= {$person_id_second_cnic}";
        $sql2 = "DELETE FROM person_assets WHERE person_id= {$person_id_second_cnic}";
        $sql3 = "DELETE FROM person_assets_url WHERE person_id= {$person_id_second_cnic}";
        $sql4 = "DELETE FROM person_banks WHERE person_id= {$person_id_second_cnic}";
        $sql5 = "DELETE FROM person_banks WHERE person_id= {$person_id_second_cnic}";
        //person call log
        $sql6 = "DELETE FROM person_category WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_category_history WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_criminal_record WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_detail_info WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_education WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_identities WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_income_sources WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_initiate WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_linked_projects WHERE person_id= {$person_id_second_cnic}";
        //person location history
        $sql6 = "DELETE FROM person_location_history WHERE person_id= {$person_id_second_cnic}";

        $sql6 = "DELETE FROM person_monthly_summary WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_nadra_profile WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_phone_device WHERE person_id= {$person_id_second_cnic}";
        //person phonenumber
        $sql6 = "DELETE FROM person_phone_number WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_pictures WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_relations WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_reports WHERE person_id= {$person_id_second_cnic}";
        //person sms log
        $sql6 = "DELETE FROM person_sms_log WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_social_links WHERE person_id= {$person_id_second_cnic}";
        //person summary
        $sql6 = "DELETE FROM person_summary WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM person_tags WHERE person_id= {$person_id_second_cnic}";
        $sql6 = "DELETE FROM user_favorite_person WHERE person_id= {$person_id_second_cnic}";
    }
*/
    public static function get_telenor_flag() {
        $DB = Database::instance();
        $sql = "select is_second from telco_emails WHERE mnc =6";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->is_second) ? $results->is_second : 0;
        return $results;
    }

    //get bank list
    public static function get_bank_list($id = Null) {
        $DB = Database::instance();
        $sql = "Select * from lu_banks as t";
        if (!empty($id)) {
            $sql .= " where t.id= {$id}";
            $result = DB::query(Database::SELECT, $sql)->execute()->current();
            $result = !empty($result) ? $result ['name'] : 'name';
        } else {
            $result = $DB->query(Database::SELECT, $sql, TRUE);
        }

        return $result;
    }

    //Check Users CNIC exist
    public static function check_user_cnic_exist($user_id) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.user_id) AS chk
                FROM  users_profile AS t1
                WHERE cnic_number is not null and  user_id = {$user_id}";
        // print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //Check User have changed password or not
    public static function password_change_required($user_id) {
        $DB = Database::instance();
        $sql = "SELECT is_password_changed
                FROM  users AS t1
                WHERE id = {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $data = isset($results->is_password_changed) && $results->is_password_changed ? $results->is_password_changed : 0;
        return $data;
    }

    // Encryption 256
    public static function encrypted_key($uID, $action) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'Irfan love CTD';
        $secret_iv = 'SEStoPakistan';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($uID, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($uID), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /* sql injection */

    public static function remove_injection($postdata) {
        return $postdata;
        if (is_array($postdata)) {
            //echo 'testd';
            foreach ($postdata as $key => $value) {
                $postdata[$key] = Helpers_Utilities::your_filter($value);
            }
        } else {
            //echo 'test';
            $postdata = Helpers_Utilities::your_filter($postdata);
        }
        return $postdata;
    }

    public static function your_filter($newVal) {
        if (!is_array($newVal)) {
            $newVal = trim($newVal);
            $newVal = htmlspecialchars($newVal);
            //$newVal = mysqli_real_escape_string($newVal);
            $newVal = Helpers_Escapstr::mres($newVal);
        }
        return $newVal;
    }

    public static function mres($value) {
        $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $value);
    }

    /* Block IP */

    public static function checkblockIP($ip, $username) {
        $DB = Database::instance();
        $sql = "select ip from LoginAttempts where ip = '{$ip}' and Username = '{$username}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        if (!empty($results))
            return 1;
        else
            return 0;
    }

    public static function checkblockIPforever($ip) {
        $DB = Database::instance();
        $sql = "select ip from LoginAttempts where ip = '{$ip}' and (Username is null OR Username = '')";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        if (!empty($results))
            return 1;
        else
            return 0;
    }

    public static function addblockIP($ip, $username) {
        $DB = Database::instance();
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('LoginAttempts', array('IP', 'Attempts', 'LastLogin', 'Username', 'is_block'))
                ->values(array($ip, 3, $date, $username, 1))
                ->execute();
    }

    public static function addblockIPapache($ip) {
        $DB = Database::instance();
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('LoginAttempts', array('IP', 'Attempts', 'LastLogin', 'is_block'))
                ->values(array($ip, 10, $date, 1))
                ->execute();
    }

    public static function your_php_validation($newVal, $type, $min = NULL, $max = NULL) {
        if (empty($newVal))
            return FALSE;
        if ($min != NULL && $max != NULL)
            if (strlen($newVal) >= $min && strlen($newVal) <= $max) {
                $true = TRUE;
            } else {
                return FALSE;
            }
        if ($true)
            switch ($type) {
                case 'alphanumric':
                    if (preg_match('/^[a-zA-Z0-9\s]+$/', $newVal)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'alphanumricdecimal':
                    if (preg_match('/^[a-zA-Z0-9\.\s]+$/', $newVal)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'alphanumricspecial':
                    if (preg_match('/^[a-zA-Z0-9\@\.\_\*\!\+\-\^\s]+$/', $newVal)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'integer':
                    if (ctype_digit($newVal)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'string':
                    if (is_string($newVal) && !ctype_digit($newVal)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'email':
                    if (filter_var($newVal, FILTER_VALIDATE_EMAIL)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'boolean':
                    if (filter_var($newVal, FILTER_VALIDATE_BOOLEAN)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
                case 'url':
                    if (filter_var($newVal, FILTER_VALIDATE_URL)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                    break;
            }
    }

    public static function setwetcookies() {
        $return_res = 0;
        if (!empty($_COOKIE['__utma'])) {
            if (!isset($_SESSION["__utma"])) {
                $_SESSION["__utma"] = $_COOKIE['__utma'];
            }

            if (!isset($_COOKIE["_utac"])) {
                if (!isset($_COOKIE["session"])) {
                    $secure_cook = '';
                } else
                    $secure_cook = 'aies_' . $_COOKIE['session'];

                $cookid = Helpers_Utilities::encrypted_key($secure_cook, "encrypt");
                setcookie("_utac", $cookid);
            }

            $cookid = Helpers_Utilities::encrypted_key($_COOKIE["_utac"], "decrypt");
            $result = str_replace("aies_", "", $cookid);
            if (!isset($_SESSION["secure_cook"]))
                $_SESSION["secure_cook"] = $result;

            if ($_SESSION["secure_cook"] != $result && $_SESSION["__utma"] != $_COOKIE['__utma']) {
                if (!isset($_SESSION["attempts"]))
                    $_SESSION["attempts"] = 0;
                $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
                $return_res = 1;
            }
            //$params1 = session_set_cookie_params(time()+60*60*24*365, '', 'www.example.com', TRUE, TRUE);
            //$params = session_get_cookie_params();             
        }else {
            if (!isset($_SESSION["attempts"]))
                $_SESSION["attempts"] = 0;

            $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
            //$return_res = 1;
            $_COOKIE['__utma'] = 'afdjlskdfjoiqwenvheroiwc';
        }

        //  echo '<br>' . $_SESSION["secure_cook"] . '  ==>  ' . $result;        
        // echo '<br>' . $_SESSION["__utma"] . '  ==>  ' . $_COOKIE['__utma']; 

        return $return_res;
    }

    /*
      CREATE TABLE `slow_query_log_data_2` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `host_name` varchar(45) DEFAULT NULL,
      `query_time` varchar(45) DEFAULT NULL,
      `rows_sent` int(11) DEFAULT NULL,
      `rows_examined` bigint(50) DEFAULT NULL,
      `query_text` varchar(5000) DEFAULT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB AUTO_INCREMENT=494625 DEFAULT CHARSET=utf8;
     */

    public static function insert_slow_query_log_data($data) {
        $host = isset($data['host']) ? $data['host'] : '';
        $time = isset($data['time']) ? $data['time'] : '';
        $rows_sent = isset($data['rows_sent']) ? $data['rows_sent'] : '';
        $rows_examined = isset($data['rows_examined']) ? $data['rows_examined'] : '';
        $query = isset($data['query']) ? preg_replace('!\s+!', ' ', $data['query']) : '';
        $query = DB::insert('slow_query_log_data_last_50', array('host_name', 'query_time', 'rows_sent', 'rows_examined', 'query_text'))
                ->values(array($host, $time, $rows_sent, $rows_examined, $query))
                ->execute();
        return $query[1];
    }

    // get user name form ip table
    public static function get_user_name_from_ip($ip_address) {
        $DB = Database::instance();
        $sql = "SELECT t1.user_name as user_name FROM lu_ip_user_list as t1
                where t1.ip_address = '$ip_address'";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $results = isset($results->user_name) && !empty($results->user_name) ? $results->user_name : "Unknown";
        return $results;
    }

    public static function check_file_from_blacklist($image) {
        $blacklist = array(".php", ".phtml", ".php3", ".php4", ".js", ".shtml", ".pl", ".py");
        // $blacklist = array(".jpg");
        foreach ($blacklist as $file) {
            if (preg_match("/$file/", $image['name'])) {
                $error = 'Invalid file extension';
                throw new Exception($error);
            }
        }
    }

    //special group for developers
    public static function check_user_id_developers($user_id) {
        $developers = array(842, 137, 2031, 2603);
        if (in_array($user_id, $developers)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //request count against project id
    public static function project_request_count($project_id, $data) {

        $DB = Database::instance();
        //dates filter
        $serach_date = ' ';
        //if end date is not selected then user current date as end date
        if (empty($data['enddate'])) {
            $data['enddate'] = date("Y-m-d");
        }
        if (!empty($data['startdate'])) {
            $start_date = date("Y-m-d", strtotime($data['startdate']));
            $end_date = date("Y-m-d", strtotime($data['enddate']));

            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 23:59:59';
            $serach_date = " and created_at between '{$start_date}' and '{$end_date}' ";
        }
        $sql = "SELECT count(request_id) as requests "
                . "FROM user_request where project_id = {$project_id} $serach_date";
//        echo '<pre>';
//        print_r($sql);
//        exit;

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->requests) && !empty($results->requests) ? $results->requests : 0;
        return $count;
    }

    // get user name form ip table
    public static function get_bts_longlat($company_name, $cell_id, $lac_id) {
        $DB = Database::instance();
        switch ($company_name) {
            case 'zong':
                $table_name = " lu_bts_data_zong ";
                break;
            case 'telenor':
                $table_name = " lu_bts_data_telenor ";
                break;
        }
        $sql = "select * from {$table_name} where cell_id = {$cell_id} and lac_id ={$lac_id};";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    public static function get_request_file_info($bank_id, $dispatch_id) {
        $DB = Database::instance();
        $sql = "SELECT * FROM ctfu_user_request_files as t1
                where t1.request_bank_id = {$bank_id} AND t1.dispatch_id = {$dispatch_id}";
        return $DB->query(Database::SELECT, $sql, FALSE)->current();
    }

    public static function get_ctfu_user_request_files($record_id) {
        $DB = Database::instance();
        $sql = "select * from ctfu_user_request_files where record_id= {$record_id} ";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return $result;
    }

    //CTFU Request file path
    public static function ctfu_requests_file_path() {
        return getcwd() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'branchlessbanking' . DIRECTORY_SEPARATOR;
    }

    public static function get_data_array_cnic($file) {
        $inputfilename = !empty($file['tmp_name']) ? $file['tmp_name'] : '';
        $inputfiletype = PHPExcel_IOFactory::identify($inputfilename);
        $objReader = PHPExcel_IOFactory::createReader($inputfiletype);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        //read only data (without formating) for memory and time performance
        
       // $objReader->setReadDataOnly(true);
        //$objPHPExcel = $objReader->load($inputfilename);
        
        ini_set('memory_limit', '9999999990024M');
        $excelData = array();
        $filePath = $inputfilename;
        $objPHPExcel = PHPExcel_IOFactory::load($filePath);
        
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            $data = array();
            $data['foreigner'] = array();
            $data['local'] = array();
            for ($row = 1; $row <= $highestRow; ++$row) {
                $values = array();
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    if (PHPExcel_Shared_Date::isDateTime($cell) && ($row != 1)) {
                        $val = PHPExcel_Shared_Date::ExcelToPHP($cell->getValue());
                    }
                    if (isset($val) && $val) {
                        if (!ctype_digit(trim($val))) {
                            $val = "'" . $val . "'";
                            $data['foreigner'][$row] = $val;
                        } else {
                            //$data[$row][$col] = $val;
                            $data['local'][$row] = $val;
                        }
                    }
                }
            }
        }
        return $data;
    }
    public static function get_data_array($file) {

        $inputfilename = !empty($file['tmp_name']) ? $file['tmp_name'] : '';

        $inputfiletype = PHPExcel_IOFactory::identify($inputfilename);

        $objReader = PHPExcel_IOFactory::createReader($inputfiletype);
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        
        
        
        $cacheSettings = array(' memoryCacheSize ' => '64MB');
        
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        //read only data (without formating) for memory and time performance
        
       // $objReader->setReadDataOnly(true);
        //$objPHPExcel = $objReader->load($inputfilename);
        
        ini_set('memory_limit', '80M');
        $excelData = array();
        $filePath = $inputfilename;
        
        $objPHPExcel = PHPExcel_IOFactory::load($filePath);
        
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            $data = array();
//            $data['foreigner'] = array();
            $data['mobile'] = array();
            for ($row = 1; $row <= $highestRow; ++$row) {
                $values = array();
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    if (PHPExcel_Shared_Date::isDateTime($cell) && ($row != 1)) {
                        $val = PHPExcel_Shared_Date::ExcelToPHP($cell->getValue());
                    }
                    if (isset($val) && $val) {
//                        if (!ctype_digit(trim($val))) {
//                            $val = "'" . $val . "'";
//                            $data['foreigner'][$row] = $val;
//                        } else {
                            //$data[$row][$col] = $val;
                            $data['mobile'][$row] = $val;
//                        }
                    }
                }
            }
        }
        return $data;
    }

    //Check menu against role exists
    public static function check_menu_ag_role($menu_id, $role_id) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.id) AS chk
                FROM  manu_management AS t1
                WHERE t1.manu_id= {$menu_id} and t1.role_id = {$role_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }
    
        public static function get_company_branchless_transactions($code) {
        $DB = Database::instance();
        $sql = "select * from lu_branchless_transactions "
                . "where ($code) in (transaction_code,transaction_code_countrycode);";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return $result;
    }
    
        public static function get_lu_branchless_transactions($code = Null) {
        $DB = Database::instance();
        $sql = "SELECT * FROM lu_branchless_transactions";
        if (!empty($code)) {
            $sql .= " where ($code) in (transaction_code,transaction_code_countrycode) LIMIT 1";
        }
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }
// subscriber against mobile number total parsing error
    public static function get_person_total_subscriber_requests_p_error($user_id) {
        $DB = Database::instance();
        $sql = "SELECT count(request_id) as count
                from user_request ur 
                where user_id=$user_id and user_request_type_id=1 and processing_index =3 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }
    // current location total parsing error
    public static function get_person_total_current_location_p_error($user_id) {
        $DB = Database::instance();
        $sql = "SELECT count(request_id) as count
                from user_request ur 
                where user_id=$user_id and user_request_type_id=3 and processing_index =3 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }
    // sims against cnic total parsing error
    public static function get_person_total_sims_against_cnic_p_error($user_id) {
        $DB = Database::instance();
        $sql = "SELECT count(request_id) as count
                from user_request ur 
                where user_id=$user_id and user_request_type_id=5 and processing_index =3 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }
    //sim against imsi total parsing error
    public static function get_person_total_sims_against_imsi_p_error($user_id) {
        $DB = Database::instance();
        $sql = "SELECT count(request_id) as count
                from user_request ur 
                where user_id=$user_id and user_request_type_id=7 and processing_index =3 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }

    public static function get_person_db_match_count($person_id) {
        $DB = Database::instance();
        $sql = "select count(distinct ppn.person_id,table1.phone,ppn.phone_number) as count 
                from ( (SELECT person_id as person_id, phone_number as phone, other_person_phone_number as otherphone, is_outgoing as calltype, call_at as calldate, 1 as is_call 
                FROM person_call_log where person_id = $person_id) 
                UNION all (SELECT person_id as person_id, phone_number as phone, other_person_phone_number as otherphone, is_outgoing as calltype, sms_at as calldate, 0 as is_call 
                FROM person_sms_log where person_id = $person_id) ) as table1 
                join person_phone_number as ppn on ppn.phone_number = table1.otherphone join person as pt on ppn.person_id = pt.person_id where 1 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }
    public static function get_person_total_favourite_person($person_id) {
        $DB = Database::instance();
        $sql = "	SELECT Count(*) as count 
                    FROM person_summary ps
                    WHERE person_id=$person_id";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }
    public static function get_linked_with_affiliated_person($person_id) {
        $DB = Database::instance();
        $sql = "	SELECT count(DISTINCT ps.other_person_phone_number) as count FROM person_summary ps 
                    Inner join person_summary as pa1 on pa1.phone_number = ps.other_person_phone_number 
                    Inner join person_affiliations as pa on pa.person_id = pa1.person_id WHERE ps.person_id=$person_id";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $count;
    }
    

}

?>
