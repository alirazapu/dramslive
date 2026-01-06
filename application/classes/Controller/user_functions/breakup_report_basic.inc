<?php

$this->template->content = View::factory('templates/user/breakup_report_basic')
        ->set('search_post', $search_post);