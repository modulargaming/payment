<?php defined('SYSPATH') OR die('No direct script access.');

class View_Payment_Package extends Abstract_View_Payment {

	/**
	 * @var Model_Payment_Package
	 */
	public $package;

	public function title()
	{
		return $this->package->name;
	}

	public function paypal_url()
	{
		return Route::url('payment.paypal', array('id' => $this->package->id));
	}

	public function get_breadcrumb()
	{
		return array_merge(parent::get_breadcrumb(), array(
			array(
				'title' => $this->title(),
				'href'  => Route::url('payment.package', array(
					'id' => $this->package->id
				)),
			)
		));
	}

}