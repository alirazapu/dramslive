<?php

$result = Helpers_Inneruse::get_gmail_pw();
//print_r($result); exit;
$username = $result['receive']['user'];
$password = $result['receive']['password'];

?>