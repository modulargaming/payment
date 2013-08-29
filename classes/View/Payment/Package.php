<?php defined('SYSPATH') OR die('No direct script access.');

class View_Payment_Package extends Abstract_View {

	/**
	 * @var Model_Payment_Package
	 */
	public $package;

	public function paypal_url()
	{
		return Route::url('payment.paypal', array('id' => $this->package->id));
	}

}