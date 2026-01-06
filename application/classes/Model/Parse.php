<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with Parseing 
 */
class Model_Parse {
    /*
     * Get Person id from phone number 
     * 
     */

    public static function get_person_id($phone_number) {
        $DB = Database::instance();
        $sql = "select person_id from person_phone_number WHERE phone_number = {$phone_number}";        
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results->person_id;
    }

    /* check first and last date */
    public static function cdr_date_rang($person_id, $party_a, $start_date, $end_date, $type = NULL) {
            $DB = Database::instance();
            $sql = "SELECT min(call_at) as min, max(call_at) as max FROM `person_call_log`
                        where person_id = {$person_id}                        
                        and phone_number = {$party_a}                                
                        limit 1";                            
                $call_result = $DB->query(Database::SELECT, $sql, TRUE)->current();                
            $sql = "SELECT min(sms_at) as min, max(sms_at) as max FROM `person_sms_log`
                where person_id = {$person_id}                        
                and phone_number = {$party_a}    
                limit 1";                        
                $sms_result = $DB->query(Database::SELECT, $sql, TRUE)->current();
                
                if(!empty($sms_result->min) && !empty($call_result->min))
                {
                    if(strtotime($sms_result->min)< strtotime($call_result->min))
                        $min = $sms_result->min;
                    else         
                        $min = $call_result->min;
                }
                if(!empty($sms_result->max) && !empty($call_result->max))
                {
                    if(strtotime($sms_result->max)> strtotime($call_result->max))
                        $max = $sms_result->max;
                    else         
                        $max = $call_result->max;
                }
                if(empty($sms_result->min) && empty($call_result->min))
                {
                    $min = '';
                }elseif(!empty($sms_result->min) && empty($call_result->min)){
                    $min = $sms_result->min;
                }elseif(empty($sms_result->min) && !empty($call_result->min)){
                    $min = $call_result->min;
                }
                if(empty($sms_result->max) && empty($call_result->max))
                {
                    $max = '';
                }elseif(!empty($sms_result->max) && empty($call_result->max)){
                    $max = $sms_result->max;
                }elseif(empty($sms_result->max) && !empty($call_result->max)){
                    $max = $call_result->max;
                }
                $result ['min'] = $min;
                $result ['max'] = $max;
                
                return $result;
        }
    
    
    /* check record exist or not 
     * 
     */

    public static function cdr_exist($person_id, $party_a, $start_date, $end_date, $type = NULL) {

        if ($type == NULL) {
            $DB = Database::instance();
//            $sql = "SELECT psl.phone_number FROM `person_sms_log`  as psl
//            join person_call_log as pcl on psl.person_id = psl.person_id
//            where pcl.phone_number = psl.phone_number
//            and psl.sms_at >= '{$start_date}'
//            and psl.sms_at <= '{$end_date}'
//            and psl.person_id = {$person_id}
//            and psl.phone_number = {$party_a}
//            limit 1";
//            echo $sql;
//            exit;
//            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $sql = "SELECT phone_number FROM `person_call_log`
                        where person_id = {$person_id}
                        and call_at >= '{$start_date}'
                        and call_at <= '{$end_date}'
                        and phone_number = {$party_a}    
                        limit 1";                          
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                if(!empty($results->phone_number))                    
                {   return $results;
                
                }else {
                    $sql = "SELECT phone_number FROM `person_sms_log`
                        where person_id = {$person_id}
                        and sms_at >= '{$start_date}'
                        and sms_at <= '{$end_date}'
                        and phone_number = {$party_a}    
                        limit 1";                        
                    $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                    return $results;
                }
        } else {
            if ($type == 'Call - Outgoing' || $type == 'Call - Incoming') {
                $DB = Database::instance();
                $sql = "SELECT phone_number FROM `person_call_log`
                        where person_id = {$person_id}
                        and call_at >= '{$start_date}'
                        and call_at <= '{$end_date}'
                        and phone_number = {$party_a}    
                        limit 1";                          
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                return $results;
            } else {
                $DB = Database::instance();
                $sql = "SELECT phone_number FROM `person_sms_log`
                        where person_id = {$person_id}
                        and sms_at >= '{$start_date}'
                        and sms_at <= '{$end_date}'
                        and phone_number = {$party_a}    
                        limit 1";                        
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                return $results;
            }
        }
    }

