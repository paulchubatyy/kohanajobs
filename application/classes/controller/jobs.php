<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Jobs extends Controller_Website {

	public function action_index()
	{
		$this->template->content = View::factory('jobs')
			->set('jobs', ORM::factory('job')->find_all());
	}

}