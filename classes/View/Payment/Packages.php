<?php

class View_Payment_Packages extends Abstract_View {

	/**
	 * @var Model_Payment_Package[]
	 */
	public $packages;

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