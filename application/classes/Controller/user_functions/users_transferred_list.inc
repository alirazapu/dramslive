<?php

$this->template->content = View::factory('templates/user/users_transferred_list')
        ->set('search_post', $post);