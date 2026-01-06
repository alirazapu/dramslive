<?php

$this->template->content = View::factory('templates/user/no_record_search')
        ->set('search_post', $post);