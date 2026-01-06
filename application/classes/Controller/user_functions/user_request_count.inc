<?php

$this->template->content = View::factory('templates/user/user_request_count')
        ->bind('message' , $message)
        ->set('search_post', $post);