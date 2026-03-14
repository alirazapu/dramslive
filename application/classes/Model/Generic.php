<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with Manual Subscriber Entry   
 */
class Model_Generic {

    /**
     * Project IDs that require the "use existing concerned_person_id" guard
     * in ManualSubInfoinsert() instead of the default CNIC-based person
     * creation path.  Add project IDs here if the same behaviour is needed
     * for other projects in the future.
     */
    const PROJECTS_SKIP_CNIC_CREATE = [1629];

    /* Manual Subscriber Entry data insert */

    public static function imei_finder($imei) {
        $imeino = Helpers_Utilities::find_imei_last_digit($imei);
        return $imeino;
    }

    public static function ManualSubInfoinsert($data) {
        //parametesrs for this function
        /* $data['imei']
          /* $data['imsi']
         * $data['cnic_number']
         * $data['person_name']
         * $data['person_name1']
         * $data['address']
         * $data['user_id']
         * $data['is_foreigner']
         * $data['inputproject'][0]
         * $data['act_date']
         * $data['StatusRadios']
         * $data['ConnectionTypeRadios']
         * $data['company_name_get']
         * $data['mobile_number']
         * $data['phone_name']
         */
        $user_obj = Auth::instance()->get_user();
        $current_date = date('Y-m-d H:i:s');
        if (!empty($data['act_date'])) {
            $newDate = date("Y-m-d H:i:s", strtotime($data['act_date']));
        } else {
            $newDate = $current_date;
        }
        if (empty($data['inputproject'])) {
            $data['inputproject'][0] = 1;
        }
        //chect foreign is set
        if (!empty($data['is_foreigner'])) {
            $data['is_foreigner'] = 1;
        } else {
            $data['is_foreigner'] = 0;
        }

        // create new person otherwise it will returen person_id
        /* Parameters in array:
         * cnic_number
         * first_name
         * middle_name
         * last_name
         * father_name
         * address
         * is_foreigner
         * project_id
         * reason
         */
        // Determine project context (passed from cronjob when available)
        $project_id_ctx        = isset($data['project_id'])         ? (int) $data['project_id']         : 0;
        $concerned_person_id_ctx = isset($data['concerned_person_id']) ? (int) $data['concerned_person_id'] : 0;

        if (in_array($project_id_ctx, self::PROJECTS_SKIP_CNIC_CREATE, true) && $concerned_person_id_ctx > 0) {
            // ----------------------------------------------------------------
            // Project-1629 guard: use the existing person referenced by the
            // request's concerned_person_id.  Do NOT call update_cnic_number()
            // because that function can create a new person and causes
            // person_phone_number.sim_owner to drift away from person_id.
            // ----------------------------------------------------------------
            $person_id = $concerned_person_id_ctx;

            Model_ErrorLog::log(
                'ManualSubInfoinsert',
                'Project-1629 guard: enriching existing person, skipping CNIC-based person creation',
                [
                    'request_id'          => isset($data['requestid'])        ? $data['requestid']        : null,
                    'project_id'          => $project_id_ctx,
                    'concerned_person_id' => $concerned_person_id_ctx,
                    'requested_value'     => isset($data['mobile_number'])    ? $data['mobile_number']    : null,
                    'company_name'        => isset($data['company_name_get']) ? $data['company_name_get'] : null,
                ],
                null,
                'info',
                'subscriber_insert_project1629'
            );

            // Fill blanks only in person_initiate (CNIC fields)
            $existing_pi = DB::select()
                ->from('person_initiate')
                ->where('person_id', '=', $person_id)
                ->limit(1)
                ->execute()
                ->current();

            if ($existing_pi !== false) {
                $pi_updates = [];
                if (!empty($data['cnic_number']) &&
                    // '0' is the sentinel stored when CNIC was not available at import time
                    (empty($existing_pi['cnic_number']) || $existing_pi['cnic_number'] === '0')
                ) {
                    $pi_updates['cnic_number'] = $data['cnic_number'];
                }
                if (!empty($data['cnic_number_foreigner']) && empty($existing_pi['cnic_number_foreigner'])) {
                    $pi_updates['cnic_number_foreigner'] = $data['cnic_number_foreigner'];
                }
                if (!empty($pi_updates)) {
                    DB::update('person_initiate')
                        ->set($pi_updates)
                        ->where('person_id', '=', $person_id)
                        ->execute();
                }
            }

            // Fill blanks only in person (name / address)
            $existing_person = DB::select()
                ->from('person')
                ->where('person_id', '=', $person_id)
                ->limit(1)
                ->execute()
                ->current();

            if ($existing_person !== false) {
                $p_updates = [];
                if (!empty($data['person_name']) && empty($existing_person['first_name'])) {
                    $p_updates['first_name'] = ucwords(trim((string) $data['person_name']));
                }
                if (!empty($data['person_name1']) && empty($existing_person['last_name'])) {
                    $p_updates['last_name'] = ucwords(trim((string) $data['person_name1']));
                }
                if (!empty($data['address']) && empty($existing_person['address'])) {
                    $p_updates['address'] = ucwords(trim((string) $data['address']));
                }
                if (!empty($p_updates)) {
                    DB::update('person')
                        ->set($p_updates)
                        ->where('person_id', '=', $person_id)
                        ->execute();
                }
            }
        } else {
            // Default path: create or retrieve a person record via CNIC logic
            $array_person['cnic_number'] = $data['cnic_number'];
            $array_person['first_name'] = $data['person_name'];
            $array_person['last_name'] = $data['person_name1'];
            $array_person['father_name'] = '';
            $array_person['address'] = $data['address'];
            $array_person['is_foreigner'] = $data['is_foreigner'];
            $array_person['project_id'] = $data['inputproject'][0];
            $array_person['reason'] = '';
            $array_person['user_id'] = $data['user_id'];
            $content = new Model_Generic();
            $person_id = $content->update_cnic_number($array_person);
        }

        //update mobile details (subscriber)
        /* Parameters in array:
         * mobile_number
         * mnc
         * sim_status
         * person_id
         * user_id
         * imsi
         * connection_type
         * sim_activated_at
         */
        $array_sub['mobile_number'] = $data['mobile_number'];
        $array_sub['mnc'] = $data['company_name_get'];
        $array_sub['sim_status'] = $data['StatusRadios'];
        $array_sub['person_id'] = $person_id;
        $array_sub['user_id'] = $data['user_id'];
        $array_sub['imsi'] = $data['imsi'];
        $array_sub['connection_type'] = $data['ConnectionTypeRadios'];
        $array_sub['sim_activated_at'] = $data['act_date'];
        $content = new Model_Generic();
        $sub_update_status = $content->update_subscriber_details($array_sub);

        //update mobile imei number
        /* Parameters in array:
         * imei  
         * person_id
         * mobile_number
         * phone_name
         * in_use_since
         * last_interaction_at
         * user_id
         * is_active
         */
        $array_imei['imei'] = $data['imei'];
        $array_imei['person_id'] = $person_id;
        $array_imei['mobile_number'] = $data['mobile_number'];
        $array_imei['phone_name'] = $data['phone_name'];
        $array_imei['in_use_since'] = $data['act_date'];
        $array_imei['last_interaction_at'] = $data['act_date'];
        $array_imei['user_id'] = $data['user_id'];
        $array_imei['is_active'] = $data['StatusRadios'];
        $content = new Model_Generic();
        $sub_update_status = $content->update_imei_mobile_number($array_imei);

        // update person details (unconditional overwrite — default path only)
        // For projects in PROJECTS_SKIP_CNIC_CREATE the fill-blanks update was already applied above.
        if (!in_array($project_id_ctx, self::PROJECTS_SKIP_CNIC_CREATE, true)) {
            if ( isset($person_id) && !empty($person_id) &&
                isset($data['person_name']) && isset($data['person_name1']) &&
                !empty($data['person_name']) && !empty($data['person_name1']) &&
                isset($data['address']) && !empty($data['address'])
            ) {
                DB::update('person')
                    ->set([
                        'first_name' => ucwords(trim((string) $data['person_name'])),
                        'last_name'  => ucwords(trim((string) $data['person_name1'])),
                        'address'    => ucwords(trim((string) $data['address'])),
                    ])
                    ->where('person_id', '=', (int) $person_id)
                    ->execute();
            }
        }

        if (!empty($data['requestid'])) {
            $reference_number = Model_Email::email_status($data['requestid'], 2, 5);
            return -7;
        }
    }

    /* Manual Location Entry data insert */

    public static function ManualLocationinsert($data) {
        // echo '<pre>';        print_r($data); exit;
        if (empty($data['user_id'])) {
            $login_user = Auth::instance()->get_user();
            $login_id = $login_user->id;
            $uid = $login_id;
        } else {
            $uid = $data['user_id'];
        }
        date_default_timezone_set("Asia/Karachi");
        $newDate = date('Y-m-d H:i:s', time());
        $locationdate = date("Y-m-d H:i:s", strtotime($data['locdate']));
        //update person mobile number details
        $query = DB::update('person_phone_number');
        if (!empty($data['locimsi']))
            $query->set(array('imsi_number' => $data['locimsi']));

        if (!empty($data['locdate']))
            $query->set(array('sim_last_used_at' => $locationdate));

        if (!empty($data['user_id']))
            $query->set(array('user_id' => $data['user_id']));

        if ($data['locstatus'] != 0)
            $query->set(array('status' => 1));

        $query->where('phone_number', '=', $data['locationmsisdn'])
                ->execute();
        Helpers_Profile::user_activity_log($uid, 23, "Mobile Info Updated", $data['locationmsisdn']);

        //update mobile imei number
        /* Parameters in array:
         * imei  
         * person_id
         * mobile_number
         * phone_name
         * in_use_since
         * last_interaction_at
         * user_id
         * is_active
         */
        $array_imei['imei'] = $data['locimei'];
        $array_imei['person_id'] = $data['person_id'];
        $array_imei['mobile_number'] = $data['locationmsisdn'];
        $array_imei['phone_name'] = $data['locphonename'];
        $array_imei['in_use_since'] = '';
        $array_imei['last_interaction_at'] = $locationdate;
        $array_imei['user_id'] = $data['user_id'];
        $array_imei['is_active'] = 1;
        $content = new Model_Generic();
        $sub_update_status = $content->update_imei_mobile_number($array_imei);

        // print_r($data); exit;
        //update person location history details
        $query = DB::insert('person_location_history', array('person_id', 'phone_number', 'mnc', 'network', 'lac_id', 'cell_id', 'sector', 'latitude', 'longitude', 'address', 'moved_in_at', 'moved_out_at', 'status'))
                ->values(array($data['person_id'], $data['locationmsisdn'], $data['loccompany'], $data['locnetwork'], $data['loclac'], $data['loccellid'], '', $data['loclat'], $data['loclong'], $data['locaddress'], $locationdate, '', $data['locstatus']))
                ->execute();

        Helpers_Profile::user_activity_log($uid, 24, NULL, NULL, $data['person_id']);
        // for request status
        if (!empty($data['requestid'])) {
            $reference_number = Model_Email::email_status($data['requestid'], 2, 5);
            return -7;
        }
    }

