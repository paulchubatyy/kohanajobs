<?php defined('SYSPATH') or die('No direct script access.');

abstract class Session extends Kohana_Session {

	// Use database sessions by default
	protected static $type = 'database';

}