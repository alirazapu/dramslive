<?php

$this->template->content = View::factory('templates/persons/cell_log_summary')
        ->set('search_post', $post)
        ->bind('person_id', $pid);
