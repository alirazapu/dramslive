<?php

$this->template->content = View::factory('templates/persons/add_social_link')
        ->bind('person_id', $pid);
