<?php

$this->template->content = View::factory('templates/user/project_persons')
        ->set('search_post', $post_data);