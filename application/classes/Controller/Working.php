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

        $this->role_id = Helpers_Utilities::get_user_role_id($user->id);
      //  Session::instance('native');
        if (!empty($user->id))
            Helpers_Utilities::inactive_user($user->id);

        if (!$user) {
            Session::instance()->regenerate();
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
