<?php

$this->template->content = View::factory('templates/user/admin_reports')
        ->set('search_post', $post);