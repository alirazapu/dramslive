<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Working extends Controller_Template {

    public $role_id;
    public function before() {

        //flag for request id generator
        if (!isset($GLOBALS['id_generator'])) {
            $GLOBALS['id_generator'] = 0;
        }

        $current_ip = $_SERVER['REMOTE_ADDR'];
        $check_ip_exist = Helpers_Utilities::checkblockIPforever($current_ip);
        if ($check_ip_exist == 1) {
            header("Location : ".URL::site('blocked/')."?id=1");
            exit;
            //$this->response->body(View::factory('templates/user/block'));
        }
        /*
          $result = Helpers_Utilities::setwetcookies();
          if($result==1)
          $this->redirect('errors'); */
        //block query string 
        //Helpers_Layout::get_query_string();

        parent::before();

        // Authenticated pages must never be served from the browser's disk
        // cache or back/forward cache - otherwise a kicked-out session (see
        // the single-session check below) can still appear "logged in" from
        // a stale cached render that never re-hits the server.
        $this->response->headers('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $this->response->headers('Pragma', 'no-cache');

        $controller = $this->request->controller();
        $action = $this->request->action();
        $data1 = 'yasers22';
        $this->template = View::factory('template');
        View::bind_global('menu_name', $action);
        $user = Auth::instance()->get_user();
							if ($user)
							{
								$user = ORM::factory('User', $user);
							}
        if(isset($user->id)) {
            // Single-session enforcement: a newer login elsewhere overwrites
            // current_session_token, which invalidates this session here.
            $session_token = Session::instance()->get('session_token');
            if (empty($session_token) || $session_token !== $user->current_session_token) {
                // logout(FALSE, ...) only clears the auth key and regenerates
                // the session id - logout(TRUE, ...) would fully destroy the
                // session and silently drop the flash message set below.
                Auth::instance()->logout(FALSE, TRUE);
                Session::instance()->set('error_message', 'You have been logged out because your account was signed in from another browser or device.');
                $this->redirect('login');
            }

            $this->role_id = Helpers_Utilities::get_user_role_id($user->id);
            //  Session::instance('native');
            if (!empty($user->id))
                Helpers_Utilities::inactive_user($user->id);

            if (!$user) {
                Session::instance()->regenerate();
                $this->redirect();
            }
        }else{
            $this->redirect();
        }
    }

    public function after() {

        if (!isset($this->template->content)) {
            $this->template->content = '';
            $this->auto_render = FALSE;
        }
        return parent::after();
    }

}
