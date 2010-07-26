<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Template_Website extends Controller_Template {

	/**
	 * @var  string  page template
	 */
	public $template = 'template/website';

	// OAuth
	protected $provider = 'twitter';
	protected $consumer;
	protected $token;

	/**
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		// Start a session
		Session::$default = 'database';
		Session::instance();

		// Try to log in a user by cookie
		Auth::instance()->auto_login();

		// OAuth start
		// Load the configuration for this provider
		$config = Kohana::config('oauth.'.$this->provider);

		// Create a consumer from the config
		$this->consumer = OAuth_Consumer::factory($config);

		// Load the provider
		$this->provider = OAuth_Provider::factory($this->provider);

		if ($token = Cookie::get('oauth_token'))
		{
			// Get the token from storage
			$this->token = unserialize($token);
		}
		// OAuth end

		if ($this->auto_render)
		{
			// Initialize default values
			$this->template->title = 'KohanaJobs v2';
			$this->template->content = '';
		}
	}

	/**
	 * @return  void
	 */
	public function after()
	{
		parent::after();
	}

}