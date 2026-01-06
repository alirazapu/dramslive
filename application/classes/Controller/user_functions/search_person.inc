<?php

$this->template->content = View::factory('templates/user/search_person')
                         ->set('search_post', $post);