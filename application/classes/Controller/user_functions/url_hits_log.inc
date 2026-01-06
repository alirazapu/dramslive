<?php

$this->template->content = View::factory('templates/user/url_hits_log')
        ->set('search_post', $post);
