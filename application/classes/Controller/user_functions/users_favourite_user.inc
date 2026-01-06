<?php

$this->template->content = View::factory('templates/user/users_favourite_user')
        ->set('search_post', $post);