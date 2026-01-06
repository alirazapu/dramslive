<?php

$this->template->content = View::factory('templates/persons/links_with_other_portals')
        ->set('search_post', $post)
        ->bind('person_id', $pid);
