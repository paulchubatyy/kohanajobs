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
			// Sign (a possible other user) out and redirect to sign in
			Auth::instance()->logout();
			Request::instance()->redirect(Route::get('user')->uri(array('action' => 'signin')));
		}
		else
		{
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

	public function action_oauth()
	{
		if ($this->token AND $this->token->name === 'access')
		{
			// http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-account%C2%A0verify_credentials
			$response = OAuth_Request::factory('resource', 'GET', 'http://api.twitter.com/1/account/verify_credentials.json')
				->param('oauth_consumer_key', Kohana::config('oauth.twitter.key'))
				->param('oauth_token', $this->token)
				->sign(OAuth_Signature::factory('HMAC-SHA1'), $this->consumer, $this->token)
				->execute();
			$this->template->content = HTML::chars(print_r($response, TRUE));
		}
		else
		{
			$this->template->content = HTML::anchor($this->request->uri(array('action' => 'signin_twitter')), 'Sign in with Twitter');
		}
	}

	public function action_signin_twitter()
	{
		// We will need a callback URL for the user to return to
		$callback = URL::site($this->request->uri(array('action' => 'signin_twitter_complete')), Request::$protocol);

		// Add the callback URL to the consumer
		$this->consumer->callback($callback);

		// Get a request token for the consumer
		$token = $this->provider->request_token($this->consumer);

		// Store the token
		Cookie::set('oauth_token', serialize($token));

		// Redirect to the provider's login page
		$this->request->redirect($this->provider->authorize_url($token));
	}

	public function action_signin_twitter_complete()
	{
		if ($this->token AND $this->token->token !== Arr::get($_GET, 'oauth_token'))
		{
			// Delete the token, it is not valid
			Cookie::delete('oauth_token');

			// Send the user back to the beginning
			$this->request->redirect($this->request->uri(array('action' => 'index')));
		}

		// Get the verifier
		$verifier = Arr::get($_GET, 'oauth_verifier');

		// Store the verifier in the token
		$this->token->verifier($verifier);

		// Exchange the request token for an access token
		$token = $this->provider->access_token($this->consumer, $this->token);

		// Store the token
		Cookie::set('oauth_token', serialize($token));

		$this->request->redirect('');
		// $this->request->redirect($this->request->uri(array('action' => FALSE)));
	}

}