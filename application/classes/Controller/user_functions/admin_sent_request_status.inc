<?php

$this->template->content = View::factory('templates/user/admin_sent_request_status')
        ->bind('message' , $message)
        ->set('search_post', $post);