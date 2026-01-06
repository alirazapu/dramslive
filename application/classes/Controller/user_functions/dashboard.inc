<?php

$this->template->content = View::factory('templates/user/dashboard')
        ->bind('role', $role)
        ->bind('user_id', $userid)
        ->bind('cnic_exist', $cnic_exist)
        ->bind('password_change_required', $password_change_required);