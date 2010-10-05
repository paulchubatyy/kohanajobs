<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Jobs extends Controller_Template_Website {

	public function before()
	{
		parent::before();
		$this->jobs = ORM::factory('job');
	}

	public function action_index()
	{
		$total = $this->jobs->count_all();
		$pagination = Pagination::factory(array(
			'total_items' => $total,
			'items_per_page' => 15,
		));

		$this->template->content = View::factory('jobs')
			->set('jobs', $this->jobs->order_by('created', 'DESC')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all())
			->set('total_jobs', $total)
			->set('pagination', $pagination);
	}

	public function action_search($term)
	{
		$total = $this->jobs->search($term)->count_all();
		$pagination = Pagination::factory(array(
			'total_items' => $total,
			'items_per_page' => 15,
		));

		$this->template->content = View::factory('jobs')
			->set('jobs', $this->jobs->search($term)->order_by('created', 'DESC')->limit($pagination->items_per_page)->offset($pagination->offset)->find_all())
			->set('total_jobs', $total)
			->set('pagination', $pagination)
			->set('term', $term);
	}
}
