<?php

$this->template->content = View::factory('templates/user/no_request_send_detail')
        ->set('search_post', $post);