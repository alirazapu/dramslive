<?php

$this->template->content = View::factory('templates/user/top_search_person')
        ->set('search_post', $post);