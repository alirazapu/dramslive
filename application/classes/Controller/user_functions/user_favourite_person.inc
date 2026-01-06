<?php

$this->template->content = View::factory('templates/user/user_favourite_person')
        ->set('search_post', $post);