<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Controller for handling PayPal IPN requests.
 *
 * @package    MG/Payment
 * @category   Controller
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Controller_Payment_IPN extends Controller {

	/**
	 * @var IPN
	 */
	protected $_IPN;

	/**
	 * @var Model_Payment_Subscription
	 */
	protected $_subscription;

	public function action_index()
	{
		// Log the output
		Kohana::$log->add(Log::DEBUG, self::array_to_string($this->request->post()));

		$this->_IPN = new IPN();
		$this->_IPN->process($this->request->post());

		// If the request did not come from PayPal show a 404 page.
		if ( ! $this->_IPN->is_verified())
		{
			throw HTTP_Exception::factory('404', 'File not found!');
		}

		// TODO: We want to log all IPN actions and ensure we do not process the same action TWICE!

		// Find the correct subscription.
		$this->_subscription = ORM::factory('Payment_Subscription')
			->where('recurring_payment_id', '=', $this->_IPN->get_data('recurring_payment_id'))
			->find();

		Kohana::$log->add(Log::DEBUG, $this->_IPN->get_transaction_type());

		switch($this->_IPN->get_transaction_type())
		{
			case IPN::RECURRING_PAYMENT_PROFILE_CREATED:
				Kohana::$log->add(Log::DEBUG, 'PROFILE CREATED');
				$this->_profile_created();
				break;
			case IPN::RECURRING_PAYMENT:
				Kohana::$log->add(Log::DEBUG, 'PAYMENT RECEIVED');
				$this->_payment();
				break;
			case IPN::RECURRING_PAYMENT_PROFILE_CANCEL:
				Kohana::$log->add(Log::DEBUG, 'PROFILE CANCEL');
				$this->_profile_cancel();
				break;
		}

		$this->response->status(200);
		$this->response->body('OK');
	}

	/**
	 * Format the data array to string.
	 *
	 * @param $data
	 * @return string
	 */
	public static function array_to_string(array $data)
	{
		$r = "\n";
		foreach ($data as $key => $value)
		{
			$r .= str_pad($key, 25).$value."\n";
		}

		return $r;
	}

	/**
	 * Update the profile status to Active, and set expires to 1 month.
	 */
	protected function _profile_created()
	{
		// Set subscription to 1 month.
		$this->_subscription->values(array(
			'status'  => Model_Payment_Subscription::ACTIVE,
			'expires' => strtotime('+1 month'),
		))->update();

	}

	/**
	 * Update the profile status to CANCELLED.
	 */
	protected function _profile_cancel()
	{
		$this->_subscription->values(array(
			'status' => Model_Payment_Subscription::CANCELLED,
		))->update();
	}

	/**
	 * Update the expires field to 1 month.
	 */
	protected function _payment()
	{
		// Set subscription to 1 month.
		$this->_subscription->values(array(
			'expires' => strtotime('+1 month'),
		))->update();
	}

}