    /* create person with cnic */

    public static function update_imei_mobile_number($data) {

        /* Parameters in array:
         * imei  
         * person_id
         * mobile_number
         * phone_name
         * in_use_since
         * last_interaction_at
         * user_id
         * is_active
         */
        $imei_number = !empty($data['imei']) ? $data['imei'] : 0;
        
        if (!empty($imei_number)) {
            $imei_number = Helpers_Utilities::find_imei_last_digit($imei_number);
            // to check if imei number exist
            //  $chkimei = Helpers_Utilities::check_imei_exist($imei_number);
            $current_date = date('Y-m-d H:i:s');

            $person_id = !empty($data['person_id']) ? $data['person_id'] : 0;
            $mobile_number = !empty($data['mobile_number']) ? $data['mobile_number'] : 0;
            $phone_name = !empty($data['phone_name']) ? $data['phone_name'] : '';
            $in_use_since = !empty($data['in_use_since']) ? date("Y-m-d H:i:s", strtotime($data['in_use_since'])) : '';
            $last_interaction_at = !empty($data['last_interaction_at']) ? date("Y-m-d H:i:s", strtotime($data['last_interaction_at'])) : '';
            $login_user = Auth::instance()->get_user();
            $uid = !empty($login_user->id) ? $login_user->id :1;
            $user_id = !empty($data['user_id']) ? $data['user_id'] : $uid;

            // to update imei number
            if (!empty($imei_number)) {
                $send_array['imei'] = $imei_number;
                $send_array['person_id'] = $person_id;
                $send_array['phone_name'] = $phone_name;
                $send_array['in_use_since'] = $in_use_since;
                $send_array['last_interaction_at'] = $last_interaction_at;
                $send_array['user_id'] = $user_id;
                $content = new Model_Generic();
                $device_id = $content->update_imei_number($send_array);
                return $device_id;
            }
            
            if (empty($device_id)) {
                $device_id = Helpers_Utilities::get_device_id($imei_number);
            }

            if (!empty($mobile_number)) {
                $chkdeviceno = Helpers_Utilities::check_device_number_exist($device_id, $mobile_number);
                $imeimobile = $imei_number . "/" . $mobile_number;
                if ($chkdeviceno == 0) {
                    $usefrom = !empty($data['use_from']) ? $data['use_from'] : $last_interaction_at;
                    $useto = !empty($data['use_to']) ? $data['use_to'] : '';
                    DB::update('person_device_numbers')->set(array('is_active' => 0))
                            ->where('phone_number', '=', $mobile_number)
                            ->execute();
                    $query = DB::insert('person_device_numbers', array('device_id', 'phone_number', 'is_active', 'first_use', 'last_use'))
                            ->values(array($device_id, $mobile_number, 1, $usefrom, $useto))
                            ->execute();
                    Helpers_Profile::user_activity_log($user_id, 79, "IMEI/Mobile", $imeimobile, $person_id);
                } else {
                    DB::update('person_device_numbers')->set(array('is_active' => 0))
                            ->where('phone_number', '=', $mobile_number)
                            ->execute();
                    DB::update('person_device_numbers')->set(array('is_active' => 1))
                            ->where('phone_number', '=', $mobile_number)
                            ->and_where('device_id', '=', $device_id)
                            ->execute();
                    Helpers_Profile::user_activity_log($user_id, 80, "IMEI/Mobile", $imeimobile, $person_id);
                }
            }

            return 1;
        } else {
            return 0;
        }
    }

    /* function to update imei number in aies database */

    public static function update_imei_number($data) {
        /* Parameters in array:
         * imei  
         * person_id
         * phone_name
         * in_use_since
         * last_interaction_at
         * user_id
         */
        $imei_number = !empty($data['imei']) ? $data['imei'] : 0;
        $device_id = 0;
        if (!empty($imei_number)) {
           
            $imei_number = Helpers_Utilities::find_imei_last_digit($imei_number);
            $current_date = date('Y-m-d H:i:s');

            $person_id = !empty($data['person_id']) ? $data['person_id'] : 0;
            $phone_name = !empty($data['phone_name']) ? $data['phone_name'] : '';
            $in_use_since = !empty($data['in_use_since']) ? date("Y-m-d H:i:s", strtotime($data['in_use_since'])) : '';
            $last_interaction_at = !empty($data['last_interaction_at']) ? date("Y-m-d H:i:s", strtotime($data['last_interaction_at'])) : '';
            $login_user = Auth::instance()->get_user();
             $uid = !empty($login_user->id) ? $login_user->id :1;
            $user_id = !empty($data['user_id']) ? $data['user_id'] : $uid;
            // to check if imei number exist
            $chkimei = Helpers_Utilities::check_imei_exist($imei_number);
            // if imei number not exist then new entry 
            if (empty($chkimei) && !empty($imei_number)) {
                $query = DB::insert('person_phone_device', array('person_id', 'imei_number', 'phone_name', 'in_use_since', 'last_interaction_at', 'user_id'))
                        ->values(array($person_id, $imei_number, $phone_name, $in_use_since, $last_interaction_at, $user_id))
                        ->execute();
                $device_id = $query[0];
                Helpers_Profile::user_activity_log($user_id, 78, "IMEI No", $imei_number, $person_id);
            } elseif (!empty($chkimei) && !empty($imei_number)) {
    
                $device_id = Helpers_Utilities::get_device_id($imei_number);
                if (!empty($person_id)) {
                    
                    DB::update('person_phone_device')->set(array('person_id' => $person_id, 'user_id' => $user_id))
                            ->where('id', '=', $device_id)
                            ->execute();
                    Helpers_Profile::user_activity_log($user_id, 77, "IMEI No", $imei_number, $person_id);
                }
            }
            return $device_id;
        } else {
            return $device_id;
        }
    }

    /* create person with cnic */

    public static function update_cnic_number($data, $chk = Null) {

        /* Parameters in array:
         * cnic_number
         * first_name
         * middle_name
         * last_name
         * father_name
         * address
         * is_foreigner
         * project_id
         * reason
         * user_id
         * created_from
         * access_by
         * cis_desktop
         */
        $cnic_number = !empty($data['cnic_number']) ? $data['cnic_number'] : '';
       // $cnic_number = !empty($data) ? $data : '';
        $person_id = Helpers_Utilities::get_person_id_with_cnic($cnic_number);
        if (empty($person_id) && !empty($cnic_number)) {
            $first_name = !empty($data['first_name']) ? $data['first_name'] : '';
            $middle_name = !empty($data['middle_name']) ? $data['middle_name'] : '';
            $last_name = !empty($data['last_name']) ? $data['last_name'] : '';
            $father_name = !empty($data['father_name']) ? $data['father_name'] : '';
            $address = !empty($data['address']) ? $data['address'] : '';
            $is_foreigner = !empty($data['is_foreigner']) ? $data['is_foreigner'] : 0;
            $project_id = !empty($data['project_id']) ? $data['project_id'] : 1;
            $created_from = !empty($data['created_from']) ? $data['created_from'] : 0;
            $access_by = !empty($data['access_by']) ? $data['access_by'] : 0;
            $reason = !empty($data['reason']) ? $data['reason'] : '';
            //force to make foreginer if cnic is string
            if (ctype_digit(trim($cnic_number))) {
                
            } else {
                $is_foreigner = 1;
            }
            
            if ($is_foreigner==1)
            {   
                if (ctype_digit(trim($cnic_number))) 
                    $chk = null;
                else 
                    $chk = 1;
            }
            
            if (empty($data['user_id'])) {
                try {
                    $login_user = Auth::instance()->get_user();
                    $uid = $login_user->id;
                } catch (Exception $ex) {
                    $uid=0;
                }
                
            } else {
                $uid = $data['user_id'];
            }

            $current_date = date('Y-m-d H:i:s');

            //getting person_id            
            $person_id = Helpers_Utilities::id_generator("person_id");

            //echo $person_id;
            if (empty($is_foreigner)) {
                if(!empty($chk)) {
                    $query = DB::insert('person_initiate', array('person_id', 'cnic_number', 'cnic_number_foreigner', 'is_foreigner', 'is_fingerprints_exist', 'user_id', 'created_from', 'access_by', 'created_at', ))
                        ->values(array($person_id, $cnic_number, 0, $is_foreigner, 0, $uid, $chk, $access_by, $current_date))
                        ->execute();
                }
                else{
                    $query = DB::insert('person_initiate', array('person_id', 'cnic_number', 'cnic_number_foreigner', 'is_foreigner', 'is_fingerprints_exist', 'user_id', 'created_from', 'access_by', 'created_at'))
                        ->values(array($person_id, $cnic_number, 0, $is_foreigner, 0, $uid, $created_from, $access_by, $current_date))
                        ->execute();
                }
                
                $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'user_id', 'person_name','person_g_name', 'person_gender', 'person_dob', 'person_present_add', 'person_permanent_add', 'person_photo_url', 'person_nadra_status', 'is_cnic_image_available'))
                        ->values(array($person_id, $cnic_number, $uid,0,0,0,0,0,0,0,0,0))
                        ->execute();
              
                
            } else {
                if(!empty($chk)) {
                    $query = DB::insert('person_initiate', array('person_id', 'cnic_number', 'cnic_number_foreigner', 'is_foreigner', 'is_fingerprints_exist', 'user_id', 'created_from', 'access_by', 'created_at'))
                        ->values(array($person_id, 0, $cnic_number, $is_foreigner, 0, $uid, $chk, $access_by, $current_date))
                        ->execute();
                }
                else
                {
                    $query = DB::insert('person_initiate', array('person_id', 'cnic_number', 'cnic_number_foreigner', 'is_foreigner', 'is_fingerprints_exist', 'user_id', 'created_from', 'access_by', 'created_at'))
                                                 ->values(array($person_id, $cnic_number, 0, $is_foreigner, 0, $uid, $created_from, $access_by, $current_date))
                        ->execute();
                }
                $query = DB::insert('person_foreigner_profile', array('person_id', 'person_name', 'cnic_number', 'user_id'))
                        ->values(array($person_id, $first_name, $cnic_number, $uid))
                        ->execute();
            }

