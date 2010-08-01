<?php defined('SYSPATH') or die('No direct access allowed.');

class Auth_ORM extends Kohana_Auth_ORM {

	// Array with all available OAuth providers for this website.
	// Each provider should have an id field in the users table: 'provider_id'.
	protected $oauth_providers = array('twitter');

	/**
	 * Checks if a user logged in via an OAuth provider.
	 *
	 * @param   string   provider name (e.g. 'twitter', 'google', etc.)
	 * @return  boolean
	 */
	public function logged_in_oauth($provider = NULL)
	{
		// For starters, the user needs to be logged in
		if ( ! parent::logged_in())
			return FALSE;

		// Get the user from the session.
		// Because parent::logged_in returned TRUE, we know this is a valid user ORM object.
		$user = $this->get_user();

		if ($provider !== NULL)
		{
			// Check for one specific OAuth provider
			$provider = $provider.'_id';
			return ! empty($user->$provider);
		}

		// Otherwise, just check the password field.
		// We don't store passwords for OAuth users.
		return empty($user->password);
	}

}