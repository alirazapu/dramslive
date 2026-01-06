<?php

$this->template->content = View::factory('templates/user/call_analysis')
        ->set('search_post', $post_data);