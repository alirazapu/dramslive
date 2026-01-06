<?php

$this->template->content = View::factory('templates/user/user_person_list')
        ->set('search_post', $post)
        ->bind('user_id', $id);