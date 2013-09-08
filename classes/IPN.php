<?php defined('SYSPATH') OR die('No direct script access.');


class IPN {

	const PAYPAL_HOST = 'https://www.paypal.com:/cgi-bin/webscr';
	const SANDBOX_HOST = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	public $use_ssl = TRUE;

	protected $_data;

	protected $_is_verified = FALSE;

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