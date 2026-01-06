<?php

$this->template->content = View::factory('templates/user/access_control_list')
        
        ->set('search_post', $post);

