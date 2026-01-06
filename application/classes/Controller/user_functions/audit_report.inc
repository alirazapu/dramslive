<?php

$this->template->content = View::factory('templates/user/audit_report')
        ->set('search_post', $post);