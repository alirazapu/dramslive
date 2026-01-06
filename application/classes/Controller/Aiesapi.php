<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Aiesapi extends Controller {
    /*
     *  a. GET – To read single or multiple records.
     *          url:  	/api/person for all and /api/person/5 for single
      b. POST – To create a new record.
     *          url:   /api/person
      c. PUT – To Update a record.
     *          url:   	/api/person/3
      d. DELETE – To delete a record.
     *           url:   	/api/person/3
     *  */

//    public function action_index() {
//
//            header("HTTP/1.0 405 Method Not Allowed");
//            $message = 'Bad Request';
//            echo json_encode($message);
//    }

    /* Cctw Api */

        public function action_one_year() {
        
            $data = new Model_Aiesapi();                   
            $districts = $data->one_year_person();
            $result_array = [];
            $counter = 0;
            foreach($districts as $district)
            {
                
                $users = $data->one_year_person_users($district->district_id);
                
                $result_array[$district->district_id]['name'] = $district->name;
                $outpout = $data->one_year_person_cat(2, $users->userid);
                $result_array[$district->district_id]['black'] = $outpout->all_cnic_numbers;
                $outpout = $data->one_year_person_cat(1, $users->userid);
                $result_array[$district->district_id]['grey'] = $outpout->all_cnic_numbers;
                $outpout = $data->one_year_person_cat(0, $users->userid);
                $result_array[$district->district_id]['white'] = $outpout->all_cnic_numbers;                
                
            }                    
            echo json_encode($result_array);            
        }
    
    public function action_cctw_person_exist() {
//        echo 'test';
        //single or multiple number using post method
        //get all post data
        //help/model data - person id
        // person url
        //$result = array['result'=>200, 'url' => ?];
        // return json_endcode($result);

        /*
         * /aiesapi/get_person_profile_link/key/uid/pid
         * request_method : GET_PERSON(s)_Phone_Number
         * Response: person profile link
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//            print_r($_REQUEST['userid']); exit;
            $request_method = $_SERVER["REQUEST_METHOD"];
            $key = $_REQUEST['key'];
            $uid = !empty($_REQUEST['userid']) ? $_REQUEST['userid'] : 0;
         //   $pid = (int) $this->request->param('id3');
            $phone_number = $_REQUEST['mobile'];
            $requestfrom = $_REQUEST['requestfrom'];
            $key = Helpers_Utilities::remove_injection($key);
            $uid = Helpers_Utilities::remove_injection($uid);
//            $pid = Helpers_Utilities::remove_injection($pid);
            $phone_number = Helpers_Utilities::remove_injection($phone_number);
            //get pid on the basis of person phone number
            $pid= Helpers_Utilities::get_person_id_with_phone_number($phone_number);
            $requestfrom = Helpers_Utilities::remove_injection($requestfrom);

            if (!empty($key) && !empty($uid) && !empty($pid)) {

                $permission = !empty($key) ? Helpers_Aiesapi::authenticate_cctw_key($key) : 0; //$permission=1 key matched
                if ($permission == 1 && !empty($uid) && $request_method == 'POST') {
//                    if (!empty($pid) && ($requestfrom == "cis")) {
                    if (!empty($pid) && ($requestfrom == "cctw")) {
                        //get person id encrypted
                        $output = Helpers_Utilities::encrypted_key($pid, "encrypt");
                        $rows = 'http://www.aies.ctdpunjab.com/persons/dashboard/?id=' . $output;
                    }
                    if (empty($error_code)) {
                        echo json_encode($rows);
                        exit;
                    }
                } else {
                    $error_code = ($permission != 1) ? -3 : -10; // authentication faild
                }
            } else {
                $error_code = -1; //empty parameters
            }
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = !empty($error_code) ? $error_code : 0;
            $message['message'] = !empty($error_code) ? Helpers_Aiesapi::error_code($error_code) : 0;
            echo json_encode($error_code);
        } catch (Exception $e) {

        }
    }


    public function action_login() {
        echo 'contact to team';        
        exit; // i 'yaser' don't know who is write this and why? 
        /*
         * /aiesapi/login/key/username/password
         * request_method : GET_LOGIN
         * Response: uid,user_name,message[ok,none],key,login_name
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            header("Content-Type: image/jpg");
            header("Content-Type: image/bmp");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

            $request_method = $_SERVER["REQUEST_METHOD"];
            $key = $this->request->param('id');
            $uname = $this->request->param('id2');
            $pwd = $this->request->param('id3');
            $key = Helpers_Utilities::remove_injection($key);
            $uname = Helpers_Utilities::remove_injection($uname);
            $pwd = Helpers_Utilities::remove_injection($pwd);

            // print_r($pwd); exit;
            $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched        
            if ($permission == 1 and $request_method == 'GET') {
                if (!empty($uname) && !empty($pwd)) {
                    $data = new Model_Aiesapi;
                    $rows = $data->authenticate_login_and_get_uid($uname, $pwd);
                    if (!empty($rows['uid'])) {
                        $rows['message'] = "ok";
                        $rows['key'] = $key; //near future key will be seperate for each user, returned after getting with user id
                    } else {
                        $rows['message'] = "none";
                    }
                    $rows['login_name'] = $uname;
                } else {
                    $error_code = -9;
                    $rows['message'] = Helpers_Aiesapi::error_code(-9);
                }
                $response = $rows;
                // header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                $error_code = ($permission != 1) ? -3 : -10;
                header("HTTP/1.0 405 Method Not Allowed");
                $message = Helpers_Aiesapi::error_code($error_code);
                echo json_encode($message);
            }
        } catch (Exception $e) {
            
        }
    }

    /* Get Person Details With CNIC Api */

    public function action_get_person_details_with_cnic() {
        /*
         * /aiesapi/get_person_details_with_cnic/key/uid/cnic
         * request_method : GET_PERSON_DETAILS
         * Response: person details
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            header("Content-Type: image/jpg");
            header("Content-Type: image/bmp");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

            $request_method = $_SERVER["REQUEST_METHOD"];
            $key = $this->request->param('id');
            $uid = !empty($this->request->param('id2')) ? $this->request->param('id2') : 0;
            $cnic = $this->request->param('id3');
            $requestfrom = $this->request->param('id4');
            $key = Helpers_Utilities::remove_injection($key);
            $uid = Helpers_Utilities::remove_injection($uid);
            $cnic = Helpers_Utilities::remove_injection($cnic);
            $requestfrom = Helpers_Utilities::remove_injection($requestfrom);

            if (!empty($key) && !empty($uid) && !empty($cnic)) {

                $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched        
                if ($permission == 1 && !empty($uid) && $request_method == 'GET') {
                    $cnic_number = !empty($cnic) ? trim($cnic) : 0;
                    if (!empty($cnic_number) && strlen($cnic_number) == 13) {
                        $data = new Model_Aiesapi;
                        $rows = $data->get_person_profile($cnic_number);

                        if (empty($rows)) {
                            $rows['cnic_number'] = $cnic_number;
                            $rows['person_id'] = '';
                            $rows['cis_person_profile_url'] = '';
                            $rows['message'] = "Empty Results";
                        }

                        if (!empty($rows->person_id) && ($requestfrom == "cis_desktop")) {
                            $access = Model_Aiesapi::cis_sensitive_person_acl($uid, $rows->person_id);
                            if (empty($access)) {
                                $t = date("s") % 2;
                                if (empty($t)) {
                                    $error_code = -12;
                                } else {
                                    $error_code = -11;
                                }
                            }

                            //get person id encrypted
                            $output = Helpers_Utilities::encrypted_key($rows->person_id, "encrypt");
                            $rows->cis_person_profile_url = 'http://www.cis.ctdpunjab.com/persons/dashboard/?id=' . $output;
                        }
                        if (empty($error_code)) {
                            echo json_encode($rows);
                            exit;
                        }
                    } else {
                        $error_code = -6; //invalid cnic number
                    }
                } else {
                    $error_code = ($permission != 1) ? -3 : -10; // authentication faild
                }
            } else {
                $error_code = -1; //empty parameters
            }
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = $error_code;
            $message['message'] = Helpers_Aiesapi::error_code($error_code);
            echo json_encode($message);
        } catch (Exception $e) {
            
        }
    }

    /* Get check profile exist or not */
public function action_get_person_for_3D_Module() {
    try {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $request_method = $_SERVER["REQUEST_METHOD"];
        if ($request_method !== 'GET') {
            header("HTTP/1.0 405 Method Not Allowed");
            echo json_encode([
                'error' => -2,
                'message' => 'Only GET requests are allowed'
            ]);
            exit;
        }

        $key = Helpers_Utilities::remove_injection($this->request->param('id'));
        $uid = Helpers_Utilities::remove_injection($this->request->param('id2', 0));
        $value = Helpers_Utilities::remove_injection($this->request->param('id3'));
        $type = Helpers_Utilities::remove_injection($this->request->param('id4'));

     
        if (!empty($key) && $key == 'Pit@k34TaldzishmuzaABC' && !empty($type) && !empty($uid) && $uid == 671 && !empty($value)) {
            
            $data = new Model_Aiesapi;
            $rows = [];
            
            if ($type == 'mobile') {
                $result = $data->get_person_mobile($value);
            } elseif ($type == 'cnic') {
                $result = $data->get_person_profile($value);
            } else {
                throw new Exception("Invalid type parameter");
            }

           
            if ($result) {
                $output = Helpers_Utilities::encrypted_key($result->person_id, "encrypt");
                $rows = [
                    'value' => $value,
                    'person_id' => $result->person_id,
                    'message' => 'http://www.aies.ctdpunjab.com/persons/dashboard/?id=' . $output
                ];
            } else {
                $rows = [
                    'cnic_number' => $value,
                    'person_id' => null,
                    'message' => 'Empty Results'
                ];
            }

            echo json_encode($rows);
            exit;
        } else {
            echo json_encode([
                'error' => -1,
                'message' => Helpers_Aiesapi::error_code(-1)
            ]);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Exception',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}


public function action_post_person_for_3D_Module() {
    try {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $request_method = $_SERVER["REQUEST_METHOD"];
        if ($request_method !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            echo json_encode([
                'error' => -2,
                'message' => 'Only POST requests are allowed'
            ]);
            exit;
        }

        // Get POST data
        $post = json_decode(file_get_contents("php://input"), true);
        if(!empty($post['key']))
            $key   = Helpers_Utilities::remove_injection($post['key']);
        if(!empty($post['uid']))
            $uid   = Helpers_Utilities::remove_injection($post['uid']);
        if(!empty($post['type']))
            $type  = Helpers_Utilities::remove_injection($post['type']);
        if(!empty($post['values']))
            $values = $post['values']; // should be an array of CNICs or mobile numbers

        if (!is_array($values)) {
            echo json_encode(['error' => -3, 'message' => 'Values must be an array']);
            exit;
        }

        if (!empty($key) && $key == 'Pit@k34TaldzishmuzaABC' && !empty($type) && !empty($uid) && $uid == 671 && !empty($values)) {
            $data = new Model_Aiesapi;
            $response = [];

            foreach ($values as $value) {
                $value = Helpers_Utilities::remove_injection($value);
                
                if ($type == 'mobile') {
                    $result = $data->get_person_mobile($value);
                } elseif ($type == 'cnic') {
                    $result = $data->get_person_profile($value);
                } else {
                    $response[] = [
                        'value' => $value,
                        'person_id' => null,
                        'message' => 'Invalid type'
                    ];
                    continue;
                }

                if ($result) {
                    $output = Helpers_Utilities::encrypted_key($result->person_id, "encrypt");
                    $response[] = [
                        'value' => $value,
                        'person_id' => $result->person_id,
                        'message' => 'http://www.aies.ctdpunjab.com/persons/dashboard/?id=' . $output
                    ];
                } else {
                    $response[] = [
                        'value' => $value,
                        'person_id' => null,
                        'message' => 'Empty Results'
                    ];
                }
            }

            echo json_encode($response);
            exit;
        } else {
            echo json_encode([
                'error' => -1,
                'message' => Helpers_Aiesapi::error_code(-1)
            ]);
            exit;
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Exception',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

    /* Get Person Profile Link in CIS */

    public function action_get_person_profile_link() {
        /*
         * /aiesapi/get_person_profile_link/key/uid/pid/cis_desktop
         * request_method : GET_PERSON_DETAILS
         * Response: person profile link
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods: GET");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

            $request_method = $_SERVER["REQUEST_METHOD"];
            $key = $this->request->param('id');
            $uid = !empty($this->request->param('id2')) ? $this->request->param('id2') : 0;
            $pid = (int) $this->request->param('id3');
            $requestfrom = $this->request->param('id4');
            $key = Helpers_Utilities::remove_injection($key);
            $uid = Helpers_Utilities::remove_injection($uid);
            $pid = Helpers_Utilities::remove_injection($pid);
            $requestfrom = Helpers_Utilities::remove_injection($requestfrom);

            if (!empty($key) && !empty($uid) && !empty($pid)) {

                $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched        
                if ($permission == 1 && !empty($uid) && $request_method == 'GET') {
                    if (!empty($pid) && ($requestfrom == "cis")) {
                        //get person id encrypted
                        $output = Helpers_Utilities::encrypted_key($pid, "encrypt");
                        $rows = 'http://www.cis.ctdpunjab.com/persons/dashboard/?id=' . $output;
                    }
                    if (empty($error_code)) {
                        echo json_encode($rows);
                        exit;
                    }
                } else {
                    $error_code = ($permission != 1) ? -3 : -10; // authentication faild
                }
            } else {
                $error_code = -1; //empty parameters
            }
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = !empty($error_code) ? $error_code : 0;
            $message['message'] = !empty($error_code) ? Helpers_Aiesapi::error_code($error_code) : 0;
            echo json_encode($message);
        } catch (Exception $e) {
            
        }
    }

    /* Create New Person Against CNIC */

    public function action_create_new_person() {
        /*
         * /aiesapi/create_new_person
         * request_method : post
         * Parameters: key, uid, cnic_number, is_foreigner, first_name, last_name, created_from
         * Response: key, uid, cnic_number, is_foreigner, first_name, last_name, created_from, person_id
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            //header("Content-Type: image/jpg");
            // header("Content-Type: image/bmp");
            header("Access-Control-Allow-Methods:POST"); //PUT_NEW_PERSON
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
            parse_str(file_get_contents("php://input"), $input);
            $request_method = $_SERVER["REQUEST_METHOD"];
            $input = Helpers_Utilities::remove_injection($input);
            $key = !empty($input['key']) ? $input['key'] : '';            
            $uid = !empty($input['uid']) ? $input['uid'] : 0;
            $cnic = !empty($input['cnic_number']) ? $input['cnic_number'] : '';
            $is_foreigner = !empty($input['is_foreigner']) ? $input['is_foreigner'] : 0;
            $first_name = !empty($input['first_name']) ? $input['first_name'] : '';
            $last_name = !empty($input['last_name']) ? $input['last_name'] : '';
            $created_from = !empty($input['created_from']) ? $input['created_from'] : '';

            if (!empty($key) && !empty($uid) && !empty($cnic) && ($created_from == 'cis_desktop' || $created_from == 'cis' || $created_from == 'suspect')) {
                
                $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched        
            if ($permission == 1 && !empty($uid) && $request_method == 'POST') {
                    $cnic_number = !empty($cnic) ? trim($cnic) : 0;
                    if (!empty($cnic_number) && strlen($cnic_number) == 13) {
                        $cnicdata['cnic_number'] = $cnic_number;
                        $cnicdata['is_foreigner'] = $is_foreigner;
                        $cnicdata['first_name'] = $first_name;
                        $cnicdata['last_name'] = $last_name;
                        $cnicdata['created_from_name'] = $created_from;
                        $cnicdata['user_id'] = $uid;
                        if ($created_from == 'cis') {
                            $cnicdata['created_from'] = 1;
                            $cnicdata['access_by'] = 0;
                        }
                        if ($created_from == 'suspect') {
                            $cnicdata['created_from'] = 0;
                            $cnicdata['access_by'] = 0;
                        }
                        if ($created_from == 'cis_desktop') {
                            $cnicdata['created_from'] = 1;
                            $cnicdata['access_by'] = 2;
                        }
                        $update_status = '';
                        
                        // get person_id: it will create person or will returen person_id of existing person
                        $content = new Model_Generic();
                        $person_id = $content->update_cnic_number($cnicdata);
                        $rows = $input;
                        if (empty($person_id)) {
                            $rows['person_id'] = '';
                            $error_code = -8;
                            $rows['error'] = $error_code;
                            $rows['message'] = Helpers_Aiesapi::error_code($error_code);
                        } else {
                            $rows['person_id'] = $person_id;
                        }
                        if (!empty($is_foreigner)) {
                            $rows['cnic_number'] = 0;
                            $rows['cnic_number_foreigner'] = $cnic_number;
                        }
                        echo json_encode($rows);
                        exit;
                    } else {
                        $error_code = -6; //invalid cnic number
                    }
                } else {
                    $error_code = ($permission != 1) ? -3 : -10; // authentication faild
                }
            } else {
                $error_code = -1; //empty parameters
            }
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = $error_code;
            $message['message'] = Helpers_Aiesapi::error_code($error_code);
            echo json_encode($message);
        } catch (Exception $e) {
            
        }
    }

    /* update cis aies ctfu permissions */

    public function action_update_cis_aies_permissions() {
        /*
         * /aiesapi/update_cis_aies_permissions
         * request_method : get
         * Parameters: key, cnic_number,posting,request_for(aies,cis,both),request_type(block,update_posting_status)
         * Response: code(error code, 0 no error),message(error message, ok)
         * key:Md2ArQmvA9dY1yd7cYVFx
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            header("Access-Control-Allow-Methods:POST");
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

            $user_data = array();
            $error_code = '';
            $post = $this->request->post();
            $request_method = $_SERVER["REQUEST_METHOD"];
            $post = Helpers_Utilities::remove_injection($post);
            //recieved user data in array
            (!empty($post['cnic_number']) && strlen($post['cnic_number']) == 13) ? ($user_data['cnic_number'] = trim($post['cnic_number'])) : ($error_code = -1);
            !empty($post['posting']) ? ($user_data['posting'] = trim($post['posting'])) : ($error_code = -1);
            !empty($post['request_type']) ? ($user_data['request_type'] = trim($post['request_type'])) : ($error_code = -1);
            !empty($post['request_for']) ? ($user_data['request_for'] = trim($post['request_for'])) : ($error_code = -1);
            !empty($post['key']) ? ($user_data['key'] = trim($post['key'])) : ($error_code = -1);
        } catch (Exception $e) {
            
        }

        try {
            if (empty($error_code) && $request_method == "POST") {
                $permission = !empty($user_data['key']) ? Helpers_Aiesapi::authenticate_update_cis_aies_permission_key(base64_decode($user_data['key'])) : 0; //$permission=1 key matched        
                //$permission = !empty(base64_decode($user_data['key']) == 'Md2ArQmvA9dY1yd7cYVFx') ? 1 : 0; //$permission=1 key matched        
                if ($permission == 1) {
                    $exist_user = Helpers_Profile::get_user_with_cnic_and_posting($user_data['cnic_number'], $user_data['posting']);
                    if ($user_data['request_type'] == "update_posting_status") {

                        if (empty($exist_user->id)) {
                            //mark all other accounts to transferred and deactive
                            Helpers_Profile::mark_transfered_user($user_data['cnic_number']);
                            $message['aies_access'] = 0;
                            $message['cis_access'] = 0;
                            $message['ctfu_access'] = 0;
                        } else {
                            $message['aies_access'] = $exist_user->is_active;
                            $message['cis_access'] = $exist_user->is_active_cis;
                            $message['ctfu_access'] =$exist_user->is_active_ctfu;
                        }
                    } elseif ($user_data['request_type'] == "block") {
                        if (!empty($exist_user->id)) {
                            //status flags
                            if ($user_data['request_for'] == 'aies') {
                                $user_data['is_active'] = 0;
                                $user_data['is_approved'] = 0;
                                $user_data['is_active_cis'] = !empty($exist_user->is_active_cis) ? $exist_user->is_active_cis : 0;
                                $user_data['is_active_ctfu'] = !empty($exist_user->is_active_ctfu) ? $exist_user->is_active_ctfu : 0;
                                //now
                                $user_data['login_sites'] = Helpers_Profile::remove_login_sites('aies', $exist_user->login_sites);
                            } elseif ($user_data['request_for'] == 'cis') {
                                $user_data['is_active_cis'] = 0;
                                $user_data['is_approved_cis'] = 0;
                                $user_data['is_active'] = !empty($exist_user->is_active) ? $exist_user->is_active : 0;
                                $user_data['is_active_ctfu'] = !empty($exist_user->is_active_ctfu) ? $exist_user->is_active_ctfu : 0;
                                $user_data['login_sites'] = Helpers_Profile::remove_login_sites('cis', $exist_user->login_sites);
                            } elseif ($user_data['request_for'] == 'ctfu') {
                                $user_data['is_active_ctfu'] = 0;
                                $user_data['is_approved_ctfu'] = 0;
                                $user_data['is_active'] = !empty($exist_user->is_active) ? $exist_user->is_active : 0;
                                $user_data['is_active_cis'] = !empty($exist_user->is_active_cis) ? $exist_user->is_active_cis : 0;
                                $user_data['login_sites'] = Helpers_Profile::remove_login_sites('ctfu', $exist_user->login_sites);
                            } else {
                                $error_code = -2;
                            }

                            //if no error exist
                            if (empty($error_code)) {
                                //mark all other accounts to transferred and deactive
                                Helpers_Profile::mark_transfered_user($user_data['cnic_number']);

                                //update current user profile
                                $user_data['exist_email'] = $exist_user->email;
                                $user_data['exist_username'] = $exist_user->username;
                                $user_data['exist_id'] = $exist_user->id;

                                //mark current user active
                                Helpers_Profile::mark_active_and_update_profile($user_data);

                                $message['aies_access'] = $user_data['is_active'];
                                $message['cis_access'] = $user_data['is_active_cis'];
                                $message['ctfu_access'] = $user_data['is_active_ctfu'];
                            }
                        } else {
                            //mark all other accounts to transferred and deactive
                            Helpers_Profile::mark_transfered_user($user_data['cnic_number']);
                            $message['aies_access'] = 0;
                            $message['cis_access'] = 0;
                            $message['ctfu_access'] = 0;
                        }
                    } else {
                        $error_code = -2;
                    }
                } else {
                    $error_code = ($permission != 1) ? -3 : -10; // authentication faild
                }
            } else {
                $error_code = -1;
            }
        } catch (ORM_Validation_Exception $e) {
            $error_code = -5;
        }
        if (empty($error_code)) {
            $message['error'] = 0;
            $message['message'] = "OK";
        } else {
            try {
                header("HTTP/1.0 405 Method Not Allowed");
                $message['error'] = $error_code;
                $message['message'] = Helpers_Aiesapi::error_code($error_code);
            } catch (Exception $e) {
                
            }
        }
        echo json_encode($message);

        //exit;
    }

    /* Create New User Account */

    public function action_create_new_user() {
        /*
         * /aiesapi/create_new_user
         * request_method : post
         * Parameters: key, uid(creating person_id), created_from(aies,cis), user_pic,cnic_number,
         * first_name,last_name,father_name,mobile_number,email,home_district(district id),designation,
         * posting (like r-10),order (posting order letter number), username,type(user_type like: ro.do)
         * ,password,password_confirm,belt,is_active(0,1)
         * Response: code(error code, 0 no error),message(error message, ok)
         * key:qjG0RTgL65lOtC5NJHZiOfi9
         */
        try {
            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
            header("Content-Type: image/jpg");
            header("Content-Type: image/jpeg");
            // header("Content-Type: image/bmp");
            header("Access-Control-Allow-Methods:POST"); //PUT_NEW_PERSON
            header("Access-Control-Max-Age: 3600");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

            $user_data = array();
            $message = array();
            $error_code = '';
            $post = $this->request->post();
            
            $post = Helpers_Utilities::remove_injection($post);
            $request_method = $_SERVER["REQUEST_METHOD"];
            $key = !empty($post['key']) ? $post['key'] : '';

            //control api flags
            $user_data['uid'] = !empty($post['uid']) ? $post['uid'] : 0;
            $user_data['created_from'] = !empty($post['created_from']) ? $post['created_from'] : '';

            //recieved user data in array
            (!empty($post['cnic_number']) && strlen($post['cnic_number']) == 13) ? ($user_data['cnic_number'] = trim($post['cnic_number'])) : ($error_code = -1);
            !empty($post['posting']) ? ($user_data['posting'] = trim($post['posting'])) : ($error_code = -1);
            !empty($post['is_active']) ? ($user_data['is_active'] = trim($post['is_active'])) : ($error_code = -1);
        } catch (Exception $e) {
            $error_code = -7;
        }
        try {
            
            if (!empty($key) && !empty($user_data['uid']) && ($user_data['created_from'] == 'cis' || $user_data['created_from'] == 'aies' || $user_data['created_from'] == 'ctfu')  && empty($error_code) && $request_method == 'POST') {
                $permission = !empty($key) ? Helpers_Aiesapi::authenticate_user_create_key(base64_decode($key)) : 0; //$permission=1 key matched        
                if ($permission == 1) {

                    $exist_user = Helpers_Profile::get_user_with_cnic_and_posting($user_data['cnic_number'], $user_data['posting']);
//                    $message['error'] = -9;
//                    $message['message'] = $exist_user->id;
//                    echo json_encode($message);
//                    exit;
                    if (!empty($exist_user->id)) {
                        //user exist with same posting
                        //status flags
                        if ($user_data['created_from'] == 'aies') {                            
                            $user_data['is_active'] = !empty($user_data['is_active']) ? 1 : 0;
                            $user_data['is_deleted'] = !empty($user_data['is_deleted']) ? 0 : 0;
                            $user_data['is_active_cis'] = !empty($exist_user->is_active_cis) ? $exist_user->is_active_cis : 0;
                            $user_data['is_active_ctfu'] = !empty($exist_user->is_active_ctfu) ? $exist_user->is_active_ctfu : 0;
                            $user_data['login_sites'] = Helpers_Profile::set_login_sites('aies', $exist_user->login_sites);
                        }elseif ($user_data['created_from'] == 'cis') {
                            $user_data['is_active_ctfu'] = !empty($exist_user->is_active_ctfu) ? $exist_user->is_active_ctfu : 0;
                            $user_data['is_active_cis'] = !empty($user_data['is_active']) ? 1 : 0;
                            $user_data['is_active'] = !empty($exist_user->is_active) ? 1 : 0;
                            $user_data['login_sites'] = Helpers_Profile::set_login_sites('cis', $exist_user->login_sites);
                            //$user_data['login_sites'] = !empty($exist_user->is_active) ? 2 : 1;
                        }elseif ($user_data['created_from'] == 'ctfu') {
                            $user_data['is_active_cis'] = !empty($exist_user->is_active_cis) ? $exist_user->is_active_cis : 0;
                            $user_data['is_active_ctfu'] = !empty($user_data['is_active']) ? 1 : 0;
                            $user_data['is_active'] = !empty($exist_user->is_active) ? 1 : 0;
                            $user_data['login_sites'] = Helpers_Profile::set_login_sites('ctfu', $exist_user->login_sites);
                            //$user_data['login_sites'] = !empty($exist_user->is_active) ? 2 : 1;
                        }   
                        else {
                            $error_code = -2;
                        }
                        
                        
                        //if no error exist
                        if (empty($error_code)) {
                            //mark all other accounts to transferred and deactive
                            Helpers_Profile::mark_transfered_user($user_data['cnic_number']);
                            //update current user profile
                            $user_data['exist_email'] = $exist_user->email;
                            $user_data['exist_username'] = $exist_user->username;
                            $user_data['exist_id'] = $exist_user->id;   
                            
                            
                            //add extra params to update profile if exist
                        !empty($post['first_name']) ? ($user_data['first_name'] = trim($post['first_name'])) : '';
                        !empty($post['last_name']) ? ($user_data['last_name'] = trim($post['last_name'])) : '';
                        !empty($post['father_name']) ? ($user_data['father_name'] = trim($post['father_name'])) : '';
                        !empty($post['mobile_number']) ? ($user_data['mobile_number'] = trim($post['mobile_number'])) : '';
                       !empty($post['home_district_id']) ? ($user_data['district_id'] = trim($post['home_district_id'])) : '';
                       !empty($post['home_region_id']) ? ($user_data['region_id'] = trim($post['home_region_id'])) : '';
                        !empty($post['job_title']) ? ($user_data['job_title'] = trim($post['job_title'])) : '';
                        !empty($post['belt']) ? ($user_data['belt'] = trim($post['belt'])) : '';
                      
                        
                            //mark current user active                                            
                            Helpers_Profile::mark_active_and_update_profile($user_data); 
                        }
                    } else {
                        //getting required parameters
                        $user_data['user_pic'] = !empty($_FILES['user_pic']) ? $_FILES['user_pic'] : '';
                        !empty($post['first_name']) ? ($user_data['first_name'] = trim($post['first_name'])) : ($error_code = -1);
                        !empty($post['last_name']) ? ($user_data['last_name'] = trim($post['last_name'])) : ($error_code = -1);
                        !empty($post['father_name']) ? ($user_data['father_name'] = trim($post['father_name'])) : ($error_code = -1);
                        !empty($post['mobile_number']) ? ($user_data['mobile_number'] = trim($post['mobile_number'])) : ($error_code = -1);
                        !empty($post['email']) ? ($user_data['email'] = trim($post['email'])) : ($error_code = -1);
                        !empty($post['home_district']) ? ($user_data['home_district'] = trim($post['home_district'])) : ($error_code = -1);
                        !empty($post['designation']) ? ($user_data['designation'] = trim($post['designation'])) : ($error_code = -1);
                        !empty($post['belt']) ? ($user_data['belt'] = trim($post['belt'])) : ($error_code = -1);
                        !empty($post['order']) ? ($user_data['order'] = trim($post['order'])) : ($error_code = -1);
                        !empty($post['username']) ? ($user_data['username'] = trim($post['username'])) : ($error_code = -1);
                        !empty($post['type']) ? ($user_data['type'] = trim($post['type'])) : ($error_code = -1);

                        //setting user_password
                        $user_data['password'] = Helpers_Utilities::get_random_code(8);
                        $user_data['password_confirm'] = $user_data['password'];
                        
                        if (empty($error_code)) {
                            //mark all other accounts to transferred and deactive
                            Helpers_Profile::mark_transfered_user($user_data['cnic_number']);

                            //query run to create new user
                            include 'user_functions/create_user_api.inc';
//                            $query_response = $this->action_run_create_user_query($user_data);
//                            $error_code = $query_response;
                            if (empty($error_code)) {
                                $message['pwd'] = base64_encode($user_data['password']);
                            }
                        }
                    }
                } else {
                    $error_code = ($permission != 1) ? -3 : -10; // authentication faild
                }
            } else {
                $error_code = -1; //empty parameters
            }
        } catch (ORM_Validation_Exception $e) {
            //echo '<pre>';
            //print_r($e);
            $error_code = -5;
        }
        if (empty($error_code)) {
            $message['error'] = 0;
            $message['message'] = "OK";
        } else {
            try {
                header("HTTP/1.0 405 Method Not Allowed");
                $message['error'] = $error_code;
                $message['message'] = Helpers_Aiesapi::error_code($error_code);
            } catch (Exception $e) {                 
                 $message['error'] = -6;
                 $message['message'] = "";
            }
        }
        echo json_encode($message);
        exit;
    }

//    //create user query
//    public function action_run_create_user_query($user_data = '') {
//        $error_code = 0;
//        if (!empty($user_data)) {
//            try {
//                //user not exist
//                $is_duplicate_username =!empty($user_data['username']) ? Helpers_Utilities::username_duplicate($user_data['username']) : '';
//                $is_duplicate_email =!empty($user_data['email']) ?  Helpers_Utilities::email_duplicate($user_data['email']) : '';
//                if (!empty($is_duplicate_username)) {
//                    $error_code = -13;
//                } elseif (!empty($is_duplicate_email)) {
//                    $error_code = -14;
//                } else {
//                    if ($user_data['created_from'] == 'aies') {
//                        $user_data['is_active'] = 1;
//                        $user_data['is_active_cis'] = 0;
//                        $user_data['login_sites'] = 0;
//                    } elseif ($user_data['created_from'] == 'cis') {
//                        $user_data['is_active'] = 0;
//                        $user_data['is_active_cis'] = 1;
//                        $user_data['login_sites'] = 1;
//                    } elseif ($user_data['created_from'] == 'both') {
//                        $user_data['is_active'] = 1;
//                        $user_data['is_active_cis'] = 1;
//                        $user_data['login_sites'] = 2;
//                    } else {
//                        $error_code = -2;
//                    }
//                    if (empty($error_code)) {
//                        if (isset($user_data['user_pic']) and $user_data['user_pic'] != "") {
//                            $user_img = Helpers_Profile::_save_image($user_data['user_pic'], "user");
//                        } else {
//                            $user_img = "";
//                        }
//
//                        $new_user = ORM::factory('User')->create_user($user_data, array(
//                            'username',
//                            'password',
//                            'email',
//                            'login_sites',
//                            'is_active',
//                            'is_active_cis',
//                        ));
//                        $new_user->add('roles', ORM::factory('Role', array('name' => $user_data['type'])));
//
//                        $user_data['user_id'] = $new_user->id;
//                        $user_data['created_by'] = $user_data['uid'];
//                        date_default_timezone_set("Asia/Karachi");
//                        $user_data['created_at'] = date("Y-m-d H:i:s");
//                        $user_data[''] = date("Y-m-d H:i:s");
//                        $user_data['file_name'] = $user_img;
//
//                        $data = new Model_Email();
//                        $data1 = $data->user_insert($user_data);
//                    }
//                }
//            } catch (ORM_Validation_Exception $e) {
//                $error_code = -5;
//            }
//        } else {
//            $error_code = -1;
//        }
//        return $error_code;
//    }
    //to update person finger prints
    public function action_update_person_finger_print() {
        /*
         * /aiesapi/update_person_finger_print/key/uid/pid
         * request_method : GET_FP_UPDATE
         * Parameters: key, uid=user_id, pid:person_id, 
         * finger_print= finger print file
         * Response: finger print types details, person_id, saved file_name
         */
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Content-Type: image/jpg");
        header("Content-Type: image/bmp");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
            $input = json_decode(file_get_contents('php://input'), true);
//echo json_encode($_FILES);
            //   exit;
            $key = $this->request->param('id');
            $uid = $this->request->param('id2');
            $person_id = $this->request->param('id3');

            $key = Helpers_Utilities::remove_injection($key);
            $uid = Helpers_Utilities::remove_injection($uid);
            $person_id = Helpers_Utilities::remove_injection($person_id);
        } catch (Exception $e) {
            
        }
        if (!empty($key) && !empty($uid) && !empty($person_id) && !empty($_FILES['finger_print']) && $method == "POST") {
            if (!empty($key)) {
                //authenticate key
                try {
                    $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched
                } catch (Exception $e) {
                    
                }
                if ($permission == 1) {
                    try {
                        //checking finger print type exist
                        $file_name = pathinfo($_FILES['finger_print']['name']);
                        $fp_type_data = !empty($file_name['filename']) ? Helpers_Aiesapi::finger_print_type(trim(str_replace("'", "", $file_name['filename']))) : '';
                        $fp_type_data['person_id'] = $person_id;
                    } catch (Exception $e) {
                        
                    }
                    //print_r($fp_type_data['fp_type_id']); exit;
                    if (!empty($fp_type_data['fp_type_id'])) {
                        $fingerprint_image = $_FILES['finger_print'];
                        $person_fingerprint_file_name = '';
                        try {

                            if (!empty($fingerprint_image)) {
                                $person_fingerprint_file_name = Model_Aiesapi::save_fingerprint_image($fp_type_data, $fingerprint_image, $uid);
                            }
                        } catch (Exception $e) {
                            $error_code = -5;
                            $fp_type_data['error'] = $error_code;
                            $fp_type_data['message'] = Helpers_Aiesapi::error_code($error_code);
                            echo json_encode($fp_type_data);
                            exit;
                        }
                        $fp_type_data['file_name'] = $person_fingerprint_file_name;
//                                if($fp_type_data['fp_type_id']%2==0){
//                                    $msg=1;
//                                }else{
//                                    $msg=0;
//                                }
//                                
                        if (empty($person_fingerprint_file_name)) {
                            $error_code = -8;
                            $fp_type_data['error'] = $error_code;
                            $fp_type_data['message'] = Helpers_Aiesapi::error_code($error_code);
                        } else {
                            $fp_type_data['error'] = 0;
                            $fp_type_data['message'] = "OK";
                        }

                        echo json_encode($fp_type_data);
                        exit;
                    } else {
                        $error_code = -4;
                    }
                } else {
                    $error_code = -3;
                }
            } else {
                $error_code = -2;
            }
        } else {
            $error_code = -1;
        }
        try {
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = $error_code;
            $message['message'] = Helpers_Aiesapi::error_code($error_code);
            echo json_encode($message);
        } catch (Exception $e) {
            
        }
    }

    //to update person documents
    public function action_update_person_document() {
        /*
         * /aiesapi/update_person_document/key/uid/pid
         * request_method : POST
         * Parameters: key, uid=user_id, pid:person_id, 
         * document_type= type of document ("person_report" , "person_income_source", "person_assets", "person_social_link")
         * document_name=one word document name
         * personfile=attached file
         * Response:  person_id, saved file_name
         */
        // required headers        
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Content-Type: image/jpg");
        header("Content-Type: image/bmp");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
            $input = json_decode(file_get_contents('php://input'), true);
            $key = $this->request->param('id');
            $uid = $this->request->param('id2');
            $person_id = $this->request->param('id3');
            $input = Helpers_Utilities::remove_injection($input);
            $key = Helpers_Utilities::remove_injection($key);
            $uid = Helpers_Utilities::remove_injection($uid);
            $person_id = Helpers_Utilities::remove_injection($person_id);
            $_POST = Helpers_Utilities::remove_injection($_POST);

            $document_type = !empty($_POST['document_type']) ? $_POST['document_type'] : '';
            $document_name = !empty($_POST['document_name']) ? $_POST['document_name'] : '';
        } catch (Exception $e) {
            
        }
        //$_FILES = !empty($_POST['personfile']) ? $_POST['personfile'] : '';
        //print_r($_FILES['personfile']);
        //print_r($_POST);
        //exit;

        if (!empty($key) && !empty($uid) && !empty($person_id) && !empty($_FILES['personfile']) && $method == "POST" && !empty($document_type) && !empty($document_name)) {
            if (!empty($key)) {
                try {
                    //authenticate key
                    $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched
                } catch (Exception $e) {
                    
                }
                if ($permission == 1) {
                    try {

                        $file_name = Helpers_Upload::upload_person_documents($_FILES, $document_type, $person_id, $document_name);
                    } catch (Exception $e) {
                        print_r($e);
                    }
                    $return = !empty($file_name) ? $file_name : '';
                    echo json_encode($return);
                    exit;
                } else {
                    $error_code = -3;
                }
            } else {
                $error_code = -2;
            }
        } else {
            $error_code = -1;
        }
        try {
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = $error_code;
            $message['message'] = Helpers_Aiesapi::error_code($error_code);
            echo json_encode($message);
        } catch (Exception $e) {
            
        }
    }

    /* Get Person fingerprints details With CNIC Api */

    public function action_get_person_fingerprints() {
        /*
         * /aiesapi/login/key/uid/cnic
         * request_method : GET_PERSON_DETAILS
         * Response: person details
         */
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Content-Type: image/jpg");
        header("Content-Type: image/bmp");
        header("Access-Control-Allow-Methods: GET");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        try {
            $request_method = $_SERVER["REQUEST_METHOD"];
            $key = $this->request->param('id');
            $uid = !empty($this->request->param('id2')) ? $this->request->param('id2') : 0;
            $cnic = $this->request->param('id3');
            $key = Helpers_Utilities::remove_injection($key);
            $uid = Helpers_Utilities::remove_injection($uid);
            $cnic = Helpers_Utilities::remove_injection($cnic);
        } catch (Exception $e) {
            
        }
        if (!empty($key) && !empty($uid) && !empty($cnic)) {
            try {
                $permission = !empty($key) ? Helpers_Aiesapi::authenticate_key($key) : 0; //$permission=1 key matched        
            } catch (Exception $e) {
                
            }
            if ($permission == 1 && !empty($uid) && $request_method == 'GET') {
                $cnic_number = !empty($cnic) ? trim($cnic) : 0;
                if (!empty($cnic_number) && strlen($cnic_number) == 13) {
                    try {
                        $data = new Model_Aiesapi;
                        $rows = $data->get_person_profile($cnic_number);
                    } catch (Exception $e) {
                        
                    }
                    if (empty($rows)) {
                        $rows['cnic_number'] = $cnic_number;
                        $rows['person_id'] = '';
                        $rows['message'] = "Empty Results";
                    }
                    echo json_encode($rows);
                    exit;
                } else {
                    $error_code = -6; //invalid cnic number
                }
            } else {
                $error_code = ($permission != 1) ? -3 : -10; // authentication faild
            }
        } else {
            $error_code = -1; //empty parameters
        }
        try {
            header("HTTP/1.0 405 Method Not Allowed");
            $message['error'] = $error_code;
            $message['message'] = Helpers_Aiesapi::error_code($error_code);
            echo json_encode($message);
        } catch (Exception $e) {
            
        }
    }
/* Api to get data of a table */

    public function action_get_table_data() {
       // echo 'contact to team';        
       // exit; // i 'yaser' don't know who is write this and why? 
        try{
        /*
         * /aiesapi/
         * request_method : Post
         * Response: tables data in json
         * key:yG4lH[LhD]ymlix7vBG4(QRf-q@lwG_:cxvAVeCrwc^8aCz*Q7k7Pn@cpcG
         */

         header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods:POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $request_method = $_SERVER["REQUEST_METHOD"];         
        try {
            if (!empty($post['key']) && !empty($post['table_name'])) {
                $permission = !empty($post['key']) ? Helpers_Aiesapi::authenticate_table_data_key(base64_decode($post['key'])) : 0; //$permission=1 key matched        
              if ($permission == 1 and $request_method == 'POST') {
                    $data = new Model_Aiesapi();
                    $post = Helpers_Utilities::remove_injection($post);
                    $response = $data->select_table_data($post);
                    echo json_encode($response);
                    exit;
                } else {
                    $error_code = -3;
                    header("HTTP/1.0 405 Method Not Allowed");
                    $message = "Authentication Fail";
                    echo json_encode($message);
                    exit;
                }
            } else {
                $error_code = -1;
                header("HTTP/1.0 405 Method Not Allowed");
                $message = "Empty Parameters";
                echo json_encode($message);
                exit;
            }
        } catch (Exception $e) {
            $error_code = -5;
            header("HTTP/1.0 405 Method Not Allowed");
            $message = "Exception";
            echo json_encode($message);
            exit;
        }
              } catch (Exception $ex){
    echo '<script> alert("An Error Have occured: Please Contact Support Team") </script>';
} 
    }
    /* Api to get data of a query */

    public function action_get_query_data() {
        //$response = 'contact to team';        
        //echo json_encode($response);
          //        exit; // i 'yaser' don't know who is write this and why? 
        try{
        /*
         * /aiesapi/
         * request_method : Post
         * Response: tables data in json
        */

         header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods:POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $post = $this->request->post();
       // $post = Helpers_Utilities::remove_injection($post);         
        $request_method = $_SERVER["REQUEST_METHOD"];
        try {
            if (!empty($post['key']) && !empty($post['query']) && !empty($post['query_type'])) {
                $permission = !empty($post['key']) ? Helpers_Aiesapi::authenticate_table_data_key(base64_decode($post['key'])) : 0; //$permission=1 key matched        
              if ($permission == 1 and $request_method == 'POST') {
                    $data = new Model_Aiesapi();                   
                    $response = $data->select_query_data($post);
                    echo json_encode($response);
                    exit;
                } else {
                    $error_code = -3;
                    header("HTTP/1.0 405 Method Not Allowed");
                    $message = "Authentication Fail";
                    echo json_encode($message);
                    exit;
                }
            } else {
                $error_code = -1;
                header("HTTP/1.0 405 Method Not Allowed");
                $message = "Empty Parameters";
                echo json_encode($message);
                exit;
            }
        } catch (Exception $e) {
            $error_code = -5;
            header("HTTP/1.0 405 Method Not Allowed");
            $message = "Exception";
            echo json_encode($message);
            exit;
        }
              } catch (Exception $ex){
//    echo '<script> alert("An Error Have occured: Please Contact Support Team") </script>';
} 
    }
    //forgot password request from smart
    public function action_forgot_password_request() {
        //echo 'contact to team';        
        //exit; // i 'yaser' don't know who is write this and why? 
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $content = new Model_Generic();
        $content_id = $content->forgot_password_request($post);
        echo json_encode($content_id);
    }

}

// End AIES API Class


    