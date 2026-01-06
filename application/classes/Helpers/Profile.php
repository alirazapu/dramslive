<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Profile helper class. It will contain all the Helper Functions realted to Profile
 *
 * @package    Profile Helper
 * @category   Helpers
 */
abstract class Helpers_Profile {

    /**
     * Save Image 
     * @param int member_id             
     * @return self
     */
    public static function _save_image($image, $type = NULL, $pid = NULL) {


        if (! Upload::valid($image) OR ! Upload::not_empty($image) OR ! Upload::type($image, array('jpg', 'jpeg', 'png', 'gif', 'bmp' , 'pdf'))) {

            return FALSE;
        }
//    echo '<pre>';
//    print_r(substr($image['type'],-3));
//    exit;

        //get person save data path
        $person_save_data_path = !empty($pid) ? Helpers_Person:: get_person_save_data_path($pid) : '';
        //echo '<pre>';        print_r($person_save_data_path); exit;
        $file_name = '';

        if ($type == "user") {
            $directory = 'dist/uploads/user/profile_images/';
            $file_name = "user-picture";
        } else if ($type == "person") {
            $directory = $person_save_data_path;
            $file_name = "person";
        } else if ($type == "person_pictures") {
            $directory = $person_save_data_path;
            $file_name = "person-picture";
        } else if ($type == "person_verysis") {
            $directory = $person_save_data_path;
            $file_name = "person-verysis";
        } else if ($type == "person_familytree") {
            $directory = $person_save_data_path;
            $file_name = "person-familytree";
        } else if ($type == "person_travelhistory") {
            $directory = $person_save_data_path;
            $file_name = "person-travelhistory-";
        }

        //genrate a random file name that is hexa decimal and make it a jpg file
        $date = date("YmdHis", time());
        if(substr($image['type'],-3)=="pdf")
        {

            $filename = $pid . $file_name . $date . ".pdf";

//            Upload::save($_FILES['?'], path, 0777);
            Upload::save($image, $filename, $directory);
            return trim($filename);

           // Upload::save($image, $filename, 0777);

        }else {
            $filename = $pid . $file_name . $date . ".jpg";
            if ($file = Upload::save($image, NULL, $directory)) {

                // $pdf= file::factory($file);
                $img = Image::factory($file);

                $img->save($directory . $filename);

                //if ($type == "user") {

                Helpers_Profile::_resize_images($filename, $type);
                //}
                unlink($file);

                return trim($filename);
            }
        }




        return FALSE;
    }

