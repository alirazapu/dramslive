<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


defined('SYSPATH') or die('No direct script access.');

class Controller_Blocked extends Controller {
    
    public function action_index() {
        $this->response->body(View::factory('templates/user/block'));
    }
    public function action_userstatus() {
        $this->response->body(View::factory('templates/user/wronguser'));
    }
}