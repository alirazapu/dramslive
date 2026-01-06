<?php

$this->template->content = View::factory('templates/persons/person_favourite_person')
                        ->set('search_post', $post)
                        ->bind('person_id', $pid);