    /*
     * check Imei number against imei
     * 
     */

    public static function check_imei_number($imei_number) {
        $DB = Database::instance();        
        $sql = "select id from person_phone_device WHERE imei_number = {$imei_number}";        
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return !empty($results->id) ? $results->id : '';
    }

    /*
     * iNSERT IMEI against imei first time
     * 
     */

    public static function inert_imei_against_imei_first($person_id, $imei_number, $date_right) {
        //date("Y-m-d H:i:s",$val);
           $DB = Database::instance();
           $sql = "select id from person_phone_device WHERE in_use_since > '{$date_right}' and imei_number = {$imei_number}";
           $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(empty($results->id))
        {
            $query = DB::insert('person_phone_device', array('person_id', 'imei_number', 'in_use_since'))
                    ->values(array($person_id, $imei_number, $date_right))
                    ->execute();
            return $query[0];
        }
            return $results->id;        
    }
    
    
    /*
     * iNSERT IMEI Number first time
     * 
     */

    public static function inert_imei_number_first($person_id, $phone_number, $imei_number, $date_right) {
        //date("Y-m-d H:i:s",$val);
        $query = DB::insert('person_phone_device', array('person_id', 'imei_number', 'in_use_since'))
                ->values(array($person_id, $imei_number, $date_right))
                ->execute();
        $query_pdn = DB::insert('person_device_numbers', array('device_id', 'phone_number', 'is_active', 'first_use'))
                ->values(array($query[0], $phone_number, 1, $date_right))
                ->execute();
        return $query[0];
    }

    public static function last_interaction_at($person_id, $phone_number, $imei_number, $date_right, $device_id, $user_id) {
        
        $DB = Database::instance();
        if(!empty($imei_number))
        {    
                $sql = "select id from person_phone_device WHERE imei_number = {$imei_number} and (last_interaction_at < '{$date_right}' or last_interaction_at is NULL )";
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                
               // exit;
//             if(!empty($results->id) && !empty($date_right) && sizeof($date_right)>0)
             if(!empty($results->id) && !empty($date_right) && strlen($date_right)>0)
             {
                 $query = DB::update('person_phone_device')->set(array('person_id'=>$person_id,'last_interaction_at' => $date_right, 'user_id'=>$user_id))
                     ->where('id', '=', $device_id)   
                    // ->and_where('person_id', '=', $person_id)
                     ->and_where('imei_number', '=', $imei_number)    
                     ->execute();
             }
        }
        $DB = Database::instance();
           $sql = "select * from person_phone_number WHERE phone_number = {$phone_number} and (sim_last_used_at < '{$date_right}' or sim_last_used_at is NULL )";
           $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(!empty($results))
        {
            $query = DB::update('person_phone_number')->set(array('sim_last_used_at' => $date_right))
                ->where('phone_number', '=', $phone_number)                   
                ->execute();
        }
        if(!empty($device_id))
        {    
            $DB = Database::instance();
               $sql = "select * from person_device_numbers WHERE device_id = {$device_id} and (last_use < '{$date_right}' or last_use is NULL  )";
               $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            if(!empty($results))
            {
                $query = DB::update('person_device_numbers')->set(array('last_use' => $date_right))
                    ->where('device_id', '=', $device_id)                   
                    ->execute();
            }
        
        }
        
    }
    /*
     * iNSERT IMEI Number first time
     * 
     */

