<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller
{


    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request  = $request;
        $this->response = $response;

        // Get token from URL when login from workspace
        $token = $this->request->query('token');

        if ($token && !Auth::instance()->logged_in())
        {
            // Find user by token
            $user = ORM::factory('User')
                ->where('login_token', '=', $token)
                ->find();

            if ($user->loaded())
            {
                // Check if token expired
                if ($user->token_expires && $user->token_expires < date('Y-m-d H:i:s'))
                {
                    // Token exists but expired
                    Session::instance()->set('error_message', 'Your login token has expired.');
                    $this->redirect('login'); // go to login page
                }

                // Token is valid → log in the user
                Auth::instance()->force_login($user);

                // Remove token after use (one-time login)
                $user->login_token   = NULL;
                $user->token_expires = NULL;
                $user->save();

                // Redirect to dashboard
                $this->redirect('Userdashboard/dashboard');
            }
            else
            {
                // Token invalid
                Session::instance()->set('error_message', 'Invalid login token.');
                $this->redirect('login');
            }
        }

        // -------------------------------
        // 2️⃣ Already logged in (manual)
        // -------------------------------
        if (Auth::instance()->get_user())
        {
            $this->redirect('Userdashboard/dashboard');
        }

    }



    public function action_index()
    {
        $view = View::factory('main');
        $view->roles = Helpers_Utilities::get_roles_data();
        $view->message = Session::instance()->get_once('error_message');

        $this->response->body($view);
    }

