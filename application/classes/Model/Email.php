<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Email {
    /* insert request type  */

    public static function user_request($reference_id, $user_id, $request_type, $company_name, $status, $requested_value, $concerned_person_id, $projectids, $startDate, $endDate, $reason, $force_imei_last_digit_zero = 0, $file_name = '') {

        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        if ($permission == 1) {
            $request_priority = 3;
        } else {
            $request_priority = 1;
        }

        $date = date('Y-m-d H:i:s');
        // file_name is the absolute disk path of the optional "Requested
        // Attachment" file the analyst picked on the request form. NULL /
        // empty string when no file was attached (the column is nullable
        // and the four user-request forms make the input optional, mirroring
        // admin_request_sent_form). Stored as VARCHAR(250) to match the
        // admin_request column shape exactly.
        $query = DB::insert('user_request', array('reference_id', 'user_id', 'user_request_type_id', 'company_name', 'status', 'concerned_person_id', 'project_id', 'requested_value', 'startDate', 'endDate', 'reason', 'request_priority', 'force_imei_last_digit_zero', 'file_name'))
                ->values(array($reference_id, $user_id, $request_type, $company_name, $status, $concerned_person_id, $projectids, $requested_value, $startDate, $endDate, $reason, $request_priority, $force_imei_last_digit_zero, $file_name))
                ->execute();
        $query1 = DB::insert('person_linked_projects', array('user_id', 'request_type_id', 'person_id', 'project_id', 'requested_value', 'request_time'))
                ->values(array($user_id, $request_type, $concerned_person_id, $projectids, $requested_value, $date))

                ->execute();
//        echo '<pre>';
//        print_r($projectids);
//        exit();
        return $query[0];
    }



    public static function email_status($reference_number, $status, $process_status) {


        $query = DB::update('user_request')->set(array('status' => $status, 'processing_index' => $process_status))
                ->where('request_id', '=', $reference_number);
        $query->execute();
    }

    /* Update Error Messages */

    public static function file_status($reference_number, $error_status, $upload_status) {
        $query = DB::update('files')->set(array('error_type' => $error_status, 'upload_status' => $upload_status))
                ->where('id', '=', $reference_number)
                ->execute();
    }

    /* eMAIL SENDING sTATUS */

    public static function email_sending_status($reference_number, $status, $process_status) {
        $date = date('Y-m-d H:i:s');
        $query = DB::update('user_request')->set(array('status' => $status,
                    'processing_index' => $process_status, 'sending_date' => $date, 'request_send_count' => DB::expr('request_send_count + 1')))
                ->where('request_id', '=', $reference_number)
                ->execute();
    }

    /* email sended  */

    public static function email_sended($to, $subject, $body, $reference_number, $process_status, $status, $startDate = NULL, $enddate = NULL) {
        $startDate = !empty($startDate) ? (date('Y-m-d', strtotime($startDate))) : $startDate;
        $enddate = !empty($enddate) ? (date('Y-m-d', strtotime($enddate))) : $enddate;

        $date = date('Y-m-d H:i:s');
        $query = DB::insert('email_messages', array('sender_id', 'message_body', 'message_subject', 'message_date'))
                ->values(array($to, $body, $subject, $date))
                ->execute();
        //print_r($query);
        $query = DB::update('user_request')->set(array('status' => $status, 'processing_index' => $process_status, 'startDate' => $startDate, 'endDate' => $enddate, 'message_id' => $query[0]))
                ->where('request_id', '=', $reference_number)
                ->execute();        
//        return $query[0]; //updated by yaser 28-07-20
        return $reference_number;
    }

    /* get email templates  */

    public static function get_email_tempalte($email_type, $company_name) {
//        echo '<pre>';
//        print_r($email_type);
//        print_r(' ');
//        print_r($company_name);
//
//        exit();
         $query = DB::select()
                        ->from('email_templates')
                        ->where('email_type', '=', $email_type)
                        ->where('company_id', '=', $company_name)
                        ->execute()->current();

        return $query;
    }

    /* single email view  */

    public static function view($id) {
        return $query = DB::select()
                        ->from('email_templates')
                        ->where('id', '=', $id)
                        ->execute()->current();
    }
    /* single short code view  */

    public static function sc_view($id) {
        return $query = DB::select()
                        ->from('telco_short_code')
                        ->where('id', '=', $id)
                        ->execute()->current();
    }

    /* delete email template   */

    public static function deleted($id) {

        $query = DB::delete('email_templates')
                ->where('id', '=', $id)
                ->execute();
        //to add activity of user
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 14);

        return $query;
    }
    /* delete short code  */

    public static function sc_deleted($id) {     

        $query = DB::delete('telco_short_code')
                ->where('id', '=', $id)
                ->execute();
        //to add activity of user
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 89);

        return $query;
    }

    /* view all email template  */

    public static function emailtemplate() {
        $query = "SELECT et.* FROM email_templates as et inner join email_templates_type AS emt on emt.id = et.email_type WHERE emt.is_active = 1 and emt.is_deleted = 0 order by et.id DESC";
        $sql = DB::query(Database::SELECT, $query);

        $result = $sql->execute();

        return $result;
    }
    /* view all short code  */

    public static function shortcode_list() {
        $query = "SELECT * FROM telco_short_code  order by id DESC";
        $sql = DB::query(Database::SELECT, $query);

        $result = $sql->execute();

        return $result;
    }

    /* template data insert */

    public static function templateinsert($data) {
        $query = DB::insert('email_templates', array('email_type', 'user_id', 'from_email', 'company_id', 'subject', 'body_txt', 'header_img', 'footer_img'))
                //->values(array($data['email_type_name'], $data['from_email'], $data['subject'], $data['body_txt'], $data['header_img'], $data['footer_img']))
                ->values(array($data['email_type_name'], $data['user_id'], '', $data['company_name'], $data['subject'], $data['body'], '', ''))
                //->values(array('abc', 'form_email', 'subject', 'body-test', 'header', 'footer'))
                ->execute();
        $uid = $data['user_id'];
        Helpers_Profile::user_activity_log($uid, 16);
    }
    /* template data insert */

    public static function shortcode_insert($data) {
        $query = DB::insert('telco_short_code', array( 'company_name', 'code', 'created_by'))
                //->values(array($data['email_type_name'], $data['from_email'], $data['subject'], $data['body_txt'], $data['header_img'], $data['footer_img']))
                ->values(array($data['company_name'],$data['code'], $data['user_id']))
                //->values(array('abc', 'form_email', 'subject', 'body-test', 'header', 'footer'))
                ->execute();
        $uid = $data['user_id'];
        Helpers_Profile::user_activity_log($uid, 87);
    }
    /* short code updated */

    public static function shortcode_update($data) {
//        echo '<pre>';
//        print_r($data);
//        exit();
        $query = DB::update('telco_short_code')->set(array('company_name' => $data['company_name'], 'code' => $data['code'], 'updated_by' => $data['user_id']))
            ->where('id', '=', $data['id'])
            ->execute();

        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 88);
    }

    /* template data updated */

    public static function update($data) {
        $query = DB::update('email_templates')->set(array('email_type' => $data['email_type_name'], 'from_email' => '',
                    'subject' => $data['subject'], 'company_id' => $data['company_name'], 'body_txt' => $data['body'], 'header_img' => '', 'footer_img' => ''))
                ->where('id', '=', $data['id'])
                ->execute();

        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 15);
    }


    /* select all or specific email template type */

    public static function typeselection($id = Null) {
        if (!empty($id)) {
            return $query = DB::select()
                            ->from('email_templates_type')
                            ->where('id', '=', $id)
                            ->and_where('is_active', '=', 1)
                            ->and_where('is_deleted', '=', 0)
                            ->order_by('id', 'ASC')
                            ->limit(1)
                            ->execute()->current();
        } else {
            return $query = DB::select()
                            ->from('email_templates_type')
                            ->where('is_active', '=', 1)
                            ->and_where('is_deleted', '=', 0)
                            ->order_by('id', 'ASC')
                            ->execute()->as_array();
        }
    }

    /* get type id by type name */

    public static function get_type_id($type_name) {

        $sql = DB::query(Database::SELECT, "SELECT * FROM email_templates_type WHERE email_type_name=:type_name");
        $sql->param(':type_name', $type_name);

        $result = $sql->as_object()->execute();
        return $result;
    }

    /* Get all or specific token of the template */

    public static function tokentemplate($id = Null) {

        if (!empty($id)) {
            $id = (int) $id;
            $sql = "SELECT * FROM email_tokens
                    WHERE extra1 = {$id} ";
        } else {
            $sql = "SELECT * FROM email_tokens
                    WHERE extra1 = {$id} ";
        }
        return DB::query(Database::SELECT, $sql)->as_object()->execute();
    }

    /* template data insert */

    public static function user_insert($data) {
        $region = '';
        $check_region = explode('-', $data['posting']);
        if ($check_region[0] == 'd') {
            $region_query = DB::select()
                            ->from('district')
                            ->where('district_id', '=', $check_region[1])
                            ->execute()->current();
            $region = $region_query['region_id'];
        } elseif ($check_region[0] == 'p') {
//            $region_query = DB::select()
//                        ->from('ctd_police_station')
//                        ->where('region_id', '=', $check_region[1])
//                        ->execute()->current();       
            $region = $check_region[1];
        } elseif ($check_region[0] == 'r') {
            $region = $check_region[1];
        } elseif ($check_region[0] == 'h') {
            $region = 1;
        }

        $query = DB::insert('users_profile', array('user_id', 'first_name', 'last_name', 'father_name', 'mobile_number', 'job_title', 'department',
                    'district_id', 'region_id', 'posted', 'belt', 'created_by', 'created_at', 'file_name', 'order_no', 'cnic_number'))
                ->values(array(trim($data['user_id']), trim($data['first_name']), trim($data['last_name']), trim($data['father_name']), $data['mobile_number'], $data['designation'], '',
                    $data['home_district'], $region, $data['posting'], $data['belt'], $data['created_by'], $data['created_at'], $data['file_name'], $data['order'], $data['cnic_number']
                ))
                ->execute();
        //insert entry into user_activity_timeline
        if (empty($data['uid'])) {
            $login_user = Auth::instance()->get_user();
            $uid = !empty($login_user->id) ? $login_user->id : 0;
        } else {
            $uid = $data['uid'];
        }
        Helpers_Profile::user_activity_log($uid, 3);
    }

    //telco email config
    public static function update_telco_emails($emails) {
        $emails['mis_second'] = isset($emails['mis_second']) ? 1 : 0;
        $emails['tis_second'] = isset($emails['tis_second']) ? 1 : 0;
        $emails['uis_second'] = isset($emails['uis_second']) ? 1 : 0;
        $emails['wis_second'] = isset($emails['wis_second']) ? 1 : 0;
        $emails['zis_second'] = isset($emails['zis_second']) ? 1 : 0;
        $query = DB::update('telco_emails')->set(array('email1' => $emails['mobilink1'], 'email2' => $emails['mobilink2'], 'is_second' => $emails['mis_second']))
                ->where('mnc', '=', 1)
                ->execute();
        $query = DB::update('telco_emails')->set(array('email1' => $emails['ufone1'], 'email2' => $emails['ufone2'], 'is_second' => $emails['uis_second']))
                ->where('mnc', '=', 3)
                ->execute();
        $query = DB::update('telco_emails')->set(array('email1' => $emails['zong1'], 'email2' => $emails['zong2'], 'is_second' => $emails['zis_second']))
                ->where('mnc', '=', 4)
                ->execute();
        $query = DB::update('telco_emails')->set(array('email1' => $emails['telenor1'], 'email2' => $emails['telenor2'], 'is_second' => $emails['tis_second']))
                ->where('mnc', '=', 6)
                ->execute();
        $query = DB::update('telco_emails')->set(array('email1' => $emails['warid1'], 'email2' => $emails['warid2'], 'is_second' => $emails['wis_second']))
                ->where('mnc', '=', 7)
                ->execute();
        return 1;
    }

    public static function insert_request_ctfu($reference_id , $user_id, $request_type, $bank_id, $request_status, $requested_value, $concerned_person_id, $project_id, $reason){                
        $date = date('Y-m-d H:i:s');
        $query = DB::insert('ctfu_user_request', array('reference_id','user_id', 'user_request_type_id', 'bank_id', 'request_status','requested_value', 'concerned_person_id', 'project_id', 'reason'))
                ->values(array($reference_id , $user_id, $request_type, $bank_id, $request_status, $requested_value, $concerned_person_id, $project_id, $reason))
                ->execute();
        $query1 = DB::insert('person_linked_projects', array('user_id', 'request_type_id', 'person_id', 'project_id', 'requested_value', 'request_time'))
                ->values(array($user_id, $request_type, $concerned_person_id, $project_id, $requested_value, $date))
                ->execute();
        return $query[0];
    }

}
