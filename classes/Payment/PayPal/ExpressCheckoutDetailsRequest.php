<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Temporary class until https://github.com/adrianmacneil/omnipay/pull/110 gets merged.
 *
 * @package    MG/Payment
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Payment_PayPal_ExpressCheckoutDetailsRequest extends \Omnipay\PayPal\Message\AbstractRequest {

	public function getData()
	{
		$data = $this->getBaseData('GetExpressCheckoutDetails');
		$data['TOKEN'] = $this->httpRequest->query->get('token');

		return $data;
	}

}