<?php

$this->template->content = View::factory('templates/user/user_dataupload_manual')
        ->set('search_post', $post);
