<?php

$this->template->content = View::factory('templates/persons/cdr_summary')
        ->set("search_post",$post)
        ->bind('person_id', $pid);
