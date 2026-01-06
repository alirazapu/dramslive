<?php

$this->template->content = View::factory('templates/user/request_schedular')
        ->set('search_post', $post);
