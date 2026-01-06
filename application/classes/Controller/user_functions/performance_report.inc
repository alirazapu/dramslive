<?php

$this->template->content = View::factory('templates/user/performance_report')
        ->set('search_post', $post);