<?php

class View_Payment_Packages extends Abstract_View {

	/**
	 * @var Array
	 */
	public $packages;

	public function packages() {
		$packages = array();
		foreach ($this->packages as $id => $package) {
			$packages[] = array(
				'name' => $package['name'],
				'url'  => Route::url('payment.package', array('id' => $package['id']))
			);
		}
		return $packages;
	}

}