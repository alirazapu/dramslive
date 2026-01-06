<?php

$this->template->content = View::factory('templates/user/watchlist/add_watch_list')
        ->set('search_post', $post);

