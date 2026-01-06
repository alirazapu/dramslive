<?php

$this->template->content = View::factory('templates/user/request_data')
        ->set('search_post', $post);
