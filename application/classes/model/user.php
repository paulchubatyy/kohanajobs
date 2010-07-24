<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {

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
	public function login(array & $array, $remember = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('username', $this->_rules['username'])
			->rules('password', $this->_rules['password']);

		if ($array->check())
		{
			// Attempt to load the user
			$this->where('username', '=', $array['username'])->find();

			if ($this->loaded() AND Auth::instance()->login($this, $array['password'], $remember))
			{
				// Login is successful
				return TRUE;
			}
			else
			{
				$array->error('username', 'invalid');
			}
		}

		return FALSE;
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
			->rules('password_confirm', $this->_rules['password_confirm'])
			->callback('username', array($this, 'username_available'))
			->callback('email', array($this, 'email_available'));

		if ($status = $array->check())
		{
			// Add user
			$status = $this->values($array)->save();

			// Give user the "login" role
			$this->add('roles', ORM::factory('role', array('name' => 'login')));

			// Create e-mail body with account confirmation link
			$body = View::factory('email/confirm_signup', $this->as_array())
				->set('code', Auth::instance()->hash_password($this->email));

			// Get the email configuration
			$config = Kohana::config('email');

			// Load Swift Mailer
			require_once Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

			// Create an email message
			$message = Swift_Message::newInstance()
				->setSubject('KohanaJobs Sign-up')
				->setFrom(array('info@kohanajobs.com' => 'KohanaJobs.com'))
				->setTo(array($this->email => $this->username))
				->setBody($body);

			// Connect to the server
			$transport = Swift_SmtpTransport::newInstance($config->server)
				->setUsername($config->username)
				->setPassword($config->password);

			// Send the message
			Swift_Mailer::newInstance($transport)->send($message);
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
	public function confirm_signup($user_id, $code)
	{
		$this->find($user_id);

		if ( ! $this->loaded())
		{
			// Invalid user id
			return FALSE;
		}

		if ($this->has('roles', ORM::factory('role', array('name' => 'user'))))
		{
			// User is already confirmed
			return FALSE;
		}

		if ($code !== Auth::instance()->hash_password($this->email, Auth::instance()->find_salt($code)))
		{
			// Invalid confirmation code
			return FALSE;
		}

		// Give the user the "user" role
		$this->add('roles', ORM::factory('role', array('name' => 'user')));

		return TRUE;
	}

	/**
	 * Validates an array for a matching password and password_confirm field.
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function change_password(array & $array, $_useless_redirect = 'param required for compatibility')
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('old_password', $this->_rules['password'])
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm'])
			->callback('old_password', array($this, 'check_password'));

		if ($status = $array->check())
		{
			// Change the password
			$this->password = $array['password'];

			$status = $this->save();
		}

		return $status;
	}

	/**
	 * Step 1 in an email address change.
	 *
	 * @param   array    values to check
	 * @return  boolean
	 */
	public function change_email(array & $array)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('password', $this->_rules['password'])
			->rules('email', $this->_rules['email'])
			->callback('password', array($this, 'check_password'))
			->callback('email', array($this, 'email_available'));

		// We need to call the check() method first because it resets the internal _errors property.
		$array->check();

		// Now do a manual check to see whether the new and current email aren't the same
		if ($array['email'] == $this->email)
		{
			$array->error('email', 'not_changed');
		}

		if ($array->errors())
			return FALSE;

		// Create e-mail body with email change confirmation link
		$body = View::factory('email/confirm_email', $this->as_array())
			// @todo set('link', 'full url')
			->set('code', Auth::instance()->hash_password($this->email.'+'.$array['email']))
			->set('new_email', $array['email']);

		// Get the email configuration
		$config = Kohana::config('email');

		// Load Swift Mailer
		require_once Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

		// Create an email message
		$message = Swift_Message::newInstance()
			->setSubject('KohanaJobs - Change email')
			->setFrom(array('info@kohanajobs.com' => 'KohanaJobs.com'))
			->setTo(array($array['email'] => $this->username))
			->setBody($body);

		// Connect to the server
		$transport = Swift_SmtpTransport::newInstance($config->server)
			->setUsername($config->username)
			->setPassword($config->password);

		// Send the message
		Swift_Mailer::newInstance($transport)->send($message);

		return TRUE;
	}

	public function confirm_email($user_id, $code, $new_email)
	{
		// Email was base64 encoded in URL in order to make it less visible that the URL contains an email address,
		// not that that would be an immediate security threat. Base64 encoding makes urlencoding not necessary anymore.
		// Urlencoding would leave dots as is, which would require a custom route regex for that segment.
		$new_email = base64_decode($email);

		if ( ! Validate::email($new_email))
		{
			// Invalid email
			return FALSE;
		}

		$this->find($user_id);

		if ( ! $this->loaded())
		{
			// Invalid user id
			return FALSE;
		}

		if ($code !== Auth::instance()->hash_password($this->email.'+'.$new_email, Auth::instance()->find_salt($code)))
		{
			// Invalid confirmation code
			return FALSE;
		}

		if ($this->unique_key_exists($new_email))
		{
			// New email is already taken
			return FALSE;
		}

		// Actually change the email for the user in db
		$this->email = $new_email;
		$this->save();

		// It could be that the user changes his email address before he confirmed his signup.
		// In that case, the original confirm_signup link gets invalid automatically because it uses the email as hashed confirmation code.
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
	public function check_password(Validate $array, $field)
	{
		if ($user = Auth::instance()->get_user())
		{
			$stored_password = Auth::instance()->password($user->username);
			$salt = Auth::instance()->find_salt($stored_password);

			if ($stored_password === Auth::instance()->hash_password($array[$field], $salt))
			{
				// Correct password
				return;
			}
		}

		$array->error($field, 'check_password');
	}

	/**
	 * Allows a model use both email and username as unique identifiers.
	 * This method also adds support for the id field.
	 *
	 * @param   mixed   unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return (is_int($value)) ? 'id' : parent::unique_key($value);
	}

}