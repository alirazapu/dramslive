<?php

$this->template->content = View::factory('templates/user/request_status_telenor')
        ->bind('message' , $message)
        ->set('search_post', $post);