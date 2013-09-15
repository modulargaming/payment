<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Setup a recurring payment agreement.
 *
 * @package    MG/Payment
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Payment_CreateRecurringPaymentsRequest extends \Omnipay\PayPal\Message\AbstractRequest {

	// https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/CreateRecurringPaymentsProfile_API_Operation_NVP/
	public function getData()
	{
		$data = $this->getBaseData('CreateRecurringPaymentsProfile');

		$this->validate('amount');

		// Recurring Payments Profile Details Fields
		$data['PROFILESTARTDATE'] = date('Y-m-d\TH:i:s\Z', strtotime('+ 1 month')); // We need to start the profile a month later.
		//$data['PROFILEREFERENCE'] // Subscription ID?

		// Schedule Details Fields
		$data['DESC'] = $this->getParameter('description');

		// Billing Period Details Fields
		$data['BILLINGPERIOD'] = 'Month';
		$data['BILLINGFREQUENCY'] = '1';

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