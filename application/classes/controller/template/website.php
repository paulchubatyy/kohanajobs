<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Template_Website extends Controller_Template {

	/**
	 * @var  string  page template
	 */
	public $template = 'template/website';

	// The currently logged in user (ORM object),
	// FALSE if user is not logged in.
	public $user;

	/**
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		// Start a session
		Session::instance();

		// Get the currently logged in user.
		// Note that get_user will also do an auto_login check.
		$this->user = Auth::instance()->get_user();

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