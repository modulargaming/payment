<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Payment extends Abstract_Controller_Frontend {

	protected $protected = TRUE;

	// TODO: Change this to database driven?
	private $packages = array(
		array(
			'name' => '100 coins',
			'cost' => '1000', // Price including 2 decimals.
			'id'   => 1,
			'reward' => 100
		)
	);

	/**
	 * List all packages
	 */
	public function action_index() {

		$this->view = new View_Payment_Packages;
		$this->view->packages = $this->packages;

	}

	/**
	 * Package details.
	 *
	 * @throws HTTP_Exception
	 */
	public function action_package() {

		$id = $this->request->param('id');
		$package = Arr::get($this->packages, $id - 1);

		if ($package == NULL) {
			throw HTTP_Exception::factory('404', 'file not found');
		}

		$this->view = new View_Payment_Package;
		$this->view->package = $package;
	}

	/**
	 * Send the user to paypal.
	 *
	 * @throws HTTP_Exception
	 */
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

		$response = $gateway
			->purchase($this->_format_payment_details($package, $config))
			->send();

		if ($response->isSuccessful()) {
			// Can this even happen?
			Hint::success(Kohana::message('payment', 'payment.success'));
			$this->redirect(Route::get('user.dashboard')->uri());
			// TODO: We should have a proper error message.
		} elseif ($response->isRedirect()) {
			$response->redirect();
		} else {
			throw HTTP_Exception::factory('403', 'Payment was unsuccessful');
			// TODO: We should have a proper error message.
		}

	}

	/**
	 * Return the user from paypal, and process the payment.
	 *
	 * @throws HTTP_Exception
	 */
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

		$response = $gateway
			->completePurchase($this->_format_payment_details($package, $config))
			->send();

		if ($response->isSuccessful()) {
			$points = Kohana::$config->load('items.points');
			$initial_points = $points['initial'];
			$this->user->set_property('points', $this->user->get_property('points', $initial_points) + $package['reward']);
			Hint::success(Kohana::message('payment', 'payment.success'));
			$this->redirect(Route::get('user.dashboard')->uri());

		} else {
			throw HTTP_Exception::factory('403', 'Something went wrong, no cash should have been drawn, if the error proceeds contact support!');
		}

	}

	/**
	 * Format the payment details for the package.
	 *
	 * @param  array $package
	 * @param  array $config
	 * @return array
	 */
	protected function _format_payment_details($package, array $config) {
		$id = $package['id'];
		return array(
			'amount'      => $package['cost'],
			'currency'    => $config['currency'],

			'testMode'    => $config['testMode'],
			'landingPage' => array('Login', 'Billing'),
			'return_url'  => Route::url('payment.paypal_complete', array('id' => $id), TRUE),
			'cancel_url'  => Route::url('payment.package', array('id' => $id), TRUE)
		);
	}
}
