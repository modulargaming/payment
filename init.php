<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Routes for Forum.
 */
Route::set('payment', 'payment')
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'index',
	));

Route::set('payment.package', 'payment/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'package',
	));

Route::set('payment.paypal', 'payment/paypal/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'paypal',
	));

Route::set('payment.paypal_complete', 'payment/paypal-complete/<id>', array('id' => '[0-9]+'))
	->defaults(array(
		'controller' => 'Payment',
		'action'     => 'paypal_complete',
	));