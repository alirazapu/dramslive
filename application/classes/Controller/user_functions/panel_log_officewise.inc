<?php

$this->template->content = View::factory('templates/user/panel_log_officewise')
        ->set('search_post', $post);
