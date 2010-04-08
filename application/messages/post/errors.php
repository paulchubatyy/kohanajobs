<?php defined('SYSPATH') or die('No direct script access.');

return array(

	'company' => array(
		'not_empty' => 'Company name can\'t be blank.',
	),

	'location' => array(
		'not_empty' => 'Location can\'t be blank.',
	),

	'website' => array(
		'url'       => 'The URL is invalid.',
	),

	'email' => array(
		'not_empty' => 'E-mail address can\'t be blank.',
		'email'     => 'The e-mail address is invalid.',
	),

	'title' => array(
		'not_empty' => 'Job title can\'t be blank.',
	),

	'description' => array(
		'not_empty' => 'Job description can\'t be blank.',
	),

	'apply' => array(
		'not_empty' => '“How to apply” can\'t be blank.',
	),

);