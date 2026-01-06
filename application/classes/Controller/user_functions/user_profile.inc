<?php

$this->template->content = View::factory('templates/user/user_profile')
        ->bind('data', $data)
        ->bind('user_log_info', $user_log_info);