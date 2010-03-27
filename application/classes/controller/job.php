<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Job extends Controller_Website {

	public function action_index()
	{
		$this->template->content = 'Display a single job with ID: '.$this->request->param('id');
	}

}