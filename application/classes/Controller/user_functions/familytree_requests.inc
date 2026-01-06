<?php

$this->template->content = View::factory('templates/user/familytree_requests')
        ->bind('message' , $message)
        ->set('search_post', $post);