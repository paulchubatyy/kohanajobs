<?php defined('SYSPATH') or die('No direct script access.');

abstract class Session extends Kohana_Session {

	public static function instance($type = NULL, $id = NULL)
	{
		// Force database sessions by default
		return parent::instance('database', $id);
	}

}