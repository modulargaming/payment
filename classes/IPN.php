<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * IPN class for listening to PayPal Instant Payment Notifications.
 *
 * @package    MG/Payment
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class IPN {

	const PAYPAL_HOST = 'https://www.paypal.com:/cgi-bin/webscr';
	const SANDBOX_HOST = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	// Transaction Types
	const RECURRING_PAYMENT                 = 'recurring_payment';
	const RECURRING_PAYMENT_EXPIRED         = 'recurring_payment_expired';
	const RECURRING_PAYMENT_PROFILE_CREATED = 'recurring_payment_profile_created';
	const RECURRING_PAYMENT_SKIPPED         = 'recurring_payment_skipped';
	const RECURRING_PAYMENT_PROFILE_CANCEL  = 'recurring_payment_profile_cancel';

	protected $_data;

	protected $_is_verified = FALSE;

	/**
	 * @param $data
	 */
	public function process($data)
	{
		$this->_data = array('cmd' => '_notify-validate') + $data;

		$request = Request::factory($this->get_paypal_url());

		$request->client()->options(array(
			CURLOPT_SSL_VERIFYPEER => FALSE
		));

		$request->method(Request::POST)
			->post($this->_data);

		$response = $request->execute();
		$body =  $response->body();

		if ($body == 'VERIFIED')
		{
			$this->_is_verified = TRUE;
		}
	}

	/**
	 * Check if the request is verified.
	 *
	 * @return bool
	 */
	public function is_verified()
	{
		return $this->_is_verified;
	}

	/**
	 * @param      $key
	 * @param null $default
	 *
	 * @return null
	 */
	public function get_data($key, $default = NULL)
	{
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}

	/**
	 * Get the transaction type.
	 *
	 * @return null
	 */
	public function get_transaction_type()
	{
		return $this->get_data('txn_type');
	}

	/**
	 * Get the correct PayPal url depending on test_ipn value.
	 *
	 * @return string
	 */
	public function get_paypal_url()
	{
		if ($this->get_data('test_ipn') == '1')
		{
			return IPN::SANDBOX_HOST;
		}

		return IPN::PAYPAL_HOST;
	}

}