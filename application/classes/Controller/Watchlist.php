<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Controller for email Template Functionality 
 */
Class Controller_Watchlist extends Controller_Working {
    /* Watch List Configration */

    public function action_add_watch_list() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $posting = $login_user_profile->posted;
            $result = explode('-', $posting);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 25) == 1) && ($result[0] == 'd') ) {
                if (Auth::instance()->logged_in()) {
                    $post = $this->request->post();
                    if (isset($_GET)) {
                        $post = array_merge($post, $_GET);
                    }
                    $post = Helpers_Utilities::remove_injection($post);
                    //print_r($post); exit;
                    Session::instance()->set('watchlist_post', $post);
                    include 'watchlist_functions/add_watch_list.inc';
                } else {
                    echo '1';
                    exit;
                    //$this->redirect();
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxtagpersonlist() {
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
                $post = Session::instance()->get('watchlist_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
                //print_r($post); exit;
                if (isset($post)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Watchlist();
                $rows_count = $data->tag_person_list($post, 'true');
                $profiles = $data->tag_person_list($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $person_id = (isset($item['person_id'])) ? $item['person_id'] : 0;
                        $person_name = (isset($item['person_id'])) ? Helpers_Person::get_person_name($item['person_id']) : 'UnKnow';
                        $person_father_name = (isset($item['person_id'])) ? Helpers_Person::get_person_father_name($item['person_id']) : 'UnKnow';
                        $person_cnic_number = (isset($item['person_id'])) ? Helpers_Person::get_person_cnic($item['person_id']) : 'UnKnow';
                        $person_address = (isset($item['person_id'])) ? Helpers_Person::get_person_address($item['person_id']) : 'UnKnow';

                        $person_tags = (isset($item['tagname'])) ? $item['tagname'] : 'UnKnow';

                        $tag_id = (isset($item['id'])) ? $item['id'] : 0;

                        $tag_district_id = (isset($item['tag_district_id'])) ? $item['tag_district_id'] : 0;
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;

                        //$watchlist_status = Helpers_Watchlist::get_watchlist_status($person_id);
                        $watchlist_status = (isset($item['in_watchlist'])) ? $item['in_watchlist'] : 2;
                        if ($watchlist_status == 0) {
                            $action = '<a class="btn btn-small action item-' . $item['person_id'] . '" href="javascript:ConfirmChoice(' . $item['person_id'] . ')"><i class="fa fa-plus"></i> Add To Watch List</a>';
                        } else if ($watchlist_status == 1) {
                            $action = '<a class="btn btn-small action item-' . $item['person_id'] . '" href="javascript:removewatchlist(' . $item['person_id'] . ')"><i class="fa fa-times "></i> Remove from Watch List</a>';
                        }else{
                            $action = 'Error';
                        }

                        $row = array(
                            $person_name,
                            $person_father_name,
                            $person_cnic_number,
                            $person_address,
                            $person_tags,
                            $action
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

    /* add person to watch list */

    public function action_addtowatchlist() {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_profile = Helpers_Profile::get_user_perofile($user_obj->id);
                $posting = $login_user_profile->posted;
                $result = explode('-', $posting);
                $user_district = $result['1'];
                //echo '<echo>'; print_r($user_obj); exit;

                $person_id = (int) $this->request->param('id');
                $person_id = Helpers_Utilities::remove_injection($person_id);
                $user = New Model_Watchlist();
                $result = $user->add_to_watchlist($person_id, $user_district);
                //print_r($result); exit;
                echo 1;
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* add person to watch list */

    public function action_removefromwatchlist() {
        try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $login_user_profile = Helpers_Profile::get_user_perofile($user_obj->id);
                $posting = $login_user_profile->posted;
                $result = explode('-', $posting);
                $user_district = $result['1'];
                //echo '<echo>'; print_r($user_obj); exit;

                $person_id = (int) $this->request->param('id');
                $person_id = Helpers_Utilities::remove_injection($person_id);

                $user = New Model_Watchlist();
                $result = $user->remove_from_watchlist($person_id, $user_district);
                //print_r($result); exit;
                echo 1;
            } else {
                return 0;
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Watch List Configration */

    public function action_view_watch_list() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 26) == 1) {
                if (Auth::instance()->logged_in()) {
                    include 'watchlist_functions/view_watch_list.inc';
                } else {
                    $this->redirect();
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }


    //ajax call for data
    public function action_ajaxwatchlistpersons() {
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
                $post = Session::instance()->get('watchlist_post', array());

//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                                
//            } 
//                print_r($post); exit;
                if (isset($post)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Watchlist();
                $rows_count = $data->watchlist_persons($post, 'true');
                $profiles = $data->watchlist_persons($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $tag_district_id = (isset($item['tag_district_id'])) ? $item['tag_district_id'] : 'UnKnown';
                        $district_name = (isset($item['name'])) ? $item['name'] : 0;
                        $person_count = (isset($item['count'])) ? $item['count'] : 0;
                        $tag_district_id1 = Helpers_Utilities::encrypted_key($tag_district_id, 'encrypt');
                        $action = '<a href="' . URL::site('watchlist/view_watch_list_details/?district_id=' . $tag_district_id1) . '" > View Details </a>';

                        $row = array(
                            $district_name,
                            $person_count,
                            $action
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

    /* Watch List Configration */

    public function action_view_watch_list_details() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if ($permission == 1 || $permission == 2 || $permission == 3 || $permission == 4  || $permission == 5) {
                if (Auth::instance()->logged_in()) {
                    $district_id = (int) $this->request->param('id');
                    $district_id = Helpers_Utilities::remove_injection($district_id);
                    $post = $this->request->post();
                    if (isset($_GET)) {
                        $post = array_merge($post, $_GET);
                    }
                    $post = Helpers_Utilities::remove_injection($post);
                    //print_r($post); exit;
                    Session::instance()->set('watchlistdetails_post', $post);

                    include 'watchlist_functions/view_watch_list_details.inc';
                } else {
                    header("Location:" . URL::base());
                    exit;
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_users_view_watch_list() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if ($permission == 1 || $permission == 2 || $permission == 3 || $permission == 4  || $permission == 5) {

                if (Auth::instance()->logged_in()) {
                    //$district_id = (int) $this->request->param('id');
//
//                    $posting_id=Helpers_Utilities::get_user_place_of_posting($login_user->id);
//
//
//                    $district_id=Helpers_Profile::get_user_posting_place_id($posting_id);
//                    $district_id = Helpers_Utilities::remove_injection($district_id);
//                    $post = $this->request->post();
//                    if (isset($_GET)) {
//                        $post = array_merge($post, $_GET);
//                    }
//                    $post = Helpers_Utilities::remove_injection($post);

                  //  print_r($post); exit;
                    //Session::instance()->set('userwatchlistdetails_post', $post);

                    include 'watchlist_functions/view_user_watch_list_details.inc';
                } else {
                    header("Location:" . URL::base());
                    exit;
                }
            } else {


                $this->template->content = View::factory('templates/user/access_denied');
            }
        }
        catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxwatchlistdetails() {
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
                $post = Session::instance()->get('watchlistdetails_post', array());
                if (isset($post)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                //print_r($post); exit;
                $data = new Model_Watchlist();
                $rows_count = $data->watchlist_persons_details($post, 'true');
                $profiles = $data->watchlist_persons_details($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_name = Helpers_Profile::get_user_perofile($item['user_id']);
                        $user_name = $user_name->first_name . '  ' . $user_name->last_name;

                        $person_id = (isset($item['person_id'])) ? $item['person_id'] : 0;
                        $person_name = (isset($item['person_id'])) ? Helpers_Person::get_person_name($item['person_id']) : 'UnKnow';
                        $person_father_name = (isset($item['person_id'])) ? Helpers_Person::get_person_father_name($item['person_id']) : 'UnKnow';
                        $person_cnic_number = (isset($item['person_id'])) ? Helpers_Person::get_person_cnic($item['person_id']) : 'UnKnow';
                        $person_address = (isset($item['person_id'])) ? Helpers_Person::get_person_address($item['person_id']) : 'UnKnow';

                        $person_tags = (isset($item['tagname'])) ? $item['tagname'] : '';
                        $tag_description = (isset($item['tag_description'])) ? $item['tag_description'] : 'Un-Known';

                        $tags_display = '<span title="' . $tag_description . '" class="text-black">' . $person_tags . '</span>';

                        $tag_id = (isset($item['id'])) ? $item['id'] : 0;

                        $tag_district_id = (isset($item['tag_district_id'])) ? $item['tag_district_id'] : 0;
                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;

                        $action = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';

                        $row = array(
                            $person_name,
                            $person_father_name,
                            $person_cnic_number,
                            $person_address,
                            $tags_display,
                            $user_name,
                            $action
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }//ajax call for data for user watch list persons
    public function action_ajax_user_watchlistdetails() {
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
                $post = Session::instance()->get('userwatchlistdetails_post', array());
                if (isset($post)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
              //  print_r($post); exit;
                $data = new Model_Watchlist();
                $rows_count = $data->user_watchlist_persons_details($post, 'true');
                $profiles = $data->user_watchlist_persons_details($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $user_name = Helpers_Profile::get_user_perofile($item['user_id']);
                        $user_name = $user_name->first_name . '  ' . $user_name->last_name;

                        $total = (isset($item['total'])) ? $item['total'] : 0;
//                        $person_name = (isset($item['person_id'])) ? Helpers_Person::get_person_name($item['person_id']) : 'UnKnow';
//                        $person_father_name = (isset($item['person_id'])) ? Helpers_Person::get_person_father_name($item['person_id']) : 'UnKnow';
//                        $person_cnic_number = (isset($item['person_id'])) ? Helpers_Person::get_person_cnic($item['person_id']) : 'UnKnow';
//                        $person_address = (isset($item['person_id'])) ? Helpers_Person::get_person_address($item['person_id']) : 'UnKnow';
//
//                        $person_tags = (isset($item['tagname'])) ? $item['tagname'] : '';
//                        $tag_description = (isset($item['tag_description'])) ? $item['tag_description'] : 'Un-Known';
//
//                        $tags_display = '<span title="' . $tag_description . '" class="text-black">' . $person_tags . '</span>';
//
//                        $tag_id = (isset($item['id'])) ? $item['id'] : 0;
//
//                        $tag_district_id = (isset($item['tag_district_id'])) ? $item['tag_district_id'] : 0;
//                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;

                       $action = '<a href="' . URL::site('watchlist/user_wl_person/?id=' . Helpers_Utilities::encrypted_key($item['user_id'], "encrypt")) . '" > View Detail </a>';
                     //   $action='';
                        $row = array(
                            $user_name,

                            $total,
                            $action
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
    public function action_user_wl_person() {
        try {

            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            if (Helpers_Utilities::chek_role_access($this->role_id, 26) == 1) {
                if (Auth::instance()->logged_in()) {
                    $post = $this->request->post();
                    if (isset($_GET)) {
                        $post = array_merge($post, $_GET);
                    }
                    $post = Helpers_Utilities::remove_injection($post);
                    include 'watchlist_functions/view_user_watch_list.inc';
                } else {
                    $this->redirect();
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
        }
    }

    //ajax call for data for user watch list persons
    public function action_ajax_user_added_wl_persons() {
        try {
//            echo '<pre>';
//            print_r($_GET['id']);
//            exit();
            $uid= Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");

            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {

                $post = Session::instance()->get('userwatchlistdetails_post', array());
                if (isset($post)) {
                    $post = array_merge($post, $_GET);
                }

                $post = Helpers_Utilities::remove_injection($post);




                $data = new Model_Watchlist();
                $rows_count = $data->user_wl_persons_info($post, 'true',$uid);
                $profiles = $data->user_wl_persons_info($post, 'false',$uid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {


//                        $user_name = Helpers_Profile::get_user_perofile($item['user_id']);
//                        $user_name = $user_name->first_name . '  ' . $user_name->last_name;

                        $user_total_request = (isset($item['total'])) ? $item['total'] : 0;
                        $last_req_sent_date = (isset($item['sending_date'])) ? $item['sending_date'] : 0;
                        $person_name = (isset($item['person_id'])) ? Helpers_Person::get_person_name($item['person_id']) : 'UnKnow';
                        $person_info= Helpers_Profile::get_person_info($item['person_id']);

                        $person_total_request = (isset($person_info->total_request)) ? $person_info->total_request : 0;
                        //$last_req_sent_date = (isset($person_info->last_sent_req)) ? $person_info->last_sent_req : 0;


//                        $person_father_name = (isset($item['person_id'])) ? Helpers_Person::get_person_father_name($item['person_id']) : 'UnKnow';
//                        $person_cnic_number = (isset($item['person_id'])) ? Helpers_Person::get_person_cnic($item['person_id']) : 'UnKnow';
//                        $person_address = (isset($item['person_id'])) ? Helpers_Person::get_person_address($item['person_id']) : 'UnKnow';
//
//                        $person_tags = (isset($item['tagname'])) ? $item['tagname'] : '';
//                        $tag_description = (isset($item['tag_description'])) ? $item['tag_description'] : 'Un-Known';
//
//                        $tags_display = '<span title="' . $tag_description . '" class="text-black">' . $person_tags . '</span>';
//
//                        $tag_id = (isset($item['id'])) ? $item['id'] : 0;
//
//                        $tag_district_id = (isset($item['tag_district_id'])) ? $item['tag_district_id'] : 0;
//                        $user_id = (isset($item['user_id'])) ? $item['user_id'] : 0;

                  //      $action = '<a href="' . URL::site('watchlist/user_wl_person/?id=' . Helpers_Utilities::encrypted_key($item['user_id'], "encrypt")) . '" > View Detail </a>';
                        //   $action='';
                        $action = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Profile </a>';

                        $row = array(
                            $person_name,
                            $person_total_request,
                            $user_total_request,
                            $last_req_sent_date,
                            $action
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

}
