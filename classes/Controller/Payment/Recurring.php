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
			Kohana::$log->add(Log::ERROR, $this->getTextReport($response->getData()));
			throw HTTP_Exception::factory('403', 'Something went wrong, no cash should have been drawn, if the error proceeds contact support!');
		}
	}

	public function getTextReport($post)
	{
		$r = "\n";
		foreach ($post as $key => $value)
		{
			$r .= str_pad($key, 25).$value."\n";
		}

		return $r;
	}

	protected function _payment_vars()
	{
		return array(
			'amount'      => $this->_package->price,
			'currency'    => $this->_config['currency'],

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

// UGLY, we should be on the lookout for custom parameters issue.
// https://github.com/adrianmacneil/omnipay/pull/82
class TMPGateway extends \Omnipay\PayPal\ExpressGateway {

	public function authorize(array $parameters = array())
	{
		return $this->createRequest('ExpressAuthorizeRequest', $parameters);
	}

	public function createRecurringPaymentsProfile(array $parameters = array())
	{
		return $this->createRequest('CreateRecurringPaymentsRequest', $parameters);
	}

	public function fetchTransaction(array $parameters = array())
	{
		return $this->createRequest('ExpressCheckoutDetailsRequest', $parameters);
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

class ExpressAuthorizeRequest extends \Omnipay\PayPal\Message\ExpressAuthorizeRequest {

	public function getData()
	{
		$data = parent::getData();

		$data['L_BILLINGTYPE0'] = 'RecurringPayments';
		$data['L_BILLINGAGREEMENTDESCRIPTION0'] = 'Test Recurring Payment($1 monthly)';

		// Set cost to 0.
		$data['PAYMENTREQUEST_0_AMT'] = 0;

		return $data;
	}

}

class CreateRecurringPaymentsRequest extends \Omnipay\PayPal\Message\AbstractRequest {

	// https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/CreateRecurringPaymentsProfile_API_Operation_NVP/
	public function getData()
	{
		$data = $this->getBaseData('CreateRecurringPaymentsProfile');

		$this->validate('amount');

		// Recurring Payments Profile Details Fields
		//$data['PROFILESTARTDATE'] = date('Y-m-d\TH:i:s\Z', strtotime('+ 1 month')); // We need to start the profile a month later.
		//$data['PROFILEREFERENCE'] // Subscription ID?

		$data['PROFILESTARTDATE'] = date('Y-m-d\TH:i:s\Z', strtotime('+ 1 day'));

		// Schedule Details Fields
		$data['DESC'] = 'Test Recurring Payment($1 monthly)'; // Need to match the L_BILLINGAGREEMENTDESCRIPTION0.

		// Billing Period Details Fields
		//$data['BILLINGPERIOD'] = 'Month';
		$data['BILLINGFREQUENCY'] = '1';
		$data['BILLINGPERIOD'] = 'Day';

		$data['AMT'] = $this->getAmount();
		$data['CURRENCYCODE'] = $this->getCurrency();

		// Activation Details Fields
		$data['INITAMT'] = $this->getAmount(); // Set a initial payment, so we get paid directly!

		// Payer Information Fields
		$data['EMAIL'] = $this->getParameter('email');

		// Payment Details Item Fields
		$data['L_PAYMENTREQUEST_0_ITEMCATEGORY0'] = 'Digital';
		$data['L_PAYMENTREQUEST_0_NAME0'] = 'TEST';
		$data['L_PAYMENTREQUEST_0_AMT0'] = $this->getAmount();
		$data['L_PAYMENTREQUEST_0_QTY0'] = 1;

		$data['TOKEN'] = $this->httpRequest->query->get('token');
		//$data['PAYERID'] = $this->httpRequest->query->get('PayerID');

		return $data;
	}

}