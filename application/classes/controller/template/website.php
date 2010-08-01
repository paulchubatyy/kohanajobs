<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Template_Website extends Controller_Template {

	/**
	 * @var  string  page template
	 */
	public $template = 'template/website';

	// Auth instance
	protected $auth;

	// The currently active user (ORM object)
	protected $user;

	/**
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		// Start a session
		Session::instance();

		// Load Auth instance
		$this->auth = Auth::instance();

		// Get the currently logged in user or set up a new one.
		// Note that get_user will also do an auto_login check.
		if (($this->user = $this->auth->get_user()) === FALSE)
		{
			$this->user = ORM::factory('user');
		}

		if ($this->auto_render)
		{
			// Initialize default values
			$this->template->title = 'KohanaJobs v2';
			$this->template->content = '';
			$this->template->bind_global('user', $this->user);
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