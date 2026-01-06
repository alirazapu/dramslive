<?php

$this->template->content = View::factory('templates/user/telco_reports')
   /* $this->template->content = View::factory('templates/user/new_page')*/
        ->set('search_post', $post);