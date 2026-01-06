<?php

$this->template->content = View::factory('templates/user/parsing_queue')
        ->set('search_post', $post);
