<?php

$this->template->content = View::factory('templates/user/request_status')
        ->bind('message' , $message)
        ->set('search_post', $post);