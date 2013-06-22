<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Payment extends Abstract_Controller_Frontend {

	// TODO: Change this to database driven?
	private $packages = array(
		array(
			'name' => '100 coins',
			'cost' => '1000', // Price including 2 decimals.
			'id'   => 1
		)
	);

	public function action_index() {

		$this->view = new View_Payment_Packages;
		$this->view->packages = $this->packages;

	}

	public function action_package() {

		$id = $this->request->param('id');
		$package = Arr::get($this->packages, $id - 1);

		if ($package == null) {
			throw HTTP_Exception::factory('404', 'file not found');
		}

		$this->view = new View_Payment_Package;
		$this->view->package = $package;
	}

	public function action_paypal() {

		require_once DOCROOT.'vendor/autoload.php';

		$id = $this->request->param('id');
		$package = Arr::get($this->packages, $id - 1);

		if ($package == null) {
			throw HTTP_Exception::factory('404', 'file not found');
		}

		$config = Kohana::$config->load('payment.gateways.paypal');

		$gateway = Omnipay\Common\GatewayFactory::create('PayPal_Express');
		$gateway->setUsername($config['username']);
		$gateway->setPassword($config['password']);
		$gateway->setSignature($config['signature']);

		$response = $gateway->purchase(array(
			'amount'      => $package['cost'],
			'testMode'    => $config['testMode'],
			'landingPage' => array('Login', 'Billing'),
			'return_url'  => Route::url('payment.paypal_complete', array('id' => $id), TRUE),
			'cancel_url'  => Route::url('payment.package', array('id' => $id), TRUE)
		))->send();

		if ($response->isSuccessful()) {
			die('Huh, payment was successful?');
		} elseif ($response->isRedirect()) {
			$response->redirect();
		} else {
			die('Something went wrong!');
		}

	}

	public function action_paypal_complete() {

		require_once DOCROOT.'vendor/autoload.php';

		$id = $this->request->param('id');
		$package = Arr::get($this->packages, $id - 1);

		if ($package == null) {
			throw HTTP_Exception::factory('404', 'file not found');
		}

		$config = Kohana::$config->load('payment.gateways.paypal');

		$gateway = Omnipay\Common\GatewayFactory::create('PayPal_Express');
		$gateway->setUsername($config['username']);
		$gateway->setPassword($config['password']);
		$gateway->setSignature($config['signature']);

		$response = $gateway->completePurchase(array(
			'amount'      => $package['cost'],
			'testMode'    => $config['testMode'],
			'landingPage' => array('Login', 'Billing'),
			'return_url'  => Route::url('payment.paypal_complete', array('id' => $id), TRUE),
			'cancel_url'  => Route::url('payment.package', array('id' => $id), TRUE)
		))->send();

		if ($response->isSuccessful()) {
			die('Congrats we got cash!');
		} else {
			die('Something went wrong!');
		}

	}

}