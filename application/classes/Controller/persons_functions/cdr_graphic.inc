<?php

$this->template->content = View::factory('templates/persons/cdr_graphic')
                        ->bind('person_id', $pid);
