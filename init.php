<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Routes for Forum.
 */
Route::set('payment', 'payment')
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'index',
	));

Route::set('payment.paypal', 'payment/paypal/<id>(/<action>)', array('id' => '[0-9]+'))
	->defaults(array(
		'directory'  => 'Payment',
		'controller' => 'PayPal',
		'action'     => 'index',
	));

Route::set('payment.package', 'payment/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'package',
	));