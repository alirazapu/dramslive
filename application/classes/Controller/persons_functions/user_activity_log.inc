<?php

$this->template->content = View::factory('templates/persons/user_activity_log')
        ->set('search_post', $post)
        ->bind('person_id', $pid);
