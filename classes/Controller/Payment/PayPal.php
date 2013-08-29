<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Controller for handling PayPal Express Checkout.
 *
 * @package    MG/Payment
 * @category   Controller
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Controller_Payment_PayPal extends Controller_Payment {

	/**
	 * @var Model_Payment_Package
	 */
	protected $_package;

	protected $_config;

	/**
	 * @var Omnipay\PayPal\ExpressGateway
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

		$this->_config = Kohana::$config->load('payment.gateways.paypal');

		//$this->_gateway = Omnipay\Common\GatewayFactory::create('PayPal_Express');
		$this->_gateway = Omnipay\Common\GatewayFactory::create('\TMPGateway');
		$this->_gateway->setUsername($this->_config['username']);
		$this->_gateway->setPassword($this->_config['password']);
		$this->_gateway->setSignature($this->_config['signature']);

		parent::before();
	}

	public function action_index()
	{
		/** @var Omnipay\PayPal\Message\ExpressAuthorizeResponse $response */
		$response = $this->_gateway->purchase($this->_payment_vars())
			->send();

		// Redirect the user to PayPal.
		if ($response->isRedirect())
		{
			$data = $response->getData();

			ORM::factory('Payment_Transaction')
				->values(array(
					'user_id'    => $this->user->id,
					'package_id' => $this->_package->id,
					'token'      => $data['TOKEN'],
					'status'     => 'pending',
				))->create();

			$response->redirect();
		}
		else
		{
			// TODO: Improve the error message.
			throw HTTP_Exception::factory('500', 'Error calling PayPal');
		}
	}

	/**
	 * Return the user from paypal, and process the payment.
	 *
	 * @throws HTTP_Exception
	 */
	public function action_complete()
	{

		/** @var Omnipay\PayPal\Message\ExpressAuthorizeResponse $response */
		$response = $this->_gateway->completePurchase($this->_payment_vars())
			->send();

		if ($response->isSuccessful())
		{
			// Get the transaction details.
			$fetch = $this->_gateway->fetchTransaction($this->_payment_vars())->send();
			$data = $fetch->getData();

			$transaction = ORM::factory('Payment_Transaction')
				->where('TOKEN', '=', $data['TOKEN'])
				->find();

			// Update the transaction with the buyers information.
			$transaction->values(array(
				'status'     => 'completed',
				'email'      => $data['EMAIL'],
				'first_name' => $data['FIRSTNAME'],
				'last_name'  => $data['LASTNAME'],
				'country'    => $data['COUNTRYCODE'],
			))->save();

			// TODO: Code a proper reward system!
			$points = Kohana::$config->load('items.points');
			$initial_points = $points['initial'];

			// Hardcoded reward for now.
			$this->user->set_property('points', $this->user->get_property('points', $initial_points) + 100);
			$this->user->save();

			Hint::success(Kohana::message('payment', 'payment.success'));
			$this->redirect(Route::get('payment')->uri());

		}
		else
		{
			throw HTTP_Exception::factory('403', 'Something went wrong, no cash should have been drawn, if the error proceeds contact support!');
		}

	}

	/**
	 * Format the payment details for the package.
	 *
	 * @return array
	 */
	protected function _payment_vars()
	{
		return array(
			'amount'      => $this->_package->price,
			'currency'    => $this->_config['currency'],

			'testMode'    => $this->_config['testMode'],
			'landingPage' => array('Login', 'Billing'),

			'return_url'  => Route::url('payment.paypal', array(
				'action' => 'complete',
				'id'     => $this->_package->id,
			), TRUE),
			'cancel_url'  => Route::url('payment.package', array(
				'id' => $this->_package->id
			), TRUE)
		);
	}

}

// UGLY, There is a pull request for Omnipay to add this feature. We should be on the lookout for when it gets merged.
// https://github.com/adrianmacneil/omnipay/pull/110
class ExpressCheckoutDetailsRequest extends \Omnipay\PayPal\Message\AbstractRequest
{
	public function getData()
	{
		$data = $this->getBaseData('GetExpressCheckoutDetails');
		$data['TOKEN'] = $this->httpRequest->query->get('token');

		return $data;
	}
}

class TMPGateway extends \Omnipay\PayPal\ExpressGateway {

	public function fetchTransaction(array $parameters = array())
	{
		return $this->createRequest('ExpressCheckoutDetailsRequest', $parameters);
	}

}