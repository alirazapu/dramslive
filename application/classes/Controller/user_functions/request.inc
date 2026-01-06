<?php

//$this->template->content = View::factory('templates/user/access_denied');



$this->template->content = View::factory('templates/user/request')
        ->bind('id', $id) 
        ->bind('pid', $pid)
        ->bind('post', $post);


