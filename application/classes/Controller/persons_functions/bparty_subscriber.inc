<?php

$this->template->content = View::factory('templates/persons/bparty_subscriber')
        ->set("search_post",$post)
        ->bind('person_id', $pid);
