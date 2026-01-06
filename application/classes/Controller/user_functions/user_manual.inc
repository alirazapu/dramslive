<?php

$this->template->content = View::factory('templates/user/user_manual')
        ->set('search_post', $post);
