<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Post extends Controller_Template_Website {

	public function action_index()
	{
		$job = ORM::factory('job');
		$errors = array();

		$this->template->content = View::factory('post')
			->set('job', $job)
			->bind('preview', $preview)
			->bind('errors', $errors);

		if (isset($_POST) AND ! empty($_POST))
		{
			$job->values($_POST, array('company', 'location', 'website', 'email', 'title', 'description', 'apply'));

			if (isset($_POST['preview']))
			{
				$preview = View::factory('job')->set('job', $job);
			}

			if ( ! isset($_POST['terms']))
			{
				// TODO: Use Validation?
				$errors += array('terms' => __('Agree to the terms of use in order to post a job.'));
			}

			// Check the data against validation rules defined in the model
			if ($job->check())
			{
				// Save the model
				if (empty($errors) AND $job->save())
				{
					// Redirect to job listing
					$this->request->redirect(Route::get('jobs')->uri());
				}
			}
			else
			{
				$errors = $job->validate()->errors('post/errors') + $errors;
			}
		}
	}

}