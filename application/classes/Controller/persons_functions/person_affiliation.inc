<?php

$this->template->content = View::factory('templates/persons/person_affiliation')
                        ->set('search_post', $post)
                        ->bind('person_id', $pid);
