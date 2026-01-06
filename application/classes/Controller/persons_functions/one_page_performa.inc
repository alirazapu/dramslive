<?php

$this->template->content = View::factory('templates/persons/one_page_performa')
        ->set('person_data', $person_data)
        ->set('search_post', $post);