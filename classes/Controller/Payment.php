<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 *
 * @package    MG/Payment
 * @category   Controller
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Controller_Payment extends Abstract_Controller_Frontend {

	protected $protected = TRUE;

	/**
	 * List all packages
	 */
	public function action_index()
	{
		$packages = ORM::factory('Payment_Package')
			->find_all();

		$this->view = new View_Payment_Packages;
		$this->view->packages = $packages;
	}

	/**
	 * Package details.
	 *
	 * @throws HTTP_Exception
	 */
	public function action_package()
	{
		$id = $this->request->param('id');
		$package = ORM::factory('Payment_Package', $id);

		if ( ! $package->loaded())
		{
			throw HTTP_Exception::factory('404', 'file not found');
		}

		$this->view = new View_Payment_Package;
		$this->view->package = $package;
	}

}
