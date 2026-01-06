<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Controller for email Template Functionality 
 */
Class Controller_Shortcode extends Controller_Working {
    /* list of short codes */

    public function action_index() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 27) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                if (Auth::instance()->logged_in()) {
                    $data = new Model_Email;
                    $short_code_list = $data->shortcode_list();
                    $view = View::factory('templates/user/shortcode')
                            ->set('records', $short_code_list);
                    $this->template->content = $view;
                } else {
                    $this->redirect();
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page');
        }
    }

// single email template 
    public function action_showform() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_email_edit = 1; //Helpers_Profile::get_user_access_permission($login_user_id, 15);
            $access_email_add = 1; //Helpers_Profile::get_user_access_permission($login_user_id, 16);
            $access_message = 'Access denied, Contact your technical support team';
            // print_r($access_email_add); exit;
        } catch (Exception $e) {
            
        }
        if ((Helpers_Utilities::chek_role_access($this->role_id, 28) == 1) && ($access_email_add == 1)) {
            if (Auth::instance()->logged_in()) {
                try {
                    $id = $this->request->param('id');
                    $id = Helpers_Utilities::encrypted_key($id, 'decrypt');
                    $id = Helpers_Utilities::remove_injection($id);
                } catch (Exception $e) {
                    
                }
                if (isset($id) && ($id != NULL)) {
                    try {
                        $user_obj = Auth::instance()->get_user();
                        $data = new Model_Email;
                        $data1 = $data->sc_view($id);

                    } catch (Exception $e) {
                        
                    }
                    if (isset($data1) && ($data1 != NULL) && ($access_email_edit == 1)) {
                        $view = View::factory('templates/user/add_shortcode')
                                ->set('results', $data1);
                        $this->template->content = $view;
                    } else {
                        //$this->redirect('emailtemplate/?accessmessage='.$access_message);
                        $this->redirect('user/access_denied');
                    }
                } else {
                    $user_obj = Auth::instance()->get_user();
                    $view = View::factory('templates/user/add_shortcode');
                    $this->template->content = $view;
                }
            } else {
                $this->redirect();
            }
        } else {
            $this->template->content = View::factory('templates/user/access_denied');
        }
    }

    // single short code
    public function action_view_short_code() {
        if (Auth::instance()->logged_in()) {
            $id = $this->request->param('id');
            try {
                $id = Helpers_Utilities::encrypted_key($id, 'decrypt');
                $id = Helpers_Utilities::remove_injection($id);
            } catch (Exception $e) {
                
            }
            if (isset($id) && ($id != NULL)) {
                try {
                    $user_obj = Auth::instance()->get_user();
                    $data = new Model_Email;
                    $data1 = $data->sc_view($id);
                } catch (Exception $e) {
                    
                }

                if (isset($data1) && ($data1 != NULL)) {
                    $view = View::factory('templates/user/short_code_view')
                            ->set('results', $data1)
                            ->set('user_id', $user_obj->id);
                    $this->template->content = $view;
                } else {
                    $this->redirect();
                }
            } else {
                $this->redirect();
            }
        } else {
            $this->redirect();
        }
    }

    /* Add / update short codet */

    public function action_post() {
        if (Auth::instance()->logged_in()) {
            try {
                $user_obj = Auth::instance()->get_user();
                $_POST = Helpers_Utilities::remove_injection($_POST);
            } catch (Exception $e) {
                
            }
            if ((isset($_POST)) && ($_POST != '') && ($_POST['id'] == '')) {
                //   print_r($_POST); exit;
                try {
                    $_POST['user_id'] = $user_obj->id;
                    $content = new Model_Email;
                    $content_id = $content->shortcode_insert($_POST);
                } catch (Exception $e) {
                    
                }
                $this->redirect('shortcode/showform?message=1');
//                $view = View::factory('templates/user/add_template') ->set('message', $message);
//                $this->template->content = $view;               
            } else {
                try {
                    $id = $_POST['id'];
                    $_POST['user_id'] = $user_obj->id;
//                    echo '<pre>';
//                    print_r($_POST);
//                    exit();
                    $object = New Model_Email;
                    $update = $object->shortcode_update($_POST);
                } catch (Exception $e) {
                    
                }
                $this->redirect('shortcode/showform?message=2');
            }
        } else {
            $this->redirect();
        }
    }

    /* delte single email template */

    public function action_del() {
        if (Auth::instance()->logged_in()) {
            try {
                $id = (int) $this->request->param('id');
                $id = Helpers_Utilities::remove_injection($id);
            } catch (Exception $e) {
                
            }
            $user_obj = Auth::instance()->get_user();
            $login_user_id = $user_obj->id;
//            $per = 0; //Helpers_Profile::get_user_access_permission($login_user_id, 14);
//            if ($per == 0) {
//                echo -2;
//            } else {
                try {
                    $del = New Model_Email;
                    $result = $del->sc_deleted($id);
                } catch (Exception $e) {
                    
                }
                echo $result;
           // }
        } else {
            return 0;
        }
    }

}
