<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template_Website {

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

			if ($user->login($post, ! empty($_POST['remember'])))
			{
				Message::set(Message::SUCCESS, __('Welcome back, :name!', array(':name' => $user->username)));
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
		Message::set(Message::SUCCESS, 'You are now logged out. Bye!');
		Request::instance()->redirect('');
	}

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

				// Redirect to somewhere else
				Message::set(Message::SUCCESS, 'Thanks for signin up. You are now logged in.');
				Request::instance()->redirect('');
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_confirm_signup()
	{
		// Confirm the user account
		if (ORM::factory('user')->confirm_signup($this->request->param('id'), $this->request->param('code')))
		{
			// Sign (a possible other user) out and redirect to sign in
			// @todo Compare user id from URL to id from currently logged in user, only signout if needed
			Auth::instance()->logout();
			Request::instance()->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}
		else
		{
			// @todo More descriptive error message: invalid link, or user already confirmed?
			echo 'Signup confirmation failed.';
			// Request::instance()->redirect('');
		}
	}

	public function action_reset_password()
	{
		// The user is already logged in
		if (Auth::instance()->logged_in())
		{
			Request::instance()->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/reset_password')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			// $post bound to template
			$post = $_POST;

			$user = ORM::factory('user');

			if ($user->reset_password($post))
			{
				echo 'Instructions to reset your password are being sent to your email address.';
				// Request::instance()->redirect('');
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_confirm_reset_password()
	{
		$user = ORM::factory('user')->find($this->request->param('id'));

		if ( ! $user->loaded())
		{
			// Invalid user ID
			exit('#A Invalid URL.');
		}

		if ($this->request->param('code') !== Auth::instance()->hash_password($user->email.'+'.$user->password.'+'.$user->last_login.'+'.$this->request->param('time'), Auth::instance()->find_salt($this->request->param('code'))))
		{
			// Invalid confirmation code
			exit('#B Invalid URL.');
		}

		if ($this->request->param('time') + 3600 < time())
		{
			// Link expired
			echo 'Link expired ', abs($this->request->param('time') + 3600 - time()), ' seconds ago.';
			exit('#C Invalid URL.');
		}

		// Sign (a possible other user) out
		Auth::instance()->logout();

		// Show form
		$this->template->content = View::factory('user/confirm_reset_password')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			// $post bound to template
			$post = Validate::factory($_POST)
				->filter(TRUE, 'trim')
				->rules('password', array(
					'not_empty'  => NULL,
					'min_length' => array(5),
					'max_length' => array(42),
				))
				->rule('password_confirm', 'matches', array('password'));

			if ($post->check())
			{
				// Save new password
				$user->password = $post['password'];
				$user->save();

				echo 'Your password has been changed. Go to signin form.';
				Request::instance()->redirect(Route::get('user')->uri(array('action' => 'signin')));
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_change_password()
	{
		// The user is not logged in
		if ( ! Auth::instance()->logged_in())
		{
			Request::instance()->redirect('');
		}

		// OAuth users don't have a password in our database
		if (Auth::instance()->logged_in_oauth())
		{
			Request::instance()->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/change_password')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			$post = $_POST;

			$user = Auth::instance()->get_user();

			if ($user->change_password($post))
			{
				echo 'Password changed.';
				// Request::instance()->redirect('');
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_change_email()
	{
		// The user is not logged in
		if ( ! Auth::instance()->logged_in())
		{
			Request::instance()->redirect('');
		}

		// Show form
		$this->template->content = View::factory('user/change_email')
			->bind('post', $post)
			->bind('errors', $errors);

		// Form submitted
		if ($_POST)
		{
			$post = $_POST;

			$user = Auth::instance()->get_user();

			if ($user->change_email($post))
			{
				echo 'Confirmation link to change your email has been sent to: '.$user->email;
				// Request::instance()->redirect('');
			}
			else
			{
				$errors = $post->errors();
			}
		}
	}

	public function action_confirm_email()
	{
		// Confirm the new email
		if (ORM::factory('user')->confirm_email($this->request->param('id'), $this->request->param('code'), $this->request->param('new_email')))
		{
			// Sign out and redirect to sign in
			Auth::instance()->logout();
			Request::instance()->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}
		else
		{
			echo 'New email confirmation failed.';
			// Request::instance()->redirect('');
		}
	}

}