//    public function action_entry() {        
//       // $this->response->body(View::factory('entry/point'));
//    }

    /* Password Recovery */


    public function action_forget()
    {
        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $result = Helpers_Utilities::setwetcookies();
        } catch (Exception $e) {

        }
        if ($result == 1)
            $this->redirect('errors');


        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $size = strlen((string)$actual_link);
        $current_ip = $_SERVER['REMOTE_ADDR'];
        try {
            $check_ip_exist = Helpers_Utilities::checkblockIPforever($current_ip);
        } catch (Exception $e) {

        }
        if ($check_ip_exist == 1 || $size >= 100) {
            $this->response->body(View::factory('templates/user/block'));
        }
        if (!isset($_SESSION["attempts"]))
            $_SESSION["attempts"] = 0;
        $current_ip = $_SERVER['REMOTE_ADDR'];
        try {
            $check_ip_exist = Helpers_Utilities::checkblockIPforever($current_ip);
        } catch (Exception $e) {

        }
        if ($_SESSION["attempts"] < 5 && $check_ip_exist != 1) {
            $_POST['ftype'] = (int)!empty($_POST['ftype']) ? preg_replace("/[^0-9]/", "", $_POST['ftype']) : '';
            $_POST['fusername'] = !empty($_POST['fusername']) ? $_POST['fusername'] : '';
            $_POST['femail'] = !empty($_POST['femail']) ? $_POST['femail'] : '';
            if ((!empty($_POST)) && !empty($_POST['fusername']) && !empty($_POST['femail']) && !empty($_POST['ftype'])) {

                $result = Helpers_Utilities::your_php_validation($_POST['fusername'], 'alphanumricdecimal', 8, 15);
                $message = "Incorrect Username";

                if ($result) {
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    //   print_r($_POST); exit;     
                    $_POST['fusername'] = (string)(strlen((string)$_POST['fusername']) <= 20) ? $_POST['fusername'] : 'na';
                    $_POST['femail'] = (string)(strlen((string)$_POST['femail']) <= 50) ? $_POST['femail'] : 'na';
                    $_POST['ftype'] = (int)(strlen((string)$_POST['ftype']) <= 5) ? $_POST['ftype'] : 'na';


                    try {
                        $content = new Model_Generic();
                        $content_id = $content->password_update($_POST);
                    } catch (Exception $e) {
                        $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
                        $this->redirect();
                    }
                    if ($content_id == 1) {

                        $message = "Request is successful";
                    } elseif ($content_id == 2) {
                        $message = "Request is already received";
                    } else {
                        $message = "Incorrect Credentials";
                        $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
                    }
                }
                $_SESSION['error_message'] = $message;
                $this->redirect();
            } else {

                $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
                $message = "All fields must be filled";
                $_SESSION['error_message'] = $message;
                $this->redirect('blocked/userstatus?msg=Data');
            }
        } else {
            try {
                if ($check_ip_exist == 0)
                    $check_ip_exist = Helpers_Utilities::addblockIPapache($current_ip);
            } catch (Exception $e) {

            }


            $_SESSION["attempts"] = 0;
            $this->response->body(View::factory('templates/user/block'));
        }
    }

    /* User Login */

    public function action_check()
    {
        $_POST = Helpers_Utilities::remove_injection($_POST);
        $result = Helpers_Utilities::setwetcookies();
        if ($result == 1)
            $this->redirect('errors');

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $size = strlen((string)$actual_link);
        $current_ip = $_SERVER['REMOTE_ADDR'];
        $check_ip_exist = Helpers_Utilities::checkblockIPforever($current_ip);

        if ($check_ip_exist == 1 || $size >= 100) {
            $this->response->body(View::factory('templates/user/block'));
        }

        if (!isset($_SESSION["attempts"]))
            $_SESSION["attempts"] = 0;
        $current_ip = $_SERVER['REMOTE_ADDR'];
        $block_user_name = !empty($_POST['username']) ? $_POST['username'] : 'na';

        $check_ip_exist = Helpers_Utilities::checkblockIP($current_ip, $block_user_name);

        if (!Auth::instance()->logged_in()) {
            try {
                if (!empty($_POST['username']) || !empty($_POST['password'])) {
                    $name = Helpers_Utilities::your_php_validation($_POST['username'], 'alphanumricdecimal', 8, 20);
                    $psw = Helpers_Utilities::your_php_validation($_POST['password'], 'alphanumricspecial', 8, 20);
                } else {
                    $_POST['username'] = 'na';
                    $_POST['password'] = 'na';
                }
            } catch (Exception $e) {

            }
            if ($name == TRUE && $psw == TRUE) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $request = !empty($_POST['type']) ? $_POST['type'] : '';
                $_POST['username'] = (string)(strlen((string)$_POST['username']) <= 20) ? $_POST['username'] : 'na';
                $_POST['password'] = (string)(strlen((string)$_POST['password']) <= 30) ? $_POST['password'] : 'na';
                $request = (string)(strlen((string)$request) <= 7) ? $request : 'na';
                $message = 'error';
                if (HTTP_Request::POST == $this->request->method()) {
                    $remember = array_key_exists('remember', $this->request->post()) ? (bool)$this->request->post('remember') : FALSE;
                    $user = Auth::instance()->login($this->request->post('username'), $this->request->post('password'), $remember, $request);
                    if ($user) {

                        $user_obj = Auth::instance()->get_user();
                        if ($user_obj) {
                            $user_obj = ORM::factory('User', $user_obj);
                        }
                        Helpers_Profile::is_login($user_obj->id, TRUE);
                        $permission = Helpers_Utilities::get_user_permission($user_obj->id);
                        $this->redirect('Userdashboard/dashboard');
                    } else {
                        $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
                        $message = "Login Fail";
                        $view = View::factory('main')->bind('message', $message);
                        $view->roles = Helpers_Utilities::get_roles_data();
                        $this->response->body($view);
                    }
                } else {
                    $view = View::factory('main')                                           //->set('places', array('Rome', 'Paris', 'London', 'New York', 'Tokyo'));
                    ->bind('message', $message);
                    $view->roles = Helpers_Utilities::get_roles_data();
                    //$this->response->body(View::factory('main'));
                    $this->response->body($view);
                }
            } else {
                $message = "Please enter correct input";
                $view = View::factory('main')                                           //->set('places', array('Rome', 'Paris', 'London', 'New York', 'Tokyo'));
                ->bind('message', $message);                                       //$this->response->body(View::factory('main'));
                $view->roles = Helpers_Utilities::get_roles_data();
                $this->response->body($view);
            }
        } else {
            try {
                $user_obj = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($user_obj->id);
            } catch (Exception $e) {

            }
            $this->redirect('Userdashboard/dashboard');
        }

    }

    public function action_remote_login()
    {

        if (Auth::instance()->logged_in()) {
            Auth::instance()->logout(TRUE, TRUE);
        } else {
            Auth::instance()->logout(FALSE, TRUE);
        }
        // $this->response->body(View::factory('down'));
        //  exit;
        // Auth::instance()->logout(FALSE, TRUE);
        $_POST = Helpers_Utilities::remove_injection($_POST);

//       $result = Helpers_Utilities::setwetcookies();             
//        if($result==1)
//            $this->redirect('errors'); 

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $size = strlen((string)$actual_link);
        $current_ip = $_SERVER['REMOTE_ADDR'];
        $check_ip_exist = Helpers_Utilities::checkblockIPforever($current_ip);
        if ($size >= 150) {
            //header("Location : https://www.aiesdfdfdfmail.com/");
            //$this->response->body(View::factory('templates/user/block'));             
            header("Location : " . URL::site('blocked/') . "?id=2");
            exit;
        }
        if ($check_ip_exist == 1) {
            //header("Location : https://www.aiesdfdfdfmail.com/");
            //$this->response->body(View::factory('templates/user/block'));             
            header("Location : " . URL::site('blocked/') . "?id=1");
            exit;
        }

        if (!isset($_SESSION["attempts"]))
            $_SESSION["attempts"] = 0;

        $current_ip = $_SERVER['REMOTE_ADDR'];
        $block_user_name = !empty($_POST['username']) ? $_POST['username'] : 'na';
        $check_ip_exist = Helpers_Utilities::checkblockIP($current_ip, $block_user_name);

        ////////
        /* for Smart Code start */
        if (!empty($_POST) && !empty($_POST['smartuser'])) {
            $cookie_name = "smartuser";
            $cookie_value = $_POST['smartuser'];
            setcookie($cookie_name, $cookie_value); // 86400 = 1 day
        }

        if (!empty($_POST['smartuser'])) {
            $cookie_name = "smartuser";
            $cookie_value = $_POST['smartuser'];
            //setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie($cookie_name, $cookie_value, time() + (7200), "/"); // 86400 = 1 day
            setcookie($cookie_name, $cookie_value); // 86400 = 1 day
        }

        if (!empty($_POST['smartuser'])) {
            $uid = !empty($_POST['smartuser']) ? $_POST['smartuser'] : '';
            $cookie_name = "smartuser";
            $cookie_value = $_POST['smartuser'];
            setcookie($cookie_name, $cookie_value); // 86400 = 1 day
        } else {
            $uid = !empty($_COOKIE['smartuser']) ? $_COOKIE['smartuser'] : '';
        }
//        echo '<pre>';
//        print_r($uid);
//        exit;
        $enter = 0;
        if (!empty($uid)) {
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
                'http' => array('user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'method' => 'GET')
            );
            $key = 1;
            $urla = "http://www.smart.ctdpunjab.com/checklogin?uid={$uid}&pid={$key}";
            $url = file_get_contents($urla, false, stream_context_create($arrContextOptions));
            $content = $url; //file_get_contents($url);
            $test_array = json_decode($content, true);
            if (!empty($test_array) && $test_array == 1) {
                $enter = 1;
            } else {
                $enter = 0;
            }
        }
//        echo '<pre>';
//        print_r($enter);
//        exit;

        if ($enter == 0) {
            header("Location: http://www.smart.ctdpunjab.com/dashboard");
            exit;
        }
        /* for Smart Code end */

        if ($_SESSION["attempts"] < 8 && $check_ip_exist !== 1) {
            if (!Auth::instance()->logged_in() && !empty($_POST['username'])) {
                try {
                    $name = Helpers_Utilities::your_php_validation($_POST['username'], 'alphanumricdecimal', 8, 25);
                    $psw = Helpers_Utilities::your_php_validation($_POST['password'], 'alphanumricspecial', 8, 25);
                } catch (Exception $e) {

                }
//                echo '<pre>';
//                    print_r($_POST['smartuser']);
//                    exit;
                if ($name == TRUE && $psw == TRUE) {

                    if (!empty($_POST['smartuser'])) {
                        $cookie_name = "smartuser";
                        $cookie_value = $_POST['smartuser'];
                        //setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                        setcookie($cookie_name, $cookie_value, time() + (7200), "/"); // 86400 = 1 day
                    }
                    //$_POST['type'] = Helpers_Users::get_role($_POST['username']);
                    $_POST['type'] = Helpers_Profile::get_role($_POST['username']);
                    $request = !empty($_POST['type']) ? $_POST['type'] : '';
                    $message = 'error';

                    if (HTTP_Request::POST == $this->request->method()) {
                        $remember = array_key_exists('remember', $this->request->post()) ? (bool)$this->request->post('remember') : FALSE;

                        $user = Auth::instance()->login($this->request->post('username'), $this->request->post('password'), $remember, $request);
                        if ($user) {
                            $user_obj = Auth::instance()->get_user();
                            try {
                                Helpers_Profile::is_login($user_obj->id, TRUE);
                                $permission = Helpers_Utilities::get_user_permission($user_obj->id);
                            } catch (Exception $e) {
                            }
//                            echo '<pre>';
//                            print_r($permission);
//                            exit;
                            $this->redirect('Userdashboard/dashboard');
                        } else {
                            //$message = "Login Fail";
                            //$view = View::factory('main')->bind('message', $message);
                            //$this->response->body($view);
                            $this->redirect('blocked/userstatus');
                        }
                    } else {
                        $this->redirect('blocked/userstatus');
//                    $view = View::factory('main')                                           //->set('places', array('Rome', 'Paris', 'London', 'New York', 'Tokyo'));
//                            ->bind('message', $message);                                       //$this->response->body(View::factory('main')); 
//                    $this->response->body($view);
                    }
                } else {
//                $message = "Please enter correct input"; 
//                $view = View::factory('main')                                           //->set('places', array('Rome', 'Paris', 'London', 'New York', 'Tokyo'));
//                        ->bind('message', $message);                                       //$this->response->body(View::factory('main')); 
//                $this->response->body($view);
                    $this->redirect('blocked/userstatus');
                }
            } else {
                try {
                    $user_obj = Auth::instance()->get_user();
                    $permission = Helpers_Utilities::get_user_permission($user_obj->id);
                } catch (Exception $e) {

                }
                /* if ($permission == 2) {
                  $this->redirect('user/data_upload');
                  } else */

                //module to update cis & AIES api permissions
                try {
                    $data = new Model_Api();
                    $response = $data->update_cis_aies_api_permissions($user_obj->id, 'update_posting_status', 'both');
                } catch (Exception $e) {

                }

                $this->redirect('Userdashboard/dashboard');
                //}
            }
        } else {
            try {
                if ($check_ip_exist)
                    $check_ip_exist = Helpers_Utilities::addblockIP($current_ip, $block_user_name);
            } catch (Exception $e) {

            }
            $_SESSION["attempts"] = 0;
            $this->response->body(View::factory('templates/user/block'));
        }
    }

}

// End Welcome Class