            $query = DB::insert('person', array('person_id', 'first_name', 'middle_name', 'last_name', 'father_name', 'address', 'user_id', 'view_access_level_id', 'edit_access_level_id', 'is_complete'))
                    ->values(array($person_id, $first_name, $middle_name, $last_name, $father_name, $address, $uid,0,0,0))
                    ->execute();

            $query = DB::insert('person_category', array('person_id', 'category_id', 'project_id', 'reason', 'user_id', 'added_on'))
                    ->values(array($person_id, 0, $project_id, $reason, $uid, $current_date))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 76, NULL, NULL, $person_id);
        }else{
            if(!empty($data['created_from_name']) && ($data['created_from_name']=='cis_desktop') && $data['created_from']==1 ){
        $query = DB::update('person_initiate')->set(array('access_by' => 2)) //updating access by=2, accessable by aies and cis
                ->where('person_id', '=', $person_id)
                ->execute();
            }
        }        
                        
        // to make and get folder for person data
    //    $person_folder_path = !empty($person_id) ? Helpers_Upload::make_and_get_person_data_directory($person_id) : '';   // uncomment before commiting this code

        return $person_id;
    }
    /* msisdn data upload */

    public static function data_up_against_msisdn($data, $user_id, $file) {



        /* Parameters in array:
         *  is_foreigner
         *  project_id
         *  Mobile No.
         *  Activation date
         * IMSI
         * IMEI
         * first_name
         * last_name
         * cnic_number
         * address
         * connection type
         * status
         * picture
         * user_id
         */




        $country = !empty($data['is_foreigner']) ? $data['is_foreigner'] : 0;
        $project_id = !empty($data['inputproject']) ? $data['inputproject'] : 1;
        $mobile_no = !empty($data['mobile_number']) ? $data['mobile_number'] : 0;
     //   $act_date1 = !empty($data['act_date']) ? $data['act_date'] : 0;
        $act_date = date("Y-m-d", strtotime($data['act_date']));
        $imsi = !empty($data['imsi']) ? $data['imsi'] : 0;
        $imei = !empty($data['imei']) ? $data['imei'] : 0;
        $first_name = !empty($data['first_name']) ? $data['first_name'] : '';
        $last_name = !empty($data['last_name']) ? $data['last_name'] : '';
        $cnic_number = !empty($data['cnic_number']) ? $data['cnic_number'] : 0;
        $address = !empty($data['address']) ? $data['address'] : '';
        $con_type = !empty($data['ConnectionTypeRadios']) ? $data['ConnectionTypeRadios'] : 0;
        $status = !empty($data['StatusRadios']) ? $data['StatusRadios'] : 0;
//        $image = !empty($file) ? $file : '';




            $current_date = date('Y-m-d H:i:s');
            $query = DB::insert('old_data', array('country', 'project_id', 'phone_number', 'activation_date', 'imsi_number', 'imei_number', 'first_name', 'last_name', 'cnic_number', 'address', 'con_type', 'status','file', 'user_id', 'created_at' ))
                        ->values(array($country, $project_id, $mobile_no, $act_date, $imsi, $imei, $first_name, $last_name, $cnic_number, $address, $con_type, $status,  $file, $user_id, $current_date ))
                        ->execute();

            //Helpers_Profile::user_activity_log($uid, 76, NULL, NULL, $person_id);

        return 1;
    }
    //adding file
    public static function nadra_verisys_file_insertion($data,$is_forigner,$person_id) {
        if ($is_forigner==0 ) {
            $query = DB::update('person_nadra_profile')->set( array('cnic_image_url'=>$data))
                ->where('person_id', '=',$person_id)
                ->execute();
        }
        else{
            $query = DB::update('person_foreigner_profile')->set( array('cnic_image_url'=>$data))
                ->where('person_id', '=',$person_id)
                ->execute();
        }
    }


    /* update subscriner deatils */

    public static function update_subscriber_details($data) {
        /* Parameters in array:
         * mobile_number
         * mnc
         * sim_status
         * person_id
         * user_id
         * imsi
         * connection_type
         * sim_activated_at
         * contact_type
         */
        $mobile_numbers = !empty($data['mobile_number']) ? $data['mobile_number'] : 0;
        if (!empty($mobile_numbers)) {
            $phoneexist = Helpers_Person::check_person_mobile_number_exist($mobile_numbers);
            $person_id = !empty($data['person_id']) ? $data['person_id'] : 0;

            //user id
            $login_user = Auth::instance()->get_user();
            if(!empty($login_user))
                $uid = $login_user->id;
            else 
                $uid = 0;
            $user_id = isset($data['user_id']) ? $data['user_id'] : $uid;

            $imsi = !empty($data['imsi']) ? $data['imsi'] : '';
            $sim_status = !empty($data['sim_status']) ? $data['sim_status'] : 1;
            $contact_type = !empty($data['contact_type']) ? $data['contact_type'] : 1;

            $connection_type = !empty($data['connection_type']) ? $data['connection_type'] : 0;

            $current_date = date('Y-m-d H:i:s');
            $activation_date = !empty($data['sim_activated_at']) ? date("Y-m-d H:i:s", strtotime($data['sim_activated_at'])) : $current_date;

            $mnc = !empty($data['mnc']) ? $data['mnc'] : 0;
            if (empty($mnc)) {
                $array_number['number'] = $mobile_numbers;
                $mnc = Helpers_Utilities::check_mnc($array_number);
                if (empty($mnc)) {
                    $mnc = 0;
                }
            }

            if (!empty($phoneexist)) {
                $chksimuser = Helpers_Utilities::check_sim_user_exist($mobile_numbers);
                if (empty($chksimuser)) {
                    $query = DB::update('person_phone_number');
                    if (!empty($person_id))
                        $query->set(array('sim_owner' => $person_id));
                    if (!empty($person_id))
                        $query->set(array('person_id' => $person_id));
                    if (!empty($imsi))
                        $query->set(array('imsi_number' => $imsi));
                    if (!empty($contact_type))
                        $query->set(array('contact_type' => $contact_type));
                    if (isset($sim_status))
                        $query->set(array('status' => $sim_status));
                    if (isset($connection_type))
                        $query->set(array('connection_type' => $connection_type));
                    if (isset($mnc))
                        $query->set(array('mnc' => $mnc));
                    if (!empty($activation_date))
                        $query->set(array('sim_activated_at' => $activation_date));
                    if (!empty($user_id))
                        $query->set(array('user_id' => $user_id));
                    $query->where('phone_number', '=', $mobile_numbers)
                            ->execute();
                }else {
                    $query = DB::update('person_phone_number');
                    if (!empty($person_id))
                        $query->set(array('sim_owner' => $person_id));
                    if (!empty($imsi))
                        $query->set(array('imsi_number' => $imsi));
                    if (!empty($contact_type))
                        $query->set(array('contact_type' => $contact_type));
                    if (isset($sim_status))
                        $query->set(array('status' => $sim_status));
                    if (isset($connection_type))
                        $query->set(array('connection_type' => $connection_type));
                    if (isset($mnc))
                        $query->set(array('mnc' => $mnc));
                    if (!empty($user_id))
                        $query->set(array('user_id' => $user_id));
                    $query->where('phone_number', '=', $mobile_numbers)
                            ->execute();
                }
                Helpers_Profile::user_activity_log($user_id, 23, NULL, NULL, $person_id);
            }else {
                if ($mobile_numbers > 20) {
                    $query = DB::insert('person_phone_number', array('sim_owner', 'person_id', 'phone_number', 'imsi_number', 'sim_activated_at', 'status', 'connection_type', 'mnc', 'user_id'))
                        ->values(array($person_id, $person_id, $mobile_numbers, $imsi, $activation_date, $sim_status, $connection_type, $mnc, $user_id))
                        ->execute();
                }else{
                    $query = DB::insert('debugging_insertion', array('details'))
                        ->values(array('Model/Generic/update_subscriber_details -- '.$person_id.' -- ' .$person_id.' -- ' . $mobile_numbers.' -- ' . $imsi.' -- ' . $activation_date.' -- ' . $sim_status.' -- ' . $connection_type.' -- ' . $mnc, $user_id))
                        ->execute();
                }
                    Helpers_Profile::user_activity_log($user_id, 22, NULL, NULL, $person_id);

            }
            return 1;
        } else {

            return 0;
        }
    }

    /* update mobile number */

    public static function update_mobile_number($data) {
//        echo '<pre>';
//        print_r($data);
//        exit;
        /* Parameters in array:
         * mobile_number
         * mnc
         * sim_status
         * person_id
         * user_id
         */
        $check_mb = trim((string)$data['mobile_number'] ?? '');
        $check_mobile = '';
        if (strlen($check_mb) === 10 && $check_mb !== '') {
            $check_mobile = $check_mb[0];
        }
        $mobile_numbers = $check_mb;
        if (!empty($mobile_numbers) && strlen($check_mb)==10 && $check_mobile == 3) {
            $phoneexist = Helpers_Person::check_person_mobile_number_exist($mobile_numbers);            
            $person_id = !empty($data['person_id']) ? $data['person_id'] : 0;
            $login_user = Auth::instance()->get_user();
            
            if(!empty($login_user))
                $uid = $login_user->id;
            else 
                $uid = 0;
            $user_id = isset($data['user_id']) ? $data['user_id'] : $uid;   
            //updated by sajid & yaser
            if (!empty($phoneexist) && $phoneexist !=0) {                
                if (!empty($person_id)) {
                    $chksimuser = Helpers_Utilities::check_sim_user_exist($mobile_numbers);
                    if (empty($chksimuser)) {
                        DB::update('person_phone_number')->set(array('sim_owner' => $person_id, 'person_id' => $person_id, 'user_id' => $user_id))
                                ->where('phone_number', '=', $mobile_numbers)
                                ->execute();
                    } else {
                        DB::update('person_phone_number')->set(array('sim_owner' => $person_id, 'user_id' => $user_id))
                                ->where('phone_number', '=', $mobile_numbers)
                                ->execute();
                    }
                }
                Helpers_Profile::user_activity_log($uid, 26, "Mobile No", $mobile_numbers, $person_id);
                
            } else {                
                $mnc = !empty($data['mnc']) ? $data['mnc'] : 0;
                if (empty($mnc)) {
                    $array_number['number'] = $mobile_numbers;
                    if(!empty($data['mnc_imei']))
                        $mnc= $data['mnc_imei'];
                    else 
                        $mnc = Helpers_Utilities::check_mnc($array_number);
                    if (empty($mnc)) {
                        $mnc = 0;
                    }
                }
                $sim_status = !empty($data['sim_status']) ? $data['sim_status'] : 1;
                if($mobile_numbers>20) {
                    $query = DB::insert('person_phone_number', array('sim_owner', 'person_id', 'mnc', 'phone_number', 'user_id', 'status'))
                        ->values(array($person_id, $person_id, $mnc, $mobile_numbers, $user_id, $sim_status))
                        ->execute();
                }else{
                    $query = DB::insert('debugging_insertion', array('details'))
                        ->values(array('Model/Generic/update_mobile_number -- '.$person_id.' -- '. $person_id.' -- '. $mnc.' -- '. $mobile_numbers.' -- '. $user_id.' -- '. $sim_status))
                        ->execute();
                }
                Helpers_Profile::user_activity_log($uid, 48, "Mobile No", $mobile_numbers, $person_id);
            }
            return 1;
        } else {
            return 0;
        }
    }

    /* Manual CNIC SIMS Entry data insert */

    public static function Manualcnicsimsinsert($data) {
        $login_user = Auth::instance()->get_user();        
        if(!empty($login_user))
                $uid = $login_user->id;
            else 
                $uid = 0;
            
        $data['user_id'] = isset($data['user_id']) ? $data['user_id'] : $uid;
        if (empty($data['cnic_is_foreigner_value'])) {
            $data['is_foreigner'] = 0;
        } else {
            $data['is_foreigner'] = 1;
        }
        $data['cnic_number'] = $data['cnicsims'];
        // get person_id: it will create person or will returen person_id of existing person
        $content = new Model_Generic();
        $person_id = $content->update_cnic_number($data);
        if(!empty($data['mobile_number']))
        {    
        foreach ($data['mobile_number'] as $mobile_number) {
            if(!empty($mobile_number))
            {    
            $send_array['mobile_number'] = $mobile_number;
            $send_array['person_id'] = $person_id;
            $send_array['user_id'] = $data['user_id'];
            //update each mobile number
            $content = new Model_Generic();
            $status = $content->update_mobile_number($send_array);            
            }
        }
        // code to change request status
        if (!empty($data['requestid'])) {
            $reference_number = Model_Email::email_status($data['requestid'], 2, 5);
            return -7;
        } else {
            return $person_id;
        }        
        }else{            
                   $reference_number = Model_Email::email_status($data['requestid'], 2, 3);                    
                    return -7;
        }
    }

    /* Manual update device name */

    public static function Manualphonenameupdate($data) {
        // echo 'hello'; exit;
        /* Manual IMEI SIMS Entry data insert */
        $chk = Helpers_Utilities::get_device_id($data['imei_no']);
        if ($chk == 0) {
            $query = DB::insert('person_phone_device', array('phone_name', 'imei_number', 'user_id'))
                    ->values(array($data['phone_name'], $data['imei_no'], $data['user_id']))
                    ->execute();
            $device_id = $query[0];
        } else {
            $device_id = $chk;
            if (!empty($data['phone_name'])) {
                DB::update('person_phone_device')->set(array('phone_name' => $data['phone_name'], 'user_id' => $data['user_id']))
                        ->where('id', '=', $device_id)
                        ->execute();
            }
        }
    }

    public static function Manualimeisimsinsert($data) {
//        echo '<pre>';
//        print_r($data);
//        exit;
        //$data['imeisims']
        //$data['user_id']
        //$data['imei_mobile_number']

        $data['mobile_number'] = $data['imei_mobile_number'];
        $imei_number = $data['imeisims'];


        //foreach ($data['mobile_number'] as $data1) {
        for ($i = 0; $i < sizeof($data['mobile_number']); $i++) {
            date_default_timezone_set('Asia/Karachi');
            $number = $data['mobile_number'][$i];
            if (!empty($data['usefrom'][$i])) {
                $usefrom = date("Y-m-d H:i:s", strtotime($data['usefrom'][$i]));
            } else {
                $usefrom = '';
            }
            if (!empty($data['useto'][$i])) {
                $useto = date("Y-m-d H:i:s", strtotime($data['useto'][$i]));
            } else {
                $useto = '';
            }

            //update mobile number
            $send_array['mobile_number'] = $number;
            $send_array['user_id'] = $data['user_id'];
            $send_array['mnc_imei'] = $data['mnc_imei'];
            //update each mobile number
            $content = new Model_Generic();
            $status = $content->update_mobile_number($send_array);

            /* Parameters in array:
             * imei  
             * person_id
             * mobile_number
             * phone_name
             * in_use_since
             * last_interaction_at
             * user_id
             * is_active
             */
            $array_imei['imei'] = $imei_number;
            $array_imei['mobile_number'] = $number;
            $array_imei['in_use_since'] = $usefrom;
            $array_imei['last_interaction_at'] = $useto;
            $array_imei['user_id'] = $data['user_id'];
            $content = new Model_Generic();
            $person_id = $content->update_imei_mobile_number($array_imei);





//            
//            //    print_r($usefrom);                   exit;
//            if (!empty($data['mobile_number'][$i])) {
//                //query to check and update person phone_number table
//                $phoneexist = Helpers_Person::check_person_mobile_number_exist($data['mobile_number'][$i]);
//                if ($phoneexist != 0) {
//                    DB::update('person_phone_number')->set(array('mnc' => $data['imeicompany'], 'user_id' => $data['user_id']))
//                            ->where('phone_number', '=', $number)
//                            ->execute();
//                } else {
//                    $query = DB::insert('person_phone_number', array('sim_owner', 'person_id', 'sim_activated_at', 'sim_last_used_at', 'phone_number', 'mnc', 'user_id'))
//                            ->values(array('', '', '', $useto, $number, $data['imeicompany'], $data['user_id']))
//                            ->execute();
//                }
//
//                //query to check mobile number is used in this device or not
//                $getdevicenumberid = Helpers_Utilities::check_device_number_exist($device_id, $number);
//                if ($getdevicenumberid == 0) {
//                    DB::update('person_device_numbers')->set(array('is_active' => 0))
//                            ->where('phone_number', '=', $number)
//                            ->execute();
//                    $query = DB::insert('person_device_numbers', array('device_id', 'phone_number', 'is_active', 'first_use', 'last_use'))
//                            ->values(array($device_id, $number, 1, $usefrom, $useto))
//                            ->execute();
//                } else {
//                    DB::update('person_device_numbers')->set(array('is_active' => 0))
//                            ->where('phone_number', '=', $number)
//                            ->execute();
//                    DB::update('person_device_numbers')->set(array('is_active' => 1))
//                            ->where('phone_number', '=', $number)
//                            ->and_where('device_id', '=', $device_id)
//                            ->execute();
//                }
//            }
        }
        // code to change request status
        if (!empty($data['requestid'])) {
            $reference_number = Model_Email::email_status($data['requestid'], 2, 5);
            return -7;
        }
    }

    /* template data updated */
    public static function password_update($data) {
        $sql = "Select  u.is_forget_reset,u.id
                            from users u
                            inner join roles_users ru on u.id=ru.user_id
                            where email = '{$data['femail']}'
                            AND username = '{$data['fusername']}'
                            AND ru.role_id = {$data['ftype']} AND (login_sites = 0 OR login_sites = 2 OR login_sites = 4 OR login_sites = 5)
                            ";

        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        if (empty($members)) {
            return -1;
        }
        if ($members['is_forget_reset'] == 1) {
            return 2;
        }
        $query = DB::update('users')->set(array('is_forget_reset' => 1))
                ->where('email', '=', $data['femail'])
                ->and_where('username', '=', $data['fusername'])
                ->execute();
        return $query;
    }
    /* forgot password request from smart */
    public static function forgot_password_request($data) {
        $sql = "Select  u.is_forget_reset,u.id
                            from users u
                            inner join roles_users ru on u.id=ru.user_id
                            where username = '{$data['user_name']}'";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        if (empty($members)) {
            return 22;
        }
        if ($members['is_forget_reset'] == 1) {
            return 2;
        }
        $random_password = Helpers_Utilities::get_random_password(8);
        $query = DB::update('users')->set(array('is_forget_reset' => 1 , 'reset_password_text' => $random_password))
                ->and_where('username', '=', $data['user_name'])
                ->execute();
        return $query;
    }

    /* get b party data data */
    /* template data updated */

    public static function get_bparty_data() {
        $sql = "select count(person_id) as count,other_person_phone_number from person_summary
        where char_length(other_person_phone_number) > 9
        group by other_person_phone_number
        having count > 1";
        $data = DB::query(Database::SELECT, $sql)->execute();
        //return $members;
        foreach ($data as $data_1) {
            $str = trim($data_1['other_person_phone_number'], '0');

            if (strlen($str) > 5 && ctype_digit($str)) {
                $sql = "SELECT id FROM person_bparty_count where other_person_phone_number = '{$data_1['other_person_phone_number']}';";
                $bparty = DB::query(Database::SELECT, $sql)->execute()->current();
                if (!empty($bparty['id'])) {
                    $query = DB::update('person_bparty_count')->set(array('other_person_phone_number' => $data_1['other_person_phone_number'], 'count' => $data_1['count']))
                            ->where('id', '=', $bparty['id'])
                            ->execute();
                } else {
                    $query = DB::insert('person_bparty_count', array('other_person_phone_number', 'count'))
                            ->values(array($data_1['other_person_phone_number'], $data_1['count']))
                            ->execute();
                }
            }
        }
    }

    /* get file data */
    /* template data updated */

    public static function get_file_data($data) {
        $sql = "SELECT * FROM files where id = $data and upload_status = 1;";

        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members;
    }

    /* updated file status */

    public static function update_file_status($id) {
        $query = DB::update('files')->set(array('upload_status' => 2))
                ->where('id', '=', $id)
                ->execute();
        return $query;
    }

    public static function update_file_status_val($id, $val) {
        $query = DB::update('files')->set(array('upload_status' => $val))
                ->where('id', '=', $id)
                ->execute();
        return $query;
    }

    public static function get_person_identitites_merge() {
        $DB = Database::instance();
        $sql = "SELECT pi.person_id, p.user_id FROM `person_identities` as pi JOIN person as p on p.person_id = pi.person_id where pi.person_id not IN ( SELECT pc.person_id FROM person_category as pc) group by pi.person_id;";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        $newDate = date('Y-m-d H:i:s', time());
        foreach ($users as $user) {
            $query = DB::insert('person_category', array('person_id', 'category_id', 'user_id', 'added_on', 'reason', 'project_id'))
                    ->values(array($user['person_id'], 0, $user['user_id'], $newDate, ' ', 1))
                    ->execute();
        }
    }

    public static function person_id_update_initiate_table() {
        $DB = Database::instance();
        $sql = "SELECT person_id_1,cnic_number_foreigner from person_initiate where 1";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        foreach ($users as $user) {
            $query = DB::update('person_initiate')->set(array('person_id' => $user['person_id_1']))
                    ->where('cnic_number_foreigner', '=', $user['cnic_number_foreigner'])
                    ->execute();
        }
    }
