<?php

$this->template->content = View::factory('templates/user/bparty_search')
                         ->set('search_post', $post)
                         ->set('person_data', $person);