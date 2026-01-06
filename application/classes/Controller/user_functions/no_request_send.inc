<?php

$this->template->content = View::factory('templates/user/no_request_send')
        ->set('search_post', $post);