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

	public function package()
	{
		return array(
			'name' => $this->package->name,
			'price' => $this->package->price,
		);
	}

	public function checkout()
	{
		if ($this->package->type === Model_Payment_Package::TYPE_ONCE)
		{
			return array(
				array(
					'title' => 'Checkout with PayPal',
					'href'  => Route::url('payment.paypal', array('id' => $this->package->id))
				)
			);
		}
		elseif ($this->package->type === Model_Payment_Package::TYPE_RECURRING)
		{
			return array(
				array(
					'title' => 'Checkout with PayPal',
					'href'  => Route::url('payment.recurring', array('id' => $this->package->id))
				)
			);
		}
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