<?php

$tab = isset($_GET['tab'])?$_GET['tab']:1;
$this->template->content = View::factory('templates/user/data_upload')
        ->set('post_data', $data)
        ->set('tab',$tab);