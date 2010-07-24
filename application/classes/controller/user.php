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
			// Sign out and redirect to sign in
			Auth::instance()->logout();
			Request::instance()->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}
		else
		{
			echo 'Signup confirmation failed.';
			// Request::instance()->redirect('');
		}
	}

	public function action_change_password()
	{
		// The user is not logged in
		if ( ! Auth::instance()->logged_in())
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