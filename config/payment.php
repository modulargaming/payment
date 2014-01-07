<?php defined('SYSPATH') OR die('No direct script access.');

return array(

	'gateways' => array(
		'paypal' => array(
			'username'     => '',
			'password'     => '',
			'signature'    => '',
			'testMode'     => TRUE,
			'itemCategory' => 'Physical', // "Physical" or "Digital", L_PAYMENTREQUEST_0_ITEMCATEGORY0
			'currency'     => 'GBP'
		)
	)

);