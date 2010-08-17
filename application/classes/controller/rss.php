<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rss extends Controller {

	public function action_index()
	{
		$jobs = ORM::factory('job');
		
		$this->request->response = View::factory('rss')
			->set('jobs', $jobs->order_by('created', 'DESC')->find_all());
	}

}