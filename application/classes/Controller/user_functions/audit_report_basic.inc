<?php

$this->template->content = View::factory('templates/user/audit_report_basic')
        ->set('search_post', $search_post);