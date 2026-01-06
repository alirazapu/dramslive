<?php

$this->template->content = View::factory('templates/persons/person_devices')->set('person_devices_post', $post)
                    ->bind('person_id', $pid);
