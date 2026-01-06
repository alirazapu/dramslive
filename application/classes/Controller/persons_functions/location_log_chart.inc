<?php

$this->template->content = View::factory('templates/persons/location_log_chart')
        ->set('search_post', $post)
        ->bind('person_id', $pid);
