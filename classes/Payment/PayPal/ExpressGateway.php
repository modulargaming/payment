<?php defined('SYSPATH') OR die('No direct script access.');

class Payment_PayPal_ExpressGateway extends \Omnipay\PayPal\ExpressGateway {

	public function authorizeRecurring(array $parameters = array())
	{
		return $this->createRequest('Payment_PayPal_ExpressAuthorizeRecurringRequest', $parameters);
	}

	public function createRecurringPaymentsProfile(array $parameters = array())
	{
		return $this->createRequest('Payment_PayPal_CreateRecurringPaymentsRequest', $parameters);
	}

	public function fetchTransaction(array $parameters = array())
	{
		return $this->createRequest('Payment_PayPal_ExpressCheckoutDetailsRequest', $parameters);
	}

}