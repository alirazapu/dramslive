<?php

$this->template->content = View::factory('templates/user/panel_log')
        ->set('search_post', $post);
