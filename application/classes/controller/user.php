<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template_Website {

	public function action_signin()
	{
		// The user is already logged in
		if ($this->auth->logged_in())
		{
			Message::set(Message::NOTICE, 'Hey, you are already signed in.');

			// Note that Request->redirect() will stop execution.
			// It calls exit() at the end.
			$this->request->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/signin')
			->bind('post', $post) // Used to repopulate form fields
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			// At the point $post was bound to the form view (above), $post didn't exist.
			// The cool thing is that no errors will show up then in the view.
			// Now that we create $post to repopulate the form fields (in case of errors),
			// ALL form field keys MUST be set to prevent "undefined index" errors.
			// This should be no problem, as $post will be passed by reference to Model_User->login(),
			// and in there it will be converted to a Validate object containing all field values.
			// Whoa, what a comment! I hope you got something out of it.
			$post = $_POST;

			// $_POST['remember'] will only be set if the "remember me" checkbox was checked
			$remember = isset($_POST['remember']);

			// Try to log the user in
			if ($this->user->login($post, $remember))
			{
				Message::set(Message::SUCCESS, __('Welcome back, :name!', array(':name' => $this->user->username)));
				$this->request->redirect('');
			}

			// Show the error messages.
			// Remember, $errors is bound to the form view (above).
			$errors = $post->errors();
		}
	}

	public function action_signout()
	{
		if ( ! $this->auth->logged_in())
		{
			Message::set(Message::NOTICE, 'Take it easy. You are already signed out.');
			$this->request->redirect('');
		}

		$this->auth->logout();

		Message::set(Message::SUCCESS, 'You are now signed out. Bye!');
		$this->request->redirect('');
	}

	public function action_signup()
	{
		// The user is already logged in
		if ($this->auth->logged_in())
		{
			Message::set(Message::NOTICE, 'If you want to sign up somebody else, please, sign out yourself first.');
			$this->request->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/signup')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			// Try to sign the user up
			if ($this->user->signup($post = $_POST))
			{
				// Automatically log the user in
				$this->auth->force_login($post['username']);

				Message::set(Message::SUCCESS, 'Thanks for signin up. You are now logged in.');
				$this->request->redirect('');
			}

			$errors = $post->errors();
		}
	}

	public function action_confirm_signup()
	{
		// Grab the user id and token from the confirmation link.
		// Note: Type casting is necessary! $_GET could contain arrays as well,
		//       which would result in errors further down the road.
		//       E.g. ?id=123&token[evil]=666
		$id = (int) Arr::get($_GET, 'id');
		$token = (string) Arr::get($_GET, 'token');

		// The user trying to confirm his sign-up is accessing the site
		// while another user (on the same browser) was still logged in.
		if ($this->auth->logged_in() AND $id != $this->user->id)
		{
			// Cover your ears, we're blowing up the whole session!
			$this->auth->logout(TRUE);

			// Also, set up a new user
			$this->user = ORM::factory('user');
		}

		// Confirm the user's sign-up
		if ($this->user->confirm_signup($id, $token))
		{
			// @todo If logged in, redirect to profile page or something, otherwise to sign in form
			// @todo If account already confirmed, show Message::NOTICE
			Message::set(Message::SUCCESS, 'Rejoice. Your sign-up has been confirmed.');
			$this->request->redirect('');
		}

		Message::set(Message::ERROR, 'Oh no! This confirmation link is invalid.');
		$this->request->redirect('');
	}

	public function action_reset_password()
	{
		// The user is logged in, yet it is possible that he lost his password anyway
		if ($this->auth->logged_in())
		{
			// @todo Add real link to the message
			Message::set(Message::NOTICE, 'Remember your current password? [Go to the change password form](link).');
		}

		// Show form
		$this->template->content = View::factory('user/reset_password')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			// Try to reset the password
			if ($this->user->reset_password($post = $_POST))
			{
				Message::set(Message::SUCCESS, 'Instructions to reset your password are being sent to your email address.');
				$this->request->redirect('');
			}

			$errors = $post->errors();
		}
	}

	public function action_confirm_reset_password()
	{
		// Grab the user id, token and timestamp from the confirmation link.
		$id = (int) Arr::get($_GET, 'id');
		$token = (string) Arr::get($_GET, 'token');
		$time = (int) Arr::get($_GET, 'time');

		// The user trying to reset his password is accessing the site
		// while another user (on the same browser) was still logged in.
		if ($this->auth->logged_in() AND $id != $this->user->id)
		{
			// Cover your ears, we're blowing up the whole session!
			$this->auth->logout(TRUE);

			// Also, set up a new user
			$this->user = ORM::factory('user');
		}

		// Validate the confirmation link first
		if ( ! $this->user->confirm_reset_password_link($id, $token, $time))
		{
			Message::set(Message::ERROR, 'The confirmation link to reset your password has expired or is invalid.');
			$this->request->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/confirm_reset_password')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			if ($this->user->confirm_reset_password_form($post = $_POST))
			{
				Message::set(Message::SUCCESS, 'You can now sign in with your new password.');
				$this->request->redirect(Route::get('user')->uri(array('action' => 'signin')));
			}

			$errors = $post->errors();
		}
	}

	public function action_change_password()
	{
		// The user is not logged in
		if ( ! $this->auth->logged_in())
		{
			$this->request->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}

		// OAuth users don't have a password in our database
		if ($this->auth->logged_in_oauth())
		{
			Message::set(Message::NOTICE, 'You logged in via an OAuth provider. We don\'t store a password for your account.');
			$this->request->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/change_password')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			if ($this->user->change_password($post = $_POST))
			{
				Message::set(Message::SUCCESS, 'You successfully changed your password. We hope you feel safer now.');
				$this->request->redirect('');
			}

			$errors = $post->errors();
		}
	}

	public function action_change_email()
	{
		// The user is not logged in
		if ( ! $this->auth->logged_in())
		{
			$this->request->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}

		// Show form
		$this->template->content = View::factory('user/change_email')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			if ($this->user->change_email($post = $_POST))
			{
				Message::set(Message::SUCCESS, 'A confirmation link to change your email has been sent to '.$user->email.'.');
				$this->request->redirect('');
			}

			$errors = $post->errors();
		}
	}

	public function action_confirm_change_email()
	{
		// Grab the user id, token and new email from the confirmation link.
		$id = (int) Arr::get($_GET, 'id');
		$token = (string) Arr::get($_GET, 'token');
		$email = (string) Arr::get($_GET, 'email');

		// The user trying to confirm his new email is accessing the site
		// while another user (on the same browser) was still logged in.
		if ($this->auth->logged_in() AND $id != $this->user->id)
		{
			// Cover your ears, we're blowing up the whole session!
			$this->auth->logout(TRUE);

			// Also, set up a new user
			$this->user = ORM::factory('user');
		}

		// Confirm the user's new email
		if ($this->user->confirm_change_email($id, $token, $email))
		{
			// @todo If logged in, redirect to profile page or something, otherwise to sign in form
			Message::set(Message::SUCCESS, 'We\'ve updated your email to '.$this->user->email.'.');
			$this->request->redirect('');
		}

		Message::set(Message::ERROR, 'Oh no! This confirmation link is invalid.');
		$this->request->redirect('');
	}

}