    public static function inert_imei_number_last($person_id, $phone_number, $imei_number, $date_right, $device_id) {
        //date("Y-m-d H:i:s",$val);

        $DB = Database::instance();
           $sql = "select id from person_phone_device WHERE in_use_since < '{$date_right}' and id = {$device_id}";
           $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(empty($results->id))
        {
            $query = DB::update('person_phone_device')->set(array('last_interaction_at' => $date_right))
                ->where('id', '=', $device_id)   
                ->and_where('person_id', '=', $person_id)
                ->and_where('imei_number', '=', $imei_number)    
                ->execute();
        }
    }
    /*
     * iNSERT IMEI against imei last time
     * 
     */

    public static function inert_imei_against_imei_last($person_id, $date_right, $device_id, $imei_number) {
        //date("Y-m-d H:i:s",$val);
           $DB = Database::instance();
           $sql = "select id from person_phone_device WHERE in_use_since < '{$date_right}' and id = {$device_id}";
           $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        if(empty($results->id))
        {
            $query = DB::update('person_phone_device')->set(array('last_interaction_at' => $date_right))
                ->where('id', '=', $device_id)   
                ->and_where('person_id', '=', $person_id)
                ->and_where('imei_number', '=', $imei_number)    
                ->execute();
        }
            return $results->id;        
    }

    /*
     *  Setp 7 for cdr against phone
     */
    public static function update_person_p_device($imei_number, $person_id, $party_a, $date_right, $date_right_last) {
        $DB = Database::instance();
        
        if(!empty($imei_number))
        {    
        $sql = "select * from person_phone_device WHERE imei_number = {$imei_number} and in_use_since > '{$date_right}'";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            if(!empty($results) && strlen($date_right) > 0)
            {
            $query = DB::update('person_phone_device')->set(array('in_use_since' => $date_right))                    
                    ->where('id', '=', $results->id)
                    ->where('imei_number', '=', $imei_number)                    
                    ->execute();   
            }
            
            $sql = "select * from person_phone_device WHERE imei_number = {$imei_number} and last_interaction_at < '{$date_right_last}'";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            if(!empty($results) && strlen($date_right) > 0)
            {
            $query = DB::update('person_phone_device')->set(array('last_interaction_at' => $date_right_last))                    
                    ->where('id', '=', $results->id)
                    ->where('imei_number', '=', $imei_number)                    
                    ->execute();   
            }
            //return $results->id;
        }    
    }
    /*
     * iNSERT IMEI Number first time
     * 
     */

    public static function inert_imei_device($person_id, $phone_number, $device_id, $date_right) {
        //date("Y-m-d H:i:s",$val);
        $DB = Database::instance();
        $sql = "select is_active from person_device_numbers WHERE phone_number = {$phone_number} and device_id = {$device_id}";
        //echo $sql;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        //echo 'check';       
        // print_r($results);

        if (isset($results->is_active)) {
            switch ($results->is_active) {
                case 0:
                    $query = DB::update('person_device_numbers')->set(array('is_active' => 0))
                            ->where('device_id', '=', $device_id)
                            ->execute();
                    $query = DB::update('person_device_numbers')->set(array('is_active' => 1))
                            ->where('device_id', '=', $device_id)
                            ->and_where('phone_number', '=', $phone_number)
                            ->execute();
                    break;
                case 1:
                    break;
            }
        } else {
            $query = DB::update('person_device_numbers')->set(array('is_active' => 0))
                    ->where('device_id', '=', $device_id)
                    ->execute();
            $query_pdn = DB::insert('person_device_numbers', array('device_id', 'phone_number', 'is_active'))
                    ->values(array($device_id, $phone_number, 1))
                    ->execute();
        }

        return $device_id;
    }

    /* Parsing against CDR call */

