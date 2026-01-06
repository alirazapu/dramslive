<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Verisyssync extends Controller {

    //sync temp uploaded verisys with requests
    public function action_sync_temp_uploaded_verisys() {
        try {
            //get temp uploaded entries
            $sql = "select *
                FROM verisys_temp_files as vtf
                where attachment_status = 0
                ORDER BY vtf.upload_date  DESC ";
            $verisys_temp_files = DB::query(Database::SELECT, $sql)->execute()->as_array();
            foreach ($verisys_temp_files as $record) {
                $cnic = isset($record['cnic_number']) ? $record['cnic_number'] : '';
                $sql_query = "select * FROM user_request as ur
                                where user_request_type_id  = 8 and status = 1 and requested_value = $cnic
                                ORDER BY ur.request_id  DESC ";
                $user_request = DB::query(Database::SELECT, $sql_query)->execute()->current();
                //record row id
                $row_id = isset($record['row_id']) ? $record['row_id'] : 0;
                $image_name = isset($record['image_name']) ? $record['image_name'] : '';
                if (!empty($user_request)) {
                    $_POST = array();
                    $image_path = '/uploads/verisys_temp_images/' . $image_name;
                    //get image
                    $array_person['cnic_number'] = $user_request['requested_value']; //$_POST['cnic_number'];
                    $array_person['user_id'] = $user_request['user_id'];     // $requesting_user;

                    $requesting_user = $user_request['user_id'];     // $requesting_user;                    
                    $content = new Model_Generic();
                    $pid = $content->update_cnic_number($array_person);
                    //sleep(10);
                    //move verisys image and Delete record from temp folder
                    if (!empty($pid)) {
                        $personverysis = "";
                        $personverysis = Helpers_Profile::move_verisys_image($image_path, $pid, $row_id);
                        $_POST['person_verysis'] = $personverysis;
                        $_POST['cnic_number'] = $user_request['requested_value'];
                        $_POST['process_request_id'] = $user_request['request_id'];
                        $update = Model_Personprofile::update_personverisisrequest($_POST, $requesting_user, $pid);
                        echo $update;
                    }
                } else {
                    $query = DB::update('verisys_temp_files')
                            ->set(array('attachment_status' => 3))
                            ->where('row_id', '=', $row_id)
                            ->execute();
                }
//                echo 'here';
//                exit;
            }
        } catch (Exception $exc) {
//            $login_user_id = Auth::instance()->get_user();
//            $user_id = $login_user_id->id;
//            if (Helpers_Utilities::check_user_id_developers($user_id)) {
//                echo '<pre>';
//                print_r($ex->getMessage());
//                exit;
//            }
        }
    }//sync temp uploaded verisys with requests
    public function action_sync_temp_uploaded_familytree() {
        try {
            //get temp uploaded entries
            $sql = "select *
                FROM familytree_temp_files as vtf
                where attachment_status = 0
                ORDER BY vtf.upload_date  DESC ";
            $verisys_temp_files = DB::query(Database::SELECT, $sql)->execute()->as_array();
//              echo '<pre>';
//                print_r($verisys_temp_files);
//                exit;
                
            foreach ($verisys_temp_files as $record) {
                $cnic = isset($record['cnic_number']) ? $record['cnic_number'] : '';
                $sql_query = "select * FROM user_request as ur
                                where user_request_type_id  = 10 and (status = 2 OR status = 1 )and requested_value = $cnic
                                ORDER BY ur.request_id  DESC ";
                $user_request = DB::query(Database::SELECT, $sql_query)->execute()->current();
                
                //record row id
                $row_id = isset($record['row_id']) ? $record['row_id'] : 0;
                $image_name = isset($record['image_name']) ? $record['image_name'] : '';
                if (!empty($user_request)) {
                    $_POST = array();
                    $image_path = '/uploads/familytree_temp_images/' . $image_name;
                    //get image
                    $array_person['cnic_number'] = $user_request['requested_value']; //$_POST['cnic_number'];
                    $array_person['user_id'] = $user_request['user_id'];     // $requesting_user;

                    $requesting_user = $user_request['user_id'];     // $requesting_user;
                    $content = new Model_Generic();
                    $pid = $content->update_cnic_number($array_person);
                    //sleep(10);
                    //move verisys image and Delete record from temp folder
                    if (!empty($pid)) {
                        $personverysis = "";
                        $personverysis = Helpers_Profile::move_familytree_image($image_path, $pid, $row_id);
                        $_POST['personfamilytree'] = $personverysis;
                        $_POST['cnic_number'] = $user_request['requested_value'];
                        $_POST['process_request_id'] = $user_request['request_id'];
                        $update = Model_Personprofile::update_personfamilytreerequest($_POST, $requesting_user, $pid);
                        echo $update;
                        
                        $query = DB::update('familytree_temp_files')
                            ->set(array('attachment_status' => 1))
                            ->where('row_id', '=', $row_id)
                            ->execute();
                    }
                } else {
                    $query = DB::update('familytree_temp_files')
                            ->set(array('attachment_status' => 3))
                            ->where('row_id', '=', $row_id)
                            ->execute();
                }
//                echo 'here';
//                exit;
            }
        } catch (Exception $exc) {
//            $login_user_id = Auth::instance()->get_user();
//            $user_id = $login_user_id->id;
//            if (Helpers_Utilities::check_user_id_developers($user_id)) {
//                echo '<pre>';
//                print_r($ex->getMessage());
//                exit;
//            }
        }
    }

    //sync temp uploaded verisys with requests
    public function action_sync_temp_uploaded_travelhistory() {
        try {
            //get temp uploaded entries
            $sql = "select *
                FROM travelhistory_temp_files as vtf
                where attachment_status = 0
                ORDER BY vtf.upload_date  DESC ";
            $travelhistory_temp_files = DB::query(Database::SELECT, $sql)->execute()->as_array();
            
            
//            echo '<pre>';
//            print_r($travelhistory_temp_files);
//            exit;
//            
//            //code to delete worngly update files which are not requested
//            foreach ($travelhistory_temp_files as $record) {
//                $row_id = isset($record['row_id']) ? $record['row_id'] : 0;
//                $image_name = isset($record['image_name']) ? $record['image_name'] : '';
//                $image_path = '/uploads/travelhistory_temp_images/' . $image_name;
//             
//            
//            $query = DB::delete('travelhistory_temp_files')
//                                    ->where('row_id', '=', $row_id)
//                                    ->execute();
//            if($image_name){
//            unlink(getcwd(). $image_path);
//            }
//            }
//            echo 'tes';
//            exit;
            foreach ($travelhistory_temp_files as $record) {
                $cnic = isset($record['cnic_number']) ? $record['cnic_number'] : '';
                $sql_query = "select * FROM user_request as ur
                                where user_request_type_id  = 12  and requested_value = $cnic
                                    and processing_index != 7
                                and message_id != 0
                                ORDER BY ur.request_id  DESC ";
               
                $user_request = DB::query(Database::SELECT, $sql_query)->execute()->current();
                //record row id
                //and status = 1   this check is removed from where clause, becuase it was not allowing to read requests
              
                $row_id = isset($record['row_id']) ? $record['row_id'] : 0;
                $image_name = isset($record['image_name']) ? $record['image_name'] : '';
                if (!empty($user_request)) {
                    $_POST = array();
                    $image_path = '/uploads/travelhistory_temp_images/' . $image_name;
                    //get image
                    $array_person['cnic_number'] = $user_request['requested_value']; //$_POST['cnic_number'];
                    $array_person['user_id'] = $user_request['user_id'];     // $requesting_user;

                    $requesting_user = $user_request['user_id'];     // $requesting_user;                    
                    $content = new Model_Generic();
                    $pid = $content->update_cnic_number($array_person);
                    //sleep(10);
                    if (!empty($pid)) {
                        $_POST['cnic_number'] = $user_request['requested_value'];
                        $_POST['process_request_id'] = $user_request['request_id'];
                        //file update
                        $update = Model_Personprofile::update_travelhistory_request(getcwd(). $image_path, $_POST, $requesting_user, $image_name);
                        //delete file and record from temp files
                        
                        if ($update == 1) {
                          //  unlink($image_path);
                            $query = DB::delete('travelhistory_temp_files')
                                    ->where('row_id', '=', $row_id)
                                    ->execute();
                        } else {
                            $query = DB::update('travelhistory_temp_files')
                                    ->set(array('attachment_status' => 2))
                                    ->where('row_id', '=', $row_id)
                                    ->execute();
                        }                       
                                        
                    }
                } else {
                    $query = DB::update('travelhistory_temp_files')
                            ->set(array('attachment_status' => 3))
                            ->where('row_id', '=', $row_id)
                            ->execute();
                }
            }
        } catch (Exception $exc) {
            echo '<pre>';
            print_r($exc);
            exit;
        }
    }

}
