<?php defined('SYSPATH') or die('No direct script access.');

class Model_Job extends ORM {

	protected $_rules = array(
		'company'  => array('not_empty' => NULL),
		'location' => array('not_empty' => NULL),
		'email'    => array('email' => NULL),

		'title'       => array('not_empty' => NULL),
		'description' => array('not_empty' => NULL),
		'apply'       => array('not_empty' => NULL),
	);

	protected $_filters = array(TRUE => array('trim' => NULL));

	protected function _validate()
	{
		if (isset($values['website']) AND $values['website'] !== 'http://')
		{
			$this->_rules += array('website' => array('url' => NULL));
		}

		return parent::_validate();
	}
}