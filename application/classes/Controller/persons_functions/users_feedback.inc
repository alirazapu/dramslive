<?php

$this->template->content = View::factory('templates/persons/users_feedback')
        ->bind('user_id_feed', $login_user)
        ->bind('person_id_feed', $pid)
        ->bind('rowcount_feed', $rows_count);