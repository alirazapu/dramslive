<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with person profile  
  thinktank */
class Model_Personprofile {

    //update basic info
    public static function insert_nadra_info($data, $uid, $person_id) {
        if (empty($uid)) {
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
        }

        if ($data['gender'] == 'male')
            $gender = 1;
        elseif ($data['gender'] == 'female')
            $gender = 2;
        else
            $gender = 3;

        $photo = 'nadra-image' . $data['citizen_number'] . '.gif';

        $check_nadra_profile_exist = Helpers_Utilities::check_person_nadra_profile_exist($data['citizen_number']);

        if (!empty($check_nadra_profile_exist)) {
            $query = DB::update('person_nadra_profile')
                    ->set(array('person_name' => $data['name'],
                        'person_g_name' => $data['father_husband_name'],
                        'person_gender' => $gender,
                        'person_present_add' => $data['present_address'],
                        'person_permanent_add' => $data['permanent_address'],
                        'person_dob' => $data['date_of_birth'],
                        'person_photo_url' => $photo,
                        'person_nadra_status' => 1,
                        'user_id' => $uid))
                    ->where('person_id', '=', $person_id)
                    ->and_where('cnic_number', '=', $data['citizen_number'])
                    ->execute();
        } else {
            $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'user_id', 'person_name', 'person_g_name', 'person_gender', 'person_present_add', 'person_permanent_add', 'person_dob', 'person_photo_url', 'person_nadra_status'))
                    ->values(array($person_id, $data['citizen_number'], $uid, $data['name'], $data['father_husband_name'], $gender, $data['present_address'], $data['permanent_address'], $data['date_of_birth'], $photo, 1))
                    ->execute();
        }
        $query = DB::update('person')
                ->set(array('is_nadra_profile_exists' => 1,
                    'image_url' => $photo))
                ->where('person_id', '=', $person_id)
                ->execute();
        Helpers_Profile::user_activity_log($uid, 12, NULL, NULL, $person_id);
        //return $query;        
    }

    //update basic info
    public static function update_foreigner_profile($data, $uid, $person_id) {



        //  print_r($data); exit;  

        $query = DB::update('person_foreigner_profile')
                ->set(array(
                    'person_name' => $data['person_name'],
                    'person_g_name' => $data['person_g_name'],
                    'person_gender' => $data['person_gender'],
                    'martial_status' => $data['martial_status'],
                    'person_dob' => $data['person_dob'],
                    'person_present_add' => $data['person_present_add'],
                    'person_permanent_add' => $data['person_permanent_add'],
                    'family_id' => $data['family_id'],
                    'pak_district' => $data['pak_district'],
                    'pak_tehsil' => $data['pak_tehsil'],
                    'home_country' => $data['home_country'],
                    'ethnicity' => $data['ethnicity'],
                    'user_id' => $uid))
                ->where('person_id', '=', $person_id)
                ->and_where('cnic_number', '=', $data['cnic_number'])
                ->execute();
        $query = DB::update('person')
                ->set(array('is_nadra_profile_exists' => 1))
                ->where('person_id', '=', $person_id)
                ->execute();

        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 12, NULL, NULL, $person_id);
        //return $query;        
    }

    public static function update_basic_info($data, $uid, $pid) {
        $query = DB::update('person')->set(array('first_name' => $data['fname'], 'last_name' => $data['lname'], 'father_name' => $data['fathname'], 'address' => $data['add'], 'police_station_id' => $data['ps'], 'district_id' => $data['district'], 'region_id' => $data['region'], 'user_id' => $uid,))
                ->where('person_id', '=', $pid)
                ->execute();

        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 30, NULL, NULL, $pid);
        return 1;
    }

    //update detail info
    public static function update_detail_info($data, $uid, $pid) {
        $chkpid = Helpers_Person::check_person_detail_exist($pid);
        //   print_r($data); exit;
        if ($chkpid == 0) {
            $query = DB::insert('person_detail_info', array('person_id', 'alias', 'caste', 'sect', 'religion', 'marital_status', 'temporary_address', 'police_station_id', 'district_id', 'region_id', 'physical_appearance', 'is_sensitive_department'))
                    ->values(array($pid, $data['alias'], $data['caste'], $data['sect'], $data['religion'], $data['maritalstatus'], $data['temporaryaddress'], $data['policestation'], $data['district'], $data['region'], $data['physicalappearance'], $data['is_sensitive_dept']))
                    ->execute();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 32, NULL, NULL, $pid);
            return $query;
        } else {
            $query = DB::update('person_detail_info')->set(array('alias' => $data['alias'], 'caste' => $data['caste'], 'sect' => $data['sect'], 'religion' => $data['religion'], 'marital_status' => $data['maritalstatus'], 'temporary_address' => $data['temporaryaddress'], 'police_station_id' => $data['policestation'], 'district_id' => $data['district'], 'region_id' => $data['region'], 'physical_appearance' => $data['physicalappearance'], 'is_sensitive_department' => $data['is_sensitive_dept']))
                    ->where('person_id', '=', $pid)
                    ->execute();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 31, NULL, NULL, $pid);
            return 1;
        }
    }

    /* Person SIMs */

    public static function get_mobiles($data, $count, $pid) {
        /* Sorted Data */
//        echo '<pre>';
//        print_r($data); exit;
        $order_by_param = "t1.sim_owner";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {

                case "0":
                    $order_by_param = "phone_number";
                    break;
                case "1":
                    $order_by_param = "t1.contact_type";
                    break;
                case "2":
                    $order_by_param = "t1.sim_owner";
                    break;
                case "3":
                    $order_by_param = "t1.person_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.phone_number like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_phone_number as t1 
                    WHERE (t1.person_id=$pid or t1.sim_owner=$pid)    
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *
                    FROM person_phone_number as t1 
                    WHERE (t1.person_id=$pid or t1.sim_owner=$pid)  
                    {$search}
                    {$order_by}
                    {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Person linked projects */

    public static function get_linked_projects($data, $count, $pid) {
        //echo '<pre>'; print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "t1.project_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.user_id";
                    break;
                case "1":
                    $order_by_param = "t1.request_type_id";
                    break;
                case "2":
                    $order_by_param = "t1.requested_value";
                    break;
                case "3":
                    $order_by_param = "t1.project_id";
                    break;
                case "4":
                    $order_by_param = "t1.request_time";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t2.email_type_name like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT Count(*) as count 
                    FROM person_linked_projects as t1 
                    inner join email_templates_type as t2 on t1.request_type_id=t2.id
                    WHERE t1.person_id=$pid    
                    {$search}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.user_id,t1.request_time,t2.email_type_name,t1.project_id,requested_value
                    FROM person_linked_projects as t1 
                    inner join email_templates_type as t2 on t1.request_type_id=t2.id
                    WHERE t1.person_id={$pid}
                    {$search}
                    {$order_by}
                    {$limit}
                        ";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Person Category change History  */

    public static function get_category_history($data, $count, $pid) {
        //echo '<pre>'; print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "t1.added_on";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.added_on";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        //no need for table search code deleted
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(person_id) as count
                    FROM person_category_history as t1 
                    where t1.person_id={$pid}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT *
                    FROM person_category_history as t1 
                    where t1.person_id={$pid}                    
                    {$order_by}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update Mobiles info
    public static function update_mobiles_info($data, $uid, $pid) {
        //echo '<pre>';
        //print_r($data); 
        $chk = Helpers_Person::check_person_mobile_number_exist($data['number']);
        //print_r($chk); exit;
        if ($chk == 1) {
            $query = DB::update('person_phone_number')->set(array('person_id' => $pid, 'contact_type' => $data['contact_type']))
                    ->where('phone_number', '=', $data['number'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 49, NULL, NULL, $pid);
        } elseif ($chk == 0) {
            if($data['number']>20) {
                $query = DB::insert('person_phone_number', array('sim_owner', 'person_id', 'phone_number', 'contact_type'))
                    ->values(array($pid, $pid, $data['number'], $data['contact_type']))
                    ->execute();
            }else{
                $query = DB::insert('debugging_insertion', array('details'))
                    ->values(array('Model/Personprofile/update_mobiles_info -- '.$pid.' -- '. $pid.' -- '. $data['number'].' -- '. $data['contact_type']))
                    ->execute();
            }
            Helpers_Profile::user_activity_log($uid, 48, NULL, NULL, $pid);
        }
        return 1;
    }

    /* Get Person Relations */

    public static function get_person_relations($data, $count, $pid) {
        //print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "rel_f_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "rel_f_id";
                    break;
                case "1":
                    $order_by_param = "rel_id";
                    break;
                case "2":
                    $order_by_param = "rel_t_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $search = "and t3.cnic_number like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        //print_r($search); exit;
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_relations as t1
                    INNER JOIN person_initiate as t3 on t1.relation_with=t3.person_id
                    where t1.person_id={$pid} or t1.relation_with={$pid}      
                    {$search}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT t1.person_id as rel_f_id,t1.person_relation_type as rel_id,t1.relation_with as rel_t_id,t3.cnic_number,t3.cnic_number_foreigner,t3.is_foreigner,t1.under_custodian
                    FROM person_relations as t1
                    INNER JOIN person_initiate as t3 on t1.relation_with=t3.person_id
                    where t1.person_id={$pid} or t1.relation_with={$pid} 
                    {$search}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update relations info
    public static function update_relations($data, $uid, $pid) {
        if (empty($data['r_is_foreigner'])) {
            $data['is_foreigner'] = 0;
        } else {
            $data['is_foreigner'] = 1;
        }
        $data['cnic_number'] = $data['cnic'];
        $data['first_name'] = $data['name'];
        // create new person otherwise it will returen person_id
        $content = new Model_Generic();
        $prid = $content->update_cnic_number($data);
        // print_r($prid); exit;
        //$prid= Helpers_Person::check_person_id_with_cnic($data['cnic']);
        $chkrel = Helpers_Person::check_person_relation_exist($prid, $pid);

        if ($prid > 0 && !empty($chkrel)) {
            if (empty($data['relation'])) {
                $query = DB::delete('person_relations')
                        ->where('relation_with', '=', $prid)
                        ->and_where('person_id', '=', $pid)
                        ->execute();
            } else {
                $query = DB::update('person_relations')->set(array('person_relation_type' => $data['relation'], 'user_id' => $uid, 'under_custodian' => $data['relation_custodian']))
                        ->where('relation_with', '=', $prid)
                        ->and_where('person_id', '=', $pid)
                        ->execute();
            }
            Helpers_Profile::user_activity_log($uid, 51, "CNIC", $data['cnic'], $pid);
            return $query;
        } elseif ($prid > 0 && empty($chkrel)) {
            if (empty($data['relation'])) {
                $query = DB::delete('person_relations')
                        ->where('relation_with', '=', $prid)
                        ->and_where('person_id', '=', $pid)
                        ->execute();
            } else {
                $query = DB::insert('person_relations', array('person_id', 'person_relation_type', 'relation_with', 'user_id', 'under_custodian'))
                        ->values(array($pid, $data['relation'], $prid, $uid, $data['relation_custodian']))
                        ->execute();
            }
            Helpers_Profile::user_activity_log($uid, 50, "CNIC", $data['cnic'], $pid);
            return 1;
        }
    }

    /* Get Person Identities */

    public static function get_person_identity($data, $count, $pid) {
        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "identity_id";
                    break;
                case "1":
                    $order_by_param = "identity_no";
                    break;
                case "2":
                    $order_by_param = "identity_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.identity_no like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_identities as t1
                    where t1.person_id={$pid}       
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_identities as t1
                    where t1.person_id={$pid} 
                    {$search}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update identity no
    public static function update_identity($data, $uid, $pid) {
        $chk = Helpers_Person::check_person_identity_exist($data['idnno'], $pid);
        // print_r($data); exit;

        if (!empty($data['idnid'])) {
            $query = DB::update('person_identities')->set(array('person_id' => $pid, 'identity_id' => $data['idnname'], 'identity_no' => $data['idnno']))
                    ->where('id', '=', $data['idnid'])
                    ->execute();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 34, NULL, NULL, $pid);
        } elseif (!empty($chk)) {
            $query = DB::update('person_identities')->set(array('person_id' => $pid, 'identity_id' => $data['idnname'], 'identity_no' => $data['idnno']))
                    ->where('id', '=', $data['idnid'])
                    ->execute();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 34, NULL, NULL, $pid);
        } else {
            $query = DB::insert('person_identities', array('person_id', 'identity_id', 'identity_no'))
                    ->values(array($pid, $data['idnname'], $data['idnno']))
                    ->execute();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 33, NULL, NULL, $pid);
        }
        return 1;
    }

    //Delete identity no
    public static function delete_identity($recordid, $person_id) {
        //now 
        $sql = "SELECT * FROM person_identities as t1
                    where t1.id=$recordid";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;
        $query = DB::delete('person_identities')
                ->where('id', '=', $recordid)
                ->execute();
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 35, $members['identity_id'], $members['identity_no'], $person_id);
        return 1;
    }

    /* Get Person Education */

    public static function get_person_education($data, $count, $pid) {
        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "edu_type";
                    break;
                case "1":
                    $order_by_param = "degree_name";
                    break;
                case "2":
                    $order_by_param = "complete_year";
                    break;
                case "3":
                    $order_by_param = "degree_name";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.degree_name like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_education as t1
                    where t1.person_id={$pid}       
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_education as t1
                    where t1.person_id={$pid} 
                    {$search}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update education
    public static function update_education($data, $uid, $pid) {
        // $chk= Helpers_Person::check_person_education_exist($data['degname'],$pid);     
        $cmp_year = !empty($data['compyear']) ? $data['compyear'] : '';
        if (!empty($data['degid'])) {
            $query = DB::update('person_education')->set(array('edu_type' => $data['edutype'], 'degree_name' => $data['degname'], 'complete_year' => $data['compyear'], 'institute_name' => $data['institute'], 'education_level' => $data['edulevel']))
                    ->where('id', '=', $data['degid'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 37, NULL, NULL, $pid);
        } else {
//            echo '<pre>';
//            print_r($data); exit;
            $query = DB::insert('person_education', array('person_id', 'edu_type', 'degree_name', 'complete_year', 'institute_name', 'education_level'))
                    ->values(array($pid, $data['edutype'], $data['degname'], $cmp_year, $data['institute'], $data['edulevel']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 36, NULL, NULL, $pid);
        }
        return 1;
    }

    //Delete education
    public static function delete_education($degid, $person_id) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_education as t1
                    where t1.id=$degid and t1.person_id = $person_id";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;
        $query = DB::delete('person_education')
                ->where('id', '=', $degid)
                ->execute();
        $var1 = $members['edu_type'];
        $var1 .= '-';
        $var1 = $members['edu_level'];
        $var1 .= '-';
        $var1 .= $members['degree_name'];
        $var2 = $members['complete_year'];
        $var2 .= '-';
        $var2 .= $members['institute_name'];
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 38, $var1, $var2, $person_id);
        return 1;
    }

    /* Get Person banks */

    public static function get_person_banks($data, $count, $pid) {
        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "account_number";
                    break;
                case "1":
                    $order_by_param = "atm_number";
                    break;
                case "2":
                    $order_by_param = "bank_name";
                    break;
                case "3":
                    $order_by_param = "branch_name";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and (t1.bank_name like '%{$data['sSearch']}%' or t1.branch_name like '%{$data['sSearch']}%' or t1.account_number like '%{$data['sSearch']}%' or t1.atm_number like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_banks as t1
                    where t1.person_id={$pid}       
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_banks as t1
                    where t1.person_id={$pid}
                    {$search} 
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update education
    public static function update_banks($data, $uid, $pid) {
        // $chk= Helpers_Person::check_person_account_exist($data['accountno'],$pid);
        if (!empty($data['bankrecid'])) {
            $query = DB::update('person_banks')->set(array('person_id' => $pid, 'account_number' => $data['accountno'], 'atm_number' => $data['atmno'], 'bank_name' => $data['bankname'], 'branch_name' => $data['branchname'], 'is_internet_banking' => $data['is_internet_banking']))
                    ->where('id', '=', $data['bankrecid'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 43, NULL, NULL, $pid);
        } else {
            $query = DB::insert('person_banks', array('person_id', 'account_number', 'atm_number', 'bank_name', 'branch_name', 'is_internet_banking'))
                    ->values(array($pid, $data['accountno'], $data['atmno'], $data['bankname'], $data['branchname'], $data['is_internet_banking']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 42, NULL, NULL, $pid);
        }
        return 1;
    }

    //Delete bank
    public static function delete_bank($bankrecid, $person_id) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_banks as t1
                    where t1.id=$bankrecid and t1.person_id = $person_id";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;
        $query = DB::delete('person_banks')
                ->where('id', '=', $bankrecid)
                ->execute();
        $var1 = $members['account_number'];
        $var1 .= '-';
        $var1 .= $members['atm_number'];
        $var2 = $members['bank_name'];
        $var2 .= '-';
        $var2 .= $members['branch_name'];
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 44, $var1, $var2, $person_id);

        return 1;
    }

    /* Get Person Criminal Record */

    public static function get_person_crrecord($data, $count, $pid) {
        /* Sorted Data */
        $order_by_param = "t1.id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "fir_number";
                    break;
                case "1":
                    $order_by_param = "fir_date";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.sections_applied like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_criminal_record as t1                    
                    where t1.person_id={$pid}       
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_criminal_record as t1                    
                    where t1.person_id={$pid}  
                    {$search}
                    {$order_by}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update Criminal Record
    public static function update_criminalr($data, $uid, $pid) {
//        echo '<pre>';
//        print_r($data);
//        exit;
        $chk = Helpers_Person::check_person_criminal_record_exist($data['firno'], $data['policestationcr'], $data['firdate'], $pid);
        // print_r($chk); exit;
        if (!empty($chk)) {
            $query = DB::update('person_criminal_record')->set(array('sections_applied' => $data['sections'], 'case_position' => $data['caseposition'], 'accused_position' => $data['accusedposition'], 'user_id' => $uid,))
                    ->where('person_id', '=', $pid)
                    ->and_where('fir_number', '=', $data['firno'])
                    ->and_where('fir_date', '=', $data['firdate'])
                    ->and_where('police_station_id', '=', $data['policestationcr'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 53, NULL, NULL, $pid);
        } elseif ($chk == 0) {
            $query = DB::insert('person_criminal_record', array('person_id', 'fir_number', 'fir_date', 'police_station_id', 'sections_applied', 'case_position', 'accused_position', 'user_id'))
                    ->values(array($pid, $data['firno'], $data['firdate'], $data['policestationcr'], $data['sections'], $data['caseposition'], $data['accusedposition'], $uid))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 52, NULL, NULL, $pid);
        }
        return 1;
    }

    //Delete Criminal Record
    public static function delete_criminalr($post, $pid) {
        // $firNo = str_replace('_', '/', $fir);
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_criminal_record as t1
                    where t1.fir_number= {$post['firno']} AND t1.person_id = {$post['pid']}  AND t1.fir_date= '{$post['firdate']}' AND t1.police_station_id = {$post['policestationcr']} ";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($sql); exit;  

        $var1 = $members['fir_number'];
        $var1 .= '<>';
        $var1 .= $members['fir_date'];
        $var1 .= '<>';
        $var1 .= $members['police_station_id'];

        $var2 = $members['sections_applied'];
        $var2 .= '<>';
        $var2 .= $members['case_position'];
        $var2 .= '<>';
        $var2 .= $members['accused_position'];
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 54, $var1, $var2, $pid);

        $query = DB::delete('person_criminal_record')
                ->where('fir_number', '=', $post['firno'])
                ->and_where('person_id', '=', $post['pid'])
                ->and_where('fir_date', '=', $post['firdate'])
                ->and_where('police_station_id', '=', $post['policestationcr'])
                ->execute();
        //print_r($query); exit;
        return 1;
    }

    /* Get Person Affiliations & Trainings */

    public static function get_person_affiliations($data, $count, $pid) {
        //print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.organization_id";
                    break;
                case "1":
                    $order_by_param = "t1.designation";
                    break;
                case "2":
                    $order_by_param = "t1.details";
                    break;
                case "3":
                    $order_by_param = "t1.training_type";
                    break;
                case "4":
                    $order_by_param = "t1.training_duration";
                    break;
                case "5":
                    $order_by_param = "t1.training_year";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and ( t1.details like '%{$data['sSearch']}%' or t1.designation like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_affiliations as t1
                    where t1.person_id={$pid}      
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_affiliations as t1
                    where t1.person_id={$pid}
                    {$search}                      
                    {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Get Person  Trainings */

    public static function get_person_trainings($data, $count, $pid) {
        //print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.organization_id";
                    break;
                case "1":
                    $order_by_param = "t1.training_camp";
                    break;
                case "2":
                    $order_by_param = "t1.training_site";
                    break;
                case "3":
                    $order_by_param = "t1.training_type";
                    break;
                case "4":
                    $order_by_param = "t1.training_duration";
                    break;
                case "5":
                    $order_by_param = "t1.training_year";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and ( t1.training_site like '%{$data['sSearch']}%' or t1.training_purpose like '%{$data['sSearch']}%' or t1.other_details like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_trainings as t1
                    where t1.person_id={$pid}      
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_trainings as t1
                    where t1.person_id={$pid}
                    {$search}                      
                    {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    /* Get Person Affiliated organizations */

    public static function get_person_affiliated_org($pid) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT DISTINCT organization_id  FROM person_affiliations as t1
                    where  t1.person_id = {$pid} ";
        $members = DB::query(Database::SELECT, $sql)->execute()->as_array();
        return $members;
    }

    //update Affiliations/Training Record
    public static function update_affiliations($data, $uid, $pid) {

        if (empty($data['tyear'])) {
            $data['tyear'] = '';
        }
        // print_r($data); exit;
        $chk = Helpers_Person::check_person_affiliations_record_exist($data['org'], $pid);

        if ((!empty($data['recordid']) && $data['recordid'] != 0)) {
            $query = DB::update('person_affiliations')->set(array('organization_id' => $data['org'], 'designation' => $data['desig'], 'details' => $data['detail'], 'ideological_stance' => $data['stance'], 'self_recruitment_details' => $data['recruited']))
                    ->where('id', '=', $data['recordid'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 61, NULL, NULL, $pid);
        } elseif ($chk == 1) {
            $query = DB::update('person_affiliations')->set(array('organization_id' => $data['org'], 'designation' => $data['desig'], 'details' => $data['detail'], 'ideological_stance' => $data['stance'], 'self_recruitment_details' => $data['recruited']))
                    ->where('person_id', '=', $pid)
                    ->and_where('organization_id', '=', $data['org'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 61, NULL, NULL, $pid);
        } else {

            $query = DB::insert('person_affiliations', array('person_id', 'organization_id', 'designation', 'details', 'ideological_stance', 'self_recruitment_details'))
                    ->values(array($pid, $data['org'], $data['desig'], $data['detail'], $data['stance'], $data['recruited']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 60, NULL, NULL, $pid);
        }
        return 1;
    }

    //update Trainings Record
    public static function update_trainings($data, $uid, $pid) {
        // print_r($data); exit;
        if (empty($data['training_year'])) {
            $data['training_year'] = '';
        }

        if ((!empty($data['training_update']) && $data['training_update'] != 0)) {
            $query = DB::update('person_trainings')->set(array('organization_id' => $data['training_org'], 'training_camp' => $data['training_camp'], 'training_site' => $data['training_site'], 'training_type_id' => $data['training_type'], 'training_duration' => $data['training_duration'], 'training_year' => $data['training_year'], 'training_purpose' => $data['training_purpose'], 'material_taught' => $data['material_taught'], 'other_details' => $data['training_details']))
                    ->where('id', '=', $data['training_update'])
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 85, NULL, NULL, $pid);
        } else {

            $query = DB::insert('person_trainings', array('person_id', 'organization_id', 'training_camp', 'training_site', 'training_type_id', 'training_duration', 'training_year', 'training_purpose', 'material_taught', 'other_details'))
                    ->values(array($pid, $data['training_org'], $data['training_camp'], $data['training_site'], $data['training_type'], $data['training_duration'], $data['training_year'], $data['training_purpose'], $data['material_taught'], $data['training_details']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 84, NULL, NULL, $pid);
        }
        $query = DB::update('person_affiliations')->set(array('is_trained' => 1))
                ->where('person_id', '=', $pid)
                ->and_where('organization_id', '=', $data['training_org'])
                ->execute();
        return 1;
    }

    //Delete Affiliations Record
    public static function delete_affiliations($rec, $person_id) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_affiliations as t1
                    where t1.id=$rec and t1.person_id = $person_id";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;

        $var1 = $members['project_id'];
        $var1 .= '<>';
        $var1 .= $members['organization_id'];
        $var1 .= '<>';
        $var1 .= $members['designation'];
        $var1 .= '<>';
        $var1 .= $members['details'];
        $var2 = '';
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 62, $var1, $var2, $person_id);

        $query = DB::delete('person_affiliations')
                ->where('id', '=', $rec)
                ->execute();
        return $query;
    }

    //update person pic
    public static function update_personpic($data, $uid, $pid) {
        $query = DB::update('person')->set(array('image_url' => $data['person_pic'], 'user_id' => $uid,))
                ->where('person_id', '=', $pid)
                ->execute();
        Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
        return $query;
    }

    //update person Verisis
    public static function update_personverisis($data, $uid, $pid) {
        //check nadra profile exist
        $chk = Helpers_Person::check_person_nadra_profile_exist($pid);
        $cnic = Helpers_Person::get_person_cnic($pid);
        if ($chk > 0) {
            $query = DB::update('person_nadra_profile')->set(array('cnic_image_url' => $data['person_verysis'],))
                    ->where('person_id', '=', $pid)
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
            return $query;
        } else {
            $query = DB::update('person')->set(array('is_nadra_profile_exists' => 1,))
                    ->where('person_id', '=', $pid)
                    ->execute();
            $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'is_cnic_image_available', 'family_tree_id', 'permanent_street_address', 'permanent_city', 'permanent_state', 'permanent_pakistan', 'cnic_image_url', 'user_id'))
                    ->values(array($pid, $cnic, 1, '', '', '', '', 0, $data['person_verysis'], $uid))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
        }
    }
    //update person fam tree
    public static function update_personftreepic($data, $uid, $pid) {

        //check nadra profile exist
        $chk = Helpers_Person::check_person_nadra_profile_exist($pid);
        $cnic = Helpers_Person::get_person_cnic($pid);
        if ($chk > 0) {
            $query = DB::update('person_nadra_profile')->set(array('family_image_url' => $data['person_familytree'],))
                    ->where('person_id', '=', $pid)
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
            return $query;
        } else {
            $query = DB::update('person')->set(array('is_nadra_profile_exists' => 1,))
                    ->where('person_id', '=', $pid)
                    ->execute();
            $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'is_cnic_image_available', 'family_tree_id', 'permanent_street_address', 'permanent_city', 'permanent_state', 'permanent_pakistan', 'family_image_url', 'user_id'))
                    ->values(array($pid, $cnic, 1, '', '', '', '', 0, $data['person_familytree'], $uid))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
        }
    }

    //update person Verisis request
    public static function insert_temp_verisys_table() {
        
    }

    //update person Verisis request
    public static function update_personverisisrequest($data, $uid, $pid) {
//        echo '<pre>';
//        print_r($data['person_verysis']);
//        exit;

        /*
         * cnic_number
         */
        $is_foreigner = 0;
        $cnic = $data['cnic_number'];
        $current_date = date('Y-m-d H:i:s');
        /*
          //get person id by updating table
          $array_person['cnic_number']=$cnic;
          $content = new Model_Generic();
          $pid = $content->update_cnic_number($array_person);
         * 
         */

        $is_foreigner = Helpers_Utilities::check_is_foreigner($pid);

        $chk = Helpers_Person::check_person_nadra_profile_exist($pid);
        if ($chk > 0) {
            if (!empty($is_foreigner)) {
                $query = DB::update('person_foreigner_profile')->set(array('cnic_image_url' => $data['person_verysis'],))
                        ->where('person_id', '=', $pid)
                        ->execute();
            } else {
                $query = DB::update('person_nadra_profile')->set(array('cnic_image_url' => $data['person_verysis'],))
                        ->where('person_id', '=', $pid)
                        ->execute();
            }
            //Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
            // return $query; 
        } else {
            if (!empty($is_foreigner)) {
                $query = DB::insert('person_foreigner_profile', array('person_id', 'cnic_number', 'user_id', 'cnic_image_url', 'is_cnic_image_available'))
                        ->values(array($pid, $cnic, $uid, $data['person_verysis'], 1))
                        ->execute();
            } else {
                $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'is_cnic_image_available', 'family_tree_id', 'permanent_street_address', 'permanent_city', 'permanent_state', 'permanent_pakistan', 'cnic_image_url', 'user_id'))
                        ->values(array($pid, $cnic, 1, '', '', '', '', 0, $data['person_verysis'], $uid))
                        ->execute();
            }
        }
        $query = DB::update('person')->set(array('is_nadra_profile_exists' => 1,))
                ->where('person_id', '=', $pid)
                ->execute();
        if(!empty($data['process_request_id']))
        $query = DB::update('user_request')->set(array('status' => 2, 'concerned_person_id' => $pid))
                ->where('request_id', '=', $data['process_request_id'])
                ->execute();
        $login_user_id = Auth::instance()->get_user();
        $uid = $login_user_id->id;
        Helpers_Profile::user_activity_log($uid, 64, "CNIC", $cnic, $pid);
        return $query;
    }
    //update person family tree request
    public static function update_personfamilytreerequest($data, $uid, $pid) {
//        echo '<pre>';
//        print_r($data);
//        exit;

        /*
         * cnic_number
         */
        $is_foreigner = 0;
        $cnic = $data['cnic_number'];
        $current_date = date('Y-m-d H:i:s');
        /*
          //get person id by updating table
          $array_person['cnic_number']=$cnic;
          $content = new Model_Generic();
          $pid = $content->update_cnic_number($array_person);
         *
         */

        $is_foreigner = Helpers_Utilities::check_is_foreigner($pid);

        $chk = Helpers_Person::check_person_nadra_profile_exist($pid);
        if ($chk > 0) {
            if (!empty($is_foreigner)) {
                $query = DB::update('person_foreigner_profile')->set(array('family_image_url' => $data['personfamilytree'],))
                        ->where('person_id', '=', $pid)
                        ->execute();
            } else {
                $query = DB::update('person_nadra_profile')->set(array('family_image_url' => $data['personfamilytree'],))
                        ->where('person_id', '=', $pid)
                        ->execute();
            }
            //Helpers_Profile::user_activity_log($uid, 55, NULL, NULL, $pid);
            // return $query;
        } else {
            if (!empty($is_foreigner)) {
                $query = DB::insert('person_foreigner_profile', array('person_id', 'cnic_number', 'user_id', 'family_image_url', 'is_cnic_image_available'))
                        ->values(array($pid, $cnic, $uid, $data['personfamilytree'], 1))
                        ->execute();
            } else {
                $query = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'is_cnic_image_available', 'family_tree_id', 'permanent_street_address', 'permanent_city', 'permanent_state', 'permanent_pakistan', 'family_image_url', 'user_id'))
                        ->values(array($pid, $cnic, 1, '', '', '', '', 0, $data['personfamilytree'], $uid))
                        ->execute();
            }
        }
        $query = DB::update('person')->set(array('is_nadra_profile_exists' => 1,))
                ->where('person_id', '=', $pid)
                ->execute();
        if(!empty($data['process_request_id']))
        $query = DB::update('user_request')->set(array('status' => 2,'processing_index' => 7, 'concerned_person_id' => $pid))
                ->where('request_id', '=', $data['process_request_id'])
                ->execute();
        $login_user_id = Auth::instance()->get_user();
        $uid = $login_user_id->id;
        Helpers_Profile::user_activity_log($uid, 64, "CNIC", $cnic, $pid);
        return $query;
    }

    //update person Travel History Request
    public static function update_travelhistory_request($file, $data, $uid, $abst=Null) {
       // echo '<pre>';        print_r($file); exit;
        $request_id = !empty($data['process_request_id']) ? $data['process_request_id'] : 0;
        $sql = "SELECT request_id, reason, user_request_type_id,user_id, email_type_name, requested_value, concerned_person_id, t1.company_name,
                       created_at, status, processing_index, em.message_id, em.message_subject, em.sender_id FROM  user_request as t1 
                       join email_templates_type as t2 on t1.user_request_type_id = t2.id  join email_messages as em on em.message_id = t1.message_id                          
                       and t1.request_id = {$request_id};";
        $members = DB::query(Database::SELECT, $sql)->execute()->current(); //->as_array();
        $file_id = Helpers_Upload::get_fileid_with_requestid($members['request_id']);
        if (!empty($file_id)) {
            $is_file_exist = 1;
        } else {
            $is_file_exist = 0;
            $file_id = Helpers_Utilities::id_generator("file_id");
        }
        $file_path = !empty($file_id) ? Helpers_Upload::get_request_data_path($file_id, 'save') : '';
        if(!empty($abst))
        {
            
            $new_file_info = PATHINFO($abst);
            $filename = 'rqt' . $request_id . 'fid' . $file_id . '.' . $new_file_info['extension'];
            $cmpl_path = $file_path . $filename;   
            //echo $cmpl_path; exit;
            file_put_contents($cmpl_path, file_get_contents($file));
            unlink($file);
        }else{    
            $new_file_info = PATHINFO($file['name']);
            $filename = $file;
            //echo '<pre>';        print_r($new_file_info); exit;
            if (!empty($filename) && !empty($new_file_info) && !empty($new_file_info['extension'])) {
                if (!empty($filename)) {
                    $filename = 'rqt' . $request_id . 'fid' . $file_id . '.' . $new_file_info['extension'];
                } else {
                    $filename = 'rqt' . $request_id . 'fid' . $file_id . '.' . $new_file_info['extension'];
                }
            }
            if($new_file_info['extension'] !='pdf')
            {    
            if ($file = Upload::save($file, NULL, $file_path)) {
                $img = Image::factory($file);
                
                $img->save($file_path . $filename);
                //if ($type == "user") {
                Helpers_Profile::_resize_images($filename, $new_file_info['extension']);
                //}
                unlink($file);
                //return trim($filename);
            }            
            }else{                
                $uploaded = Upload::save($file, $filename, $file_path);
//                if ($uploaded)
//                {                  
//                    $this->set('file', $file['file_new_name']);
//                    $this->set('type', strtolower(pathinfo($file['file_new_name'], PATHINFO_EXTENSION)));
//                    $this->set('size', $file['size']);
//                }
            }
        }
        if (!empty($file_id) && $filename != 'na' && $is_file_exist == 0) {
            Helpers_Upload::insert_file_record($filename, $members['user_id'], $members['user_request_type_id'], $members['company_name'], $members['requested_value'], $members['request_id'], $members['reason'], $file_id);
        }
        Helpers_Email::change_status_raw($filename, '', '', $members['message_id'], $members['request_id'], $is_file_exist, 7);
        return 1;
    }

    /* Get Person Affiliations & Trainings */

    public static function get_person_reports($data, $count, $pid) {
        /* Sorted Data */
//        $order_by_param = "person_id";
//        if(isset($data['iSortCol_0'])){
//            switch ($data['iSortCol_0']){
//                case "0":
//                    $order_by_param = "report_type";
//                    break;
//                case "1":
//                    $order_by_param = "report_reference_no";
//                    break; 
//                case "2":
//                    $order_by_param = "report_date";
//                    break;                 
//                case "3":
//                    $order_by_param = "file_link";
//                    break;                   
//            }
//        }      

        /* Order By */
//        $order_by_type = "desc";
//        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
//            $order_by_type = $data['sSortDir_0'];
//        }
        //if(!$need_for_count){
//            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and t1.report_details like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_reports as t1
                    where t1.person_id={$pid}       
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_reports as t1
                    where t1.person_id={$pid} 
                    {$search}  
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //update Reports Record
    public static function update_personreports($data, $uid, $pid) {
        $chk = Helpers_Person::check_person_reports_record_exist($data['reporttype'], $data['reportno'], $pid);
        if ($chk == 1) {
            $query = DB::update('person_reports')->set(array('person_id' => $pid, 'report_type' => $data['reporttype'], 'report_reference_no' => $data['reportno'], 'report_date' => $data['reportdate'], 'report_details' => $data['reportbrief'], 'file_link' => $data['file_link']))
                    ->where('person_id', '=', $pid)
                    ->and_where('report_type', '=', $data['reporttype'])
                    ->and_where('report_reference_no', '=', $data['reportno'])
                    ->execute();
            //print_r($query); exit;
            Helpers_Profile::user_activity_log($uid, 58, NULL, NULL, $pid);
        } elseif ($chk == 0) {
            $query = DB::insert('person_reports', array('person_id', 'report_type', 'report_reference_no', 'report_date', 'report_details', 'file_link'))
                    ->values(array($pid, $data['reporttype'], $data['reportno'], $data['reportdate'], $data['reportbrief'], $data['file_link']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 57, NULL, NULL, $pid);
        }
        return 1;
    }

    //Delete person report
    public static function delete_personreport($type, $no, $pid) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_reports as t1
                    where t1.report_type=$type and t1.report_reference_no = '{$no}'";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;

        $var1 = $members['report_type'];
        $var1 .= '<>';
        $var1 .= $members['report_reference_no'];
        $var1 .= '<>';
        $var1 .= $members['report_date'];
        $var2 = $members['report_details'];
        $var2 .= '<>';
        $var2 .= $members['file_link'];
        $uid = $login_user->id;
        //delete link
        //get person save data path
        $person_save_data_path = !empty($pid) ? Helpers_Person:: get_person_save_data_path($pid) : '';
//        unlink($person_save_data_path . $members['file_link']);

        Helpers_Profile::user_activity_log($uid, 59, $var1, $var2, $pid);
        $query = DB::delete('person_reports')
                ->where('person_id', '=', $pid)
                ->and_where('report_type', '=', $type)
                ->and_where('report_reference_no', '=', $no)
                ->execute();
        return 1;
    }

    /* Get Person recommendations/remarks */

    public static function get_person_income_sources($data, $count, $pid) {
        /* Sorted Data */
//        $order_by_param = "person_id";
//        if(isset($data['iSortCol_0'])){
//            switch ($data['iSortCol_0']){
//                case "0":
//                    $order_by_param = "person_id";
//                    break;
//                case "1":
//                    $order_by_param = "income_source_name";
//                    break; 
//                case "2":
//                    $order_by_param = "details";
//                    break;                 
//                case "3":
//                    $order_by_param = "file_link";
//                    break;                   
//            }
//        }      
//        
//        /* Order By */
//        $order_by_type = "desc";
//        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
//            $order_by_type = $data['sSortDir_0'];
//        }
//        //if(!$need_for_count){
//            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and ( t1.details like '%{$data['sSearch']}%' or t1.income_source_name like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_income_sources as t1
                    where t1.person_id={$pid}       
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_income_sources as t1
                    where t1.person_id={$pid} 
                    {$search}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //Delete Income SOurce Record
    public static function delete_income_source($sourceid, $person_id) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_income_sources as t1
                    where t1.id=$sourceid and t1.person_id = $person_id";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;
        $query = DB::delete('person_income_sources')
                ->where('id', '=', $sourceid)
                ->execute();
        $var1 = $members['income_source_name'];

        $var2 = $members['details'];
        $var2 .= '<>';
        $var2 .= $members['file_link'];
        $uid = $login_user->id;

//get person save data path
        $person_save_data_path = !empty($person_id) ? Helpers_Person:: get_person_save_data_path($person_id) : '';
        unlink($person_save_data_path . $members['file_link']);
        Helpers_Profile::user_activity_log($uid, 41, $var1, $var2, $person_id);
        return 1;
    }

    //update Reports Record
    public static function update_personincomesource($data, $uid, $pid) {
        // $chk= Helpers_Person::check_person_income_source_record_exist($data['sourcename'],$pid);
        if (!empty($data['sourceid'])) {
            if (!empty($data['file_link'])) {
                $query = DB::update('person_income_sources')->set(array('person_id' => $pid, 'income_source_name' => $data['sourcename'], 'details' => $data['sourcedetails'], 'file_link' => $data['file_link']))
                        ->where('id', '=', $data['sourceid'])
                        ->execute();
                Helpers_Profile::user_activity_log($uid, 40, NULL, NULL, $pid);
            } else {
                $query = DB::update('person_income_sources')->set(array('person_id' => $pid, 'income_source_name' => $data['sourcename'], 'details' => $data['sourcedetails']))
                        ->where('id', '=', $data['sourceid'])
                        ->execute();
                Helpers_Profile::user_activity_log($uid, 40, NULL, NULL, $pid);
            }
        } else {
            $query = DB::insert('person_income_sources', array('person_id', 'income_source_name', 'details', 'file_link'))
                    ->values(array($pid, $data['sourcename'], $data['sourcedetails'], $data['file_link']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 39, NULL, NULL, $pid);
        }
        return 1;
    }

    //update Reports Record
    public static function update_personassets($data, $uid, $pid) {
        // $chk= Helpers_Person::check_person_assets_record_exist($data['assetname'],$pid);
        if (!empty($data['assetid'])) {
            if (!empty($data['file_link'])) {
                $query = DB::update('person_assets')->set(array('person_id' => $pid, 'asset_name' => $data['assetname'], 'details' => $data['assetdetails'], 'file_link' => $data['file_link']))
                        ->where('person_id', '=', $pid)
                        ->and_where('id', '=', $data['assetid'])
                        ->execute();
                Helpers_Profile::user_activity_log($uid, 46, NULL, NULL, $pid);
            } else {
                $query = DB::update('person_assets')->set(array('person_id' => $pid, 'asset_name' => $data['assetname'], 'details' => $data['assetdetails'], 'file_link' => $data['file_link']))
                        ->where('person_id', '=', $pid)
                        ->and_where('id', '=', $data['assetid'])
                        ->execute();
                Helpers_Profile::user_activity_log($uid, 46, NULL, NULL, $pid);
            }
        } else {
            $query = DB::insert('person_assets', array('person_id', 'asset_name', 'details', 'file_link'))
                    ->values(array($pid, $data['assetname'], $data['assetdetails'], $data['file_link']))
                    ->execute();
            Helpers_Profile::user_activity_log($uid, 45, NULL, NULL, $pid);
        }
        return 1;
    }

    /* Get Person recommendations/remarks */

    public static function get_person_assets($data, $count, $pid) {
        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "person_id";
                    break;
                case "1":
                    $order_by_param = "asset_name";
                    break;
                case "2":
                    $order_by_param = "details";
                    break;
                case "3":
                    $order_by_param = "file_link";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);
            $search = "and (t1.details like '%{$data['sSearch']}%' or t1.asset_name like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT count(t1.person_id) as count
                    FROM person_assets as t1
                    where t1.person_id={$pid}
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {
            $sql = "SELECT * 
                    FROM person_assets as t1
                    where t1.person_id={$pid}
                    {$search}
                    {$limit}";
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }

    //Delete asset Record
    public static function delete_asset($rec, $person_id) {
        $login_user = Auth::instance()->get_user();
        $sql = "SELECT * FROM person_assets as t1
                    where t1.id=$rec and t1.person_id = $person_id";
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        //print_r($members); exit;
        $var1 = $members['asset_name'];
        $var1 .= '<>';
        $var1 .= $members['details'];
        $var2 = $members['file_link'];
        $uid = $login_user->id;
        //delete link
        //get person save data path
        $person_save_data_path = !empty($person_id) ? Helpers_Person:: get_person_save_data_path($person_id) : '';

        unlink($person_save_data_path . $members['file_link']);
        Helpers_Profile::user_activity_log($uid, 47, $var1, $var2, $person_id);
        $query = DB::delete('person_assets')
                ->where('id', '=', $rec)
                ->execute();
        return 1;
    }

    //update person pic
    public static function upload_person_pictures($data, $uid, $pid) {
        if (!empty($data['person_pic'])) {
            $chk = Helpers_Person::check_person_picture_exist($data['picture_type'], $pid);
            if (empty($chk)) {
                $query = DB::insert('person_pictures', array('person_id', 'picture_type', 'image_url'))
                        ->values(array($pid, $data['picture_type'], $data['person_pic']))
                        ->execute();
            } else {
                $query = DB::update('person_pictures')->set(array('image_url' => $data['person_pic']))
                        ->where('person_id', '=', $pid)
                        ->and_where('picture_type', '=', $data['person_pic'])
                        ->execute();
            }

            switch ($data['picture_type']) {
                case 1:
                    Helpers_Profile::user_activity_log($uid, 72, NULL, NULL, $pid);
                    break;
                case 2:
                    Helpers_Profile::user_activity_log($uid, 74, NULL, NULL, $pid);
                    break;
                case 3:
                    Helpers_Profile::user_activity_log($uid, 73, NULL, NULL, $pid);
                    break;
            }
        } else {
            $query = 0;
        }
        return $query;
    }

    public static function update_verisys_info($data, $user_id, $pid) {
        $issue_date = !empty($data['issue_date']) ? date('Y-m-d', strtotime($data['issue_date'])) : '';
        $expiry_date = !empty($data['expiry_date']) ? date('Y-m-d', strtotime($data['expiry_date'])) : '';
        //check if record exists in  person_nadra_profile_history     
        $record_count = Helpers_Profile::check_with_dates($pid, $issue_date, $expiry_date);
        //if record id is 0, means no record exists
        if ($record_count == 0) {
            //insert new record with all new data 
            $query = DB::insert('person_nadra_profile_history', array('person_id', 'person_name', 'person_g_name', 'person_gender', 'person_dob', 'issue_date', 'expiry_date', 'person_present_add', 'person_permanent_add', 'person_birth_place', 'person_religion', 'person_mother_name', 'cnic_image_url', 'user_id'))
                    ->values(array($pid, $data['person_name'], $data['person_g_name'], $data['Person_gender'], date('Y-m-d', strtotime($data['date_of_birth'])), date('Y-m-d', strtotime($data['issue_date'])), date('Y-m-d', strtotime($data['expiry_date'])), $data['present_address'], $data['permanent_address'], $data['birth_place'], $data['religion'], $data['mother_name'], $data['cnic_image_url'], $user_id))
                    ->execute();
            $query2 = DB::update('person_nadra_profile')
                    ->set(array('person_name' => $data['person_name'],
                        'person_g_name' => $data['person_g_name'],
                        'person_gender' => $data['Person_gender'],
                        'person_present_add' => $data['present_address'],
                        'person_permanent_add' => $data['permanent_address'],
                        'person_dob' => date('Y-m-d', strtotime($data['date_of_birth'])),
                        'user_id' => $user_id))
                    ->where('person_id', '=', $pid)
                    ->execute();
            return ($query[1]);
        } else {
            return 99;
        }
    }

}
