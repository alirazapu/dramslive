<?php

$this->template->content = View::factory('templates/persons/sms_summary')->set('sms_summary', $post)
                    ->set('search_post', $post)
                    ->bind('person_id', $pid);
