<?php defined('SYSPATH') OR die('No direct script access.');

class Payment_PayPalGateway extends \Omnipay\PayPal\ExpressGateway {

	public function authorizeRecurring(array $parameters = array())
	{
		return $this->createRequest('Payment_ExpressAuthorizeRecurringRequest', $parameters);
	}

	public function createRecurringPaymentsProfile(array $parameters = array())
	{
		return $this->createRequest('Payment_CreateRecurringPaymentsRequest', $parameters);
	}

	public function fetchTransaction(array $parameters = array())
	{
		return $this->createRequest('Payment_ExpressCheckoutDetailsRequest', $parameters);
	}

}