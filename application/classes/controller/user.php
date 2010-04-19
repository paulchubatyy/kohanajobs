<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Website {

	public function action_signup()
	{
		// The user is already logged in
		if (Auth::instance()->logged_in())
		{
			Request::instance()->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/signup')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			// $post bound to template
			$post = $_POST;

			$user = ORM::factory('user');

			if ($user->signup($post))
			{
				// Automatically log the user in
				Auth::instance()->force_login($post['username']);

				// Create e-mail body with account confirmation link
				$body = View::factory('email/signup', $user->as_array())
					->set('code', Auth::instance()->hash_password($user->email));

				// Get the email configuration
				$config = Kohana::config('email');

				// Load Swift Mailer
				require Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

				// Create an email message
				$message = Swift_Message::newInstance()
					->setSubject('KohanaJobs Sign-up')
					->setFrom(array('info@kohanajobs.com' => 'KohanaJobs.com'))
					->setTo(array($user->email => $user->username))
					->setBody($body);

				// Connect to the server
				$transport = Swift_SmtpTransport::newInstance($config->server)
					->setUsername($config->username)
					->setPassword($config->password);

				// Send the message
				Swift_Mailer::newInstance($transport)->send($message);

				// Redirect to somewhere else
				Request::instance()->redirect('');
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_signin()
	{
		// The user is already logged in
		if (Auth::instance()->logged_in())
		{
			Request::instance()->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/signin')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			$post = $_POST;
			$user = ORM::factory('user');

			// TODO: Autologin checkbox
			// Note: the login method below will remove the remember me value from $post.
			// It would be more flexible if instead of an array it took a Validate object,
			// then we could set ->label('remember', 'remember') to allow the value.
			// Also, the login method has no built-in support for remember stuff.
			// Best solution: completely build our own login method?
			if ($user->login($post))
			{
				Request::instance()->redirect('');
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_signout()
	{
		// The user is not logged in
		if ( ! Auth::instance()->logged_in())
		{
			Request::instance()->redirect('');
		}

		Auth::instance()->logout();
		Request::instance()->redirect('');
	}

	public function action_confirm()
	{
		// Confirm the user account
		if (ORM::factory('user')->confirm($this->request->param('id'), $this->request->param('code')))
		{
			// Sign out and redirect to sign in
			Auth::instance()->logout();
			Request::instance()->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}
		else
		{
			echo 'Confirmation failed.';
			// Request::instance()->redirect('');
		}
	}

}