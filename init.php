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

Route::set('payment.recurring', 'payment/recurring/<id>(/<action>)', array('id' => '[0-9]+'))
	->defaults(array(
		'directory'  => 'Payment',
		'controller' => 'Recurring',
		'action'     => 'index',
	));

Route::set('payment.ipn', 'payment/ipn')
	->defaults(array(
		'directory'  => 'Payment',
		'controller' => 'IPN',
		'action'     => 'index',
	));

Route::set('payment.package', 'payment/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'package',
	));