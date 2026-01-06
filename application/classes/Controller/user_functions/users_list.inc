<?php

$this->template->content = View::factory('templates/user/users_list')
        ->set('search_post', $post);