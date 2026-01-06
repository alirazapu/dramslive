<?php

$this->template->content = View::factory('templates/persons/shortcode_analysis')
                    ->set('search_post', $post)
                    ->bind('personid', $pid);