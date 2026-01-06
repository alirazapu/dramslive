<?php

$this->template->content = View::factory('templates/persons/person_sims')
                    ->set('person_sims_post', $post)
                    ->bind('person_id', $pid);
