<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {

	/**
	 * @return  void
	 */
	public function __construct()
	{
		// Override default min_length
		$this->_rules['username']['min_length'] = array(3);

		parent::__construct();
	}

	/**
	 * Validates login information and logs a user in.
	 *
	 * @param   array    values to check
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	public function login(array & $data, $remember = FALSE)
	{
		$data = Validate::factory($data)
			->filter(TRUE, 'trim')
			->rules('username', $this->_rules['username'])
			->rules('password', $this->_rules['password']);

		if ( ! $data->check())
			return FALSE;

		// Attempt to load the user
		$this->where('username', '=', $data['username'])->find();

		// Login is successful
		if ($this->loaded() AND Auth::instance()->login($this, $data['password'], $remember))
			return TRUE;

		// Manually add error for non-existing usernames
		$data->error('username', 'invalid');

		return FALSE;
	}

	/**
	 * Sign-up: step 1.
	 * Validates sign-up information and creates a new user with the "login" role only.
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function signup(array & $data)
	{
		$data = Validate::factory($data)
			->filter(TRUE, 'trim')
			->rules('username', $this->_rules['username'])
			->rules('email', $this->_rules['email'])
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm'])
			->callback('username', array($this, 'username_available'))
			->callback('email', array($this, 'email_available'));

		if ( ! $data->check())
			return FALSE;

		// Add user
		$this->values($data)->save();

		// Give user the "login" role
		$this->add('roles', ORM::factory('role', array('name' => 'login')));

		// Create e-mail body with account confirmation link
		$body = View::factory('email/confirm_signup', $this->as_array())
			->set('url', URL::site(
				Route::get('user')->uri(array('action' => 'confirm_signup')).
				'?id='.$this->id.'&token='.Auth::instance()->hash_password($this->email),
				TRUE // Add protocol to URL
			));

		// Get the email configuration
		$config = Kohana::config('email');

		// Load Swift Mailer
		require_once Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

		// Create an email message
		$message = Swift_Message::newInstance()
			->setSubject('KohanaJobs Sign-up')
			->setFrom(array(Kohana::config('site.email') => 'KohanaJobs.com'))
			->setTo(array($this->email => $this->username))
			->setBody($body);

		// Connect to the server
		$transport = Swift_SmtpTransport::newInstance($config->server)
			->setUsername($config->username)
			->setPassword($config->password);

		// Send the message
		Swift_Mailer::newInstance($transport)->send($message);

		return TRUE;
	}

	/**
	 * Sign-up: step 2.
	 * Confirms a user sign-up by validating the confirmation link.
	 * Adds the "user" role to the user.
	 *
	 * @param   integer  user id
	 * @param   string   confirmation token
	 * @return  boolean
	 */
	public function confirm_signup($id, $token)
	{
		// Don't even bother, save us the user lookup query
		if (empty($id) OR empty($token))
			return FALSE;

		// Load user by id
		$this->find($id);

		// Invalid user id
		if ( ! $this->loaded())
			return FALSE;

		// Invalid confirmation token
		if ($token !== Auth::instance()->hash_password($this->email, Auth::instance()->find_salt($token)))
			return FALSE;

		// User is already confirmed.
		// We're not showing an error message.
		if ($this->has('roles', ORM::factory('role', array('name' => 'user'))))
			return TRUE;

		// Give the user the "user" role
		$this->add('roles', ORM::factory('role', array('name' => 'user')));

		return TRUE;
	}

	/**
	 * Reset password: step 1.
	 * The form where a user enters the email address he signed up with.
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function reset_password(array & $data)
	{
		$data = Validate::factory($data)
			->filter(TRUE, 'trim')
			->rules('email', $this->_rules['email'])
			->callback('email', array($this, 'email_not_available'));

		if ( ! $data->check())
			return FALSE;

		// Load user data
		$this->where('email', '=', $data['email'])->find();

		// Create e-mail body with reset password link
		$time = time();
		$body = View::factory('email/confirm_reset_password', $this->as_array())
			->set('time', $time)
			->set('url', URL::site(
				Route::get('user')->uri(array('action' => 'confirm_reset_password')).
				'?id='.$this->id.'&token='.Auth::instance()->hash_password($this->email.'+'.$this->password.'+'.$time).'&time='.$time,
				TRUE // Add protocol to URL
			));

		// Get the email configuration
		$config = Kohana::config('email');

		// Load Swift Mailer
		require_once Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

		// Create an email message
		$message = Swift_Message::newInstance()
			->setSubject('KohanaJobs - Reset password')
			->setFrom(array(Kohana::config('site.email') => 'KohanaJobs.com'))
			->setTo(array($this->email => $this->username))
			->setBody($body);

		// Connect to the server
		$transport = Swift_SmtpTransport::newInstance($config->server)
			->setUsername($config->username)
			->setPassword($config->password);

		// Send the message
		Swift_Mailer::newInstance($transport)->send($message);

		return TRUE;
	}

	/**
	 * Reset password: step 2a.
	 * Validates the confirmation link for a password reset.
	 *
	 * @param   integer  user id
	 * @param   string   confirmation token
	 * @param   integer  timestamp
	 * @return  boolean
	 */
	public function confirm_reset_password_link($id, $token, $time)
	{
		// Don't even bother, save us the user lookup query
		if (empty($id) OR empty($token) OR empty($time))
			return FALSE;

		// Confirmation link expired
		if ($time + Kohana::config('site.reset_password_expiration') < time())
			return FALSE;

		// Load user by id
		$this->find($id);

		// Invalid user id
		if ( ! $this->loaded())
			return FALSE;

		// Invalid confirmation token
		if ($token !== Auth::instance()->hash_password($this->email.'+'.$this->password.'+'.$time, Auth::instance()->find_salt($token)))
			return FALSE;

		return TRUE;
	}

	/**
	 * Reset password: step 2b.
	 * Validates and saves a new password.
	 * Also adds the "user" role to the user, in case his sign-up wasn't confirmed yet.
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function confirm_reset_password_form(array & $data)
	{
		$data = Validate::factory($data)
			->filter(TRUE, 'trim')
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm']);

		if ( ! $data->check())
			return FALSE;

		// Store the new password
		$this->password = $data['password'];
		$this->save();

		// It could be that the user resets his password before he confirmed his sign-up,
		// or a the reset password form could be used in case the original sign-up confirmation mail got lost.
		// Since the user could only come to this point if he supplied a valid email address,
		// we confirm his account right here.
		if ( ! $this->has('roles', ORM::factory('role', array('name' => 'user'))))
		{
			// Give the user the "user" role
			$this->add('roles', ORM::factory('role', array('name' => 'user')));
		}

		return TRUE;
	}

	/**
	 * The form where a users enters his new password, and his current password (for security).
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function change_password(array & $data, $_useless_redirect = 'param required for compatibility')
	{
		$data = Validate::factory($data)
			->filter(TRUE, 'trim')
			->rules('old_password', $this->_rules['password'])
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm'])
			->callback('old_password', array($this, 'check_password'));

		if ( ! $data->check())
			return FALSE;

		// Store the changed password
		$this->password = $data['password'];
		$this->save();

		return TRUE;
	}

	/**
	 * Change email: step 1.
	 * The form where a users enters his new email, and his current password (for security).
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function change_email(array & $data)
	{
		$data = Validate::factory($data)
			->filter(TRUE, 'trim')
			->rules('email', $this->_rules['email'])
			->callback('email', array($this, 'email_available'));

		if ( ! Auth::instance()->logged_in_oauth())
		{
			// Password check is only required for non-OAuth users
			$data->rules('password', $this->_rules['password'])
				->callback('password', array($this, 'check_password'));
		}

		// We need to call the check() method first because it resets the internal _errors property.
		$data->check();

		// Now do a manual check to see whether the new and current email aren't the same
		if ($data['email'] == $this->email)
		{
			$data->error('email', 'not_changed');
		}

		// Any errors?
		if ($data->errors())
			return FALSE;

		// Create e-mail body with email change confirmation link
		$body = View::factory('email/confirm_change_email', $this->as_array())
			->set('new_email', $data['email'])
			->set('url', URL::site(
				Route::get('user')->uri(array('action' => 'confirm_change_email')).
				'?id='.$this->id.'&token='.Auth::instance()->hash_password($this->email.'+'.$data['email']).'&email='.base64_encode($data['email']),
				TRUE // Add protocol to URL
			));

		// Get the email configuration
		$config = Kohana::config('email');

		// Load Swift Mailer
		require_once Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

		// Create an email message
		$message = Swift_Message::newInstance()
			->setSubject('KohanaJobs - Change email')
			->setFrom(array(Kohana::config('site.email') => 'KohanaJobs.com'))
			// @todo OAuth users may be entering their first email address, then also sent it to that address, not the old one (NULL value in db)
			// @todo OAuth users don't have a username field, leave it out, or use other name field if available
			->setTo(array($data['email'] => $this->username))
			->setBody($body);

		// Connect to the server
		$transport = Swift_SmtpTransport::newInstance($config->server)
			->setUsername($config->username)
			->setPassword($config->password);

		// Send the message
		Swift_Mailer::newInstance($transport)->send($message);

		return TRUE;
	}

	/**
	 * Change email: step 2.
	 * Validates the confirmation link and saves the new email.
	 * Also adds the "user" role to the user, in case his sign-up wasn't confirmed yet.
	 *
	 * @param   integer  user id
	 * @param   string   confirmation token
	 * @param   string   new email
	 * @return  boolean
	 */
	public function confirm_change_email($id, $token, $email)
	{
		// Email was base64 encoded in URL in order to make it less visible that the URL contains an email address,
		// not that that would be an immediate security threat. Base64 encoding makes urlencoding not necessary anymore.
		$email = base64_decode($email);

		// Invalid email
		if ( ! Validate::email($email))
			return FALSE;

		// Load user by id
		$this->find($id);

		// Invalid user id
		if ( ! $this->loaded())
			return FALSE;

		// Invalid confirmation token
		if ($token !== Auth::instance()->hash_password($this->email.'+'.$email, Auth::instance()->find_salt($token)))
			return FALSE;

		// New email is already taken
		if ($this->unique_key_exists($email, 'email'))
			return FALSE;

		// Actually change the email for the user in db
		$this->email = $email;
		$this->save();

		// It could be that the user changes his email address before he confirmed his sign-up.
		// In that case, the original confirm_signup link gets invalid automatically because it uses the email as hashed confirmation token.
		// No problem, though, if the user confirms his new email address, we can as well confirm his account right here.
		if ( ! $this->has('roles', ORM::factory('role', array('name' => 'user'))))
		{
			// Give the user the "user" role
			$this->add('roles', ORM::factory('role', array('name' => 'user')));
		}

		return TRUE;
	}

	/**
	 * Validates the password for the currently logged in user.
	 * Validation callback.
	 *
	 * @param   object  Validate
	 * @param   string  field name
	 * @return  void
	 */
	public function check_password(Validate $data, $field)
	{
		if ($user = Auth::instance()->get_user())
		{
			$stored_password = Auth::instance()->password($user->username);
			$salt = Auth::instance()->find_salt($stored_password);

			// Correct password
			if ($stored_password === Auth::instance()->hash_password($data[$field], $salt))
				return;
		}

		$data->error($field, 'check_password');
	}

	/**
	 * Triggers an error if the email does not exist.
	 * Validation callback.
	 *
	 * @param   object  Validate
	 * @param   string  field name
	 * @return  void
	 */
	public function email_not_available(Validate $data, $field)
	{
		if ( ! $this->unique_key_exists($data[$field], 'email'))
		{
			$data->error($field, 'email_not_available', array($data[$field]));
		}
	}

	/**
	 * Allows a model to use a user id, in addition to email and username, as unique identifier.
	 *
	 * @param   mixed   unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return (is_int($value)) ? 'id' : parent::unique_key($value);
	}

}