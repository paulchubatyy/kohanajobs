<?php defined('SYSPATH') or die('No direct script access.');

class Model_Job extends ORM {

	// Auto-update column for creation
	protected $_created_column = array(
		'column' => 'created', 
		'format' => TRUE
	);

	// Validation rules
	protected $_rules = array(
		'company'  => array('not_empty' => NULL),
		'location' => array('not_empty' => NULL),
		'website'  => array('url' => NULL),
		'email'    => array('not_empty' => NULL, 'email' => NULL),

		'title'       => array('not_empty' => NULL),
		'description' => array('not_empty' => NULL),
		'apply'       => array('not_empty' => NULL),
	);

	// Input filter
	protected $_filters = array(
		TRUE => array('trim' => NULL)
	);
}