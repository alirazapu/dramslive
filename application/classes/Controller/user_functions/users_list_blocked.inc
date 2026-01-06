<?php

$this->template->content = View::factory('templates/user/users_list_blocked')
              ->set('search_post', $post);