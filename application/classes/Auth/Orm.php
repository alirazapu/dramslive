<?php defined('SYSPATH') or die('No direct script access.');

class Auth_Orm extends Kohana_Auth_Orm
{
    /**
     * Compatibility layer:
     * - Session stores only user ID
     * - get_user() returns ORM object
     */
    public function get_user($default = NULL)
    {
        $user_id = $this->_session->get($this->_config['session_key']);

        if ( ! $user_id)
            return $default;

        // Return ORM object (same behavior as before)
        return ORM::factory('User', (int) $user_id);
    }
}
