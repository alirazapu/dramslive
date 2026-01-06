<?php

$this->template->content = View::factory('templates/user/person_list')
        ->set('search_post', $post_data);