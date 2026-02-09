<?php

//
//5440060238719
//3330296630431
//3410122697031
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Cronmanual extends Controller {
    /* Manual Phone Parsing */

    public function action_email_parse_phone() {
        
        $sql = "SELECT * FROM files as t1 where t1.is_manual=1 AND t1.is_deleted !=1 AND t1.request_type = 1 AND t1.upload_status = 0 AND t1.error_type=0 ORDER BY id ASC limit 1";                              //Where t1.user_id = {$user_id}  
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();
        foreach ($parse_data as $data) {
            $phone_data['company_name'] = $data['company_name'];
           echo $phone_data['phone_number'] = $data['phone_number'];
            $phone_data['userrequestid'] = '';
             $phone_data['file_id'] = $data['id']; 
            $data['received_file_path'] = $data['file'];
            $phone_data['is_manual'] = 1;
            try {
                switch ($data['company_name']) {
                    case 1: // mobilink                        
                        include 'cron_job' . DS . 'parse_phone' . DS . 'mobilink.inc';
                        break;
                    case 7: // warid                        
                        include 'cron_job' . DS . 'parse_phone' . DS . 'warid.inc';
                        break;
                    case 3: // Ufone                        
                        include 'cron_job' . DS . 'parse_phone' . DS . 'ufone.inc';
                        break;
                    case 6: // Telenor                        
                        include 'cron_job' . DS . 'parse_phone' . DS . 'telenor.inc';
                        break;
                    case 4: // Zong                        
                        include 'cron_job' . DS . 'parse_phone' . DS . 'zong.inc';
                        break;
                }
                /* Insertion Code */
                if (!empty($data['request_id'])) {
                    $reference_number = $data['request_id'];
                    
                    Model_ErrorLog::log(
                        'cronmanual_email_parse_phone',
                        'Request not found or could not be processed - setting email status to 5',
                        [
                            'request_id'        => $reference_number,
                            'company_name'      => $data['company_name'] ?? 'unknown',
                            'processing_index'  => 5,
                            'phone_number'      => $data['phone_number'] ?? null,
                            'file_id'           => $phone_data['file_id'] ?? null
                        ],
                        null,
                        'not_found',
                        'manual_phone_parsing_completion','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                }
                /*echo $phone_data['file_id']; exit;
                if (!empty($phone_data['file_id']))
                    $error_number = Model_Email::file_status($phone_data['file_id'], 2, 3);*/
                /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                  { */
                $sub_model = new Model_Generic();
                //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);

                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cronmanual_parse_phone',
                    $error_msg,
                    [
                        'phone_number'     => $data['phone_number'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'file_id'          => $phone_data['file_id'] ?? null
                    ],
                    $error_trace,
                    'parsing_failure',
                    'manual_phone_parsing'
                );
                
                echo $e;
                if (!empty($data['request_id'])) {
                    $reference_number = $data['request_id'];
                    
                    Model_ErrorLog::log(
                        'cronmanual_email_parse_phone',
                        'Exception caught during phone parsing - setting email status to 3 for validation/parsing error',
                        [
                            'request_id'        => $reference_number,
                            'company_name'      => $data['company_name'] ?? 'unknown',
                            'processing_index'  => 3,
                            'phone_number'      => $data['phone_number'] ?? null,
                            'file_id'           => $phone_data['file_id'] ?? null,
                            'exception_message' => $error_msg
                        ],
                        $error_trace,
                        'parsing_error',
                        'manual_phone_parsing_exception'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 3);
                }
                if (!empty($phone_data['file_id']))
                    $error_number = Model_Email::file_status($phone_data['file_id'], 1, 3);
            }
        }
    }

    public function action_email_parse_imei() {

        echo 'IMEI';
        $sql = "SELECT * FROM files as t1 where t1.is_manual=1 AND t1.is_deleted !=1 AND t1.request_type = 2 AND t1.upload_status = 0 AND t1.error_type=0 AND t1.imei != 0 ORDER BY id ASC limit 1";                              //Where t1.user_id = {$user_id}  
        $parse_data = DB::query(Database::SELECT, $sql)->execute()->as_array();

        $not_fount = 0;
        foreach ($parse_data as $data) {
            try {
                //$data['phone_number'] = Helpers_Utilities::find_imei_last_digit($data['imei']);
                $data['requested_value'] = Helpers_Utilities::find_imei_last_digit($data['imei']);
                $data['company_name'] = $data['company_name'];
                $data['userrequestid'] = '';
                $data['file_id'] = $data['id'];
                $data['received_file_path'] = $data['file'];
                $data['is_manual'] = 1;

                switch ($data['company_name']) {
                    case 1: // mobilink  
                        echo '<br>' . 'Mobilink' . '<br>';
                        include 'cron_job' . DS . 'parse_imei' . DS . 'mobilink.inc';
                        break;
                    case 7: // warid
                        echo '<br>' . 'Warid' . '<br>';
                        //print_r($data['received_file_path']);      
                        include 'cron_job' . DS . 'parse_imei' . DS . 'warid.inc';

                        break;
                    case 3: // Ufone
                        echo '<br>' . 'Ufone' . '<br>';
                        // print_r($data['received_file_path']);
                        include 'cron_job' . DS . 'parse_imei' . DS . 'ufone.inc';

                        break;
                    case 6: // Telenor
                        echo '<br>' . 'Telenor' . '<br>';
                        //print_r($data['received_file_path']);                        
                        include 'cron_job' . DS . 'parse_imei' . DS . 'telenor.inc';
                        break;
                    case 4: // Zong
                        // echo '<br>' . 'Zong' .'<br>';                        
                        include 'cron_job' . DS . 'parse_imei' . DS . 'zong.inc';

                        break;
                }

                /* Insertion Code */
                if (!empty($data['request_id'])) {
                    $reference_number = $data['request_id'];
                    
                    Model_ErrorLog::log(
                        'cronmanual_email_parse_imei',
                        'IMEI request not found or could not be processed - setting email status to 5',
                        [
                            'request_id'        => $reference_number,
                            'company_name'      => $data['company_name'] ?? 'unknown',
                            'processing_index'  => 5,
                            'imei'              => $data['imei'] ?? null,
                            'requested_value'   => $data['requested_value'] ?? null,
                            'file_id'           => $data['file_id'] ?? null
                        ],
                        null,
                        'not_found',
                        'manual_imei_parsing_completion','success'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 5);
                }
                if (!empty($phone_data['file_id']))
                    $error_number = Model_Email::file_status($phone_data['file_id'], 2, 3);

                /* if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
                  { */
                //$sub_model = new Model_Generic();
                //  $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);

                /* }else{                    
                  $reference_number = Model_Email::email_status($reference_number, 2, 3);
                  } */
            } catch (Exception $e) {
                $error_msg = $e->getMessage();
                $error_trace = $e->getTraceAsString();
                
                Model_ErrorLog::log(
                    'cronmanual_parse_imei',
                    $error_msg,
                    [
                        'imei'             => $data['imei'] ?? 'unknown',
                        'company_name'     => $data['company_name'] ?? 'unknown',
                        'file_id'          => $data['file_id'] ?? null
                    ],
                    $error_trace,
                    'parsing_failure',
                    'manual_imei_parsing'
                );
                
                //re-throw exception
                //throw new customException($email);
                //echo $e;
                if (!empty($data['request_id'])) {
                    $reference_number = $data['request_id'];
                    
                    Model_ErrorLog::log(
                        'cronmanual_email_parse_imei',
                        'Exception caught during IMEI parsing - setting email status to 3 for validation/parsing error',
                        [
                            'request_id'        => $reference_number,
                            'company_name'      => $data['company_name'] ?? 'unknown',
                            'processing_index'  => 3,
                            'imei'              => $data['imei'] ?? null,
                            'requested_value'   => $data['requested_value'] ?? null,
                            'file_id'           => $data['file_id'] ?? null,
                            'exception_message' => $error_msg
                        ],
                        $error_trace,
                        'parsing_error',
                        'manual_imei_parsing_exception'
                    );
                    
                    $reference_number = Model_Email::email_status($reference_number, 2, 3);
                }
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 2, 3);
            }
        }
    }

    public function action_apaceh_log() {
        $test = Helpers_Apachelog::get_parse_log();
        exit;
    }

    public function action_flag_change() {
        try {
            $request_type = $this->request->param('id');
            $company_name = $this->request->param('id2');
            $request_id = 0;
            $company_id = 0;
            $request_id = '';
            if ($request_type != NULL) {
                switch ($request_type) {
                    case 'sub':
                        $request_id = 3;
                        break;
                    case 'nic':
                        $request_id = 5;
                        break;
                    case 'phone':
                        $request_id = 1;
                        break;
                    case 'loc':
                        $request_id = 4;
                        break;
                    case 'imei':
                        $request_id = 2;
                        break;
                    default :
                        $request_id = 0;
                }
            }
            if ($company_name != NULL) {
                switch ($company_name) {
                    case 'm':
                        $company_id = 1;
                        break;
                    case 'w':
                        $company_id = 7;
                        break;
                    case 'u':
                        $company_id = 3;
                        break;
                    case 't':
                        $company_id = 6;
                        break;
                    case 'z':
                        $company_id = 4;
                        break;
                    default :
                        $company_id = 0;
                }
            }
            if ($request_id != 0) {
                if ($company_id != 0) {
                    $query = DB::update('user_request')->set(array('processing_index' => 4))
                            ->where('status', '=', 2)
                            ->and_where('processing_index', '=', 3)
                            ->and_where('user_request_type_id', '=', $request_id)
                            ->and_where('company_name', '=', $company_id)
                            ->execute();
                } else {
                    $query = DB::update('user_request')->set(array('processing_index' => 4))
                            ->where('status', '=', 2)
                            ->and_where('processing_index', '=', 3)
                            ->and_where('user_request_type_id', '=', $request_id)
                            ->execute();
                }
            } else {
                $query = DB::update('user_request')->set(array('processing_index' => 4))
                        ->where('status', '=', 2)
                        ->and_where('processing_index', '=', 3)
                        ->execute();
            }
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'cronmanual_flag_change',
                $e->getMessage(),
                [
                    'request_type' => $request_type ?? 'unknown',
                    'company_name' => $company_name ?? 'unknown',
                    'request_id'   => $request_id ?? 'unknown',
                    'company_id'   => $company_id ?? 'unknown'
                ],
                $e->getTraceAsString(),
                'processing_failure',
                'flag_change'
            );
            error_log("[" . date('c') . "] action_flag_change failed: " . $e->getMessage());
        }
    }

    public function action_update_person_created_at() {
        try {
            $person_count = 0;
            $person_ativity_date = 0;
            $query = "SELECT * FROM person_initiate as t1 where created_at_update_flag = 0;";
            $persons_data = DB::query(Database::SELECT, $query)->execute();
            foreach ($persons_data as $person) {
                $person_count = $person_count + 1;
                $query2 = "SELECT * FROM user_activity_timeline as t2 where t2.user_activity_type_id = 76 and t2.person_id = {$person['person_id']}";
                $time_line_data = DB::query(Database::SELECT, $query2)->execute()->current();
                $date_of_activity = !empty($time_line_data['activity_time']) ? $time_line_data['activity_time'] : '';                               
                
                if (!empty($time_line_data)) {
                    $person_ativity_date = $person_ativity_date + 1;
                    $query = DB::update('person_initiate')
                            ->set(array('created_at' => $date_of_activity , 'created_at_update_flag' => 1))
                            ->where('person_id', '=', $person['person_id'])
                            ->execute();
                }else{
                    $query = DB::update('person_initiate')
                            ->set(array('created_at' => '2017-11-22 09:10:31.0' , 'created_at_update_flag' => 1))
                            ->where('person_id', '=', $person['person_id'])
                            ->execute();
                }
            }            
            echo '<pre>';
            print_r($person_count);
            echo '******';
            print_r($person_ativity_date);
            exit;
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'cronmanual_update_person_created_at',
                $e->getMessage(),
                [
                    'person_count'         => $person_count ?? 0,
                    'person_ativity_date'  => $person_ativity_date ?? 0
                ],
                $e->getTraceAsString(),
                'processing_failure',
                'update_person_created_at'
            );
            
            echo '<pre>';
            print_r($e);
            exit;
        }
    }

}
