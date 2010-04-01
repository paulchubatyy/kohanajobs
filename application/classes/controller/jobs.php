<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Jobs extends Controller_Website {

	public function action_index()
	{
		$jobs       = ORM::factory('job');
		$total      = $jobs->count_all();
		$pagination = Pagination::factory(array(
			'total_items' => $total,
			'items_per_page' => 15,
		));

		$this->template->content = View::factory('jobs')
			->set('jobs', $jobs->limit($pagination->items_per_page)->offset($pagination->offset)->find_all())
			->set('total_jobs', $total)
			->set('pagination', $pagination);
	}

}