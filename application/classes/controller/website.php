<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Website extends Controller_Template {

	/**
	 * @var  string  page template
	 */
	public $template = 'layout';

	/**
	 * @return  void
	 */
	public function before()
	{
		parent::before();

		// Start a session
		Session::instance();

		if ($this->auto_render)
		{
			// Initialize default values
			$this->template->title = 'KohanaJobs';
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