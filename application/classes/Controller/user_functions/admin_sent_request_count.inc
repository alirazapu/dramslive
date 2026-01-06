<?php

$this->template->content = View::factory('templates/user/admin_sent_request_count')
        ->bind('message' , $message)
        ->set('search_post', $post);