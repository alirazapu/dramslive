<?php
//echo $pid; exit;
$this->template->content = View::factory('templates/persons/person_dashboard')
         ->bind('person_id', $pid);
