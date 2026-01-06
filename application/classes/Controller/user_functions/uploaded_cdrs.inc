<?php

$this->template->content = View::factory('templates/user/uploaded_cdrs')
        ->bind('message' , $message);