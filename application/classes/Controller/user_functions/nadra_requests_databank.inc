<?php

$this->template->content = View::factory('templates/user/nadra_requests_databank')
        ->bind('message' , $message)
        ->set('search_post', $post);