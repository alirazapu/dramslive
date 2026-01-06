<?php

$this->template->content = View::factory('templates/user/users_favourite_agent')
        ->set('search_post', $post)
        ->bind('user_id', $id);