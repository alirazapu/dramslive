<?php

$this->template->content = View::factory('templates/user/changepassword')
        ->bind('data', $data);