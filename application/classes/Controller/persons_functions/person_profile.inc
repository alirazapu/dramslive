<?php

$this->template->content = View::factory('templates/persons/person_profile')
        ->bind('data', $data)
        ->bind('person_id', $pid);