//to updae person traingin table
    public static function update_person_training_table() {
        $DB = Database::instance();
        $sql = "SELECT * from person_affiliations where 1";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        
        foreach ($users as $user) {
            $trainging_id=array();
           if(!empty($user['training_type']))
            {    
                $user['training_type'] = str_replace(',', ' ', $user['training_type']);
                $user['training_type'] = str_replace('/', ' ', $user['training_type']);
                $user['training_type'] = str_replace('-', ' ', $user['training_type']);
                $training_array = explode(' ', $user['training_type']);
                                
                foreach($training_array as $training)
                {
                    if(!empty(trim($training)))
                    {    
                        $training = $training . ',';
                        $sql = "SELECT id from lu_training_type where temp_type like '%{$training}%'";
                        $training_type_id = DB::query(Database::SELECT, $sql)->execute()->current();
                        if(!empty($training_type_id['id']))
                        {
                                $trainging_id[]= $training_type_id['id'];
                        }
                    }
                }   
                
                $trainging_id = array_unique($trainging_id);
                $trainging_id = implode(',', $trainging_id);
                
                echo ' <br> ' . $user['training_type'] . ' <br> ';
                echo ' <br> ' . $sql . ' <br> ';
                echo ' <br> ' . $trainging_id . ' <br> ';
                
                //trainign duration
            if(!empty($user['training_duration']))
            {    
                echo ' ID: '.$user['id'].'<br>' . $user['training_duration'];
                
                $year='';
                $month='';
                $day='';
                $week= '';
                $user['training_duration'] = str_replace(',', ' ', $user['training_duration']);
                $user['training_duration'] = str_replace('to', ' ', $user['training_duration']);
                $user['training_duration'] = str_replace('and', ' ', $user['training_duration']);
                $user['training_duration'] = str_replace(',', ' ', $user['training_duration']);                
                $user['training_duration'] = str_replace('-', ' ', $user['training_duration']);
                
                if (strpos(strtolower($user['training_duration']), 'year') !== false) {                    
                    $year = explode('year', strtolower($user['training_duration']));
                    $year= $year[0];
                    if(!empty($year[1]))
                    $user['training_duration'] = $year[1];
                }
                if (strpos(strtolower($user['training_duration']), 'month') !== false) {                    
                    $month = explode('month', strtolower($user['training_duration']));
                    
                    if (strpos(strtolower($month[0]), 'one') !== false)
                        $month[0] = 1;
                    if (strpos(strtolower($month[0]), 'four') !== false)
                        $month[0] = 4;
                    if (strpos(strtolower($month[0]), 'three') !== false)
                        $month[0] = 3;
                    
                    $month= $month[0];
                    if(!empty($month[1]))
                    $user['training_duration'] = $month[1];
                    
                }
                
                if (strpos(strtolower($user['training_duration']), 'week') !== false) {                    
                    $week = explode('week', strtolower($user['training_duration']));
                     
                    if (strpos(strtolower($week[0]), 'one') !== false)
                        $week[0] = 1;
                     
                    $week= $week[0];
                    if(!empty($week[1]))
                    $user['training_duration'] = $week[1];
                }
                
                if (strpos(strtolower($user['training_duration']), 'day') !== false) {                    
                    $day = explode('day', strtolower($user['training_duration']));
                    if (strpos(strtolower($day[0]), '/') !== false)
                        $day = explode('/', strtolower($day[0]));
                    if (strpos(strtolower($day[0]), ' ') !== false)
                        $day = explode(' ', strtolower($day[0]));
                    if (strpos(strtolower($day[0]), 'balakot') !== false)
                        $day[0] = 8;
                    if (strpos(strtolower($day[0]), 'fifteen') !== false)
                        $day[0] = 15;
                    if (strpos(strtolower($day[0]), 'forty') !== false)
                        $day[0] = 40;
                    $day= $day[0];
                }
                
                if ((strpos(strtolower($user['training_duration']), 'year') == false) && (strpos(strtolower($user['training_duration']), 'month') == false) && (strpos(strtolower($user['training_duration']), 'week') == false) && (strpos(strtolower($user['training_duration']), 'day') == false)) {  
                    $day=$user['training_duration'];
                   // echo "<br>";
                   // print_r($day+1); exit;
                    if(empty($day))
                        $day=0;
                    if(is_string($day))
                        $day=0;
                }
                
                $total_days = '';
                $day_1 = 0;
                $day_2 = 0;
                $day_3 = 0;
                $day_4 = 0;
                
                if(!empty($year))
                    $day_1 =!empty (trim($year)) ? trim($year) * 365 : 0;
                if(!empty($month))
                    $day_2 =!empty (trim($month)) ?  trim($month) * 30 : 0;
                if(!empty($day))
                    $day_3 =!empty (trim($day)) ?  trim($day) : 0;
                if(!empty($week))
                    $day_4 =!empty (trim($week)) ?  trim($week) * 7 : 0;
                        
                $total_days = $day_1 + $day_2 + $day_3 + $day_4;                
                echo  ' => ' . $total_days . '<br>';
            }else{
                $total_days=0;
            }
              
            $query = DB::insert('person_trainings', array('organization_id', 'person_id', 'training_type_id','training_duration','training_year','other_details'))
                           ->values(array($user['organization_id'], $user['person_id'], $trainging_id,$total_days,$user['training_year'],$user['training_type']))
                        ->execute(); 
            }  
        }
    }
