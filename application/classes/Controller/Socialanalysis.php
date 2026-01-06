<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Socialanalysis extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    /*
     *  Person Dashboard (social links)
     */

    public function action_social_links() {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('social_analysis_post', $post);
            //get Person id from session
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //$pid = Session::instance()->get('personid');
                include 'persons_functions/social_analysis.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    /* person links with other portals */

    public function action_other_portals_links() {
        try {
            //$id = (int) $this->request->param('id');
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('other_portals_post', $post);
            //get Person id from session
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            if ($pid != 0) {
                //$pid = Session::instance()->get('personid');
                include 'persons_functions/other_portal_link.inc';
            } else {
                header("Location:" . url::base() . "errors?_e=wrong_parameters");
                exit;
                //$this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*  Person Dashboard (add social links) */

    public function action_add_social_link() {
        try {
            if (Auth::instance()->logged_in()) {
                $_GET = Helpers_Utilities::remove_injection($_GET);
                $pid = Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if ($pid != 0) {
                    // print_r($pid);        exit();
                    include 'persons_functions/add_social_link.inc';
                } else {
                    header("Location:" . url::base() . "errors?_e=wrong_parameters");
                    exit;
                    //$this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                $this->redirect();
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Person Dashboard (social_analysis ajax function)
     */

    public function action_ajaxsociallinks() {
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
                $post = Session::instance()->get('social_analysis_post', array());
                //get id from url
                // $pid = Session::instance()->get('personid');
                $pid = Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {
//                $_GET['iDisplayStart']=0;                
//                
//            } 

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Socialanalysis;
                $rows_count = $data->social_analysis_table($post, 'true', $pid);

                $profiles = $data->social_analysis_table($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        // edit done here.... !!! 
                        $record_id = ( isset($item['record_id']) ) ? $item['record_id'] : 'NA';
                        $person_sw_id = ( isset($item['person_sw_id']) ) ? $item['person_sw_id'] : '';
                        $phone_number = (!empty(($item['phone_number'])) ) ? $item['phone_number'] : 'NA';

                        $profile_link1 = ( isset($item['profile_link']) ) ? $item['profile_link'] : 'NA';
                        if (!empty($profile_link1)) {
                            $profile_link = '<a target="_blank" href="' . $profile_link1 . '"><span class="badge badge-pill badge-success">View</span></a>';
                        } else {
                            $profile_link = '<span class="badge badge-pill badge-warning">No Data</span>';
                        }
                        $authenticity1 = ( isset($item['authenticity']) ) ? $item['authenticity'] : 'NA';
                        if (!empty($authenticity1)) {
                            $authenticity = '<span class="badge badge-pill badge-success">Verified</span>';
                        } else {
                            $authenticity = '<a href="#" title="Click to verify " onclick="approverecord(' . $record_id . ')"><span class="badge badge-pill badge-danger">Not Verified</span></a>';
                        }
                        $suggested_by1 = ( isset($item['suggested_by']) ) ? $item['suggested_by'] : 'NA';
                        if (!empty($suggested_by1) && $suggested_by1 != 'NA') {
                            $suggested_by = '<p title="Suggested by officer">Agent</P>';
                        } else {
                            $suggested_by = '<p title="Suggested by AIES system">AIES</P>';
                        }
                        $time_stamp = ( isset($item['time_stamp']) ) ? $item['time_stamp'] : 'NA';
                        $website_id = ( isset($item['website_id']) ) ? $item['website_id'] : 'NA';
                        $website_name1 = ( isset($item['website_name']) ) ? $item['website_name'] : 'NA';
                        $website_logo1 = ( isset($item['website_logo']) ) ? $item['website_logo'] : 'NA';
                        if (!empty($website_logo1)) {
                            $website_name = HTML::image("dist/img/social_websites/{$website_logo1}", array("height" => "auto", "width" => "auto", "alt" => "{$website_name1}", "title" => "{$website_name1}"));
                        } else {
                            $website_name = $website_name1;
                        }
                        $namealert = $identity_no = "'" . trim($website_name1) . "'";
                        // $personid=Helpers_Utilities::encrypted_key($pid, "decrypt");
                        $member_name_link = '<a class="btn btn-small" style="padding:1px !important" href="#" onclick="view_link_details(' . $record_id . ');"><i class="fa fa-certificate"></i>View</a>' . '<a class="btn btn-small" style="padding:1px !important" href="' . URL::base() . 'socialanalysis/edit_social_link/?id=' . Helpers_Utilities::encrypted_key($pid, "encrypt") . '&rec=' . Helpers_Utilities::encrypted_key($record_id, "encrypt") . '"><i class="fa fa-edit"></i>Edit</a>'
                                . '<a class="btn btn-small" style="padding:1px !important" href="#" onclick="deletelink(' . $record_id . ',' . $namealert . ');"><i class="fa fa-trash"></i>Delete</a>';

                        $row = array(
                            $website_name,
                            $person_sw_id,
                            $phone_number,
                            $profile_link,
                            $suggested_by,
                            $authenticity,
                            $time_stamp,
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
     *  delete social link
     */

    public function action_delete_link() {
        try {
            $this->auto_render = FALSE;
            //$pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            $recid = $this->request->param('id');
            $recid = Helpers_Utilities::remove_injection($recid);
            $update = Model_Socialanalysis::delete_record_link($recid, $uid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  view social link details
     */

    public function action_view_link_details() {
        try {
            $this->auto_render = FALSE;
            //$pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            $recid = $_POST['id'];
            $update = Model_Socialanalysis::view_link_details($recid);

            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  delete social link
     */

    public function action_approve_record() {
        try {
            $this->auto_render = FALSE;
            //$pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            $recid = $this->request->param('id');
            $recid = Helpers_Utilities::remove_injection($recid);
            $update = Model_Socialanalysis::approve_record($recid, $uid);
            echo json_encode($update);
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /* Add / update new social link */

    public function action_updatelink() {
        if (Auth::instance()->logged_in()) {
            try {
                $user_obj = Auth::instance()->get_user();
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if ((!empty($_POST)) && (empty($_POST['record_id']))) {
                    $_POST['user_id'] = $user_obj->id;
                    $user_id = $user_obj->id;
                    //file upload
                    if (!empty($_FILES)) {
                        Helpers_Utilities::check_file_from_blacklist($_FILES['personfile']);
                        $reportfile1 = Helpers_Upload::upload_person_documents($_FILES, "person_social_link", $_POST['person_id'], 'social-link');
                    } else {
                        $reportfile1 = "";
                    }
                    $_POST['file_link'] = $reportfile1;
                    $content = new Model_Socialanalysis();
                    $content_id = $content->sociallink_insert($_POST);
                    $this->redirect('socialanalysis/add_social_link?message=1&id=' . Helpers_Utilities::encrypted_key($_POST['person_id'], "encrypt"));
                } else {
                    $_POST['user_id'] = $user_obj->id;
                    //file upload
                    if (!empty($_FILES)) {
                        Helpers_Utilities::check_file_from_blacklist($_FILES['personfile']);
                        $reportfile1 = Helpers_Upload::upload_person_documents($_FILES, "person_social_link", $_POST['person_id'], 'social-link');
                    } else {
                        $reportfile1 = "";
                    }
                    $_POST['file_link'] = $reportfile1;
                    $object = New Model_Socialanalysis();
                    $update = $object->sociallink_update($_POST);

                    $this->redirect('socialanalysis/add_social_link?message=2&id=' . Helpers_Utilities::encrypted_key($_POST['person_id'], "encrypt"));
                }
            } catch (Exception $ex) {
                if (Helpers_Utilities::check_user_id_developers($user_id)) {
                    echo '<pre>';
                    print_r($ex->getMessage());
                    exit;
                } else {
                    $this->redirect('user/some_thing_went_wrong');
                }                
            }
        } else {
            $this->redirect();
        }
    }

// update new social link
    public function action_edit_social_link() {
        try {
            $DB = Database::instance();
            if (Auth::instance()->logged_in()) {
                // $pid = (int) $this->request->param('id');
                //  $recid = (int) $this->request->param('id1');
                $_GET = Helpers_Utilities::remove_injection($_GET);

                $recid = (int) Helpers_Utilities::encrypted_key($_GET['rec'], "decrypt");
                $personid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                // $pid = (int) Helpers_Utilities::encrypted_key($personid, "decrypt");
                if (!empty($recid)) {
                    $user_obj = Auth::instance()->get_user();
                    $uid = $user_obj->id;
                    $data = new Model_Socialanalysis();
                    $data1 = $data->getlinkrecord($recid);
                    if (isset($data1) && ($data1 != NULL)) {
                        $view = View::factory('templates/persons/add_social_link')
                                ->set('record', $data1)
                                ->set('person_id', $personid);
                        $this->template->content = $view;
                    }
                } else {
                    $user_obj = Auth::instance()->get_user();
                    $view = View::factory('templates/persons/add_social_link')
                            ->set('person_id', $personid);
                    $this->template->content = $view;
                }
            } else {
                $this->redirect();
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

}

// End Persons Class

