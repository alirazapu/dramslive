<?php

$this->template->content = View::factory('templates/persons/location_call_log')
        ->set('search_post', $post)                
        ->bind('person_id', $pid);
