<?php

$tab = isset($_GET['tab'])?$_GET['tab']:1;
$this->template->content = View::factory('templates/user/up_against_imei')
        ->set('post_data', $data)
        ->set('tab',$tab);