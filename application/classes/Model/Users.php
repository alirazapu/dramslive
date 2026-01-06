<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Users extends Model_Auth_User {

	public function rules()
	{
		return array(
			'username' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
				array('max_length', array(':value', 32)),
                array(array($this, 'username_available')),
			),
			'email' => array(
				
			),
			'password' => array(),
		);
		
		/*return array(
			'username' => array(
				'not_empty' => 'You must provide a username.',
				'min_length' => 'The username must be at least :param2 characters long.',
				'max_length' => 'The username must be less than :param2 characters long.',
				'username_available' => 'This username is already in use.',
			),
			'email' => array(
				'not_empty' => 'You must enter an email address',
				'min_length' => 'This email is too short, it must be at least :param2 characters long',
				'max_length' => 'This email is too long, it cannot exceed :param2 characters',
				'email' =>	'Please enter valid email address',
				'email_available' => 'This email address is already in use.',
			)
		);*/
	}
	
	public function username_available($username)
    {
        // There are simpler ways to do this, but I will use ORM for the sake of the example
        return ORM::factory('users', array('username' => $username))->loaded();
    }
    
   

} // End User Model