    //public static function person_call_log($person_id, $phone_number, $other_person_phone_number, $call_at, $call_end_at, $duration_in_seconds, $cell_id, $address, $imei_number, $is_outgoing, $latitude, $longitude, $imsi_number, $mnc){
    public static function person_call_log($data) {
        DB::insert('person_call_log', array('person_id', 'phone_number', 'other_person_phone_number', 'call_at', 'call_end_at', 'duration_in_seconds', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc'))
                //  ->values(array($person_id, $phone_number, $other_person_phone_number, $call_at, $call_end_at, $duration_in_seconds, $cell_id, $address, $imei_number, $is_outgoing,  $latitude, $longitude, $imsi_number, $mnc))
                ->values($data)
                ->execute();
    }

    /* Parsing against CDR sms */

    //public static function person_sms_log($person_id, $phone_number, $other_person_phone_number, $sms_at, $cell_id, $address, $imei_number, $is_outgoing, $latitude, $longitude, $imsi_number, $mnc) {   
    public static function person_sms_log($data) {
        DB::insert('person_sms_log', array('person_id', 'phone_number', 'other_person_phone_number', 'sms_at', 'cell_id', 'address', 'imei_number', 'is_outgoing', 'latitude', 'longitude', 'imsi_number', 'mnc', 'upload_at'))
                //  ->values(array($person_id, $phone_number, $other_person_phone_number, $sms_at, $cell_id, $address, $imei_number, $is_outgoing, $latitude, $longitude, $imsi_number, $mnc, $date))
                ->values($data)
                ->execute();
    }
//get user profile exist
    public static function check_msisdn($data) {
        $DB = Database::instance();
        $sql = "SELECT t1.sim_owner
                FROM  person_phone_number AS t1
                WHERE t1.phone_number= $data ";
        $results = DB::query(Database::SELECT, $sql)->as_object()->execute()->current();
        $simownerid = isset($results->sim_owner) && !empty($results->sim_owner) ? $results->sim_owner : -1;        
        return $simownerid;
    }
//get user profile data against msisdn
    public static function check_msisdn_detail($data) {
       $DB = Database::instance();
        $sql = "SELECT t1.sim_owner,t1.person_id,t1.phone_number,t1.sim_activated_at,t1.status,t2.first_name,t2.last_name,t2.address 
                FROM  person_phone_number AS t1
                inner join person as t2 on t1.sim_owner=t2.person_id
                WHERE t1.phone_number= $data ";
        $prof = DB::query(Database::SELECT, $sql)->as_object()->execute()->current();
        
       // print_r($sql); exit;
        $ownerid = isset($prof->sim_owner)  ? $prof->sim_owner : ""; 
        $simuserid = isset($prof->person_id)  ? $prof->person_id : ""; 
        $sim = isset($prof->phone_number)  ? $prof->phone_number : "NA"; 
        $fname = isset($prof->first_name)  ? $prof->first_name : "NA"; 
        $lname = isset($prof->last_name)  ? $prof->last_name : ""; 
        $cnic = isset($prof->person_id )  ? Helpers_Person::get_person_cnic($prof->person_id) : "NA"; 
        $simact = isset($prof->sim_activated_At)  ? $prof->sim_activated_At : "NA"; 
        $simstatus = isset($prof->status)  ? $prof->status : "NA"; 
       // $is_foreigner_id_owner = isset($prof->is_foreigner)  ? $prof->is_foreigner : "NA"; 
        if($simstatus!="NA" || $simstatus!=""){
            if($simstatus==1){
                $simstatus="Active";
            }else{
                $simstatus="InActive";
            }
        }
        
        $company = !empty($data) ? Helpers_Utilities::get_company_name_by_mobile($data) : "NA"; 
        $simuserdetail='NA';
        $simusercnic='NA';
        $simownerdetails='NA';
        if(!empty($ownerid)){
            $simownerdetails='<a href="'.URL::site('persons/dashboard/?id='.Helpers_Utilities::encrypted_key($ownerid,"encrypt")). '"> <span class="text-black"> <b>SIM Owner:</b> '.$fname." ".$lname.' </span><span class="active"> (Profile)</span></a>';
        }
        if((!empty($ownerid)) && $ownerid==$simuserid){           
            $simuserdetail='<span class="text-black"><b>SIM User: </b> Self</span><span class="active"></span>';
            $simusercnic="same as above"; 
        }elseif(!empty ($simuserid)){
        
        $simuser =!empty($simuserid)  ? Helpers_Person::get_person_name($simuserid) : "NA";
        $simusercnic = !empty($simuserid)  ? Helpers_Person::get_person_cnic($simuserid) : "NA"; 
        $simuserdetail='<a href="'.URL::site('persons/dashboard/?id='.Helpers_Utilities::encrypted_key($simuserid,"encrypt")).'"> <span class="text-black"><b>SIM User: </b>'.$simuser.'</span><span class="active"> (Profile)</span></a>';       
        }
        
         ?>

                                        <div >
                                            <h4><b>Phone Number Details: </b></h4>
                                        </div> 
                                        <div class="col-sm-6 ">
                                        <ul class="todo-list">
                                        <li >
                                            <span class="text-black"> <b>Phone Number: </b><?php echo $sim; ?> </span>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Company: </b><?php echo $company; ?> </span>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Activated At: </b><?php echo $simact; ?> </span>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Status: </b><?php echo $simstatus; ?> </span>
                                        </li>
                                        
                                        </ul>
                                        </div>
                                        <div class="col-sm-6 ">
                                        <ul class="todo-list">
                                        <li>
                                            <?php 
                                            echo $simownerdetails;                                           
                                        
                                            ?>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Owner CNIC: </b><?php echo $cnic; ?> </span>
                                        </li>
                                        <li >
                                            <?php echo $simuserdetail; ?>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>SIM User CNIC: </b><?php echo $simusercnic; ?> </span>
                                        </li>
                                    </ul>
                                        </div>
                                            <div class="col-sm-12">
                                            <hr class="style14 "> 
                                            </div>
                                      
            
            <?php
      
    }
//get user profile data against cnic
    public static function check_cnic_detail($data) {
        $is_foreigner=-1;
        $person_id=  Helpers_Utilities::get_person_id_with_cnic($data);
        if(!empty($person_id)){
            $is_foreigner=  Helpers_Utilities::check_is_foreigner($person_id);
         $sub_query="FROM person as t1";
        $DB = Database::instance();
        $sql = "SELECT t1.person_id,t1.first_name,t1.last_name,t1.address,t1.father_name
                    {$sub_query}
                    WHERE t1.person_id= $person_id ";
        $prof = DB::query(Database::SELECT, $sql)->as_object()->execute()->current();
        
        }else{
            $prof = '';
        }
        
        //$person_id = isset($prof->person_id)  ? $prof->person_id : "NA"; 
        $firstname = isset($prof->first_name)  ? $prof->first_name : "NA"; 
        $lastname = isset($prof->last_name) ? $prof->last_name : ""; 
        $name=$firstname." ".$lastname;
        $fname = isset($prof->father_name)  ? $prof->father_name : "NA"; 
        $address = isset($prof->address)  ? $prof->address : "NA";  
        if($is_foreigner==0){
            $is_foreigner="Pakistani";
        }elseif($is_foreigner==1){
            $is_foreigner="Foreigner";
        }else{
            $is_foreigner="NA";
        }
        
         ?>

                                        
                                        <div class="col-sm-12 ">
                                        <ul class="todo-list">
                                        <li >
                                            <span class="text-black"> <b>CNIC Number: </b><?php echo $data; ?> </span>
                                        </li>
                                        <?php
                                        if (!empty($person_id)) {
                                            ?>
                                        <li >                                            
                                        <a href="<?php echo URL::site('persons/dashboard/?id='.Helpers_Utilities::encrypted_key($person_id,"encrypt")); ?>"> <span class="text-black"> <b>Person Name:</b> <?php echo $name;  ?> </span><span class="active">(Profile)</span></a>                                            
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Father Name: </b><?php echo $fname; ?> </span>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Address: </b><?php echo $address; ?> </span>
                                        </li>
                                        <li >
                                            <span class="text-black"> <b>Country: </b><?php echo $is_foreigner; ?> </span>
                                        </li>
                                        <?php } ?>
                                        </ul>
                                        </div>
                                        
                                      
            
            <?php
      
    }
    //check cnic
    public static function check_cnic($data) {
        $DB = Database::instance();
        $sql = "SELECT  person_id
                        FROM  person
                        WHERE cnic_number = {$data} LIMIT 1";
        $cnic_id = DB::query(Database::SELECT, $sql)->execute()->current();
        return $cnic_id;
    }
    
    
    
    /*
     * iNSERT device against imei
     * 
     */

    public static function update_device_against_imei_last($device_id, $party_, $date_right_last){    
         $DB = Database::instance();
        $sql = "select device_id from person_device_numbers WHERE device_id = {$device_id} and last_use > '{$date_right_last}' and phone_number = {$party_}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        //echo 'check';       
        // print_r($results);

        if (!isset($results->device_id)) {
            $query = DB::update('person_device_numbers')->set(array('last_use' => $date_right_last))
                    ->where('device_id', '=', $device_id)
                    ->where('phone_number', '=', $party_)
                    ->execute();                   
        }        
    } 
    /*
     * iNSERT device against imei
     * 
     */

    public static function update_device_against_imei($device_id, $party_a, $date_right){
    
         $DB = Database::instance();
        $sql = "select device_id from person_device_numbers WHERE device_id = {$device_id} and phone_number = {$party_a}";
        //echo $sql;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        //echo 'check';       
        // print_r($results);

        if (!isset($results->device_id)) {
            
            DB::insert('person_device_numbers', array('device_id','phone_number','is_active','first_use'))
                ->values(array($device_id, $party_a, 1, $date_right))                
                ->execute();            
        }
        
    }    
    /*
     * iNSERT phone against imei
     * 
     */

    public static function update_phone_against_imei($party_a, $imsi, $date_right, $status, $mnc, $user_id){
            $DB = Database::instance();
        $sql = "select phone_number from person_phone_number WHERE phone_number = {$party_a}";
        //echo $sql;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        //echo 'check';       
        // print_r($results);

        if (!isset($results->phone_number)) {
            if($party_a>20) {
                DB::insert('person_phone_number', array('phone_number', 'imsi_number', 'sim_last_used_at', 'status', 'mnc', 'user_id'))
                    ->values(array($party_a, $imsi, $date_right, $status, $mnc, $user_id))
                    ->execute();
            }else{
                DB::insert('debugging_insertion', array('details'))
                    ->values(array('Model/Parse/update_phone_against_imei -- '.$party_a, $imsi, $date_right, $status, $mnc, $user_id))
                    ->execute();
            }
            
        }
        
    }    
    /*
     * updated file against imei
     * 
     */

    public static function update_file_against_imei($file_id, $imei, $total_record, $from, $to, $upload_status, $request_type){
        
        if(!empty($request_type))
        {
            if($request_type==1)
            {
                $query = DB::update('files')->set(array('no_of_record' => $total_record, 'data_from_date'=> $from, 'data_to_date'=>$to, 'upload_status'=>$upload_status, 'request_type'=> $request_type, 'phone_number'=> $imei))
                    ->where('id', '=', $file_id)
                    ->execute();
            }else{
                $query = DB::update('files')->set(array('no_of_record' => $total_record, 'data_from_date'=> $from, 'data_to_date'=>$to, 'upload_status'=>$upload_status, 'request_type'=> $request_type, 'imei'=> $imei))
                        ->where('id', '=', $file_id)
                        ->execute();
            } 
        }else{
            $query = DB::update('files')->set(array('no_of_record' => $total_record, 'data_from_date'=> $from, 'data_to_date'=>$to, 'upload_status'=>$upload_status))
                    ->where('id', '=', $file_id)
                    ->execute();
        }
        
    }
    
    public static function update_file_against_phone($file_id, $imei, $total_record, $from, $to, $upload_status, $request_type){        
          $query = DB::update('files')->set(array('no_of_record' => $total_record, 'data_from_date'=> $from, 'data_to_date'=>$to, 'upload_status'=>$upload_status))
                    ->where('id', '=', $file_id)
                    ->execute();                
    }
    

}
