<?php

$this->template->content = View::factory('templates/user/search_identity')
                         ->set('search_post', $post);