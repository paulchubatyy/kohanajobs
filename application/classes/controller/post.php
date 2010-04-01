<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Post extends Controller_Website {

	public function action_index()
	{
		$this->template->content = View::factory('post')
			->bind('errors', $errors)
			->bind('defaults', $fields);

		$job = ORM::factory('job');
		$fields = array(
			'company'     => '',
			'location'    => '',
			'website'     => 'http://',
			'email'       => '',
			'title'       => '',
			'description' => '',
			'apply'       => '',
		);

		if (isset($_POST))
		{
			if ($job->values($_POST)->check())
			{
				$job->save();
				
				$this->request->redirect('');
			}
			else
			{
				$errors = $job->validate()->errors('post');
				echo Kohana::debug($job->as_array());
				$fields = Arr::overwrite($fields, $job->as_array());
			}
		}
	}

}