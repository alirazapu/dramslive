<?php

$this->template->content = View::factory('templates/user/imei_against_sim')
                         ->set('search_post', $post) ;