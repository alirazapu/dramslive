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
                $this->start_single_session($user);

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
            $name = FALSE;
            $psw = FALSE;

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

            // Look up the account being attempted (if any) so wrong-password
            // attempts count toward the per-account lockout below, even when
            // the submitted value fails the format check further down - it's
            // still a wrong-password attempt against a real account either way.
            $login_account = (!empty($_POST['username']) && $_POST['username'] !== 'na')
                ? ORM::factory('User')->where('username', '=', $_POST['username'])->find()
                : ORM::factory('User');

            if ($login_account->loaded() && (int)$login_account->is_active === 0) {
                $message = "Your account has been disabled due to multiple failed login attempts. Please contact the administrator.";
                $view = View::factory('main')->bind('message', $message);
                $view->account_disabled = TRUE;
                $view->roles = Helpers_Utilities::get_roles_data();
                $this->response->body($view);
                return;
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

                    // Remember-me is not honoured until after OTP verification below,
                    // so the autologin cookie can't be used to skip the OTP step.
                    $user = Auth::instance()->login($this->request->post('username'), $this->request->post('password'), FALSE, $request);
                    if ($user) {

                        $user_obj = Auth::instance()->get_user();
                        if ($user_obj) {
                            $user_obj = ORM::factory('User', $user_obj);
                        }

                        if ($login_account->loaded() && (int)$login_account->failed_login_attempts !== 0) {
                            $login_account->failed_login_attempts = 0;
                            $login_account->save();
                        }

                        $public_ip = filter_var($this->request->post('public_ip'), FILTER_VALIDATE_IP) ?: NULL;
                        $geo = Helpers_Utilities::validate_geo_coordinates($this->request->post('geo_lat'), $this->request->post('geo_lng'), $this->request->post('geo_accuracy'));

                        // Password was correct, but log back out immediately: the
                        // user isn't signed in until the WhatsApp OTP is verified
                        // (see action_otp / action_verify_otp below).
                        Auth::instance()->logout();

                        // OTP is a per-account opt-in (users.is_login_otp_enabled).
                        // Accounts without it keep the original login behaviour.
                        if ((int)$user_obj->is_login_otp_enabled !== 1) {
                            $this->complete_login_and_redirect($user_obj, $remember, $public_ip, $geo);
                            return;
                        }

                        $otp = Helpers_Whatsapp::generate_otp();

                        Session::instance()->set('otp_pending', array(
                            'user_id'     => $user_obj->id,
                            'code_hash'   => hash('sha256', $otp),
                            'expires'     => time() + (int)Kohana::$config->load('whatsapp')->get('otp_ttl', 300),
                            'attempts'    => 0,
                            'resend_at'   => time() + (int)Kohana::$config->load('whatsapp')->get('resend_cooldown', 30),
                            'resends'     => 0,
                            'remember'    => $remember,
                            'public_ip'   => $public_ip,
                            'geo'         => $geo,
                        ));

                        $delivery = $this->deliver_otp($user_obj, $otp);

                        if ($delivery['status']) {
                            Session::instance()->set('otp_channel', $delivery['channel']);
                            $this->redirect('login/otp');
                        }

                        // Neither WhatsApp nor e-mail could deliver the code. The
                        // account has OTP enabled, so we must NOT sign the user in.
                        Session::instance()->delete('otp_pending');
                        $message = "We could not send your verification code. Please try again or contact the administrator.";
                        $view = View::factory('main')->bind('message', $message);
                        $view->roles = Helpers_Utilities::get_roles_data();
                        $this->response->body($view);
                    } else {
                        $_SESSION["attempts"] = $_SESSION["attempts"] + 1;
                        $message = $login_account->loaded() ? $this->register_failed_login($login_account) : "Login Fail";

                        $view = View::factory('main')->bind('message', $message);
                        $view->account_disabled = $login_account->loaded() && (int)$login_account->is_active === 0;
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
                $message = $login_account->loaded() ? $this->register_failed_login($login_account) : "Please enter correct input";
                $view = View::factory('main')                                           //->set('places', array('Rome', 'Paris', 'London', 'New York', 'Tokyo'));
                ->bind('message', $message);                                       //$this->response->body(View::factory('main'));
                $view->account_disabled = $login_account->loaded() && (int)$login_account->is_active === 0;
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
            //$urla = "http://www.smart.ctdpunjab.com/checklogin?uid={$uid}&pid={$key}";
            //$url = file_get_contents($urla, false, stream_context_create($arrContextOptions));
            //$content = $url; //file_get_contents($url);
            //$test_array = json_decode($content, true);
            /*if (!empty($test_array) && $test_array == 1) {
                $enter = 1;
            } else*/
            {
                $enter = 0;
            }
        }
//        echo '<pre>';
//        print_r($enter);
//        exit;

        if ($enter == 0) {
          //  header("Location: http://www.smart.ctdpunjab.com/dashboard");
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
                            $this->start_single_session($user_obj);
                            try {
                                $public_ip = filter_var($this->request->post('public_ip'), FILTER_VALIDATE_IP) ?: NULL;
                                $geo = Helpers_Utilities::validate_geo_coordinates($this->request->post('geo_lat'), $this->request->post('geo_lng'), $this->request->post('geo_accuracy'));
                                Helpers_Profile::is_login($user_obj->id, TRUE, $public_ip, $geo['lat'], $geo['lng'], $geo['accuracy']);
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

    /* WhatsApp OTP verification (second factor after password) */

    public function action_otp()
    {
        $pending = Session::instance()->get('otp_pending');

        if (empty($pending)) {
            $this->redirect('login');
        }

        $message = Session::instance()->get_once('otp_message');
        $seconds_left = max(0, $pending['expires'] - time());
        $channel = Session::instance()->get('otp_channel', 'whatsapp');
        $resend_in = max(0, (int)Arr::get($pending, 'resend_at', 0) - time());

        $view = View::factory('templates/user/otp')
            ->bind('message', $message)
            ->bind('channel', $channel)
            ->bind('resend_in', $resend_in)
            ->bind('seconds_left', $seconds_left);
        $this->response->body($view);
    }

    public function action_verify_otp()
    {
        $pending = Session::instance()->get('otp_pending');

        if (empty($pending)) {
            $this->redirect('login');
        }

        if (HTTP_Request::POST != $this->request->method()) {
            $this->redirect('login/otp');
        }

        if (time() > $pending['expires']) {
            Session::instance()->delete('otp_pending');
            Session::instance()->set('error_message', 'Your OTP has expired. Please log in again.');
            $this->redirect('login');
        }

        $entered = trim((string)$this->request->post('otp'));

        if ($entered === '' || !hash_equals((string)$pending['code_hash'], hash('sha256', $entered))) {
            $pending['attempts']++;

            if ($pending['attempts'] >= 5) {
                Session::instance()->delete('otp_pending');
                Session::instance()->set('error_message', 'Too many incorrect attempts. Please log in again.');
                $this->redirect('login');
            }

            Session::instance()->set('otp_pending', $pending);
            Session::instance()->set('otp_message', 'Incorrect code. Please try again.');
            $this->redirect('login/otp');
        }

        $user_obj = ORM::factory('User', $pending['user_id']);

        Session::instance()->delete('otp_pending');

        $this->complete_login_and_redirect($user_obj, $pending['remember'], $pending['public_ip'], $pending['geo']);
    }

    public function action_resend_otp()
    {
        $pending = Session::instance()->get('otp_pending');

        if (empty($pending)) {
            $this->redirect('login');
        }

        $config = Kohana::$config->load('whatsapp');
        $max_resends = (int)$config->get('max_resends', 3);
        $cooldown = (int)$config->get('resend_cooldown', 30);

        // Server-side cooldown: the disabled button in the view is only a hint.
        if (isset($pending['resend_at']) && time() < $pending['resend_at']) {
            Session::instance()->set('otp_message', 'Please wait a moment before requesting another code.');
            $this->redirect('login/otp');
        }

        if ((int)Arr::get($pending, 'resends', 0) >= $max_resends) {
            Session::instance()->delete('otp_pending');
            Session::instance()->set('error_message', 'Too many code requests. Please log in again.');
            $this->redirect('login');
        }

        $user_obj = ORM::factory('User', $pending['user_id']);
        $otp = Helpers_Whatsapp::generate_otp();

        // Issuing a new code replaces the old one and resets the guess counter.
        $pending['code_hash'] = hash('sha256', $otp);
        $pending['expires']   = time() + (int)$config->get('otp_ttl', 300);
        $pending['attempts']  = 0;
        $pending['resends']   = (int)Arr::get($pending, 'resends', 0) + 1;
        $pending['resend_at'] = time() + $cooldown;
        Session::instance()->set('otp_pending', $pending);

        $delivery = $this->deliver_otp($user_obj, $otp);

        if ($delivery['status']) {
            Session::instance()->set('otp_channel', $delivery['channel']);
        }

        Session::instance()->set('otp_message', $delivery['status'] ? 'A new code has been sent.' : 'Failed to resend code, please try again.');

        $this->redirect('login/otp');
    }

    /**
     * Deliver a login OTP: WhatsApp first, e-mail as fallback.
     *
     * E-mail is attempted ONLY when WhatsApp did not actually deliver -
     * missing number, a failed gateway health check, a network/timeout
     * failure, or a provider-level rejection. The SAME code is used for
     * both channels so a late-arriving WhatsApp message stays valid.
     *
     * Any WhatsApp-side failure also triggers a (throttled) alert e-mail to
     * the address in config 'alert_email', so the gateway going dark is
     * noticed instead of quietly costing every user the slower channel.
     *
     * @return array ['status' => bool, 'channel' => 'whatsapp'|'email'|NULL]
     */
    private function deliver_otp($user_obj, $otp)
    {
        $profile = Helpers_Profile::get_user_perofile($user_obj->id);
        $mobile_number = !empty($profile->mobile_number) ? $profile->mobile_number : NULL;

        if ($mobile_number) {
            // Ask the gateway whether it is alive before handing it a code.
            // A dead API answers /api/send/whatsapp with a plausible-looking
            // HTTP 200, so probing /api/get/devices first is what actually
            // tells us the account is still usable.
            $gateway = Helpers_Whatsapp::check_gateway();

            if (empty($gateway['status'])) {
                $reason = Arr::get($gateway, 'message', 'unknown error');
                $this->log_otp_failure('WhatsApp', $user_obj->id, 'gateway check failed - ' . $reason);
                Helpers_Whatsapp::notify_gateway_down($reason);
            } else {
                $sent = Helpers_Whatsapp::send_otp($mobile_number, $otp);

                if (!empty($sent['status'])) {
                    return array('status' => TRUE, 'channel' => 'whatsapp');
                }

                // The check passed but the send did not, so the gateway is
                // still broken from the user's point of view - same alert.
                $reason = Arr::get($sent, 'message', 'unknown error');
                $this->log_otp_failure('WhatsApp', $user_obj->id, $reason);
                Helpers_Whatsapp::notify_gateway_down('Send failed: ' . $reason);
            }
        } else {
            $this->log_otp_failure('WhatsApp', $user_obj->id, 'no mobile number on file');
        }

        // --- Fallback: e-mail ---
        if (empty($user_obj->email)) {
            $this->log_otp_failure('Email', $user_obj->id, 'no e-mail address on file');
            return array('status' => FALSE, 'channel' => NULL);
        }

        $ttl_minutes = max(1, (int)round(Kohana::$config->load('whatsapp')->get('otp_ttl', 300) / 60));
        $unit = $ttl_minutes === 1 ? 'minute' : 'minutes';

        $body = '<p>Your DRAMS login verification code is: <b>' . HTML::chars($otp) . '</b></p>'
              . '<p>This code expires in ' . $ttl_minutes . ' ' . $unit . '. Do not share it with anyone.</p>';

        try {
            $result = Helpers_Email::send_email($user_obj->email, $user_obj->username, 'DRAMS login verification code', $body);
        } catch (Exception $e) {
            $this->log_otp_failure('Email', $user_obj->id, $e->getMessage());
            return array('status' => FALSE, 'channel' => NULL);
        }

        if ((int)$result === 1) {
            return array('status' => TRUE, 'channel' => 'email');
        }

        $this->log_otp_failure('Email', $user_obj->id, 'SMTP send returned ' . var_export($result, TRUE));

        return array('status' => FALSE, 'channel' => NULL);
    }

    /**
     * Record a failed OTP delivery. Never logs the code itself.
     */
    private function log_otp_failure($channel, $user_id, $reason)
    {
        try {
            Kohana::$log->add(Log::ERROR, 'OTP delivery failed via :channel for user :user - :reason', array(
                ':channel' => $channel,
                ':user'    => $user_id,
                ':reason'  => $reason,
            ));
        } catch (Exception $e) {
        }
    }

    /**
     * Restrict the account to a single active browser session: mint a fresh
     * token, store it on the user row, and stash it in this session. Any
     * other session carrying an older token gets kicked out on its next
     * request (see Controller_Working::before()).
     */
    private function start_single_session($user_obj)
    {
        $token = bin2hex(random_bytes(32));

        $user_obj->current_session_token = $token;
        $user_obj->save();

        Session::instance()->set('session_token', $token);
    }

    /**
     * Count a wrong-password attempt against an account; disable it
     * (is_active = 0) once it reaches 3. Returns the flash message to show
     * on the login page, including how many attempts are left.
     */
    private function register_failed_login($login_account)
    {
        $login_account->failed_login_attempts = (int)$login_account->failed_login_attempts + 1;

        if ($login_account->failed_login_attempts >= 3) {
            $login_account->is_active = 0;
            $login_account->save();

            return "Your account has been disabled due to multiple failed login attempts. Please contact the administrator.";
        }

        $login_account->save();

        $remaining = 3 - $login_account->failed_login_attempts;
        return "Login Fail. {$remaining} attempt(s) remaining before your account is locked.";
    }

    /**
     * Finish logging a user in after OTP verification (or immediately, for
     * users without a WhatsApp number on file): force the auth session,
     * recreate the remember-me autologin cookie if requested, record the
     * login, and redirect to the dashboard.
     */
    private function complete_login_and_redirect($user_obj, $remember, $public_ip, $geo)
    {
        if (!Auth::instance()->logged_in()) {
            Auth::instance()->force_login($user_obj);
        }

        $this->start_single_session($user_obj);

        if ($remember === TRUE) {
            $auth_config = Kohana::$config->load('auth');
            $token = ORM::factory('User_Token')->values(array(
                'user_id'    => $user_obj->pk(),
                'expires'    => time() + $auth_config->get('lifetime'),
                'user_agent' => sha1(Request::$user_agent),
            ))->create();
            Cookie::set('authautologin', $token->token, $auth_config->get('lifetime'));
        }

        try {
            Helpers_Profile::is_login($user_obj->id, TRUE, $public_ip, $geo['lat'], $geo['lng'], $geo['accuracy']);
            Helpers_Utilities::get_user_permission($user_obj->id);
        } catch (Exception $e) {

        }

        $this->redirect('Userdashboard/dashboard');
    }

}

// End Welcome Class
