<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {

	public function __construct()
	{
		// Override default min_length
		$this->_rules['username']['min_length'] = array(3);

		parent::__construct();
	}

	/**
	 * Validates signup information and creates a new user.
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function signup(array & $array)
	{
		// Validation setup
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('username', $this->_rules['username'])
			->rules('email', $this->_rules['email'])
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm']);

		// Add validation callbacks for all fields
		foreach ($this->_callbacks as $field => $callbacks)
		{
			foreach ($callbacks as $callback)
			{
				if (is_string($callback) AND strpos($callback, '::') === FALSE)
				{
					// Add $this object to the callback method,
					// which is why this whole callback loop is needed.
					$callback = array($this, $callback);
				}

				$array->callback($field, $callback);
			}
		}

		// Validate
		if ($status = $array->check())
		{
			// Add user
			$status = $this->values($array)->save();

			// Give user the "login" role
			$this->add('roles', ORM::factory('role', array('name' => 'login')));
		}

		return $status;
	}

	/**
	 * Confirms a user signup by validating the confirmation link.
	 *
	 * @param   integer  user id
	 * @param   string   confirmation code
	 * @return  boolean
	 */
	public function confirm($id, $code)
	{
		$this->find($id);

		if ( ! $this->loaded())
		{
			// Invalid user id
			return FALSE;
		}

		if ($this->confirmed)
		{
			// User is already confirmed
			return FALSE;
		}

		if ($code !== Auth::instance()->hash_password($this->email, Auth::instance()->find_salt($code)))
		{
			// Invalid confirmation code
			return FALSE;
		}

		// Confirm this account
		$this->confirmed = TRUE;
		$this->save();

		// Give the user the "user" role
		$this->add('roles', ORM::factory('role', array('name' => 'user')));

		return TRUE;
	}
}