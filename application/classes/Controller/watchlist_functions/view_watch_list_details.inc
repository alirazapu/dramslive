<?php

$this->template->content = View::factory('templates/user/watchlist/view_watch_list_details')
        ->set('search_post', $post);

