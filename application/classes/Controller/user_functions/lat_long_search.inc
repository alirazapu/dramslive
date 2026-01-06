<?php

$this->template->content = View::factory('templates/user/lat_long_search')
                         ->set('search_post', $post) ;