//to updae police staion table
    public static function update_police_stations_table() {
        $DB = Database::instance();
        $sql = "SELECT * from police_stations where 1";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        
        foreach ($users as $user) {
            $ps_name='';
            echo $user['ps_name'];
           if(!empty($user['ps_name']))
            {    
                $ps_raw_data = explode('_', $user['ps_name']);                               
                $ps1= $ps_raw_data[0];
                if(!empty($ps_raw_data[1]))
                $ps2 = $ps_raw_data[1];
                
            switch ($ps2){
                case 'M.B DIN' :
                    $ps2='Mandi Bahauddin';
                    break;
                case 'FAISLABAD' :
                    $ps2='Faisalabad';
                    break;
                case 'T.T. SINGH' :
                    $ps2='Toba Tek Singh';
                    break;
                case 'KHOSHAB' :
                    $ps2='Khushab';
                    break;
                case 'NANKANA' :
                    $ps2='Nankana sahab';
                    break;
                case 'PAKPATAN' :
                    $ps2='Pakpattan';
                    break;
                case 'JEHLUM' :
                    $ps2='Jhelum';
                    break;
                case 'RAHIMYAR KHAN' :
                    $ps2='Rahim Yar Khan';
                    break;
                case 'DEERA GHAZI KHAN' :
                    $ps2='Dera Ghazi Khan';
                    break;
                case 'MUZAFAR GHAR' :
                    $ps2='Muzaffarghar';
                    break;
                case 'LYYAH' :
                    $ps2='Layyah';
                    break;
                case 'Muzaffargarh' :
                    $ps2='Muzaffarghar';
                    break;
            }
               $ps_name= $ps1.'_'.$ps2;
               echo "<br>";
               echo $ps_name."<br>";
                //strtolower
                $query = DB::update('police_stations')->set(array('ps_name' => $ps_name))
                       ->where('ps_id', '=', $user['ps_id'])
                        ->execute();
            }
                 
                 
            }  
        
    }

    public static function cnic_number_update_initiate_table() {
        $DB = Database::instance();
        $sql = "SELECT person_id,cnic_number_foreigner from person_initiate where 1";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        foreach ($users as $user) {
            $query = DB::update('person_initiate')->set(array('cnic_number' => $user['cnic_number_foreigner']))
                    ->where('person_id', '=', $user['person_id'])
                    ->and_where('is_foreigner', '=', 0)
                    ->execute();
        }
    }

    //to make person nadra profile
    public static function make_person_nadra_profile() {
        $DB = Database::instance();
        $sql = "select person_id,cnic_number,user_id
                from person_initiate as t1 
                WHERE t1.is_foreigner =0 AND t1.person_id NOT IN (SELECT person_id FROM person_nadra_profile where 1)";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        foreach ($users as $user) {
            if (!empty($user['person_id']) && $user['cnic_number']) {

                $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'user_id'))
                        ->values(array($user['person_id'], $user['cnic_number'], $user['user_id']))
                        ->execute();
            }
        }
    }

    public static function make_person_assets_directory() {
        $DB = Database::instance();
        $sql = "SELECT person_id from person_initiate where 1";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        foreach ($users as $user) {

            // to make and get folder for person data
            $person_folder_path = !empty($user['person_id']) ? Helpers_Upload::make_and_get_person_data_directory($user['person_id']) : '';
        }
    }
    public static function make_fingerprint_directory() {
       $fp_type_data= Helpers_Aiesapi::finger_print_type();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        //get server details to upload request data
       $pid=10;
        $serverdata = !empty($pid) ? Helpers_Upload::server_details_for_finger_print_data($pid) : '';
       
        foreach ($fp_type_data as $user) {
             $category = $serverdata['save_data_path'] . $user->fp_category ;
             $pingerprint = $serverdata['save_data_path'] . $user->fp_category . '/' . $user->fp_type;
           
            //category
            if (!is_dir($category)) {
            mkdir("{$category}", 0777);
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/.htaccess', $category.'/.htaccess');
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/index.php', $category.'/index.php');
            }
            //fingerprint
            if (!is_dir($pingerprint)) {
            mkdir("{$pingerprint}", 0777);
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/.htaccess', $pingerprint.'/.htaccess');
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/index.php', $pingerprint.'/index.php');
            }
}       
    }
    public static function make_fingerprint_tables() {
       $fp_type_data= Helpers_Aiesapi::get_finger_print_category();
       //print_r($fp_type_data); exit;
        foreach ($fp_type_data as $user) {
             $fp_cat = $user->fp_category ;
             $fp_cat_id = $user->id ;
             $table_comumns='';
            $fp_type_data= Helpers_Aiesapi::finger_print_type();
            foreach ($fp_type_data as $column){  
                if($fp_cat_id==$column->fp_category_id && !empty($column->fp_type)){                    
                    $table_comumns.= " `".$column->fp_type."` varchar(100) DEFAULT NULL COMMENT '".$column->fp_file_name."', ";
                }
            }
             //create catergory table
             $DB = Database::instance();
        $query = DB::query(NULL, "CREATE TABLE IF NOT EXISTS `" . $fp_cat . "` (
         `id` bigint(25) NOT NULL AUTO_INCREMENT,
        `person_id` bigint(25) DEFAULT NULL,
        ".$table_comumns."
        `user_id` int(5) DEFAULT NULL,
        `timestamp` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        
            $result = $query->execute();
            // $fp_type =$user->fp_type;
             echo "Table ".$fp_cat.' created. </br>';
           
        
    
           
        }
      
    }

    public static function transfer_person_nadra_pictures() {
        $DB = Database::instance();
        $sql = "select person_id,person_photo_url,cnic_number,is_update
                from person_nadra_profile as t1 
                WHERE t1.person_photo_url <> '' and is_update=0";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['cnic_number']) && !empty($user['person_photo_url'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/profile_images/' . urlencode($user['person_photo_url']);
                $person_nadra_image_1 = 'dist/uploads/person/profile_images/' . urlencode($user['person_photo_url']);

                //new pic name
                $new_pic_name = 'nadra-image' . $user['person_photo_url'];

                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {

                        // new image name
                        //$photo = 'nadra-image'.$user['cnic_number'] . '.gif'; 
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_nadra_profile')
                                    ->set(array(
                                        'person_photo_url' => $new_pic_name,
                                        'is_update' => 1,
                                    ))
                                    ->where('person_id', '=', $user['person_id'])
                                    ->execute();

//                    $query = DB::update('person')
//                            ->set(array(
//                                'image_url' => ''
//                            ))
//                            ->where('person_id', '=', $user['person_id'])
//                            ->execute();
                        }

                        unlink($fpath);
                    }
                }
            }
        }
    }

    // transfer person other pictures
    public static function transfer_person_other_pictures() {
        $DB = Database::instance();
        $sql = "select person_id,image_url
                from person as t1 
                WHERE t1.image_url <> '' and t1.is_update=0";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['image_url'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/profile_images/' . urlencode($user['image_url']);
                $person_nadra_image_1 = 'dist/uploads/person/profile_images/' . urlencode($user['image_url']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['image_url']);
                $new_pic_name = $user['person_id'] . 'other-image' . $newDate . $i . '.' . $new_file_info['extension'];


                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {


                            $query = DB::update('person')
                                    ->set(array(
                                        'image_url' => $new_pic_name,
                                        'is_update' => 1
                                    ))
                                    ->where('person_id', '=', $user['person_id'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
                        $query = DB::update('person')
                                ->set(array(
                                    'image_url' => ''
                                ))
                                ->where('person_id', '=', $user['person_id'])
                                ->execute();
                    }
                }
            }
            $i++;
        }
    }

    // transfer person other pictures
    public static function transfer_person_pictures() {
        $DB = Database::instance();
        $sql = "SELECT * from person_pictures as t1 "
                . "where (image_url <> 0 and image_url <> '') AND is_update =0  ";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['image_url']) && !empty($user['picture_type'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person1/profile_images/' . urlencode($user['image_url']);
                $person_nadra_image_1 = 'dist/uploads/person1/profile_images/' . urlencode($user['image_url']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['image_url']);
                $new_pic_name = $user['person_id'] . 'person-picture' . $newDate . $i . '.' . $new_file_info['extension'];


                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_pictures')
                                    ->set(array(
                                        'image_url' => $new_pic_name,
                                        'is_update' => 1
                                    ))
                                    ->where('person_id', '=', $user['person_id'])
                                    ->and_where('picture_type', '=', $user['picture_type'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
//                        $query = DB::update('person_pictures')
//                                    ->set(array(
//                                        'image_url' => ''
//                                    ))
//                                    ->where('person_id', '=', $user['person_id'])
//                                    ->and_where('picture_type', '=', $user['picture_type'])
//                                    ->execute();
                    }
                }
            }
            $i++;
        }
    }

    // transfer person other pictures
    public static function transfer_person_reports() {
        $DB = Database::instance();
        $sql = "SELECT person_id,report_type,file_link,is_updated
                 from person_reports as t1 
                where (file_link <> '0' ) AND is_updated =0  ";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['report_type']) && !empty($user['file_link'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/profile_reports/' . urlencode($user['file_link']);
                $person_nadra_image_1 = 'dist/uploads/person/profile_reports/' . urlencode($user['file_link']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['file_link']);
                $new_pic_name = $user['person_id'] . 'report' . $newDate . $i . '.' . $new_file_info['extension'];


                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

                //  print_r(file_exists($person_nadra_image_1));
//        echo '<br>';
//        print_r($person_new_data_path);
                // exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_reports')
                                    ->set(array(
                                        'file_link' => $new_pic_name,
                                        'is_updated' => 1
                                    ))
                                    ->where('person_id', '=', $user['person_id'])
                                    ->and_where('report_type', '=', $user['report_type'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
                        $query = DB::update('person_reports')
                                ->set(array(
                                    'file_link' => ''
                                ))
                                ->where('person_id', '=', $user['person_id'])
                                ->and_where('report_type', '=', $user['report_type'])
                                ->execute();
                    }
                }
            }
            $i++;
        }
    }

    // transfer requests data
    public static function transfer_request_data() {
        $DB = Database::instance();
        $sql = "SELECT id,file,request_type,company_name,is_updated
                 from files as t1 
                where (file <> 'na' and file<>'5570_' and file<>'5130_') AND is_updated =0";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {


            //checking parameters are not empty
            if (!empty($user['id']) && !empty($user['file'])) {

                //new save data path
                $request_save_data_path = !empty($user['id']) ? Helpers_Upload::get_request_data_path($user['id'], 'save') : '';

                //old request file
                $download_file_path = URL::base() . 'uploads/cdr/' . urlencode($user['file']);
                $local_file_path = 'uploads/cdr/' . $user['file'];
                //$local_file_path = 'uploads/cdr/'.urlencode($user['file']);
                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['file']);
                // print_r($new_file_info['extension']); exit;
                try {
                    $new_pic_name = 'rqt' . $user['request_type'] . 'fid' . $user['id'] . '.' . $new_file_info['extension'];
                } catch (Exception $e) {
                    echo "<pre>";
                    print_r($e);
                    exit;
                }
                //to save file at new path
                $request_new_data_path = $request_save_data_path . $new_pic_name;


//                print_r($new_file_info);
//                echo '<br>';
//                print_r($download_file_path);
//                copy($download_file_path, $request_new_data_path);
//                exit;
                //  print_r(file_exists($person_nadra_image_1));
//        echo '<br>';
//        print_r($person_new_data_path);
                // exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                try {

                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $local_file_path;

                    if (file_exists($fpath)) {
                        if (@copy($download_file_path, $request_new_data_path)) {
                            if (file_exists($fpath)) {
                                // new image name
                                if (!empty($new_pic_name)) {

                                    $query = DB::update('files')
                                            ->set(array(
                                                'file' => $new_pic_name,
                                                'is_updated' => 1
                                            ))
                                            ->where('id', '=', $user['id'])
                                            ->execute();
                                }
                                unlink($fpath);
                            } else {
//                                   $query = DB::update('files')
//                                    ->set(array(
//                                        'file' => '',
//                                        'is_updated' => 1
//                                    ))
//                                    ->where('id', '=', $user['id'])
//                                    ->execute();
                            }
                        }
                    }
                } catch (Exception $e) {
                    print_r($e);
                }
            }
            $i++;
        }
    }

    // transfer person verysis
    public static function transfer_person_verysis() {
        $DB = Database::instance();
        $sql = "select person_id,cnic_image_url, is_updated_verysis
                from person_nadra_profile as t1 
                WHERE t1.cnic_image_url <> ''  and  is_updated_verysis = 0 ";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['cnic_image_url'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/verysis_images/' . urlencode($user['cnic_image_url']);
                $person_nadra_image_1 = 'dist/uploads/person/verysis_images/' . urlencode($user['cnic_image_url']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['cnic_image_url']);
                $new_pic_name = $user['person_id'] . 'person-verysis' . $newDate . $i . '.' . $new_file_info['extension'];

                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_nadra_profile')
                                    ->set(array(
                                        'cnic_image_url' => $new_pic_name,
                                        'is_updated_verysis' => 1
                                    ))
                                    ->where('person_id', '=', $user['person_id'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
//                        $query = DB::update('person_nadra_profile')
//                                    ->set(array(
//                                        'cnic_image_url' => ''
//                                    ))
//                                    ->where('person_id', '=', $user['person_id'])
//                                    ->execute();
                    }
                }
            }
            $i++;
        }
    }

    // transfer person assets
    public static function transfer_person_assets() {
        $DB = Database::instance();
        $sql = "select id,person_id,file_link, is_updated
                from person_assets as t1 
                WHERE t1.file_link <> '0'  and  t1.is_updated = 0  ";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['file_link'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/profile_assets/' . urlencode($user['file_link']);
                $person_nadra_image_1 = 'dist/uploads/person/profile_assets/' . urlencode($user['file_link']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['file_link']);
                $new_pic_name = $user['person_id'] . 'asset' . $newDate . $i . '.' . $new_file_info['extension'];
                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_assets')
                                    ->set(array(
                                        'file_link' => $new_pic_name,
                                        'is_updated' => 1
                                    ))
                                    ->where('id', '=', $user['id'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
                        $query = DB::update('person_assets')
                                ->set(array(
                                    'file_link' => ''
                                ))
                                ->where('id', '=', $user['id'])
                                ->execute();
                    }
                }
            }
            $i++;
        }
    }

    // transfer person income sources
    public static function transfer_person_income() {
        $DB = Database::instance();
        $sql = "select id,person_id,file_link, is_updated
                from person_income_sources as t1 
                WHERE t1.file_link <> '0'  and  t1.is_updated = 0  ";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['file_link'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/profile_income_sources/' . urlencode($user['file_link']);
                $person_nadra_image_1 = 'dist/uploads/person/profile_income_sources/' . urlencode($user['file_link']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['file_link']);
                $new_pic_name = $user['person_id'] . 'income-source' . $newDate . $i . '.' . $new_file_info['extension'];
                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_income_sources')
                                    ->set(array(
                                        'file_link' => $new_pic_name,
                                        'is_updated' => 1
                                    ))
                                    ->where('id', '=', $user['id'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
                        $query = DB::update('person_income_sources')
                                ->set(array(
                                    'file_link' => ''
                                ))
                                ->where('id', '=', $user['id'])
                                ->execute();
                    }
                }
            }
            $i++;
        }
    }

    // transfer person social links
    public static function transfer_person_social() {
        $DB = Database::instance();
        $sql = "select id,person_id,file_link, is_updated
                from person_social_links as t1 
                WHERE t1.file_link <> '0'  and  t1.is_updated = 0 ";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();
        $i = 1;
        foreach ($users as $user) {
            //checking parameters are not empty
            if (!empty($user['person_id']) && !empty($user['file_link'])) {
                //new save data path
                $person_save_data_path = !empty($user['person_id']) ? Helpers_Person:: get_person_save_data_path($user['person_id']) : '';

                //person old nadra profile image path
                $person_nadra_image = URL::base() . 'dist/uploads/person/social_links/' . urlencode($user['file_link']);
                $person_nadra_image_1 = 'dist/uploads/person/social_links/' . urlencode($user['file_link']);

                //new pic name
                $newDate = date('YmdHis', time());
                $new_file_info = PATHINFO($user['file_link']);
                $new_pic_name = $user['person_id'] . 'social-link' . $newDate . $i . '.' . $new_file_info['extension'];
                //to save file at new path
                $person_new_data_path = $person_save_data_path . $new_pic_name;

//        print_r($person_nadra_image);
//        echo '<br>';
//        print_r($person_new_data_path);
//        exit;
                // move_uploaded_file($person_nadra_image, $person_new_data_path);
                if (copy($person_nadra_image, $person_new_data_path)) {
                    $fpath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $person_nadra_image_1;
                    if (file_exists($fpath)) {
                        // new image name
                        if (!empty($new_pic_name)) {

                            $query = DB::update('person_social_links')
                                    ->set(array(
                                        'file_link' => $new_pic_name,
                                        'is_updated' => 1
                                    ))
                                    ->where('id', '=', $user['id'])
                                    ->execute();
                        }
                        unlink($fpath);
                    } else {
                        $query = DB::update('person_social_links')
                                ->set(array(
                                    'file_link' => ''
                                ))
                                ->where('id', '=', $user['id'])
                                ->execute();
                    }
                }
            }
            $i++;
        }
    }

    public static function insert_foreigner_in_person() {
        $DB = Database::instance();
        $sql = "SELECT * from person where 1";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        foreach ($users as $user) {
            $query = DB::insert('person', array('person_id', 'first_name', 'last_name', 'middle_name', 'father_name', 'is_nadra_profile_exists', 'view_access_level_id', 'address', 'is_complete', 'is_deleted', 'user_id', 'view_count', 'image_url'))
                    ->values(array($user['person_id'], $user['first_name'], $user['last_name'], $user['middle_name'], $user['father_name'], $user['is_nadra_profile_exists'], $user['view_access_level_id'], $user['address'], $user['is_complete'], $user['is_deleted'], $user['user_id'], $user['view_count'], $user['image_url']))
                    ->execute();
        }
    }

    // this function will update or insert file name in files table from request table
    public static function to_update_files_table_with_request() {

        $DB = Database::instance();
        $sql = 'SELECT t1.request_id,t1.user_id,t1.user_request_type_id,t1.company_name,t1.requested_value,t2.received_file_path
                from user_request as t1
                inner join email_messages as t2 on t1.message_id=t2.message_id
                 where (t2.received_file_path <> "na" && t2.received_file_path <> "") ';
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        foreach ($users as $user) {
            //this helper will check record exist in file table with file name
            $chk = Helpers_Upload::check_record_exist_with_file_name($user['received_file_path']);
            if (empty($chk)) {
                if ($user['user_request_type_id'] == 2) {
                    $file_id = Helpers_Utilities::id_generator("file_id");
                    $query = DB::insert('files', array('id', 'file', 'size', 'no_of_record', 'description', 'upload_status', 'request_type', 'request_id', 'company_name', 'is_deleted', 'is_manual', 'created_by', 'error_type', 'changed_by', 'phone_number', 'imei'))
                            ->values(array($file_id, $user['received_file_path'], 0, 0, $user['user_request_type_id'], 0, $user['user_request_type_id'], $user['request_id'], $user['company_name'], 0, 2, $user['user_id'], 0, 0, 0, $user['requested_value']))
                            ->execute();
                } elseif ($user['user_request_type_id'] == 1 || $user['user_request_type_id'] == 3 || $user['user_request_type_id'] == 4 || $user['user_request_type_id'] == 5 || $user['user_request_type_id'] == 6) {
                    $file_id = Helpers_Utilities::id_generator("file_id");
                    $query = DB::insert('files', array('id', 'file', 'size', 'no_of_record', 'description', 'upload_status', 'request_type', 'request_id', 'company_name', 'is_deleted', 'is_manual', 'created_by', 'error_type', 'changed_by', 'phone_number', 'imei'))
                            ->values(array($file_id, $user['received_file_path'], 0, 0, $user['user_request_type_id'], 0, $user['user_request_type_id'], $user['request_id'], $user['company_name'], 0, 2, $user['user_id'], 0, 0, $user['requested_value'], 0))
                            ->execute();
                }
            } else {
                $query = DB::update('files')->set(array('request_id' => $user['request_id']))
                        ->where('file', '=', $user['received_file_path'])
                        ->execute();
            }
        }
    }

    public static function insert_last_person_id_in_id_generator() {
        $DB = Database::instance();
        $sql = "SELECT person_id from person_initiate where 1 order by person_id desc limit 1";
        $users = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $person_id = isset($users->person_id) ? $users->person_id : 0;

        $query = DB::insert('id_generator', array('id_type', 'last_id', 'comment'))
                ->values(array('person_id', $person_id, 'last person_id from person initiate table'))
                ->execute();
    }

    public static function get_person_imei_merge_1() {
        $DB = Database::instance();
        $sql = "SELECT id, imei_number FROM person_phone_device where imei_number != 0;";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        $newDate = date('Y-m-d H:i:s', time());
        foreach ($users as $user) {
            $updated_imei = Helpers_Utilities::find_imei_last_digit($user['imei_number']);
            $query = DB::update('person_phone_device')->set(array('imei_number' => $updated_imei))
                    ->where('id', '=', $user['id'])
                    ->execute();
        }
    }

    public static function get_person_imei_merge_2() {
        $DB = Database::instance();
        $sql = "SELECT person_id, imei_number FROM person_call_log group by imei_number;";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        foreach ($users as $user) {
            $updated_imei = Helpers_Utilities::find_imei_last_digit($user['imei_number']);
            $query = DB::update('person_call_log')->set(array('imei_number' => $updated_imei))
                    ->where('imei_number', '=', $user['imei_number'])
                    ->execute();
        }
    }

    public static function get_person_imei_merge_3() {
        $DB = Database::instance();
        $sql = "SELECT person_id, imei_number FROM person_sms_log group by imei_number;";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        foreach ($users as $user) {
            $updated_imei = Helpers_Utilities::find_imei_last_digit($user['imei_number']);
            $query = DB::update('person_sms_log')->set(array('imei_number' => $updated_imei))
                    ->where('imei_number', '=', $user['imei_number'])
                    ->execute();
        }
    }

    public static function get_person_imei_merge_4() {
        $DB = Database::instance();
        $sql = "SELECT id, imei FROM files where imei !=0";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        foreach ($users as $user) {
            $updated_imei = Helpers_Utilities::find_imei_last_digit($user['imei']);
            $query = DB::update('files')->set(array('imei' => $updated_imei))
                    ->where('id', '=', $user['id'])
                    ->execute();
        }
    }

    public static function get_person_image_merge() {
        $DB = Database::instance();
        $sql = "SELECT person_id, image_url FROM `person` where image_url !=0 and image_url != '';";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();

        foreach ($users as $user) {
            $query = DB::insert('person_pictures', array('person_id', 'picture_type', 'image_url'))
                    ->values(array($user['person_id'], 1, $user['image_url']))
                    ->execute();
        }
    }

    public static function family_tree_complete() {
        $array = array(7,9,10,11);        
        DB::update('user_request')->set(array('processing_index' => 7))
                    ->where('user_request_type_id', 'IN', $array)
                    ->and_where('status', '=', 2)
                    ->and_where('processing_index', '=', 4)
                    ->execute();
        
    }
    public static function resend_parse_queue() {
        DB::update('user_request')->set(array('processing_index' => 4))
                    ->where('status', '=', 2)
                    ->and_where('processing_index', '=', 3)
                    ->execute();
        
    }
    public static function resend_error_in_queue() {
        $query=DB::update('user_request')->set(array('processing_index' => 0, 'status'=> 0))
                    ->where('status', '=', 3)
                    ->and_where('processing_index', '=', 1);
        $query->execute();
        
    }
    public static function resend_error_id_queue($id) {        
         DB::update('user_request')->set(array('processing_index' => 0, 'status'=> 0))
                    ->where('request_id', '=', $id)                    
                    ->execute();
        
    }
    public static function replace_and_delete_cnic($cnic_array) {

        $DB = Database::instance();
        foreach ($cnic_array as $cnic) {
            $serial = $cnic[0];
            $first_cnic = $cnic[1];
            $second_cnic = $cnic[2];
            $sql = "SELECT person_id as pid FROM `person_initiate` where cnic_number = {$first_cnic} ";
            $results = DB::query(Database::SELECT, $sql)->execute()->current();
            $person_id_first_cnic = isset($results['pid']) && !empty($results['pid']) ? $results['pid'] : 0;
//            echo '<pre>';
//            print_r($person_id_first_cnic); exit;
            $sql1 = "SELECT person_id FROM `person_initiate` where cnic_number = {$second_cnic}";
            $results2 = DB::query(Database::SELECT, $sql1)->execute()->current();
            $person_id_second_cnic = isset($results2['person_id']) && !empty($results2['person_id']) ? $results2['person_id'] : 0;
            //first exist second also exist
//            if ($person_id_first_cnic != 0 && $person_id_second_cnic != 0) {
//                echo $serial . '=';
//                echo 'Person with first     CNIC =      ' . $first_cnic . '       exist  ';
//                echo ' Person with Second CNIC = ' . $second_cnic . ' exist ';
//                echo '<br>';
//            }
            //first exist second does not exist
            if ($person_id_first_cnic != 0 && $person_id_second_cnic == 0) {                
                if(strlen($first_cnic)==13 && strlen($second_cnic)==13)
                {
                    echo $serial . '=';
                    echo 'Person with first     CNIC =      ' . $first_cnic . '       exist  ';
                    echo ' Person with Second CNIC = ' . $second_cnic . ' doest not exist ';
                    echo '<br>';
                    /*
                 DB::update('person_initiate')->set(array('cnic_number' => $second_cnic))
                    ->where('cnic_number', '=', $first_cnic)
                    ->execute();
                 DB::update('person_nadra_profile')->set(array('cnic_number' => $second_cnic))
                    ->where('cnic_number', '=', $first_cnic)
                    ->execute();  */              
                } 
                
            }
            //first cnic doest not exist
//            if ($person_id_first_cnic == 0 ) {
//                echo $serial . '=';
//                echo 'Person with first     CNIC =      ' . $first_cnic . ' does not  exist  ';                
//                echo '<br>';
//            }
        }
        exit;
    }
    
    //get user posting
    public static function  get_user_posting($posting){
        $DB = Database::instance();        
        $result =  explode('-', $posting);        
        switch ($result[0])
        {
            case 'r':
                $sql = "SELECT t.name as name FROM region as t where  t.region_id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $regionname = isset($results->name) && !empty($results->name) ? $results->name : 0;
                return "Region: ".$regionname;
                break;
            case 'd':
                $sql = "SELECT t.name as name FROM district as t where  t.district_id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $distname = isset($results->name) && !empty($results->name) ? $results->name : 0;
                return "District: ".$distname;
                break;            
            case 'p':
                $sql = "SELECT t.name as name FROM ctd_police_station as t WHERE t.region_id = $result[1]";
                //print_r($sql); exit;
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $psname = isset($results->name) && !empty($results->name) ? $results->name : 0;
                return "CTD PS: ".$psname;
                break;            
            case 'h':
                $sql = "SELECT t.name as name FROM headquarter as t WHERE t.id = $result[1]";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                $hqname = isset($results->name) && !empty($results->name) ? $results->name : 0;
                return "Headquarter: ".$hqname;
                break; 
            default :
                $defaulposting = ' ';
                return $defaulposting;
            
        }
    }
    
        public static function get_users_data() {
        $DB = Database::instance();
        $sql = "select t1.id,t1.email,t1.username,
                t2.cnic_number,t2.first_name,t2.file_name,t2.last_name,t2.father_name,t2.mobile_number,t2.job_title,t2.belt,
                t3.name as district,t4.name as region,t2.posted
                from users as t1
                inner join users_profile as t2 on t1.id=t2.user_id
                inner join district as t3 on t2.district_id=t3.district_id
                inner join region as t4 on t2.region_id=t4.region_id";
        $users = DB::query(Database::SELECT, $sql)->execute()->as_array();
        //$relation = $DB->query(Database::SELECT, $sql, TRUE)->execute();

        
        return $users;
    }
    
    public static function resend_remove_dublicated_number() {
         /* select */
            $DB = Database::instance();
            $sql = "select *, COUNT(phone_number) as t 
                    from person_phone_number
                    group by phone_number
                    HAVING t > 1
                    ORDER by t DESC;";
            exit;
            $phone_number = DB::query(Database::SELECT, $sql)->execute()->as_array();
         /* Delete */
            foreach($phone_number as $ph)
            {
                echo '<pre>';
                print_r($ph);               
                
                if($ph['t']>1)
                {
                    $query = DB::delete('person_phone_number')
                        ->where('sim_owner', '=', $ph['sim_owner'])
                        ->and_where('phone_number', '=', $ph['phone_number'])
                        ->and_where('person_id', '=', $ph['person_id'])
                        ->and_where('id', '!=', $ph['id'])
                       ->execute(); 
                }
            }    
        
    }
    public static function resend_remove_dublicated_personid() {
         /* select */
            $DB = Database::instance();
            $sql = "select *, COUNT(cnic_number) as t 
                    from person_initiate
                    where cnic_number!=0
                    group by cnic_number
                    HAVING t > 1
                    ORDER by t DESC;";
            $phone_number = DB::query(Database::SELECT, $sql)->execute()->as_array();
         /* Delete */
            foreach($phone_number as $ph)
            {
                echo '<pre>';
                print_r($ph);               
                
                if($ph['t']>1)
                {
                    $query = DB::delete('person_initiate')
                        ->where('cnic_number', '=', $ph['cnic_number'])
                       // ->and_where('person_id', '=', $ph['person_id'])
                        ->and_where('id', '!=', $ph['id'])
                       ->execute(); 
                }
            }    
        
    }

}
