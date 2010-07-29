<?php defined('SYSPATH') or die('No direct script access.');

abstract class Session extends Kohana_Session {

	/**
	 * @var  string  default session adapter
	 */
	public static $default = 'database';

}