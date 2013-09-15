<?php defined('SYSPATH') OR die('No direct script access.');


class Abstract_View_Payment extends ABstract_View {

	public function get_breadcrumb()
	{
		return array_merge(parent::get_breadcrumb(), array(
			array(
				'title' => 'Payments',
				'href' => Route::url('payment')
			)
		));
	}

}