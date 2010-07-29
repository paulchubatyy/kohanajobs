<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
* Set the production status by the domain.
* Note: the default value for Kohana::$environment is Kohana::DEVELOPMENT.
*/
if ($_SERVER['HTTP_HOST'] !== 'localhost')
{
	Kohana::$environment = Kohana::PRODUCTION;
}

/**
 * Set the default time zone.
 *
 * @see  http://docs.kohanaphp.com/about.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Brussels');

/**
 * Set the default locale.
 *
 * @see  http://docs.kohanaphp.com/about.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://docs.kohanaphp.com/about.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => Kohana::$environment === Kohana::PRODUCTION ? '/' : '/github/kohanajobs/',
	'index_file' => FALSE,
	'profile'    => Kohana::$environment !== Kohana::PRODUCTION,
	'caching'    => Kohana::$environment === Kohana::PRODUCTION,
	));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'       => MODPATH.'auth',
	'oauth'      => MODPATH.'oauth',
	'database'   => MODPATH.'database',
	'orm'        => MODPATH.'orm',
	'pagination' => MODPATH.'pagination',
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
if ( ! Route::cache())
{
	// List of all jobs
	Route::set('jobs', '')
		->defaults(array(
			'controller' => 'jobs',
			'action'     => 'index',
		));

	// A single job
	Route::set('job', 'jobs/<id>', array('id' => '\d++'))
		->defaults(array(
			'controller' => 'job',
			'action'     => 'index',
		));

	// Post a new job
	Route::set('post', 'post')
		->defaults(array(
			'controller' => 'post',
			'action'     => 'index',
		));

	// User system related
	Route::set('user', 'user/<action>')
		->defaults(array(
			'controller' => 'user',
			'action'     => 'index',
		));
	Route::set('user/oauth', 'oauth/<controller>(/<action>)')
		->defaults(array(
			'directory'  => 'oauth',
			'action'     => 'index',
		));
	Route::set('user/confirm_signup', 'user/confirm_signup/<id>/<code>', array('id' => '\d++'))
		->defaults(array(
			'controller' => 'user',
			'action'     => 'confirm_signup',
		));
	Route::set('user/confirm_email', 'user/confirm_email/<id>/<code>/<new_email>', array('id' => '\d++'))
		->defaults(array(
			'controller' => 'user',
			'action'     => 'confirm_email',
		));
	Route::set('user/confirm_reset_password', 'user/confirm_reset_password/<id>/<code>/<time>', array('id' => '\d++', 'time' => '\d++'))
		->defaults(array(
			'controller' => 'user',
			'action'     => 'confirm_reset_password',
		));

	// Cache the routes in production
	Route::cache(Kohana::$environment === Kohana::PRODUCTION);
}

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::instance()
	->execute()
	->send_headers()
	->response;
