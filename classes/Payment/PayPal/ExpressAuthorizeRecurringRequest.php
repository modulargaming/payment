<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Express Authorize Request for Recurring payments.
 *
 * @package    MG/Payment
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Payment_PayPal_ExpressAuthorizeRecurringRequest extends \Omnipay\PayPal\Message\ExpressAuthorizeRequest {

	/**
	 * Add support for recurring payments by setting the required variables if the is_recurring flag is true.
	 *
	 * @return array
	 */
	public function getData()
	{
		$data = parent::getData();

		$data['L_BILLINGTYPE0'] = 'RecurringPayments';
		$data['L_BILLINGAGREEMENTDESCRIPTION0'] = $this->getDescription();

		// Set cost to 0.
		$data['PAYMENTREQUEST_0_AMT'] = 0;

		return $data;
	}

}