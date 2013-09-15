<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Controller for handling PayPal Recurring payments.
 *
 * @package    MG/Payment
 * @category   Controller
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Controller_Payment_Recurring extends Controller_Payment {

	/**
	 * @var Model_Payment_Package
	 */
	protected $_package;

	protected $_config;

	/**
	 * @var Payment_PayPalGateway
	 */
	protected $_gateway;

	public function before()
	{
		$id = $this->request->param('id');
		$this->_package = ORM::factory('Payment_Package', $id);

		if ( ! $this->_package->loaded())
		{
			throw HTTP_Exception::factory('404', 'file not found');
		}

		if ($this->_package->type !== Model_Payment_Package::TYPE_RECURRING)
		{
			throw HTTP_Exception::factory('404', 'file not found');
		}

		$this->_config = Kohana::$config->load('payment.gateways.paypal');

		$this->_gateway = Omnipay\Common\GatewayFactory::create('\Payment_PayPalGateway');
		$this->_gateway->setUsername($this->_config['username']);
		$this->_gateway->setPassword($this->_config['password']);
		$this->_gateway->setSignature($this->_config['signature']);

		parent::before();
	}

	public function action_index()
	{
		/** @var Omnipay\PayPal\Message\ExpressAuthorizeResponse $response */
		$response = $this->_gateway->authorizeRecurring($this->_payment_vars())
			->send();

		// Redirect the user to PayPal.
		if ($response->isRedirect())
		{
			$response->redirect();
		}
		else
		{
			// TODO: Improve the error message.
			throw HTTP_Exception::factory('500', 'Error calling PayPal');
		}
	}

	public function action_complete()
	{
		// Get the transaction details.
		$fetch = $this->_gateway->fetchTransaction($this->_payment_vars())->send();
		$data = $fetch->getData();

		// Add the buyer email to parameters.
		$parameters = $this->_payment_vars() + array('email' => $data['EMAIL']);

		/** @var Omnipay\PayPal\Message\ExpressAuthorizeResponse $response */
		$response = $this->_gateway->createRecurringPaymentsProfile($parameters)
			->send();

		Kohana::$log->add(Log::ERROR, IPN::array_to_string($response->getData()));

		if ($response->isSuccessful())
		{
			$response_data = $response->getData();

			// Get the transaction details.
			// $fetch = $this->_gateway->fetchTransaction($this->_payment_vars())->send();
			// $data = $fetch->getData();

			ORM::factory('Payment_Subscription')
				->values(array(
					'user_id'              => $this->user->id,
					'package_id'           => $this->_package->id,
					'status'               => Model_Payment_Subscription::PENDING,
					'recurring_payment_id' => $response_data['PROFILEID']
				))->create();

			Hint::success(Kohana::message('payment', 'payment.success'));
			$this->redirect(Route::get('payment')->uri());

		}
		else
		{
			Kohana::$log->add(Log::ERROR, IPN::array_to_string($response->getData()));
			throw HTTP_Exception::factory('403', 'Something went wrong, no cash should have been drawn, if the error proceeds contact support!');
		}
	}

	protected function _payment_vars()
	{
		return array(
			'amount'      => $this->_package->price,
			'currency'    => $this->_config['currency'],
			'description' => $this->_package->name,

			'testMode'    => $this->_config['testMode'],
			'landingPage' => array('Login', 'Billing'),

			'return_url'  => Route::url('payment.recurring', array(
				'action' => 'complete',
				'id'     => $this->_package->id,
			), TRUE),
			'cancel_url'  => Route::url('payment.package', array(
				'id' => $this->_package->id
			), TRUE)
		);
	}

}