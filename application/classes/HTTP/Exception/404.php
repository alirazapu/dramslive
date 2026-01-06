<?php defined('SYSPATH') or die('No direct script access.');



class HTTP_Exception_404 extends Kohana_HTTP_Exception_404 {
 
    /**
     * Generate a Response for the 401 Exception.
     * 
     * The user should be redirect to a login page.
     * 
     * @return Response
     */
    public function get_response()
    {
        $response = Response::factory()
            ->status(404)
            ->headers('Location', URL::site('user/error_page')); 
        return $response; 
    }
}

