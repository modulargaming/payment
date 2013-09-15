<?php

class View_Payment_Packages extends Abstract_View_Payment {

	/**
	 * @var Model_Payment_Package[]
	 */
	public $packages;

	public $title = 'Packages';

	public function packages()
	{
		$packages = array();
		foreach ($this->packages as $package)
		{
			$packages[] = array(
				'name' => $package->name,
				'url'  => Route::url('payment.package', array('id' => $package->id))
			);
		}
		return $packages;
	}

}