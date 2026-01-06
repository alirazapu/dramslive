<?php

$this->template->content = View::factory('templates/user/rejected_requests')
        ->bind('message' , $message);