    public static function reArrayFiles($file_post) {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

    public static function _save_verisys_temp_image($images, $user_id) {
        $directory = 'uploads/verisys_temp_images/';
        $error = array();
        $images = Helpers_Profile::reArrayFiles($images);
        foreach ($images as $key => $value) {
            Helpers_Utilities::check_file_from_blacklist($value);
            if (!Upload::valid($value) OR ! Upload::not_empty($value) OR ! Upload::type($value, array('jpg', 'jpeg', 'png', 'gif', 'bmp'))) {
                $error[$key] = 'Invalid Format';
            } else {
                $filename_temp = substr($value['name'], 0, strpos($value['name'], '.'));
                $filename = $filename_temp . ".jpg";
                if ($file = Upload::save($value, NULL, $directory)) {
                    $img = Image::factory($file);
                    $img->save($directory . $filename);
                    $type = "person_verysis";
                    Helpers_Profile::_resize_images($filename, $type);
                    unlink($file);
                    Helpers_Profile::save_verisys_temp_image_db($filename_temp, $filename, $user_id);
                } else {
                    $error[$key] = 'Upload Fail';
                }
            }
        }
        return $error;
    }
    public static function _save_familytree_temp_image($images, $user_id) {
        $directory = 'uploads/familytree_temp_images/';
        $error = array();
        $images = Helpers_Profile::reArrayFiles($images);
        foreach ($images as $key => $value) {
            Helpers_Utilities::check_file_from_blacklist($value);
            if (!Upload::valid($value) OR ! Upload::not_empty($value) OR ! Upload::type($value, array('jpg', 'jpeg', 'png', 'gif', 'bmp'))) {
                $error[$key] = 'Invalid Format';
            } else {
                $filename_temp = substr($value['name'], 0, strpos($value['name'], '.'));
                $filename = $filename_temp . ".jpg";
                if ($file = Upload::save($value, NULL, $directory)) {
                    $img = Image::factory($file);
                    $img->save($directory . $filename);
                    $type = "person_familytree";
                    Helpers_Profile::_resize_images($filename, $type);
                    unlink($file);
                    Helpers_Profile::save_familytree_temp_image_db($filename_temp, $filename, $user_id);
                } else {
                    $error[$key] = 'Upload Fail';
                }
            }
        }
        return $error;
    }
    public static function _save_travelhistory_temp_image($images, $user_id) {
        $directory = 'uploads/travelhistory_temp_images/';
        $error = array();
        $images = Helpers_Profile::reArrayFiles($images);
        foreach ($images as $key => $value) {
            Helpers_Utilities::check_file_from_blacklist($value);
            
            if (!Upload::valid($value) OR ! Upload::not_empty($value) OR ! Upload::type($value, array('pdf','jpg', 'jpeg', 'png', 'gif', 'bmp'))) {
                $error[$key] = 'Invalid Format';
            } else {
                //$filename_temp = substr($value['name'], 0, strpos($value['name'], '.'));
                //$filename = $filename_temp . ".jpg";
                $new_file_info = PATHINFO($value['name']);
                $filename_temp = $new_file_info['filename'];
                $filename = $new_file_info['basename'];
                
                if($new_file_info['extension'] !='pdf')
                {
                if ($file = Upload::save($value, NULL, $directory)) {
                    $img = Image::factory($file);
                    $resutl = $img->save($directory . $filename);
                    $type = "travelhistoryfiles";
                    Helpers_Profile::_resize_images($filename, $type);
                    unlink($file);
                    Helpers_Profile::save_travelhistory_temp_image_db($filename_temp, $filename, $user_id);
                } else {
                    $error[$key] = 'Upload Fail';
                }
             }else{                
                $uploaded = Upload::save($value, $filename, $directory);
                if ($uploaded)
                {   
                    Helpers_Profile::save_travelhistory_temp_image_db($filename_temp, $filename, $user_id);
//                    $this->set('file', $file['file_new_name']);
//                    $this->set('type', strtolower(pathinfo($file['file_new_name'], PATHINFO_EXTENSION)));
//                    $this->set('size', $file['size']);
                }else{
                    $error[$key] = 'Upload Fail';
                }
            }   
            }
        }
        return $error;
    }

    public static function save_travelhistory_temp_image_db($cnic_number, $image_name, $user_id) {
        $date = date('Y-m-d H:i:s');
        $check_cnic_exist = Helpers_Profile::check_travelhistory_temp_image_db_exist($cnic_number);
        if ($check_cnic_exist == 0) {
            $query = DB::insert('travelhistory_temp_files', array('cnic_number', 'image_name', 'uploaded_by_user', 'upload_date'))
                    ->values(array($cnic_number, $image_name, $user_id, $date))
                    ->execute();
        } else {
            $result = DB::update("travelhistory_temp_files")
                    ->set(array('image_name' => $image_name, 'uploaded_by_user' => $user_id, 'upload_date' => $date))
                    ->where('cnic_number', '=', $cnic_number)
                    ->execute();
        }
    }
    public static function save_verisys_temp_image_db($cnic_number, $image_name, $user_id) {
        $date = date('Y-m-d H:i:s');
        $check_cnic_exist = Helpers_Profile::check_verisys_temp_image_db_exist($cnic_number);
        if ($check_cnic_exist == 0) {
            $query = DB::insert('verisys_temp_files', array('cnic_number', 'image_name', 'uploaded_by_user', 'upload_date'))
                    ->values(array($cnic_number, $image_name, $user_id, $date))
                    ->execute();
        } else {
            $result = DB::update("verisys_temp_files")
                    ->set(array('image_name' => $image_name, 'uploaded_by_user' => $user_id, 'upload_date' => $date))
                    ->where('cnic_number', '=', $cnic_number)
                    ->execute();
        }
    }
    public static function save_familytree_temp_image_db($cnic_number, $image_name, $user_id) {
        $date = date('Y-m-d H:i:s');
        $check_cnic_exist = Helpers_Profile::check_familytree_temp_image_db_exist($cnic_number);
        if ($check_cnic_exist == 0) {
            $query = DB::insert('familytree_temp_files', array('cnic_number', 'image_name', 'uploaded_by_user', 'upload_date'))
                    ->values(array($cnic_number, $image_name, $user_id, $date))
                    ->execute();
        } else {
            $result = DB::update("familytree_temp_files")
                    ->set(array('image_name' => $image_name, 'uploaded_by_user' => $user_id, 'upload_date' => $date))
                    ->where('cnic_number', '=', $cnic_number)
                    ->execute();
        }
    }

    //    Check person mobile number exist or not
    public static function check_verisys_temp_image_db_exist($cnic) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.cnic_number) AS cnic
                    FROM verisys_temp_files AS t1
                    WHERE t1.cnic_number= $cnic";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $cnic_count = isset($results->cnic) && !empty($results->cnic) ? $results->cnic : 0;
        return $cnic_count;
    }//    Check person mobile number exist or not
    public static function check_familytree_temp_image_db_exist($cnic) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.cnic_number) AS cnic
                    FROM familytree_temp_files AS t1
                    WHERE t1.cnic_number= $cnic";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $cnic_count = isset($results->cnic) && !empty($results->cnic) ? $results->cnic : 0;
        return $cnic_count;
    }
    //check travelhistory file record exist
    public static function check_travelhistory_temp_image_db_exist($cnic) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.cnic_number) AS cnic
                    FROM travelhistory_temp_files AS t1
                    WHERE t1.cnic_number= $cnic";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $cnic_count = isset($results->cnic) && !empty($results->cnic) ? $results->cnic : 0;
        return $cnic_count;
    }

    //move image for verisys
    public static function move_verisys_image($image_old_path, $pid = NULL, $row_id) {
        $person_save_data_path = !empty($pid) ? Helpers_Person:: get_person_save_data_path($pid) : '';
        $directory = $person_save_data_path;        
        $file_name = "person-verysis";
        //genrate a random file name that is hexa decimal and make it a jpg file
        $date = date("YmdHis", time());
        $filename = $pid . $file_name . $date . ".jpg";
        //move file to new path
        $result = rename(getcwd() . $image_old_path, "$directory" . "$filename");
        if ($result == 1) {
            $query = DB::delete('verisys_temp_files')
                    ->where('row_id', '=', $row_id)
                    ->execute();
        } else {
            $query = DB::update('verisys_temp_files')
                    ->set(array('attachment_status' => 2))
                    ->where('row_id', '=', $row_id)
                    ->execute();
        }
        return $filename;
    }
    //move image for familytree
    public static function move_familytree_image($image_old_path, $pid = NULL, $row_id) {
        $person_save_data_path = !empty($pid) ? Helpers_Person:: get_person_save_data_path($pid) : '';
        $directory = $person_save_data_path;
        $file_name = "person-familytree";
        //genrate a random file name that is hexa decimal and make it a jpg file
        $date = date("YmdHis", time());
        $filename = $pid . $file_name . $date . ".jpg";
        //move file to new path
        $result = rename(getcwd() . $image_old_path, "$directory" . "$filename");
        if ($result == 1) {
            $query = DB::delete('familytree_temp_files')
                    ->where('row_id', '=', $row_id)
                    ->execute();
        } else {
            $query = DB::update('familytree_temp_files')
                    ->set(array('attachment_status' => 2))
                    ->where('row_id', '=', $row_id)
                    ->execute();
        }
        return $filename;
    }

    /**
     * Get image path of a user by id        *
     * @param int member_id             
     * @return self
     */
    public static function get_user_image_by_id($id) {
        $sql = "Select t.file_name as name from users_profile as t
                where t.user_id = $id";
        $result = DB::query(Database::SELECT, $sql)->as_object()->execute()->current();
        $name = !empty($result->name) ? $result->name : "avatar5.png";
        return $name;
    }

    public static function _resize_images($filename, $type = NULL, $cropOrRotate = NULL) {
        if (isset($cropOrRotate) && !empty($cropOrRotate) && $cropOrRotate == 1) {

            $filepath = 'dist/uploads/' . $type . '/profile_images/cropped/' . $filename;
        } else {
            $filepath = 'dist/uploads/' . $type . '/profile_images/' . $filename;
        }
    }

    /* Profile View */

    public static function count_profile_view() {
        $DB = Database::instance();
        $sql = "";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
    }

    /* Count Password Recovery Requests */

    public static function count_password_requests() {
        $DB = Database::instance();
        $sql = "select count(*) as ct
                    from users
                    where is_forget_reset = 1 && is_active=1 && is_deleted = 0";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results->ct;
    }

    /* Users Requested Password Recovery */

    public static function password_requests() {
        $DB = Database::instance();
        $sql = "select id, email, username
                    from users
                    where is_forget_reset=1 && is_active=1 && is_deleted=0 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     * Get User Image
     */

    public static function get_image_user($user_ud) {
        $DB = Database::instance();
        $sql = "";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get User Profile Detail 
     */

    public static function get_user_perofile($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT * from users_profile where user_id = {$user_ud}";
        // print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }/*
     *  Get Person Profile Detail
     */

    public static function get_person_info($pid) {
        $DB = Database::instance();
        $sql = "Select count(DISTINCT request_id) as total_request, max(sending_date) as last_sent_req
                from user_request ur 
                where concerned_person_id  = {$pid}";
        // print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get Users Details
     */

    public static function get_users() {
        $DB = Database::instance();
        $sql = "SELECT *
                             from users as t1
							 inner join users_profile as t2 on t1.id=t2.user_id
                             where 1 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     *  Get Users Details
     */

    public static function get_user_block_reasons($uid) {
        $DB = Database::instance();
        $sql = "SELECT *
                 from user_block_reason as t1
                where t1.user_id={$uid} ORDER BY t1.id DESC";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    /*
     *  Get Users Details
     */

    public static function get_user_with_cnic($cnic = '') {
        $DB = Database::instance();
        $sql = "SELECT *
                             from users as t1
                             inner join users_profile as t2 on t1.id=t2.user_id
                             where t2.cnic_number={$cnic}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get Users Details
     */

    public static function get_user_with_cnic_and_posting($cnic = '', $posted = '') {
        $DB = Database::instance();
        $sql = "SELECT *
                             from users as t1
                             inner join users_profile as t2 on t1.id=t2.user_id
                             where t2.cnic_number={$cnic} and t2.posted='{$posted}'";                             
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  mark transferred
     */

    public static function mark_active_and_update_profile($data) {
        $username = explode("::transferred", $data['exist_username']);
        $email = explode("::transferred", $data['exist_email']);
        $array = array(
            'username' => $username[0],
            'email' => $email[0],
            'is_active' => $data['is_active'],
            'is_active_cis' => $data['is_active_cis'],
            'is_active_ctfu' => $data['is_active_ctfu'],
            'login_sites' => $data['login_sites']
        );
        $result = DB::update("users")->set($array)->where('id', '=', $data['exist_id'])->execute();
        
        if(!empty($data['mobile_number']) && !empty($data['designation'])){
            
              $array = array(
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'father_name' => $data['father_name'],
//            'file_name' => $data['user_pic'],
            'mobile_number' => $data['mobile_number'],
            'job_title' => $data['job_title'],
            'district_id' => $data['district_id'],
            'region_id' => $data['region_id'],
            'belt' => $data['belt']
        );
              
              $result = DB::update("users_profile")->set($array)->where('user_id', '=', $data['exist_id'])->execute();
         }
        return 1;
    }

    /*
     *  mark transferred
     */

    public static function mark_transfered_user($cnic = '') {
        $DB = Database::instance();
        $sql = "SELECT *
                             from users as t1
			     inner join users_profile as t2 on t1.id=t2.user_id
                             where t2.cnic_number={$cnic}";
        $results = $DB->query(Database::SELECT, $sql, FALSE);

        foreach ($results as $row) {
            $date = date('Y-m-d H:i:s');
            $username = explode("::transferred", $row['username']);
            $email = explode("::transferred", $row['email']);
            $array = array(
                'username' => $username[0] . '::transferred',
                'email' => $email[0] . '::transferred',
                'is_active' => 0,
                'is_active_cis' => 0,
                'deactivated_at' => $date
            );
            $result = DB::update("users")->set($array)->where('id', '=', $row['id'])->execute();
        }
        return 1;
    }

    /* Get email id         
     */

    public static function get_user_log_info($user_ud) {
        $DB = Database::instance();
        $sql = "SELECT username, email, logins, Last_login, is_active
                             from users
                             where id = {$user_ud} AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /* Get First Litter of Each Words */

    public static function get_first_letters($id) {
        $DB = Database::instance();
        $sql = "SELECT CONCAT_WS(' ',first_name, last_name) as name
                             from users_profile
                             where user_id = {$id} ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $name = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";
        $acronym = '';
        $word = '';
        $words = preg_split("/(\s|\-|\.)/", $name);
        foreach ($words as $w) {
            $acronym .= substr($w, 0, 1);
        }
        $word = $word . $acronym;
        return ucfirst(substr($word, 0, 2));
    }

    /*    get online user's Count records */

    public static function get_login_user_count($user_id = Null) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(id) as CNT
                  FROM users as t1 
                  WHERE t1.is_login = 1";
        if (!empty($user_id))
            $sql .= " AND user_id = {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countlogin = isset($results->CNT) && !empty($results->CNT) ? $results->CNT : 0;
        return $countlogin;
    }

    /* Online User List Ajax Call Data */

    public static function user_list_online() {

        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        if ($permission == 1) {
            $where_clause = "where 1";
        } elseif ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where 1";
                    break;
                case 'r':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where u1.id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        } else {
            $where_clause = "where u1.id  = {$login_user->id}";
        }

        $sql = "Select  COUNT(*) AS count
                        from users as u1
                        join users_profile u2
                        on u2.user_id=u1.id   
                        {$where_clause}
                        and u1.is_login = 1 AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5) ";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
    }

    /*    get  user's Count records */

    public static function get_user_count($user_id = Null) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(id) as CNT
                  FROM users as t1";
        if (!empty($user_id))
            $sql .= " where user_id = {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countuser = isset($results->CNT) && !empty($results->CNT) ? $results->CNT : 0;
        return $countuser;
    }

    /*    get  user's Count records */

    public static function get_user_access_permission($user_id, $user_activity) {
        $DB = Database::instance();
        $sql = "SELECT t1.permission as permi 
                FROM user_access_matrix AS t1
                where t1.user_id = $user_id and t1.user_activity_type = $user_activity";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $permission = isset($results->permi) && !empty($results->permi) ? $results->permi : 0;
        return $permission;
    }

    /*    get  Blocked user's Count records */

    public static function get_blocked_user_count($user_id = Null) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(id) as CNT
                  FROM users as t1 
                  where t1.is_active= 0";
        if (!empty($user_id))
            $sql .= " AND id = {$user_id}";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countblockuser = !empty($results->CNT) ? $results->CNT : 0;
        return $countblockuser;
    }

    /*    get  Blocked user's Count records */

    public static function get_appvoed_user_count($user_id = Null) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(id) as CNT
                  FROM users as t1 
                  where t1.is_approved != 1";
        if (!empty($user_id))
            $sql .= " AND id = {$user_id}";
        //print_r($sql); exit;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $countapproveuser = isset($results->CNT) && !empty($results->CNT) ? $results->CNT : 0;
        return $countapproveuser;
    }

    /*    get  Most Favourite User records */

    public static function get_user_highest_black() {
        $DB = Database::instance();
        $sql = "SELECT t1.user_id as userid,COUNT(t1.category_id) as total
                 FROM person_category as t1 WHERE t1.category_id=2 
                 GROUP by t1.user_id ORDER BY total DESC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $userid = isset($results->userid) && !empty($results->userid) ? $results->userid : 0;
        return $userid;
    }

    //get user posting
    public static function get_user_posting($posting) {
        $DB = Database::instance();
        $result = explode('-', $posting);
        switch ($result[0]) {
            case 'r':
                $sql = "SELECT t.name as name FROM region as t where  t.region_id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $regionname = isset($results->name) && !empty($results->name) ? 'RO ' . $results->name : 0;
                return $regionname;
                break;
            case 'd':
                $sql = "SELECT t.name as name FROM district as t where  t.district_id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $distname = isset($results->name) && !empty($results->name) ? 'DO ' . $results->name : 0;
                return $distname;
                break;
            case 'p':
                $sql = "SELECT t.name as name FROM ctd_police_station as t WHERE t.id = $result[1]";
                //print_r($sql); exit;
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $psname = isset($results->name) && !empty($results->name) ? 'PS ' . $results->name : 0;
                return $psname;
                break;
            case 'h':
                $sql = "SELECT t.name as name FROM headquarter as t WHERE t.id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $hqname = isset($results->name) && !empty($results->name) ? 'HQ ' . $results->name : 0;
                return $hqname;
                break;
            default :
                $defaulposting = ' ';
                return $defaulposting;
        }
    }
    /*    gget_users_place of posting ids */

    public static function get_user_place_of_posting_against_uid($userid = NULL) {
        $DB = Database::instance();
        if(!empty($userid)) {
            $sql = "SELECT distinct(posted) from users_profile
                   WHERE user_id in($userid) group by posted";

            $results = $DB->query(Database::SELECT, $sql, false)->as_array();
        }else
        {
            $results='';
        }

//        $personid = isset($results->personid) && !empty($results->personid) ? $results->personid : 0;
        return $results;
    }

    //get user posting id
    public static function get_user_posting_place_id($posting) {
        $DB = Database::instance();
        $result = explode('-', $posting);
        switch ($result[0]) {
            case 'r':

                return $result[1];
                break;
            case 'd':

                return $result[1];
                break;
            case 'p':

                return $result[1];
                break;
            case 'h':

                return $result[1];
                break;
            default :
                $defaulposting = ' ';
                return $defaulposting;
        }
    }

    //get user region and district id
    public static function get_user_region_district($userid) {
        $DB = Database::instance();
        $user_data = Helpers_Profile::get_user_perofile($userid);
        $user_posting = (!empty($user_data->posted)) ? $user_data->posted : 0;
        $result = explode('-', $user_posting);
        switch ($result[0]) {
            case 'r':
                $sql = "SELECT t.name as name FROM region as t where  t.region_id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $regionname = isset($results->name) && !empty($results->name) ? 'RO ' . $results->name : 0;
                return $regionname;
                break;
            case 'd':
                $sql = "SELECT t.name as name FROM district as t where  t.district_id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $distname = isset($results->name) && !empty($results->name) ? 'DO ' . $results->name : 0;
                return $distname;
                break;
            case 'p':
                if ($result[1]>=900)
                    $sql = "SELECT t.name as name FROM ctd_police_station as t WHERE t.id = $result[1]";
                else 
                    $sql = "SELECT t.name as name FROM ctd_police_station as t WHERE t.region_id = $result[1]";                
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $psname = isset($results->name) && !empty($results->name) ? 'PS ' . $results->name : 0;
                return $psname;
                break;
            case 'h':
                $sql = "SELECT t.name as name FROM headquarter as t WHERE t.id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $hqname = isset($results->name) && !empty($results->name) ? 'HQ ' . $results->name : 0;
                return $hqname;
                break;
            default :
                $defaulposting = ' ';
                return $defaulposting;
        }
    }

    /*    gget_user_latest_favourite_person records */

    public static function get_user_latest_favourite_person($userid = NULL) {
        $DB = Database::instance();
        $sql = "SELECT T1.person_id AS personid FROM user_favorite_person AS T1
                   WHERE T1.user_id = $userid ORDER BY T1.added_on DESC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $personid = isset($results->personid) && !empty($results->personid) ? $results->personid : 0;
        return $personid;
    }

    /*    Check user Favourite user */

    public static function is_favourite_user($loginuser, $userid) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(favourite_user_id) as CONT FROM user_favourite_user as t where t.user_id =  $loginuser and t.favourite_user_id = $userid ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $COUNT = isset($results->CONT) && !empty($results->CONT) ? $results->CONT : 0;
        return $COUNT;
    }

    /*    Check user Favourite user owner */

    public static function is_own_favourite_user($loginuser, $userid) {
        $DB = Database::instance();
        $sql = "SELECT COUNT(favourite_user_id) as CONT FROM user_favourite_user as t where t.user_id =  $loginuser and t.favourite_user_id = $userid ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $COUNT = isset($results->CONT) && !empty($results->CONT) ? $results->CONT : 0;
        return $COUNT;
    }

    /*    get_user_latest_favourite_user records */

    public static function get_user_latest_favourite_user($userid = NULL) {
        $DB = Database::instance();
        $sql = "SELECT T1.favourite_user_id AS userid FROM user_favourite_user AS T1
                   WHERE T1.user_id = $userid ORDER BY T1.added_on DESC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $userid = isset($results->userid) && !empty($results->userid) ? $results->userid : 0;
        return $userid;
    }

    /*
     *  Person Login in or Logout check status
     */

    public static function is_login($user_id, $status = True) {

        if ($status) {
            $result = DB::update("users")->set(array('is_login' => 1))->where('id', '=', $user_id)->execute();
            Helpers_Profile::user_activity_log($user_id, 1);
        } else {
            $result = DB::update("users")->set(array('is_login' => 0))->where('id', '=', $user_id)->execute();
            Helpers_Profile::user_activity_log($user_id, 2);
        }
        return $result;
    }

    /*
     *  Person Activity Log
     */

    public static function user_activity_log($user_id, $activity_id, $search_key = Null, $search_value = Null, $person_id = Null, $company = Null, $user = NULL) {
        $month = date("F");
        date_default_timezone_set("Asia/Karachi");
        $date = date("Y-m-d H:i:s");
        $date_only = date("Y-m-d");
        $query = DB::insert('user_activity_timeline', array('user_id', 'user_activity_type_id', 'person_id', 'activity_time'))
                ->values(array($user_id, $activity_id, $person_id, $date))
                ->execute();
        $activity_array = array(50, 51, 62, 64, 59, 54, 47, 44, 41, 38, 35, 11, 27, 28, 8, 10, 19, 20, 21, 4, 5, 71, 75, 77, 78, 79, 80, 26, 48, 81, 82, 83);
        //if($activity_id == 62 || $activity_id == 59 || $activity_id == 54 ||$activity_id == 47 || $activity_id == 44 || $activity_id == 41 || $activity_id == 38 || $activity_id == 35 || $activity_id == 11 || $activity_id == 27 ||$activity_id == 28 ||$activity_id == 8 || $activity_id == 10 || $activity_id == 19 || $activity_id == 20 || $activity_id == 21 || $activity_id == 4 || $activity_id == 5 || $activity_id == 71 || $activity_id == 75 || $activity_id == 76 || $activity_id == 77 || $activity_id == 78  || $activity_id == 79 || $activity_id == 80)
        if (in_array($activity_id, $activity_array)) {
            $query = DB::insert('user_activity_timeline_detail', array('timeline_id', 'key_name', 'key_value', 'request_company', 'user_id'))
                    ->values(array($query[0], $search_key, $search_value, $company, $user))
                    ->execute();
        }

        $DB = Database::instance();
        $sql = "select * FROM `user_summary` where user_id = {$user_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        if (!empty($results)) {
            switch ($activity_id) {
                case 1: //Login
                    $result = DB::update("user_summary")->set(array('last_logged_in_at' => $date))->where('user_id', '=', $user_id)->execute();
                    break;
                case 19: //Request for Email (Request date and count)
                    $result = DB::update("user_summary")->set(array('last_request_made_at' => $date, 'request_count' => ($results->record_add_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 17: // Record added count 
                    $result = DB::update("user_summary")->set(array('record_add_count' => ($results->record_add_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 13: // Record View count
                case 14:
                    $result = DB::update("user_summary")->set(array('record_view_count' => ($results->record_view_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 9: //Deactivate account
                    $result = DB::update("user_summary")->set(array('record_lock_count' => ($results->record_lock_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 6: // Record Favorite count
                    $result = DB::update("user_summary")->set(array('record_favorite_count' => ($results->record_favorite_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
            }
        } else {
            $query = DB::insert('user_summary', array('user_id', 'last_logged_in_at', 'request_count', 'record_add_count', 'record_view_count', 'record_lock_count', 'record_favorite_count'))
                    ->values(array($user_id, $date, 0, 0, 0, 0, 0))
                    ->execute();
        }

        $sql = "SELECT * FROM `user_monthly_summary` where MONTHNAME(reported_month) = '{$month}' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if (!empty($results)) {
            switch ($activity_id) {
                case 1: //Login
                    $result = DB::update("user_monthly_summary")->set(array('login_count' => ($results->login_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 19: //Request for Email (Request date and count)
                    $result = DB::update("user_monthly_summary")->set(array('request_count' => ($results->request_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 17: // Record added count 
                    $result = DB::update("user_monthly_summary")->set(array('record_add_count' => ($results->record_add_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 13: // Record View count
                case 14:
                    $result = DB::update("user_monthly_summary")->set(array('record_view_count' => ($results->record_view_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 9: //Deactivate account
                    $result = DB::update("user_monthly_summary")->set(array('record_lock_count' => ($results->record_lock_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
                case 6: // Record Favorite count
                    $result = DB::update("user_monthly_summary")->set(array('record_favorite_count' => ($results->record_favorite_count + 1)))->where('user_id', '=', $user_id)->execute();
                    break;
            }
        } else {
            $query = DB::insert('user_monthly_summary', array('user_id', 'reported_month', 'request_count', 'record_add_count', 'record_view_count', 'record_lock_count', 'record_favorite_count', 'login_count'))
                    ->values(array($user_id, $date_only, 0, 0, 0, 0, 0, 1))
                    ->execute();
        }

        return 1;
    }

    public static function get_role($name) {
        $DB = Database::instance();
        $sql = "SELECT r.name FROM `users`  as u 
                JOIN  roles_users as ru on ru.user_id = u.id
                JOIN  roles as r on r.id = ru.role_id
                WHERE u.username like '{$name}'";
        $result = DB::query(Database::SELECT, $sql)->execute()->current();
        return $result['name'];
    }

    public static function add_user_block_reason($data) {
        $DB = Database::instance();
        $date = date("YmdHis", time());
        $query = DB::insert('user_block_reason', array('user_id', 'block_reason', 'timestamp'))
                ->values(array($data['id'], $data['reason'], $date))
                ->execute();
    }

    public static function get_user_blocked($id) {
        $DB = Database::instance();
        $sql = "SELECT id FROM users WHERE id = {$id} and is_deleted=1 and is_active = 0";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $userid = isset($results->id) && !empty($results->id) ? $results->id : 0;
        return $userid;
    }

    public static function check_with_dates($pid, $issue_date, $expiry_date) {
        $DB = Database::instance();
        $sql = "SELECT count(record_id) as count FROM person_nadra_profile_history"
                . " WHERE person_id = {$pid} "
                . " AND issue_date = {$issue_date}"
                . " AND expiry_date= {$expiry_date}"
                . " order by record_id desc limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $record_count = isset($results->count) && !empty($results->count) ? $results->count : 0;
        return $record_count;
    }

    public static function check_expiry_date($pid) {
        $DB = Database::instance();
        $sql = "SELECT expiry_date FROM person_nadra_profile_history"
                . " WHERE person_id = {$pid} order by record_id desc limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $expiry_date = isset($results->expiry_date) && !empty($results->expiry_date) ? $results->expiry_date : '';
        return $expiry_date;
    }

    public static function check_image_url($pid) {
        $DB = Database::instance();
        $sql = "SELECT cnic_image_url FROM person_nadra_profile"
                . " WHERE person_id = {$pid}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $nadra_url = isset($results->cnic_image_url) && !empty($results->cnic_image_url) ? $results->cnic_image_url : 3;
        $sql2 = "SELECT cnic_image_url FROM person_nadra_profile_history"
                . " WHERE person_id = {$pid} order by record_id desc limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $nadra_url_history = isset($results->cnic_image_url) && !empty($results->cnic_image_url) ? $results->cnic_image_url : 4;
        if ($nadra_url == $nadra_url_history) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function check_image_url_exist($pid) {
        $DB = Database::instance();
        $sql = "SELECT cnic_image_url FROM person_nadra_profile"
            . " WHERE person_id = {$pid}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $nadra_url = isset($results->cnic_image_url) && !empty($results->cnic_image_url) ? $results->cnic_image_url : '';

        if (empty($nadra_url)) {
            $sql = "SELECT cnic_image_url FROM person_foreigner_profile"
                . " WHERE person_id = {$pid}";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $nadra_url = isset($results->cnic_image_url) && !empty($results->cnic_image_url) ? $results->cnic_image_url : '';

        }
        return $nadra_url;
    }
    public static function check_verisys_file_exist($cnic) {

        $DB = Database::instance();
        $sql = "SELECT image_name FROM verisys_temp_files"
            . " WHERE cnic_number = {$cnic}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $nadra_url = isset($results->image_name) && !empty($results->image_name) ? $results->image_name : '';


        return $nadra_url;
    }

    //set login sites for aies cis and ctfu
    public static function set_login_sites($new_project, $old_login_sites) {
        $login_sites = 99;
        switch ($old_login_sites) {
            case 0: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 0;
                            break;
                        case 'cis':
                            $login_sites = 2;
                            break;
                        case 'ctfu':
                            $login_sites = 4;
                            break;
                    }
                    break;
                }
            case 1: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 2;
                            break;
                        case 'cis':
                            $login_sites = 1;
                            break;
                        case 'ctfu':
                            $login_sites = 6;
                            break;
                    }
                    break;
                }
            case 2: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 2;
                            break;
                        case 'cis':
                            $login_sites = 2;
                            break;
                        case 'ctfu':
                            $login_sites = 5;
                            break;
                    }
                    break;
                }
            case 3: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 4;
                            break;
                        case 'cis':
                            $login_sites = 6;
                            break;
                        case 'ctfu':
                            $login_sites = 3;
                            break;
                    }
                    break;
                }
            case 4: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 4;
                            break;
                        case 'cis':
                            $login_sites = 5;
                            break;
                        case 'ctfu':
                            $login_sites = 4;
                            break;
                    }
                    break;
                }
            case 5: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 5;
                            break;
                        case 'cis':
                            $login_sites = 5;
                            break;
                        case 'ctfu':
                            $login_sites = 5;
                            break;
                    }
                    break;
                }
            case 6: {
                    switch ($new_project){
                        case 'aies':
                            $login_sites = 5;
                            break;
                        case 'cis':
                            $login_sites = 6;
                            break;
                        case 'ctfu':
                            $login_sites = 6;
                            break;
                    }
                    break;
                }
            default:
                $login_sites = 99;
                break;
        }
        return $login_sites;
    }
    //update loginsites in case of remove one project
    public static function remove_login_sites($remove_site, $old_login_sites) {
        //$remove_site my be aies, cis, ctfu
        $login_sites = $old_login_sites;
        switch ($old_login_sites) {
            case 0: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 0;
                            break;
                        case 'cis':
                            $login_sites = 0;
                            break;
                        case 'ctfu':
                            $login_sites = 0;
                            break;
                    }
                    break;
                }
            case 1: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 1;
                            break;
                        case 'cis':
                            $login_sites = 0;
                            break;
                        case 'ctfu':
                            $login_sites = 1;
                            break;
                    }
                    break;
                }
            case 2: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 1;
                            break;
                        case 'cis':
                            $login_sites = 0;
                            break;
                        case 'ctfu':
                            $login_sites = 2;
                            break;
                    }
                    break;
                }
            case 3: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 3;
                            break;
                        case 'cis':
                            $login_sites = 3;
                            break;
                        case 'ctfu':
                            $login_sites = 0;
                            break;
                    }
                    break;
                }
            case 4: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 3;
                            break;
                        case 'cis':
                            $login_sites = 4;
                            break;
                        case 'ctfu':
                            $login_sites = 0;
                            break;
                    }
                    break;
                }
            case 5: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 6;
                            break;
                        case 'cis':
                            $login_sites = 4;
                            break;
                        case 'ctfu':
                            $login_sites = 2;
                            break;
                    }
                    break;
                }
            case 6: {
                    switch ($remove_site){
                        case 'aies':
                            $login_sites = 6;
                            break;
                        case 'cis':
                            $login_sites = 3;
                            break;
                        case 'ctfu':
                            $login_sites = 1;
                            break;
                    }
                    break;
                }
            default:
                $login_sites = $old_login_sites;
                break;
        }
        return $login_sites;
    }

}

?>
