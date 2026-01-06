<?php

$this->template->content = View::factory('templates/user/msisdn_breakup_report')
        ->set('search_post', $post);