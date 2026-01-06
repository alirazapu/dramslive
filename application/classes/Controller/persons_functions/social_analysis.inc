<?php

$this->template->content = View::factory('templates/persons/social_analysis')
        ->set('search_post', $post)
        ->bind('person_id', $pid);
