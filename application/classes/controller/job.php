<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Job extends Controller_Website {

	public function action_index()
	{
		$this->template->content = View::factory('job')
			->set('job', ORM::factory('job', (int) $this->request->param('id')));
	}

}