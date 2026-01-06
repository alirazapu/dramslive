<?php

$this->template->content = View::factory('templates/user/breakup_report')
        ->set('search_post', $post);