<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    // query to update person criminal record in table
    /*
      public function action_index() {
      $DB = Database::instance();
      $sql = "select id, fir_number from person_criminal_record";
      $results = $DB->query(Database::SELECT, $sql, TRUE);

      foreach ($results as $r)
      {
      $r->fir_number = trim($r->fir_number);
      if(!is_numeric($r->fir_number))
      {
      if (strpos($r->fir_number, '/') !== false)
      {
      $value = explode('/', $r->fir_number);
      $r->fir_number = $value[sizeof($value)-2];
      $value = explode(' ', $r->fir_number);
      $r->fir_number = $value[sizeof($value)-1];

      } elseif (strpos($r->fir_number, ' ') !== false) {
      $value = explode(' ', $r->fir_number);
      $r->fir_number = $value[sizeof($value)-1];
      }
      // exit;
      }
      $query = DB::update('person_criminal_record')->set(array('fir_number' => $r->fir_number))
      ->where('id', '=', $r->id)
      ->execute();
      }
      echo 'tst';
      exit;
      }

     */
    public function action_error() {
        $this->template->content = View::factory('templates/user/access_denied');
    }

    /* Some thing went wrong */

    public function action_some_thing_went_wrong() {
        $this->template->content = View::factory('templates/user/some_thing_went_wrong');
    }

    /*
     *      search person list
     */

    public function action_access_denied() {
        $this->template->content = View::factory('templates/user/access_denied');
    }
    public function action_bulksearch_instructions() {
        $this->template->content = View::factory('templates/user/bulksearch_instructions');
    }

    public function action_mark_complete() {

        // this is mark completed status, updated in case of inpropriate email
        $_POST = Helpers_Utilities::remove_injection($_POST);
        $reference_number = $_POST['requestid'];
        $reference_number = Helpers_Utilities::encrypted_key($reference_number, 'decrypt');
        $reference_numberrst = Model_Email::email_status($reference_number, 2, 7);
        $reference_number = Helpers_Utilities::encrypted_key($reference_number, 'encrypt');
        $this->redirect('userrequest/request_status_detail/' . $reference_number);
        //HTTP::redirect('Userrequest/request_status_detail/' . $reference_number);
    }

    public function action_search_person() {
        try {
            if (Auth::instance()->logged_in()) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $login_user_id = $login_user->id;
                $access_message = 'Access denied, Contact your technical support team';
                $access_search_person = Helpers_Profile::get_user_access_permission($login_user_id, 6);
                if ((Helpers_Utilities::chek_role_access($this->role_id, 4) == 1) && $access_search_person == 1) {
                    $post = $this->request->post();
                    $post['imei'] = !empty($post['imei']) ? Helpers_Utilities::find_imei_last_digit($post['imei']) : '';
                    if (isset($_GET)) {
                        $post = array_merge($post, $_GET);
                    }
                    $post = Helpers_Utilities::remove_injection($post);
                    /* Set Session for post data carrying for the  ajax call */
                    Session::instance()->set('search_person_post', $post);
                    include 'user_functions/search_person.inc';
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                $this->redirect();
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxsearchperson() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('search_person_post', array());
                if (!empty($post)) {
                    $search_value = NULL;
                    $search_key = NULL;
                    if (!empty($post['personname'])) {
                        $search_value .= $post['personname'];
                        $search_key .= 'Person Name';
                    }
                    if (!empty($post['fathername'])) {
                        $search_value .= "-";
                        $search_value .= "{$post['fathername']}";
                        $search_key .= '-';
                        $search_key .= 'Father Name';
                    }
                    if (!empty($post['cnic'])) {
                        $search_value .= "-";
                        $search_value .= "{$post['cnic']}";
                        $search_key .= '-';
                        $search_key .= 'CNIC';
                    }
                    if (!empty($post['phonenumber'])) {
                        $search_value .= "-";
                        $search_value .= "{$post['phonenumber']}";
                        $search_key .= '-';
                        $search_key .= 'Phone Number';
                    }
                    if (!empty($post['imei'])) {
                        $search_value .= "-";
                        $search_value .= "{$post['imei']}";
                        $search_key .= '-';
                        $search_key .= 'IMEI Number';
                    }
                    if (!empty($post['imsi'])) {
                        $search_value .= "-";
                        $search_value .= "{$post['imsi']}";
                        $search_key .= '-';
                        $search_key .= 'IMSI Number';
                    }
                    if (!empty($post['organization'])) {
                        $search_value .= "-";
                        $search_value .= Helpers_Utilities::get_banned_organizations($post['organization'][0])->org_name;
                        $search_key .= '-';
                        $search_key .= 'Organization';
                    }
                    if (!empty($post['category'])) {
                        $search_value .= "-";
                        $search_value .= Helpers_Utilities::get_category_name($post['category']);
                        $search_key .= '-';
                        $search_key .= 'Category';
                    }
                    //echo '<pre>'; print_r($post); exit;
                    $login_user = Auth::instance()->get_user();
                    //print_r($login_user->id); exit;
                    if (!empty($search_key) && !empty($search_value)) {
                        $uid = $login_user->id;
                        Helpers_Profile::user_activity_log($uid, 8, $search_key, $search_value);
                    }
                }
                // print_r($_GET); exit;
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            }                    
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();
                //$rows_count = $data->search_person($post, 'true');
                $profiles_ = $data->search_person($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles =  $profiles_['result'];      
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $is_foreigner = (!empty($item['is_foreigner']) ) ? $item['is_foreigner'] : '';
                        if (!empty($is_foreigner)) {
                            $cnic = (!empty($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : '<span class="label label-default">Not Found</span>';
                        } else {
                            $cnic = (!empty($item['cnic_number']) ) ? $item['cnic_number'] : '<span class="label label-default">Not Found</span>';
                        }

                        $full_name = (!empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';

                        $person_id = ( isset($item['p_id']) ) ? $item['p_id'] : 0;

                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['p_id'], "encrypt")) . '" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $row = array(
                            $full_name,
                            $father_name,
                            $cnic,
                            // $phonenumber,
                            // $imeinumber,
                            // $address,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /*
     *      Error Page 
     */

    public function action_error_page() {
        include 'user_functions/error_page.inc';
    }

    //ajax call for data
    public function action_ajaxsearchidentity() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('search_person_identity_post', array());

                if (!empty($post)) {
                    $search_value = NULL;
                    $search_key = NULL;

                    if (!empty($post['identity_search'])) {
                        $search_value .= "-";
                        $search_value .= "{$post['identity_search']}";
                        $search_key .= '-';
                        $search_key .= 'Identity';
                    }
                    if (!empty($post['category'])) {
                        $search_value .= "-";
                        $search_value .= Helpers_Utilities::get_category_name($post['category']);
                        $search_key .= '-';
                        $search_key .= 'Category';
                    }
                    //echo '<pre>'; print_r($post); exit;
                    $login_user = Auth::instance()->get_user();
                    //print_r($login_user->id); exit;
                    Helpers_Profile::user_activity_log($login_user->id, 8, $search_key, $search_value);
                }
                // print_r($_GET); exit;
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            }                    
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_User();
                //$rows_count = $data->search_identity($post, 'true');
                $profiles_ = $data->search_identity($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $is_foreigner = (!empty(($item['is_foreigner'])) ) ? $item['is_foreigner'] : '';
                        $searched_key = (!empty(trim($item['searched_key']))) ? $item['searched_key'] : -7;
                        $identity_id = ( isset($item['identity_type_id']) ) ? $item['identity_type_id'] : -7;

                        $full_name = (!empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        if ($searched_key == -7 || $identity_id == 4) {
                            if (!empty($is_foreigner)) {
                                $searched_key = (!empty(trim($item['cnic_number_foreigner']))) ? $item['cnic_number_foreigner'] : '<span class="label label-default">Not Found</span>';
                            } else {
                                $searched_key = (!empty(trim($item['cnic_number']))) ? $item['cnic_number'] : '<span class="label label-default">Not Found</span>';
                            }
                        }
                        if ($identity_id == -7) {
                            $identity_id = 4;
                        }

                        if ($identity_id != -7) {
                            $identity_type = Helpers_Person::get_person_identity_type($identity_id);
                        }
                        $identity_number = $searched_key;
                        $person_id = ( isset($item['p_id']) ) ? $item['p_id'] : 0;

                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['p_id'], "encrypt")) . '" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $row = array(
                            $full_name,
                            $father_name,
                            $identity_type,
                            $identity_number,
                            // $phonenumber,
                            // $imeinumber,
                            // $address,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    // search person by identity
    public function action_search_identity() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            $access_search_person = Helpers_Profile::get_user_access_permission($login_user_id, 6);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 3) == 1) && $access_search_person == 1) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('search_person_identity_post', $post);
                include 'user_functions/search_identity.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_bparty_search() {
        try {

            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $role_id = Helpers_Utilities::get_user_role_id($login_user_id);
            $access_message = 'Access denied, Contact your technical support team';
            $access_bparty_search = Helpers_Profile::get_user_access_permission($login_user_id, 11);
            if ((Helpers_Utilities::chek_role_access($role_id, 2) == 1) && $access_bparty_search == 1) {
                $post = $this->request->post();
                if (!empty($post['phonenumber'])) {
                    $post = Helpers_Utilities::remove_injection($post);
                    $person = Helpers_Person::get_person_by_number($post['phonenumber']);
                } else {
                    $person = '';
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['other_phone'])) {
                    $post['phonenumber'] = $post['other_phone'];
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('search_person_post', $post);
                include 'user_functions/bparty_search.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    public function action_bparty_search_aies() {
        try {

            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $role_id = Helpers_Utilities::get_user_role_id($login_user_id);
            $access_message = 'Access denied, Contact your technical support team';
            $access_bparty_search = Helpers_Profile::get_user_access_permission($login_user_id, 11);
            if ((Helpers_Utilities::chek_role_access($role_id, 2) == 1) && $access_bparty_search == 1) {
                $post = $this->request->post();
                if (!empty($post['phonenumber'])) {
                    $post = Helpers_Utilities::remove_injection($post);
                    $person = Helpers_Person::get_person_by_number($post['phonenumber']);
                } else {
                    $person = '';
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['other_phone'])) {
                    $post['phonenumber'] = $post['other_phone'];
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('search_person_post', $post);
                $this->template->content = View::factory('templates/user/bparty_search_aies')
                         ->set('search_post', $post)
                         ->set('person_data', $person);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_multi_analysis_against_mob_numbers() {
        try {


            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $role_id = Helpers_Utilities::get_user_role_id($login_user_id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($role_id, 2) == 1) {
                $post = $this->request->post();

                if (!empty($post['phonenumber'])) {
                    $post = Helpers_Utilities::remove_injection($post);
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['other_phone'])) {
                    $post['phonenumber'] = $post['other_phone'];
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('search_multi_analysis_post', $post);
                include 'user_functions/multi_analysis_mob.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxbpartysearch() {
//        try {

            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('search_person_post', array());
//                echo '<pre>';
//                print_r($post);
//                exit();
                if (!empty($post)) {
                    $search_value = NULL;
                    $search_key = 'bParty Number';

                    if (!empty($post['phonenumber'])) {
                        $search_value = "{$post['phonenumber']}";
                    }
                    //echo '<pre>'; print_r($post); exit;
                    $login_user = Auth::instance()->get_user();
                    //print_r($login_user->id); exit;
                    $uid = $login_user->id;
                    Helpers_Profile::user_activity_log($uid, 71, $search_key, $search_value);
                }
                // print_r($_GET); exit;
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            }                    
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_User();
                //$rows_count = $data->bparty_search($post, 'true');
                if (!empty($post['phonenumber'])) {                    
                
                $profiles_ = $data->bparty_search($post, 'false');
              //  print_r(count($profiles_['result'])); exit;
                //$rows_count = $profiles_['count'];
                $rows_count = count($profiles_['result']);
                $profiles = $profiles_['result'];
                }else{
                    $rows_count = 0;
                    $profiles = array();
                }
                
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        // print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';                    
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';                    
                        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 0;
                        /* person category */
                        $cat_id = Helpers_Person::get_person_category_id($person_id);
                        $cat_name = Helpers_Utilities::get_category_name($cat_id);

                        $person_name = Helpers_Person::get_person_name($person_id);
                        $person_father_name = Helpers_Person::get_person_father_name($person_id);
                        $person_cnic = Helpers_Person::get_person_cnic($person_id);
                        /*$url_path = "http://www.ims.ctdpunjab.com/frontcat/pid?cnic=" . (int)trim($person_cnic);
                        $url = file_get_contents($url_path);
                        $wms_pid = !empty($url)? $url:'Not Found';*/
                        $wms_pid ='Not Found';
                        $login_user = Auth::instance()->get_user();

                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $row = array(
                            $person_name,
                            $person_father_name,
                            $person_cnic,
                            $wms_pid,
                            $cat_name,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
//        } catch (Exception $ex) {
////echo '<pre>';
////print_r($ex);
////exit();
//        }
    }
    //ajax call for data
    public function action_ajaxbpartysearch_aies() {
//        try {

            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('search_person_post', array());
//                echo '<pre>';
//                print_r($post);
//                exit();
                if (!empty($post)) {
                    $search_value = NULL;
                    $search_key = 'bParty Number';

                    if (!empty($post['phonenumber'])) {
                        $search_value = "{$post['phonenumber']}";
                    }
                    //echo '<pre>'; print_r($post); exit;
                    $login_user = Auth::instance()->get_user();
                    //print_r($login_user->id); exit;
                    $uid = $login_user->id;
                    Helpers_Profile::user_activity_log($uid, 71, $search_key, $search_value);
                }
                // print_r($_GET); exit;
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            }                    
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_User();
                //$rows_count = $data->bparty_search($post, 'true');
                if (!empty($post['phonenumber'])) {                    
                
                $profiles_ = $data->bparty_search($post, 'false');
              //  print_r(count($profiles_['result'])); exit;
                //$rows_count = $profiles_['count'];
                $rows_count = count($profiles_['result']);
                $profiles = $profiles_['result'];
                }else{
                    $rows_count = 0;
                    $profiles = array();
                }
                
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        // print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';                    
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';                    
                        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 0;
                        /* person category */
                        $cat_id = Helpers_Person::get_person_category_id($person_id);
                        //$cat_name = Helpers_Utilities::get_category_name($cat_id);

                        $person_name = Helpers_Person::get_person_name($person_id);
                        //$person_father_name = Helpers_Person::get_person_father_name($person_id);
                        $person_cnic = Helpers_Person::get_person_cnic($person_id);
                        $login_user = Auth::instance()->get_user();

                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $row = array(
                            $person_name,
                            //$person_father_name,
                            $person_cnic,                            
                           // $cat_name,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
//        } catch (Exception $ex) {
////echo '<pre>';
////print_r($ex);
////exit();
//        }
    }
    //ajax call for data
    public function action_ajaxbpartybulksearch() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
             //   $post = Session::instance()->get('search_person_post', array());
                $post = Session::instance()->get('bulk_search_post', array());
                $bulk_search_mobile = Session::instance()->get('bulk_search_mobile');  
//                if (!empty($post)) {
//                    $search_value = NULL;
//                    $search_key = 'bParty Number';
//
//                    if (!empty($post['phonenumber'])) {
//                        $search_value = "{$post['phonenumber']}";
//                    }
//                    //echo '<pre>'; print_r($post); exit;
//                    $login_user = Auth::instance()->get_user();
//                    //print_r($login_user->id); exit;
//                    $uid = $login_user->id;
//                    Helpers_Profile::user_activity_log($uid, 71, $search_key, $search_value);
//                }
                // print_r($_GET); exit;
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            }                    
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();
                //$rows_count = $data->bparty_search($post, 'true');
                $profiles_ = $data->bparty_bulk_search($post,$bulk_search_mobile, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        // print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';                    
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';                    
                        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 0;

                        /* person category */
                        $cat_id = Helpers_Person::get_person_category_id($person_id);
                        $cat_name = Helpers_Utilities::get_category_name($cat_id);
                        /* person tags  */
                        $tags = Helpers_Watchlist::get_person_tags_data_comma($person_id);
                        $tags= !empty($tags)? $tags:'<span class="label label-default">No Data</span>';
                        /* person project districts */
                        $link_with_projects = Helpers_Person::get_link_with_project($person_id);
                        $user_ids_array = array_column($link_with_projects, 'user_id');
                        $user_ids=implode(',' , $user_ids_array);
                        $user_posting= Helpers_Profile::get_user_place_of_posting_against_uid($user_ids);

                        if (!is_string($user_posting)) {
                            $user_ids_posting_array = array_column($user_posting, 'posted');

                            $postingplace = '';
                            foreach ($user_ids_posting_array as $item1) {


                                if (!empty($item1)) {
                                    $postingplace .= Helpers_Profile::get_user_posting($item1) . ',<br>';
                                } else {
                                    $postingplace = '<span class="label label-default">No Data</span>';
                                }
                            }
                        }else{
                            if (!empty($user_posting)) {
                                $postingplace = Helpers_Profile::get_user_posting($user_posting);
                            } else {
                                $postingplace = '<span class="label label-default">No Data</span>';
                            }
                        }


                        $person_name = Helpers_Person::get_person_name($person_id);
                        $person_father_name = Helpers_Person::get_person_father_name($person_id);
                        $person_cnic = Helpers_Person::get_person_cnic($person_id);
                        $requested_no = !empty($item['other_person_phone_number']) ? $item['other_person_phone_number'] :'';
                        
                        $login_user = Auth::instance()->get_user();

                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" target="_blank"> View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $phone_number= !empty($item['phone_number'])?$item['phone_number']:'N/A';

                        $row = array(
                            $phone_number,
                            $requested_no,
                            $person_name,
                            $person_father_name,
                            $person_cnic,
                            $cat_name,
                            $tags,
                            $postingplace,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }
    
    //ajax call for data
    public function action_ajaxbpartybulksearchsub() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
             //   $post = Session::instance()->get('search_person_post', array());
                $post = Session::instance()->get('bulk_search_post', array());
                $bulk_search_mobile = Session::instance()->get('bulk_search_mobile');  
//                if (!empty($post)) {
//                    $search_value = NULL;
//                    $search_key = 'bParty Number';
//
//                    if (!empty($post['phonenumber'])) {
//                        $search_value = "{$post['phonenumber']}";
//                    }
//                    //echo '<pre>'; print_r($post); exit;
//                    $login_user = Auth::instance()->get_user();
//                    //print_r($login_user->id); exit;
//                    $uid = $login_user->id;
//                    Helpers_Profile::user_activity_log($uid, 71, $search_key, $search_value);
//                }
                // print_r($_GET); exit;
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            }                    
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                
                if(!empty($bulk_search_mobile['mobile']))
        {    
            $bulk_search_mobile['mobile'] = array_map('trim', $bulk_search_mobile['mobile']);
            $bulk_search_mobile['mobile'] = preg_replace('/[^a-zA-Z0-9_ -]/s','',$bulk_search_mobile['mobile']);
//            $mobile_nos = implode(',', $bulk_search_mobile['mobile']);
//            $subquery = " t1.other_person_phone_number in ({$mobile_nos}) ";
        }
        
                $data = new Model_User();
                //$rows_count = $data->bparty_search($post, 'true');
                $profiles = !empty($bulk_search_mobile['mobile']) ? $bulk_search_mobile['mobile'] :[];
                
                $rows_count = sizeof($profiles);
                 $uid = Auth::instance()->get_user()->id;
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (!empty($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        
                        $phone_number= !empty($item)? $item:'';
                        $rst_resp='';
                        $other_phone=$phone_number;
                        if(!empty($phone_number)) {
                            
                            $search_type = 'msisdn';
                            $search_value = $phone_number;
                            include 'user_functions/subscriber_api_key.inc';
                            
                            if(!empty($test_array['data'])) {
                                //$rst_resp = '';
                                $j=1;
                                foreach ($test_array['data'] as $i=>$result) {
                                    if (!empty($result)) {
//                                        print_r($result); exit;
//                                        if (!empty($result['ADDRESS1']) && $result['BVS']=='VERIFIED') {
                                        $rst_resp .='<br><h5 class="text-primary"><u>Record-'.($i+$j).'</u></h5>';
                                            $rst_resp .= !empty($result['CNIC']) ? '<b> CNIC:</b> '.$result['CNIC'] : 'NA';
                                            $rst_resp .= !empty($result['FIRSTNAME']) ? '<br><b> Name: </b>'.$result['FIRSTNAME'] : 'NA';
                                            $address1 = !empty($result['ADDRESS1']) ? $result['ADDRESS1'] : '';
                                            $address2 = !empty($result['ADDRESS2']) ? $result['ADDRESS2'] : '';
                                            $address3 = !empty($result['ADDRESS3']) ? $result['ADDRESS3'] : '';
                                            $address4 = !empty($result['ADDRESS4']) ? $result['ADDRESS4'] : '';
                                            $resident_contact = !empty($result['RESCONTACT']) ? $result['RESCONTACT'] : '';
                                            $phone_office = !empty($result['PHONE_OFFICE']) ? $result['PHONE_OFFICE'] : '';
                                           $rst_resp .='<br><b>Address: </b>'. $address1 . " " . $address2 . " " . $address3 . " " . $address4 . ", Home#" . $resident_contact . ", Office#" . $phone_office;
                                            $rst_resp .= '<br><b> Biometric Verification: </b>' .(!empty($result['BVS'] || $result['BVS']=='VERIFIED') ? 'Yes' : 'No');
                                            $rst_resp .= '<br><b> IMSI: </b>' .(!empty($result['IMSI']) ? $result['IMSI'] : '');
                                            $rst_resp .= '<br><b> ACTIVATION DATE: </b>' .(!empty($result['ACTDATE']) ? $result['ACTDATE'] : '');
                                            $rst_resp .= '<br><b> MNC: </b>' .(!empty($result['NETWORK']) ? $result['NETWORK'] : '');
                                            $rst_resp .='<br>';
//                                            break;
//                                        }
                                    }
                                }

                            } 
                            else
                                $rst_resp=$other_phone.'<p style="color: red"><b>(Not Found)</b></p>';
                        }

                        $row = array(
                            $phone_number,
                            $rst_resp, 
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    public function action_bparty_active() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('bparty_active_post', $post);
                include 'user_functions/bparty_active.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
// most active lat and long
    public function action_lat_long_active() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('lat_long_active_post', $post);
                include 'user_functions/lat_long_active.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
// most active lac and cell id
    public function action_lac_cell_active() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('lac_cell_active_post', $post);
                include 'user_functions/lac_cell_active.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
// kpk accused person
    public function action_kpk_person() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('kpk_person_post', $post);
                include 'user_functions/kpk_person.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    
// kpk accused person
    public function action_afghan_data() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('afghan_person_post', $post);
                
                $this->template->content = View::factory('templates/user/afghan_person')
                         ->set('search_post', $post) ;
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }    
    
    //ajax call for data
    public function action_ajaxafghanperson() {
//        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('afghan_person_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->afghan_person($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {                         
                        $row = array(
                            !empty($item['master_acc_number'])?$item['master_acc_number']:'',
                            !empty($item['master_name'])?$item['master_name']:'',
                            !empty($item['master_pak_province'])?$item['master_pak_province']:'',
                            !empty($item['master_pak_district'])?$item['master_pak_district']:'',
                            !empty($item['master_pak_tehsil'])?$item['master_pak_tehsil']:'',
                            !empty($item['cnic'])?$item['cnic']:'',
                            !empty($item['msisdn'])?$item['msisdn']:'',
                            !empty($item['status'])?$item['status']:'',
                            !empty($item['site_lat'])?$item['site_lat']:'',
                            !empty($item['site_longitude'])?$item['site_longitude']:'',
                            !empty($item['site_address'])?$item['site_address']:'',
                            !empty($item['operator'])?$item['operator']:''
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
//        } catch (Exception $ex) {
//
//        }
    }
    
//most active imei
    public function action_imei_active() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('imei_active_post', $post);
                include 'user_functions/imei_active.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
// imei against sim
    public function action_imei_against_sim() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('imei_against_sim_post', $post);
                include 'user_functions/imei_against_sim.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    //sims against active imei
    public function action_sims_against_imei_active() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
              //  echo '<pre>'; print_r($post); exit;

                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('sims_against_imei_active_post', $post);
                include 'user_functions/sim_against_imei.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    //sims against lat long
    public function action_lat_long_search() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
              //  echo '<pre>'; print_r($post); exit;

                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('lat_long_search_post', $post);
                include 'user_functions/lat_long_search.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    //sims against lac cell id
    public function action_lac_cell_search() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
              //  echo '<pre>'; print_r($post); exit;

                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('lac_cell_search_post', $post);
                include 'user_functions/lac_cell_search.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    //imeis against sim
    public function action_most_imei_against_sim() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            if ((Helpers_Utilities::chek_role_access($this->role_id, 7) == 1)) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
              //  echo '<pre>'; print_r($post); exit;

                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('imei_against_sim_post', $post);
                include 'user_functions/imeis_against_sims.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxbpartyactive() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('bparty_active_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();
                
                //$rows_count = $data->bparty_active($post, 'true');                
                $profiles_ = $data->bparty_active($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        // print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';                    
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';                    
                        $bparty_number = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 0;
                        $bparty_number_count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $member_link = '<a href="' . URL::site('user/bparty_search/?other_phone=' . $bparty_number) . '" > View Detail </a>';


                        $row = array(
                            $bparty_number,
                            $bparty_number_count,
                            $member_link,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }
    //ajax call for data active lat long
    public function action_ajaxlatlongactive() {
        try {
//        echo phpinfo();
        //echo ini_get('max_execution_time'); exit;
            $this->auto_rednder = false;

            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('lat_long_active_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->latlong_active($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                    //  echo '<pre>';   print_r($item); exit;

                        $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 0;
                        $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 0;
                        $count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $lat_enc= Helpers_Utilities::encrypted_key($latitude, 'encrypt') ;
                        $long_enc= Helpers_Utilities::encrypted_key($longitude, 'encrypt') ;
                        $count_enc= Helpers_Utilities::encrypted_key($count, 'encrypt') ;
                        $member_link = '<a href="' . URL::site('user/lat_long_search/?lat=' . $lat_enc.'&long='.$long_enc.'&count='. $count_enc) .'" target="_blank" > View Detail </a>';



                        $row = array(
                            $latitude,
                            $longitude,
                            $count,
                            $member_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data active lac and cell id
    public function action_ajaxlaccellactive() {
        try {
//        echo phpinfo();
        //echo ini_get('max_execution_time'); exit;
            $this->auto_rednder = false;

            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('lac_cell_active_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();

                $profiles_ = $data->laccell_active($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {


                        $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 0;
                        $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 0;
                        $count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $cell_enc= Helpers_Utilities::encrypted_key($cell_id, 'encrypt') ;
                        $lac_enc= Helpers_Utilities::encrypted_key($lac_id, 'encrypt') ;
                        $count_enc= Helpers_Utilities::encrypted_key($count, 'encrypt') ;
                        $member_link = '<a href="' . URL::site('user/lac_cell_search/?cell=' . $cell_enc.'&lac='.$lac_enc.'&count='. $count_enc) .'" target="_blank" > View Detail </a>';



                        $row = array(

                            $lac_id,
                            $cell_id,
                            $count,
                            $member_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data
    public function action_ajaximeiactive() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('imei_active_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->imei_active($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
//                        echo '<pre>'; print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';
                        $imei_number = ( isset($item['imei_number']) ) ? $item['imei_number'] : 0;
                        $imei_number_count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $imei_number_id= ( isset($item['id']) ) ? $item['id'] : 0;
                        $imei_number_id_enc= Helpers_Utilities::encrypted_key($imei_number_id, 'encrypt') ;


                        $member_link = '<a href="' . URL::site('user/sims_against_imei_active/?imei_id=' . $imei_number_id_enc) . '" > View Detail </a>';


                        $row = array(
                            $imei_number,
                            $imei_number_count,
                            $member_link,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data
    public function action_ajaxkpkperson() {
//        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('imei_active_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->kpk_accused_person($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
//                        echo '<pre>'; print_r($item); exit;

                         $full_name = ( isset($item['name']) ) ? $item['name'] : 'NA';
                        $father_name = ( isset($item['father_name']) ) ? $item['father_name'] : 'NA';
                        $cnic = ( isset($item['cnic']) ) ? $item['cnic'] : 'NA';

                        $person_profile_id = Helpers_Utilities::search_pid_of_cnic($cnic);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $cnic1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $cnic . ')">' . $cnic . '</a>' : $cnic;


                        $address = ( isset($item['perm_add_place']) ) ? $item['perm_add_place'] : 0;
                        $terrorism_attack_id = ( isset($item['terrorism_attack_id']) ) ? $item['terrorism_attack_id'] : 0;
                        $motive_name= ( isset($item['motive_name']) ) ? $item['motive_name'] : 'NA';
                        $fir_no= ( isset($item['fir_no']) ) ? $item['fir_no'] : 0;
                        $fir_date= ( isset($item['fir_date']) ) ? $item['fir_date'] : 0;
                        $section_law= ( isset($item['section_law']) ) ? $item['section_law'] : "NA";
                        $ps_name= ( isset($item['ps_name']) ) ? $item['ps_name'] : "NA";
                        $notification_status= ( isset($item['notification_status']) ) ? $item['notification_status'] : "NA";
                        $case_source= ( isset($item['case_source']) ) ? $item['case_source'] : "NA";
                        $motive_detail= ( isset($item['motive_detail']) ) ? $item['motive_detail'] : "NA";
                        $occ_distt= ( isset($item['occ_distt']) ) ? $item['occ_distt'] : "NA";




                        $row = array(
                            "<b>Name:</b> ".$full_name."<br> <b>Father Name: </b>".$father_name."<br><b>CNIC: </b>".$cnic1,
                            $address,
                            "<b>Name: </b>".$motive_name."<br><b>Detail: </b>".$motive_detail,
                            "<b>FIR No.: </b>".$fir_no."<br><b>FIR Date: </b>".$fir_date."<br><b>Sections.: </b>".$section_law."<br><b>Police Station: </b>".$ps_name."<br><b>Attack ID: </b>".$terrorism_attack_id."<br><b>Case Source: </b>".$case_source,
                            "<b>Notification Status: </b>".$notification_status."<br><b>Ocassion District: </b>".$occ_distt,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
//        } catch (Exception $ex) {
//
//        }
    }
    //ajax call for data imeis against sim
    public function action_ajaximeiagainstsim() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('imei_against_sim_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->imei_active_against_sim($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
//                        echo '<pre>'; print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 0;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;


                        $imei_number_count = ( isset($item['count']) ) ? $item['count'] : 0;
                        $sim_device_id= ( isset($item['device_id']) ) ? $item['device_id'] : 0;
                        $phone_number_enc= Helpers_Utilities::encrypted_key($phone_number, 'encrypt') ;


                        $member_link = '<a href="' . URL::site('user/most_imei_against_sim/?ph_id=' . $phone_number_enc) . '" > View Detail </a>';


                        $row = array(
                            $other_phone1.' '.$person_profile_link ,
                            $imei_number_count,
                            $member_link,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data
    public function action_ajaxsimsagainstimei() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('sims_against_imei_active_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit();
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->sim_against_imei($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                      //  echo '<pre>'; print_r($item); exit;
                        /* Concate name full name */
                        // $full_name = ( !empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        // $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';
                        // $cnic = ( !empty($item['cnic_number']) ) ?  $item['cnic_number'] : '<span class="label label-default">Not Found</span>';
                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 0;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;

                        $status = ( isset($item['is_active']) ) ? $item['is_active'] : 0;
                        if($status==1){
                            $status='Active';
                        }
                        else{
                            $status='In-Active';
                        }
                        $first_use= ( isset($item['first_use']) ) ? $item['first_use'] : 0;
                        $last_use= ( isset($item['last_use']) ) ? $item['last_use'] : 0;
//                        $imei_number_id_enc= Helpers_Utilities::encrypted_key($imei_number_id, 'encrypt') ;


//                        $member_link = '<a href="' . URL::site('user/sims_against_imei_active/?imei_id=' . $imei_number_id_enc) . '" > View Detail </a>';


                        $row = array(
                            $other_phone1.' '.$person_profile_link ,
                            $status,
                           '<b>First Use: </b>'. $first_use.'<br> <b> Last Use: </b>'.$last_use,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data
    public function action_ajaxlonglatsearch() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('lat_long_search_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit();
                $data = new Model_User();
                $profiles_ = $data->lat_long_search_sims($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
//                        echo '<pre>'; print_r($item); exit;

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 0;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;

                        $imei_number = ( isset($item['imei_number']) ) ? $item['imei_number'] : 0;
                        $imsi_number= ( isset($item['imsi_number']) ) ? $item['imsi_number'] : 0;
                        $address= ( isset($item['address']) ) ? $item['address'] : 0;

                        $row = array(
                            $other_phone1.' '.$person_profile_link ,
                            '<b> IMEI:</b>b '.$imei_number.'<br><b>IMSI:  </b>'.$imsi_number,
                            $address,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data
    public function action_ajaxlaccellsearch() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('lac_cell_search_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit();
                $data = new Model_User();
                $profiles_ = $data->lac_cell_search_sims($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
//                        echo '<pre>'; print_r($item); exit;

                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 0;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;

                        $address= ( isset($item['address']) ) ? $item['address'] : 0;

                        $row = array(
                            $other_phone1.' '.$person_profile_link ,
                            $address,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }
    //ajax call for data
    public function action_ajaximeisagainstsim() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('imei_against_sim_post', array());

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
//                echo '<pre>';
//                print_r($post);
//                exit();
                $data = new Model_User();

                //$rows_count = $data->bparty_active($post, 'true');
                $profiles_ = $data->imeis_against_most_active_sim($post, 'false');
                $rows_count = $profiles_['count'];
                $profiles = $profiles_['result'];
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        // echo '<pre>';
                        // print_r($item);
                        // exit;

                        $imei_number = ( isset($item['imei_number']) ) ? $item['imei_number'] : 0;
                        $first_use= ( isset($item['in_use_since']) ) ? $item['in_use_since'] : 0;
                        $last_use= ( isset($item['last_interaction_at']) ) ? $item['last_interaction_at'] : 0;

                        $row = array(
                            $imei_number ,

                            $first_use,
                            $last_use,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {

        }
    }

    /* User Logout */

    public function action_logout() {
        try {
            //Auth::instance()->logout();	
            $user_obj = Auth::instance()->get_user();
            Helpers_Profile::is_login($user_obj->id, False);
            Auth::instance()->logout(TRUE, TRUE);
        } catch (Exception $e) {
            
        }
        $this->redirect();
    }

    /*
     *  User Audit Report (audit_report)
     */

    public function action_audit_report() {
        try {
            include 'user_functions/audit_report.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User Audit Request Detail (audit_request_detail)
     */

    public function action_audit_request_detail() {
        try {
            include 'user_functions/audit_request_detail.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User Manual Data Upload (data_upload)
     */

    public function action_upload_against_msisdn() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 9) == 1) {
                if (!empty($_POST)) {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $data = $_POST;
                } else {
                    $data = '';
                }
                include 'user_functions/up_against_msisdn.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  User Manual Data Upload (data_upload)
     */

    public function action_upload_against_cnic() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 10) == 1) {
                if (!empty($_POST)) {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $data = $_POST;
                } else {
                    $data = '';
                }
                include 'user_functions/up_against_cnic.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    /*
     *  User Manual Data Upload (data_upload)
     */

    public function action_upload_against_imei() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 11) == 1) {
                if (!empty($_POST)) {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $data = $_POST;
                } else {
                    $data = '';
                }
                include 'user_functions/up_against_imei.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  User uploaded cdrs (uploaded_cdrs)
     */

    public function action_uploaded_cdrs() {
        try {

            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 12) == 1) {
                /* Posted Data */
                $post = $this->request->post();
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            }             
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['message']) && $post['message'] == 1) {
                    $message = 'Congratulation! Request Sent successfully';
                }
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('request_status_post', $post);
                /* File Included */
                include 'user_functions/uploaded_cdrs.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     * User manual data upload guide
     */

    public function action_cdr_upload_format() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if (Helpers_Utilities::chek_role_access($this->role_id, 13) == 1) {
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('panel_log', $post);
                /* File Included */
                include 'user_functions/user_dataupload_manual.inc';
            } else {

                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxuseruplloadedcdrs() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('user_request_status_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Userrequest;
                $rows_count = $data->user_uploaded_cdrs($post, 'true');
                $profiles = $data->user_uploaded_cdrs($post, 'false');


                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $record_id = ( isset($item['id']) ) ? $item['id'] : '';
                        $file_name = ( isset($item['file']) ) ? $item['file'] : 'na';
                        $file_type = ( isset($item['type']) ) ? $item['type'] : '';
                        $file_size = ( isset($item['size']) ) ? $item['size'] : 0;
                        $no_of_records = ( isset($item['no_of_record']) ) ? $item['no_of_record'] : '';
                        $data_from = ( isset($item['data_from_date']) ) ? $item['data_from_date'] : 'NA';
                        $data_to = ( isset($item['data_to_date']) ) ? $item['data_to_date'] : 'NA';
                        $file_info = $file_name . "<br /> <b>Size:</b> " . $file_size . "kb" . "<br/> <b>No of Records:</b> " . $no_of_records . " <br /> <b>Data From:</b> " . $data_from . " <b>To</b> " . $data_to;
                        $upload_status = ( isset($item['upload_status']) ) ? $item['upload_status'] : 0;
                        $upload_status_name_type = ($error_type = 'NA') ? Helpers_Utilities::get_cdr_upload_status($upload_status) : 'NA';
                        switch ($upload_status) {
                            case 0:
                                $upload_status_flag = "info";
                                break;
                            case 1:
                                $upload_status_flag = "primary";
                                break;
                            case 2:
                                $upload_status_flag = "warning";
                                break;
                            case 3:
                                $upload_status_flag = "success";
                                break;
                            case 4:
                                $upload_status_flag = "danger";
                                break;
                            default :
                                $upload_status_flag = "secondary";
                        }
                        $upload_status_name = '<span class="badge badge-pill badge-' . $upload_status_flag . '">' . $upload_status_name_type . '</span>';
                        $request_id = ( isset($item['request_id']) ) ? $item['request_id'] : 0;
                        $mnc = ( isset($item['company_name']) ) ? $item['company_name'] : 0;
                        $company_name = Helpers_Utilities::get_companies_data($mnc);
                        $company_name = !empty($company_name) ? $company_name->company_name : 'NA';
                        // $company_name=$mnc;
                        $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 0;
                        $imei = ( isset($item['imei']) ) ? $item['imei'] : 0;
                        if (!empty($phone_number)) {
                            $request_value = $phone_number;
                        } else {
                            $request_value = $imei;
                        }
                        $created_on = ( isset($item['created_on']) ) ? $item['created_on'] : 0;
                        $is_manual = ( isset($item['is_manual']) ) ? $item['is_manual'] : 0;
                        $created_by = ( isset($item['created_by']) ) ? $item['created_by'] : 0;
                        $error_type = ( isset($item['error_type']) ) ? $item['error_type'] : 'NA';
                        $_get_error_type_name = ($error_type != 'NA') ? Helpers_Utilities::get_cdr_upload_error_type($error_type) : 'NA';
                        switch ($error_type) {
                            case 0:
                                $upload_error_flag = "success";
                                break;
                            default :
                                $upload_error_flag = "danger";
                        }
                        $error_type_name = '<span class="badge badge-pill badge-' . $upload_error_flag . '">' . $_get_error_type_name . '</span>';
                        $request_type = ( isset($item['request_type']) ) ? $item['request_type'] : 0;
                        $request_type_name = !empty($request_type) ? Helpers_Utilities::get_request_type_name($request_type) : '';
                        $request_type_name = $request_type_name . "<br/><b>" . $request_value . "<b/>";
                        $user_name = (!empty($created_by) ) ? Helpers_Utilities::get_user_name($created_by) : 'Unknown';
                        if ($request_type == 2 && $upload_status == 1) {
                            $recievedfilepath = 0;
                            $recievedbody = 0;
                            $recievedfilepath = "'" . trim($recievedfilepath) . "'";
                            $recievedbody = "'" . trim($recievedbody) . "'";
                            $request_value = "'" . trim($request_value) . "'";
                            $request_id = "'" . trim($request_id) . "'";
                            $full_parse = '<span class="badge  badge-success"><a class="custom-cursor" onclick="fullparseimeicdr(' . $request_id . ',' . $recievedfilepath . ',' . $recievedbody . ',' . $request_value . ')" style="color: white">Parse</a></span>';
                        } else {
                            $full_parse = '';
                        }
                        if (empty($error_type) || $error_type == 5) {
                            if (!empty($file_name) && (trim($file_name) != 'na')) {
                                if ($is_manual == 2) {
                                    //$member_name_link = '<span class="fa fa-download badge  badge-primary"><a href="' . URL::base().'/personprofile/download?fid='.Helpers_Utilities::encrypted_key($record_id, 'encrypt').'&file='.$file_name . '" style="color: white"> Download</a></span>';
                                    $member_name_link = ' <form class="" name="download" action="' . URL::site() . 'personprofile/download' . '" id="downloadfile" method="post" enctype="multipart/form-data">';
                                    $member_name_link .= ' <input name="file" value="' . $file_name . '" type="hidden">';
                                    $member_name_link .= '<input name="fid" value="' . Helpers_Utilities::encrypted_key($record_id, 'encrypt') . '" type="hidden">';
                                    $member_name_link .= '<input style="margin-top: 5px; display:block;" type="submit" value="Download" class="btn fa fa-download  badge badge-primary" />';
                                    $member_name_link .= '</form>';
                                } else {
                                    //$member_name_link = '<span class="fa fa-download badge  badge-primary"><a href="' . URL::base().'/personprofile/download?fid='.Helpers_Utilities::encrypted_key($record_id, 'encrypt').'&file='.$file_name . '" style="color: white"> Download</a></span>';
                                    $member_name_link = ' <form class="" name="download" action="' . URL::site() . 'personprofile/download' . '" id="downloadfile" method="post" enctype="multipart/form-data">';
                                    $member_name_link .= ' <input name="file" value="' . $file_name . '" type="hidden">';
                                    $member_name_link .= '<input name="fid" value="' . Helpers_Utilities::encrypted_key($record_id, 'encrypt') . '" type="hidden">';
                                    $member_name_link .= '<input style="margin-top: 5px; display:block;" type="submit" value="Download" class="btn fa fa-download  badge badge-primary" />';
                                    $member_name_link .= '</form>';
                                }
                            } else {
                                $member_name_link = '';
                            }
                            $member_name_link = $member_name_link . "<br/>" . $full_parse;
                        } else {
                            $member_name_link = '<span class="fa fa-remove badge  badge-danger"><a class=" custom-cursor" onclick="deletecdr(' . $record_id . ',' . $request_value . ')" style="color: white"> Delete</a></span>';
                            //$member_name_link .= '<span class="fa fa-download badge  badge-primary"><a href="' . URL::base().'/personprofile/download?fid='.Helpers_Utilities::encrypted_key($record_id, 'encrypt').'&file='.$file_name . '" style="color: white"> Download</a></span>';
                            $member_name_link = ' <form class="" name="download" action="' . URL::site() . 'personprofile/download' . '" id="downloadfile" method="post" enctype="multipart/form-data">';
                            $member_name_link .= ' <input name="file" value="' . $file_name . '" type="hidden">';
                            $member_name_link .= '<input name="fid" value="' . Helpers_Utilities::encrypted_key($record_id, 'encrypt') . '" type="hidden">';
                            $member_name_link .= '<input style="margin-top: 5px; display:block;" type="submit" value="Download" class="btn fa fa-download  badge badge-primary" />';
                            $member_name_link .= '</form>';
                        }

                        $row = array(
                            $user_name,
//                      $user_role_name,                        
                            $request_type_name,
                            $file_info,
                            $company_name,
                            $created_on,
                            $upload_status_name,
                            $error_type_name,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /* Delete record from error cdr */

    public function action_deletecdr_with_error() {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_id = $user_obj->id;
                $record_id = (int) $this->request->param('id');
                $record_id = Helpers_Utilities::remove_injection($record_id);
                // print_r($blocked_id); exit;
                $user = New Model_Userrequest();
                $result = $user->deletecdr_with_error($record_id, $login_user_id);
                //print_r($result); exit;
                echo 1;
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  User Manual Data Upload Post (data_upload_post)
     */

    public function action_registrationpost() {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            $this->redirect('user/data_upload?message=1&tab=2');
        } else {
            $this->redirect();
        }
    }

    //manual subsriber upload
    public function action_data_upload_post() {
        if (Auth::instance()->logged_in()) {

            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && (!empty($_POST))) {
                //try {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $_POST['user_id'] = $user_obj->id;
                    $content = new Model_Generic();
                    $content_id = $content->ManualSubInfoinsert($_POST);
//                } catch (Exception $e) {
//                    
//                }
//                echo $content_id;
//                    exit;
                if (!empty($content_id) && $content_id == -7) {
                    $this->redirect('userrequest/request_status');
                } else {
                    $this->redirect('user/upload_against_msisdn?message=1&tab=2');
                }
            } else {
                $this->redirect();
            }
        }
    }

    //manual location upload
    public function action_location_upload_post() {
        if (Auth::instance()->logged_in()) {

            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && (!empty($_POST))) {
                try {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $_POST['user_id'] = $user_obj->id;
                    $_POST['person_id'] = Helpers_Utilities::encrypted_key($_POST['person_id'], 'decrypt');
                    $content = new Model_Generic();
                    $content_id = $content->ManualLocationinsert($_POST);
                } catch (Exception $e) {
                    
                }
                if (!empty($content_id) && $content_id == -7) {
                    $this->redirect('userrequest/request_status');
                } else {
                    $this->redirect('user/upload_against_msisdn?message=1&tab=2');
                }
            } else {
                $this->redirect();
            }
        }
    }

    //manual cnic upload
    public function action_cnic_upload_post() {
        if (Auth::instance()->logged_in()) {

            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && (!empty($_POST))) {
                try {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $_POST['user_id'] = $user_obj->id;
                    $content = new Model_Generic();
                    $content_id = $content->Manualcnicsimsinsert($_POST);
                } catch (Exception $e) {
                    echo '<pre>';
                   // print_r($e);
                    exit;
                }
                if (!empty($content_id) && ($content_id == -7 || $content_id == 1)) {
                    $this->redirect('userrequest/request_status');
                } else {
                    $this->redirect('user/upload_against_cnic?message=1&tab=2&pid=' . Helpers_Utilities::encrypted_key($content_id, "encrypt"));
                }
            } else {
                $this->redirect();
            }
        }
    }

    //manual imei upload
    public function action_imei_upload_post() {
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && (!empty($_POST))) {
                try {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $_POST['user_id'] = $user_obj->id;
                    $content = new Model_Generic();
                    $content_id = $content->Manualimeisimsinsert($_POST);
                } catch (Exception $e) {
                    
                }
                if (!empty($content_id) && $content_id == -7) {
                    print json_encode(-7);
                    exit();
                } else {
                    $this->redirect('user/upload_against_imei?message=1&tab=2');
                }
            } else {
                $this->redirect();
            }
        }
    }

    //manual imei upload
    public function action_ajaxupdatedevicename() {
        if (Auth::instance()->logged_in()) {

            $user_obj = Auth::instance()->get_user();
            if ((isset($_POST)) && (!empty($_POST) )) {
                try {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $_POST['user_id'] = $user_obj->id;
                    $content = new Model_Generic();
                    $content_id = $content->Manualphonenameupdate($_POST);
                } catch (Exception $e) {
                    
                }
                $this->redirect('user/upload_against_imei?message=1&tab=2');
            } else {
                $this->redirect();
            }
        }
    }

    //user detail for imei upload
    public function action_ajaxuserdetail() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $id = (int) $this->request->param('id');
            $id = Helpers_Utilities::remove_injection($id);
            $permission = Helpers_Utilities::get_user_permission($id);
        } catch (Exception $e) {
            
        }
        $login_user_id = $login_user->id;
        if (($permission == 1 || $permission == 2)) {
            try {
                $contents = Helpers_Profile::get_users();
                foreach ($contents as $content) {
                    print_r($content);
                }
            } catch (Exception $e) {
                
            }
        } else {
            $this->redirect('userrequest/request_status');
        }
    }

    /*
     *  User No Of LogIN (no_of_login)
     */

    public function action_no_of_login() {
        try {
            include 'user_functions/no_of_login.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User No Of Record Search (no_record_search)
     */

    public function action_no_record_search() {
        try {
            include 'user_functions/no_record_search.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User No Of Request Send (no_request_send)
     */

    public function action_no_request_send() {
        try {
            include 'user_functions/no_request_send.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User Performance Report (performance_report)
     */

    public function action_performance_report() {
        try {
            include 'user_functions/performance_report.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User Search Person (search_person)
     */

    // public function action_search_person() {
    // include 'user_functions/search_person.inc';
    // }
    /*
     *  User Top Search Person (top_search_person)
     */

//    public function action_top_search_person() {
//        include 'user_functions/top_search_person.inc';
//    }
    /*
     *  User User's Favourite  Person (user_favourite_person)
     */

    public function action_user_favourite_person() {
        try {
            include 'user_functions/user_favourite_person.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User User Profile (user_profile)
     */

    public function action_user_profile() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $id = $this->request->param('id');
            $id = Helpers_Utilities::remove_injection($id);
            if (!empty($id)) {
                $id = (int) Helpers_Utilities::encrypted_key($id, "decrypt");
                if ($permission == 4 && $id != $login_user->id) {
                    $this->template->content = View::factory('templates/user/access_denied');
                } else {
                    $data = Helpers_Profile::get_user_perofile($id);
                    if (!empty($data)) {
                        $user_log_info = Helpers_Profile::get_user_log_info($id);
                        include 'user_functions/user_profile.inc';
                    } else {
                        $this->template->content = View::factory('templates/user/access_denied');
                    }
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /* Irfan code start */
    /*
     *  User User Profile (change password)
     */

    public function action_changepassword() {
        //$id = (int) $this->request->param('id');
        $DB = Database::instance();
        try {
            $login_user = Auth::instance()->get_user();
            $login_user_id = $login_user->id;
            $data = Helpers_Profile::get_user_perofile($login_user_id);
            //$user_log_info = Helpers_Profile::get_user_log_info($id);
            include 'user_functions/user_changepassword.inc';
        } catch (Exception $e) {
            
        }
    }

    /* current password match */

    public function action_current_password() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            //print_r($post); exit;
            $this->auto_render = FALSE;
            $password_encoded = Auth::instance()->hash_password($post["oldpassword"]);
            $data = Helpers_Utilities::current_password($password_encoded);
            if ($data > 0) {
                echo json_encode(TRUE);
            } else {
                echo json_encode(FALSE);
            }
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    public function action_update_password() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            //print_r($post); exit;
            if (Auth::instance()->logged_in()) {
                $user = Auth::instance()->get_user();
                $login_user_id = $user->id;
                $post["password"] = Auth::instance()->hash_password($post["password"]);
                $updated = Model_Userreport::update_password(array('password' => $post["password"], 'is_forget_reset' => 0, 'is_password_changed' => 1), $login_user_id);
                echo json_encode($updated);
                $this->redirect('user/logout');
            } else {
                $this->redirect();
            }
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    /* Irfan code end */


    /*
     *  User User Registration (user_registration)
     */
    
      public function action_user_registration() {
      try{
      $DB = Database::instance();
      $login_user = Auth::instance()->get_user();
      $permission = Helpers_Utilities::get_user_permission($login_user->id);
      $login_user_id = $login_user->id;
      $access_user_registration = Helpers_Profile::get_user_access_permission($login_user_id, 1);
      $access_message = 'Access denied, Contact your technical support team';
      if (($permission == 1 || $permission == 2) && $access_user_registration == 1) {
      include 'user_functions/user_registration.inc';
      } else {
      $this->redirect('user/access_denied');
      }
      } catch (Exception $ex){
      $this->template->content = View::factory('templates/user/exception_error_page')
      ->bind('exception', $ex);
      }
      }
     
    /*
     *  User User Favourite Agent (users_favourite_agent)
     */

    public function action_users_favourite_agent() {
        try {
            include 'user_functions/users_favourite_agent.inc';
        } catch (Exception $e) {
            
        }
    }

    /*
     *  User User Favourite User (users_favourite_user)
     */

    public function action_users_favourite_user() {
        try {
            include 'user_functions/users_favourite_user.inc';
        } catch (Exception $e) {
            
        }
    }

    /* User Created */

    public function action_create() {
        $user = Auth::instance()->get_user();
        if (!$user) {
            $this->redirect();
        }
        if (HTTP_Request::POST == $this->request->method()) {
            try {
                // print_r($_POST); exit;                            

                if (isset($_FILES['user_pic']) and $_FILES['user_pic'] != "") {
                    $user_img = Helpers_Profile::_save_image($_FILES['user_pic'], "user");
                } else {
                    $user_img = "";
                }
                $post_data = $this->request->post();
                $post_data = Helpers_Utilities::remove_injection($post_data);
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $post_data['is_active'] = 1;
                $post_data['is_active_cis'] = 0;
                $post_data['login_sites'] = 0;
                $new_user = ORM::factory('user')->create_user($post_data, array(
                    'username',
                    'password',
                    'email',
                    'login_sites',
                    'is_active',
                    'is_active_cis',
                ));
                $type_name = isset($_POST['type']) ? $_POST['type'] : '';
                $new_user->add('roles', ORM::factory('Role', array('name' => $type_name)));

                $_POST['user_id'] = $new_user->id;
                $_POST['created_by'] = $user->id;
                date_default_timezone_set("Asia/Karachi");
                $_POST['created_at'] = date("Y-m-d H:i:s");
                $_POST[''] = date("Y-m-d H:i:s");
                $_POST['file_name'] = $user_img;

                $data = new Model_Email();
                $data1 = $data->user_insert($_POST);


                $_POST = array();
                $message = "You have added user '{$new_user->username}' to the databse.";
                // $member_name_link = '<a href="' . URL::site('persons/call_summary_detail/?partya=' . $item['phone_number'] . '&partyb=' . $item['other_person_phone_number']) . '" > View Detail </a>';
                $this->redirect('userreports/access_control_list/?newuser=' . $new_user->id . '&flag=1'); // . $login_user->id .'?accessmessage=' .$access_message);
            } catch (ORM_Validation_Exception $e) {
                $message = "There were errors. Please see form below.";
                $errors = $e->errors('models');
            }
        }

        $this->template->content = View::factory('templates/user/user_registration')
                ->bind('errors', $errors)
                ->bind('message', $message);
    }

    /* protected function _save_image($image, $type = "user") {
      if (
      !Upload::valid($image) OR
      !Upload::not_empty($image) OR
      !Upload::type($image, array('jpg', 'jpeg', 'png', 'gif'))) {
      return FALSE;
      }

      if ($type == "user") {
      $directory = 'dist/uploads/user/profile_images/';
      } else if ($type == "person") {
      $directory = 'dist/uploads/person/profile_images/';
      }

      //genrate a random file name that is hexa decimal and make it a jpg file
      $date = date("YmdHis", time());
      $filename = $date . ".jpg";


      if ($file = Upload::save($image, NULL, $directory)) {

      $img = Image::factory($file);
      $img->save($directory . $filename);

      if ($type == "user") {

      Helpers_Profile::_resize_images($filename, $type);
      }
      unlink($file);

      return trim($filename);
      }

      return FALSE;
      } */
    /*
     * Inactive User
     */

    //active & inactive 
    public function action_useractive() {
        try {
            $this->auto_render = FALSE;
            if (Auth::instance()->logged_in()) {
                $id = (int) $this->request->param('id');
                $id = Helpers_Utilities::remove_injection($id);
                if (isset($id) && ($id != NULL)) {
                    $deleted = New Model_Userreport;
                    $deleted->useractive($id);
                    print json_encode('success');
                } else {
                    echo "Key not received";
                }
            } else {
                //print ("session_expired");
                $this->redirect('/admin/?session_expired=1');
            }
            exit();
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    //password reset request cancel
    public function action_request_cancel() {
        try {
            $this->auto_render = FALSE;
            if (Auth::instance()->logged_in()) {
                $id = (int) $this->request->param('id');
                $id = Helpers_Utilities::remove_injection($id);
                //print_r($id); exit;
                if (isset($id) && ($id != NULL)) {
                    Helpers_Utilities::password_reset_request_cancel($id);
                    print json_encode('success');
                } else {
                    echo "Key not received";
                }
            } else {
                //print ("session_expired");
                $this->redirect('/admin/?session_expired=1');
            }
            exit();
        } catch (Exception $ex) {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    //Block user 
    public function action_userblock() {
        try {
            $this->auto_render = FALSE;
            if (Auth::instance()->logged_in()) {
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                $id = Helpers_Utilities::encrypted_key($post['id'], 'decrypt');

                if (isset($id) && ($id != NULL)) {
                    $deleted = New Model_Userreport;
                    $deleted->userblock($id);
                    $blockdata['id'] = !empty($id) ? $id : 0;
                    $blockdata['reason'] = !empty($post['reason']) ? $post['reason'] : '';
                    $block = !empty($blockdata['id']) ? Helpers_Profile::add_user_block_reason($blockdata) : '';
                    print json_encode('success');
                } else {
                    echo -2;
                }
            } else {
                //print ("session_expired");
                $this->redirect('/admin/?session_expired=1');
            }
            //exit();
        } catch (Exception $ex) {
            echo -3;
        }
    }

    //Un-Block user 
    public function action_userUnBlock() {
        try {
            $this->auto_render = FALSE;
            if (Auth::instance()->logged_in()) {
                $user_id_encrypted = $this->request->param('id');
                $user_id_encrypted = Helpers_Utilities::remove_injection($user_id_encrypted);
                $id = Helpers_Utilities::encrypted_key($user_id_encrypted, 'decrypt');
                if (isset($id) && ($id != NULL)) {

                    $deleted = New Model_Userreport;
                    $deleted->userUnBlock($id);
                    print json_encode('success');
                } else {
                    echo -2;
                }
            } else {
                //print ("session_expired");
                $this->redirect('/admin/?session_expired=1');
            }
            // exit();
        } catch (Exception $ex) {
            echo json_encode(-3);
        }
    }

    //User Approval 
    public function action_userApprove() {
        try {
            $this->auto_render = FALSE;
            if (Auth::instance()->logged_in()) {
                $user_id_encrypted = $this->request->param('id');
                $user_id_encrypted = Helpers_Utilities::remove_injection($user_id_encrypted);
                $id = Helpers_Utilities::encrypted_key($user_id_encrypted, 'decrypt');
                if (isset($id) && ($id != NULL)) {
                    $deleted = New Model_Userreport;
                    $deleted->userApprove($id);
                    print json_encode('success');
                } else {
                    echo -2;
                }
            } else {
                //print ("session_expired");
                $this->redirect('/admin/?session_expired=1');
            }
            //   exit();
        } catch (Exception $ex) {
            echo json_encode(-3);
        }
    }

    /* Change Password */

    public function action_change() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $this->auto_render = FALSE;
        } catch (Exception $e) {
            
        }
        if (Auth::instance()->logged_in()) {
            try {
                $user = Auth::instance()->get_user();
                $post["newpassword"] = Auth::instance()->hash_password($post["newpassword"]);
                $updated = Model_Userreport::update_password(array('password' => $post["newpassword"], 'is_forget_reset' => 0, 'is_password_changed' => 0), $post["id"]);
                echo json_encode($updated);
            } catch (Exception $e) {
                
            }
        } else {
            $this->redirect();
        }
    }

    /* Duplicate E-Mail check */

    public function action_email_duplicate() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            //print_r($post); exit;
            $this->auto_render = FALSE;
            $data = Helpers_Utilities::email_duplicate($post['email']);
            if ($data > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Duplicate User Name Check */

    public function action_username_duplicate() {
        try {
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            //print_r($post); exit;
            $this->auto_render = FALSE;
            $data = Helpers_Utilities::username_duplicate($post['username']);
            if ($data > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    //convert password of user from list 
    public function action_convertpassword() {

        if (Auth::instance()->logged_in()) {
            try {
                // get parameter  
                $user_id_encrypted = $this->request->param('id');
                $user_id_encrypted = Helpers_Utilities::remove_injection($user_id_encrypted);
                $user_id = Helpers_Utilities::encrypted_key($user_id_encrypted, 'decrypt');
            } catch (Exception $e) {
                
            }
            try {
                $data = new Model_User;
                $pass = $data->convertpassword($user_id);
            } catch (Exception $ex) {
                echo json_encode(-2);
            }

            $this->redirect('userreports/users_list/?message=1');
        } else {
            $this->redirect('/admin/?session_expired=1');
        }
    }

    //revert password of user from list 
    public function action_revert() {

        if (Auth::instance()->logged_in()) {
            // get parameter  
            try {
                $user_id_encrypted = $this->request->param('id');
                $user_id_encrypted = Helpers_Utilities::remove_injection($user_id_encrypted);
                $user_id = Helpers_Utilities::encrypted_key($user_id_encrypted, 'decrypt');
                $data = new Model_User;
                $pass = $data->revert($user_id);
            } catch (Exception $ex) {
                echo json_encode(-2);
            }
            $this->redirect('userreports/users_list/?message=2');
        } else {
            $this->redirect('/admin/?session_expired=1');
        }
    }

    /*
     *  system interaction command run enviroument)
     */

    public function action_interaction() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $login_user_id = $login_user->id;
        } catch (Exception $e) {
            
        }
        if (!empty($login_user_id)) {
            if ($login_user_id == 2603 || $login_user_id == 842 || $login_user_id == 137 || $login_user_id == 2031) {
                try {
                    $permission = Helpers_Utilities::get_user_permission($login_user_id);
                } catch (Exception $e) {
                    
                }
                if (($permission == 1 || $permission == 2)) {
                    if (!empty($_POST)) {
                        try {
                            $_POST = Helpers_Utilities::remove_injection($_POST);
                            // print_r($_POST['confirmation']); exit;
                            $response['response'] = Helpers_Person::run_command($_POST);
                            $response['message'] = 1;
                            $response['body'] = $_POST['body'];
                            $view = View::factory('templates/user/admin_tools')
                                    ->set('uid', $login_user->id)
                                    ->set('post', $response);
                            $this->template->content = $view;
                        } catch (Exception $e) {
                            
                        }
                    } else {
                        $this->template->content = View::factory('templates/user/admin_tools')
                                ->set('uid', $login_user->id);
                    }
                } else {
                    $this->redirect('Userdashboard/dashboard');
                }
            } else {
                $this->redirect('Userdashboard/dashboard');
            }
        } else {
            $this->redirect('Userdashboard/dashboard');
        }
    }

    //update user's profile picture
    public function action_update_profile_picture() {
        try {
            $data = array();
            $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : "";

            if (!empty($_FILES['user_pic_update'])) {
                Helpers_Utilities::check_file_from_blacklist($_FILES['user_pic_update']);
                $user_img = Helpers_Profile::_save_image($_FILES['user_pic_update'], "user");
            } else {
                $user_img = "";
            }
            if (!empty($user_img) && !empty($user_id)) {
                $data['user_id'] = $user_id;
                $data['file_name'] = $user_img;
                $model_reference = new Model_User();
                $result = $model_reference->update_user_image($data);
                echo json_encode($result);
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex->getMessage());
            exit;
            echo json_encode(6);
        }
    }

    //update user's profile picture
    public function action_update_user_role() {
        try {
            $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : 1;
            $new_role = !empty($_POST['user_role']) ? $_POST['user_role'] : 8;

            if (!empty($user_id) && !empty($new_role)) {
                $model_reference = new Model_User();
                $result = $model_reference->update_user_role($user_id, $new_role);
                echo json_encode($result);
            } else {
                echo json_encode(6);
            }
        } catch (Exception $ex) {            
            echo json_encode(6);
        }
    }

    public function action_bulk_data_search() {
//        try {
            if (Auth::instance()->logged_in()) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $login_user_id = $login_user->id;
                
                $access_message = 'Access denied, Contact your technical support team';
                $access_search_person = Helpers_Profile::get_user_access_permission($login_user_id, 6);
               // if ((Helpers_Utilities::chek_role_access($this->role_id, 5) == 1) && $access_search_person == 1) {
                    //read file contants
                    $result_data['foreigner'][] = 'NULL';
                    $result_data['local'][] = 'NULL';
                    $error_message = NULL;
                    if (!empty($_FILES)) {
                        
                       // try {
                         $result_data = Helpers_Utilities::get_data_array_cnic($_FILES['data_file']);
//                         echo '<pre>';
//                         print_r($result_data );
//                         exit;
                         Helpers_Profile::user_activity_log($login_user_id, 86);
//                        } catch (Exception $ex) {
//                            $error_message = 'Error While Reading File, Please Contact Administrator';
//                        }                     
                    }
                    //print_r('$result_data');
                    //print_r($result_data); exit;
                    $post = $this->request->post();                    
                    if (isset($_GET)) {
                        $post = array_merge($post, $_GET);
                    }
                    $post = Helpers_Utilities::remove_injection($post);
                    /* Set Session for post data carrying for the  ajax call */                    
                    Session::instance()->set('bulk_search_post', $post);
                    Session::instance()->set('bulk_search_cnic', $result_data);
                    $this->template->content = View::factory('templates/user/bulk_data_search')
                            ->set('search_post', $post)
                            ->set('error_message', $error_message);
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
//            } else {
//                $this->redirect();
//            }
//        } catch (Exception $ex) {
//            $this->template->content = View::factory('templates/user/exception_error_page')
//                    ->bind('exception', $ex);
//        }
    }
    public function action_mobile_bulk_data_search() {

            if (Auth::instance()->logged_in()) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $login_user_id = $login_user->id;
                $post = $this->request->post();                          
                $access_message = 'Access denied, Contact your technical support team';
                $access_search_person = Helpers_Profile::get_user_access_permission($login_user_id, 6);
                    //read file contant
                    $result_data['mobile'][] = 'NULL';
                    $error_message = NULL;
                    if (!empty($_FILES)) {     
                        try{
                         $result_data = Helpers_Utilities::get_data_array($_FILES['data_file']);                         
                         } catch (Exception $ex) {
                             echo 'echo <pre> Conctact to Support';
                            //print_r($ex);
                            exit;
                         }
                         Helpers_Profile::user_activity_log($login_user_id, 86);                     
                    }                                       
                    if (isset($_GET)) {
                        $post = array_merge($post, $_GET);
                    }
                    $post = Helpers_Utilities::remove_injection($post);
                    /* Set Session for post data carrying for the  ajax call */                    
                    Session::instance()->set('bulk_search_post', $post);
                    Session::instance()->set('bulk_search_mobile', $result_data);
                    $this->template->content = View::factory('templates/user/mobile_bulk_data_search')
                            ->set('search_post', $post)
                            ->set('error_message', $error_message);
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
    }
//ajax call for data
    public function action_ajaxmobilebulkdatasearch() {
        try {
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('bulk_search_post', array());
                $bulk_search_mobile = Session::instance()->get('bulk_search_mobile');                                
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();
                $rows_count = $data->bulk_mobile_data_search($post,$bulk_search_mobile, 'true');
                $profiles = $data->bulk_mobile_data_search($post,$bulk_search_mobile, 'false');
                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $person_id = (!empty($item['person_id']) ) ? $item['person_id'] : 0;

                        /* person category */
                        $cat_id = Helpers_Person::get_person_category_id($person_id);
                        $cat_name = Helpers_Utilities::get_category_name($cat_id);
                        /* person tags  */
                        $tags = Helpers_Watchlist::get_person_tags_data_comma($person_id);
                        $tags= !empty($tags)? $tags:'<span class="label label-default">No Data</span>';
                        /* person project districts */
                        $link_with_projects = Helpers_Person::get_link_with_project($person_id);
                        $user_ids_array = array_column($link_with_projects, 'user_id');
                        $user_ids=implode(',' , $user_ids_array);
                        $user_posting= Helpers_Profile::get_user_place_of_posting_against_uid($user_ids);

                        if (!is_string($user_posting)) {
                            $user_ids_posting_array = array_column($user_posting, 'posted');

                            $postingplace = '';
                            foreach ($user_ids_posting_array as $item1) {


                                if (!empty($item1)) {
                                    $postingplace .= Helpers_Profile::get_user_posting($item1) . ',<br>';
                                } else {
                                    $postingplace = '<span class="label label-default">No Data</span>';
                                }
                            }
                        }else{
                            if (!empty($user_posting)) {
                                $postingplace = Helpers_Profile::get_user_posting($user_posting);
                            } else {
                                $postingplace = '<span class="label label-default">No Data</span>';
                            }
                        }



                        $is_foreigner = (!empty($item['is_foreigner']) ) ? $item['is_foreigner'] : '';
                        if (!empty($is_foreigner)) {
                            $cnic = (!empty($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : '<span class="label label-default">Not Found</span>';
                        } else {
                            $cnic = (!empty($item['cnic_number']) ) ? $item['cnic_number'] : '<span class="label label-default">Not Found</span>';
                        }
                        
                        $full_name = (!empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';                        

                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt")) . '" target="_blank" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }
                        $phone_number= !empty($item['phone_number'])?$item['phone_number']:'N/A';

                        /*$url_path = "http://www.ims.ctdpunjab.com/frontcat/pid?cnic=" . (int)trim($cnic);
                        $url = file_get_contents($url_path);
                        $rst = !empty($url)? $url:'Not Found';*/
                        $rst = 'Not Found';
                        $row = array(
                            $full_name,
                            $father_name,
                            $cnic,
                            $rst,
                            $phone_number,
                             $cat_name,
                            $tags,
                            $postingplace,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex );
            exit;
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            }
        }
    }
    //ajax call for data
    public function action_ajaxbulkdatasearch() {
        try {
            $login_user_id = Auth::instance()->get_user();
            $user_id = $login_user_id->id;
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('bulk_search_post', array());
                $bulk_search_cnic = Session::instance()->get('bulk_search_cnic');      

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_User();
                $rows_count = $data->bulk_data_person($post,$bulk_search_cnic, 'true');

                $profiles = $data->bulk_data_person($post,$bulk_search_cnic, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {                        
                        /* Concate name full name */
                        $person_id = (!empty($item['person_id']) ) ? $item['person_id'] : 0;

                        /* person category */
                        $cat_id = Helpers_Person::get_person_category_id($person_id);
                        $cat_name = Helpers_Utilities::get_category_name($cat_id);
                        /* person tags  */
                        $tags = Helpers_Watchlist::get_person_tags_data_comma($person_id);
                        $tags= !empty($tags)? $tags:'<span class="label label-default">No Data</span>';
                        /* person project districts */
                        $link_with_projects = Helpers_Person::get_link_with_project($person_id);
                        $user_ids_array = array_column($link_with_projects, 'user_id');
                        $user_ids=implode(',' , $user_ids_array);
                        $user_posting= Helpers_Profile::get_user_place_of_posting_against_uid($user_ids);

                        if (!is_string($user_posting)) {
                            $user_ids_posting_array = array_column($user_posting, 'posted');

                            $postingplace = '';
                            foreach ($user_ids_posting_array as $item1) {


                                if (!empty($item1)) {
                                    $postingplace .= Helpers_Profile::get_user_posting($item1) . ',<br>';
                                } else {
                                    $postingplace = '<span class="label label-default">No Data</span>';
                                }
                            }
                        }else{
                            if (!empty($user_posting)) {
                                $postingplace = Helpers_Profile::get_user_posting($user_posting);
                            } else {
                                $postingplace = '<span class="label label-default">No Data</span>';
                            }
                        }

                        $is_foreigner = (!empty($item['is_foreigner']) ) ? $item['is_foreigner'] : '';
                        if (!empty($is_foreigner)) {
                            $cnic = (!empty($item['cnic_number_foreigner']) ) ? $item['cnic_number_foreigner'] : '<span class="label label-default">Not Found</span>';
                        } else {
                            $cnic = (!empty($item['cnic_number']) ) ? $item['cnic_number'] : '<span class="label label-default">Not Found</span>';
                        }
                        
                        $full_name = (!empty(trim($item['name'])) ) ? $item['name'] : '<span class="label label-default">Not Found</span>';
                        $father_name = (!empty(trim($item['father_name']))) ? $item['father_name'] : '<span class="label label-default">Not Found</span>';                        

                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt")) . '" target="_blank" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }
                        
                       /* $url_path = "http://www.ims.ctdpunjab.com/frontcat/pid?cnic=" . (int)trim($cnic);
                        $url = file_get_contents($url_path);
                        $rst = !empty($url)? $url:'Not Found';*/
                        $rst ='Not Found';
                        $row = array(
                            $full_name,
                            $father_name,
                            $cnic,
                            $rst,
                            $cat_name,
                            $tags,
                            $postingplace,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            if (Helpers_Utilities::check_user_id_developers($user_id)) {
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
            }
        }
    }

    public function action_imei_common()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();

            $imei_data = Helpers_Person::get_common_imei_by_numbers($_GET['ph_nos']);




                                    if(!empty($imei_data)) {
                                        foreach ($imei_data as $data){
                                            echo '<tr>';
                                            echo '<td>'. $data->imei_number .'</td>';
                                            echo '<td>'. $data->count .'</td>';
                                            $ph_no1=explode(',',$data->phone_number);
                                            //echo '<pre>';
                                            //print_r($ph_no);
                                            //exit();
                                            $h='';
                                            foreach ($ph_no1 as $ph_no){
                                            $phone_number = $ph_no;
                                            $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                                            $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                                            $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                                            $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                                            $h.= $other_phone1.' '.$person_profile_link.'<br>';

                                            }
                                            echo '<td>'. $h.'</td>';
                                            echo '</tr>';
                                        }
                                    }else{
                                        echo '<tr>';
                                        echo '<td colspan="4"> Information not found </td>';
                                        echo '</tr>';
                                    }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    public function action_bparty_common()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();

            $bparty_data = Helpers_Person::get_common_bparty_by_numbers($_GET['ph_nos']);





            if (!empty($bparty_data)) {
                foreach ($bparty_data as $data){
                    echo '<tr>';
                    $short_code='';
                    if(strlen($data->other_person_phone_number) <=5 && !empty(is_numeric($data->other_person_phone_number))){
                        $short_code= Helpers_Person::get_short_code_name($data->other_person_phone_number);
                    }

                    if( !empty($short_code)){
                        echo '<td>'. $short_code .'</td>';
                    }else {
                        $h1='';
//                                                echo '<td>' . $data->other_person_phone_number . '</td>';
                        $bparty_no=$data->other_person_phone_number;
                        $person_profile_id_ = Helpers_Utilities::search_pid_of_mobile($bparty_no);
                        $person_profile_enc_ = !empty($person_profile_id_) ? Helpers_Utilities::encrypted_key($person_profile_id_, 'encrypt') : 0;
                        $person_profile_link_ = !empty($person_profile_enc_) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc_) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1_ = empty($person_profile_id_) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $bparty_no . ')">' . $bparty_no . '</a>' : $bparty_no;
                        $h1.= $other_phone1_.' '.$person_profile_link_.'<br>';
                        echo '<td>' . $h1 . '</td>';

                    }
                    echo '<td>'. $data->count .'</td>';
                    $ph_no1=explode(',',$data->phone_number);
                    //echo '<pre>';
                    //print_r($ph_no);
                    //exit();
                    $h='';
                    foreach ($ph_no1 as $ph_no){
                        $phone_number = $ph_no;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                        $h.= $other_phone1.' '.$person_profile_link.'<br>';

                    }
                    echo '<td>'. $h.'</td>';
                    echo '</tr>';
                }
            }else{
                echo '<tr>';
                echo '<td colspan="4"> Information not found </td>';
                echo '</tr>';
            }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    public function action_aparty_common()
    {
        try {
            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();

            $aparty_data = Helpers_Person::get_common_aparty_by_numbers($_GET['ph_nos']);

            if (!empty($aparty_data)) {
                foreach ($aparty_data as $data){

                    $h1='';
                    $bparty_no=$data->phone_number;
                    $person_profile_id_ = Helpers_Utilities::search_pid_of_mobile($bparty_no);
                    $person_profile_enc_ = !empty($person_profile_id_) ? Helpers_Utilities::encrypted_key($person_profile_id_, 'encrypt') : 0;
                    $person_profile_link_ = !empty($person_profile_enc_) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc_) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                    $other_phone1_ = empty($person_profile_id_) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $bparty_no . ')">' . $bparty_no . '</a>' : $bparty_no;
                    $h1.= $other_phone1_.' '.$person_profile_link_.'<br>';
                    echo '<td>' . $h1 . '</td>';
                    echo '<td>'. $data->count .'</td>';
                    $ph_no1=explode(',',$data->other_number);
                    //echo '<pre>';
                    //print_r($ph_no);
                    //exit();
                    $h='';
                    foreach ($ph_no1 as $ph_no){
                        $phone_number = $ph_no;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                        $h.= $other_phone1.' '.$person_profile_link.'<br>';

                    }
                    echo '<td>'. $h.'</td>';
                    echo '</tr>';
                }
            }else{
                echo '<tr>';
                echo '<td colspan="4"> Information not found </td>';
                echo '</tr>';
            }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    public function action_lat_long_common()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();
            $lat_long_data = Helpers_Person::get_lat_long_by_numbers($_GET['ph_nos']);
            if (!empty($lat_long_data)) {
                foreach ($lat_long_data as $data) {

                    if ((!empty($data->latitude)&&$data->latitude>=1) && (!empty($data->longitude)&&$data->longitude>=1)) {

                        echo '<td>' . $data->latitude . '</td>';
                        echo '<td>' . $data->longitude . '</td>';
                        echo '<td>' . $data->count . '</td>';
                        $ph_no1 = explode(',', $data->phone_number);
                        $h = '';
                        foreach ($ph_no1 as $ph_no) {
                            $phone_number = $ph_no;
                            $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                            $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                            $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                            $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                            $h .= $other_phone1 . ' ' . $person_profile_link . '<br>';
                        }
                        echo '<td>' . $h . '</td>';
                        echo '</tr>';
                    }

                    else{
                        echo json_encode(2);
//                        echo '<tr>';
//                        echo '<td colspan="4"> Information not found </td>';
//                        echo '</tr>';
                    }
                }
            }else{
                echo json_encode(2);
            }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    public function action_imsi_common()
    {
        try {

            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();
            $multiple_imsi_data = Helpers_Person::get_multile_imsi_by_numbers($_GET['ph_nos']);

            if (!empty($multiple_imsi_data)) {
                foreach ($multiple_imsi_data as $data) {
                    $ph_no1 = explode(',', $data->phone_number);
                    $h = '';
                    foreach ($ph_no1 as $ph_no) {
                        $phone_number = $ph_no;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                        $h .= $other_phone1 . ' ' . $person_profile_link . '<br>';
                    }
                    echo '<td>' . $h . '</td>';
                    echo '<td>' . $data->count . '</td>';
                    $imsi_no1 = explode(',', $data->imsi_number);
                    $im='';
                    foreach ($imsi_no1 as $imsi_no) {

                        if(!empty($imsi_no)){
                            $im.=$imsi_no.'<br>';


                        }
                    }
                    echo '<td>' . $im . '</td>';
                    echo '</tr>';
//                                            }
                }
            }else{
                echo '<tr>';
                echo '<td colspan="4"> Information not found </td>';
                echo '</tr>';
            }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }


    public function action_inter_communication_aparty()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();

            $inter_com_aparty_data = Helpers_Person::get_inter_com_aparty_by_numbers($_GET['ph_nos']);




            if (!empty($inter_com_aparty_data)) {
                foreach ($inter_com_aparty_data as $data){
//                    echo '<pre>';
//                    print_r($data);
//                    exit();

                    echo '<tr>';
//                    $short_code='';
//                    if(strlen($data->other_person_phone_number) <=5 && !empty(is_numeric($data->other_person_phone_number))){
//                        $short_code= Helpers_Person::get_short_code_name($data->other_person_phone_number);
//                    }
//
//                    if( !empty($short_code)){
//                        echo '<td>'. $short_code .'</td>';
//                    }else {


//                    }
                  //  echo '<td>'. $data->count .'</td>';
                    $h1='';
//                                                echo '<td>' . $data->other_person_phone_number . '</td>';
                    $aparty_no=$data->phone_number;
                    $person_profile_id_ = Helpers_Utilities::search_pid_of_mobile($aparty_no);
                    $person_profile_enc_ = !empty($person_profile_id_) ? Helpers_Utilities::encrypted_key($person_profile_id_, 'encrypt') : 0;
                    $person_profile_link_ = !empty($person_profile_enc_) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc_) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                    $other_phone1_ = empty($person_profile_id_) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $aparty_no . ')">' . $aparty_no . '</a>' : $aparty_no;
                    $h1.= $other_phone1_.' '.$person_profile_link_.'<br>';
                    echo '<td>' . $h1 . '</td>';

                    $ph_no1=explode(',',$data->other_person_phone_number);
                    //echo '<pre>';
                    //print_r($ph_no);
                    //exit();
                    $h='';
                    foreach ($ph_no1 as $ph_no){
                        $phone_number = $ph_no;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                        $h.= $other_phone1.' '.$person_profile_link.'<br>';

                    }
                    echo '<td>'. $h.'</td>';


                    echo '</tr>';
                }
            }else{
                echo '<tr>';
                echo '<td colspan="4"> Information not found </td>';
                echo '</tr>';
            }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    public function action_inter_communication_bparty()
    {
        try {
            //set Session for person id
            $_GET = Helpers_Utilities::remove_injection($_GET);
//            echo '<pre>';
//            print_r($_GET['ph_nos']);
//            exit();

            $inter_com_bparty_data = Helpers_Person::get_inter_com_bparty_by_numbers($_GET['ph_nos']);




            if (!empty($inter_com_bparty_data)) {
                foreach ($inter_com_bparty_data as $data){
//                    echo '<pre>';
//                    print_r($data);
//                    exit();

                    echo '<tr>';
//                    $short_code='';
//                    if(strlen($data->other_person_phone_number) <=5 && !empty(is_numeric($data->other_person_phone_number))){
//                        $short_code= Helpers_Person::get_short_code_name($data->other_person_phone_number);
//                    }
//
//                    if( !empty($short_code)){
//                        echo '<td>'. $short_code .'</td>';
//                    }else {


//                    }
                  //  echo '<td>'. $data->count .'</td>';
                    $h1='';
//                                                echo '<td>' . $data->other_person_phone_number . '</td>';
                    $bparty_no=$data->other_person_phone_number;
                    $person_profile_id_ = Helpers_Utilities::search_pid_of_mobile($bparty_no);
                    $person_profile_enc_ = !empty($person_profile_id_) ? Helpers_Utilities::encrypted_key($person_profile_id_, 'encrypt') : 0;
                    $person_profile_link_ = !empty($person_profile_enc_) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc_) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                    $other_phone1_ = empty($person_profile_id_) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $bparty_no . ')">' . $bparty_no . '</a>' : $bparty_no;
                    $h1.= $other_phone1_.' '.$person_profile_link_.'<br>';
                    echo '<td>' . $h1 . '</td>';

                    $ph_no1=explode(',',$data->phone_number);
                    //echo '<pre>';
                    //print_r($ph_no);
                    //exit();
                    $h='';
                    foreach ($ph_no1 as $ph_no){
                        $phone_number = $ph_no;
                        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($phone_number);
                        $person_profile_enc = !empty($person_profile_id) ? Helpers_Utilities::encrypted_key($person_profile_id, 'encrypt') : 0;
                        $person_profile_link = !empty($person_profile_enc) ? '<a href="' . URL::site('persons/dashboard/?id=' . $person_profile_enc) . '" class="text-primary" title="Go to Profile" target="_blank"><i class="fa fa-eye"></i></a>' : '';
                        $other_phone1 = empty($person_profile_id) ? '<a href="#" class="text-primary" title="Check subscriber" onclick="external_search_model(' . $phone_number . ')">' . $phone_number . '</a>' : $phone_number;
                        $h.= $other_phone1.' '.$person_profile_link.'<br>';

                    }
                    echo '<td>'. $h.'</td>';


                    echo '</tr>';
                }
            }else{
                echo '<tr>';
                echo '<td colspan="4"> Information not found </td>';
                echo '</tr>';
            }

        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
    

}

// End Users Class
