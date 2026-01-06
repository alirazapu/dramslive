<?php

$this->template->content = View::factory('templates/persons/call_summary')
                    ->set('search_post', $post)
                    ->bind('personid', $pid);