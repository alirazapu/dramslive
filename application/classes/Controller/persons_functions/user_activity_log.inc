<?php

// Eager-fetch every user_request and admin_request row attributable to
// this person so the view can render the new "Request Information &
// Attachments" panel without an extra AJAX round-trip. The volume per
// person is small (typically <50 requests), so a single PHP-side query
// pair is cheaper than wiring a DataTables AJAX endpoint for it.
$person_requests = Helpers_Person::get_person_requests($pid);

$this->template->content = View::factory('templates/persons/user_activity_log')
        ->set('search_post', $post)
        ->set('person_requests', $person_requests)
        ->bind('person_id', $pid);
