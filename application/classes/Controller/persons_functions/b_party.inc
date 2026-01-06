<?php

$this->template->content = View::factory('templates/persons/b_party')
        ->set("search_post",$post)
        ->bind('person_id', $pid);
