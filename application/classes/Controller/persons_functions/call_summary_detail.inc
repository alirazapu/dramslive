<?php

$this->template->content = View::factory('templates/persons/call_summary_detail')
                        ->set('search_post', $post)
                        ->bind('person_id' , $pid);
