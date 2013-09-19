<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Abstract class for handling Rewards for Payments.
 *
 * @package    MG/Payment
 * @category   Reward
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
abstract class Payment_Reward {

	/**
	 * Create the correct reward class, depending on the type.
	 *
	 * @param $type
	 * @param $data
	 * @return Payment_Reward
	 * @throws Exception
	 */
	public static function factory($type, $data)
	{
		$class = NULL;

		// Attempt to create the avatar instance.
		try
		{
			$refl = new ReflectionClass('Payment_Reward_'.$type);
			$class = $refl->newInstance($data);
		}
		catch (ReflectionException $ex)
		{
			throw new Exception('Unknown reward type: '.$type);
		}

		return $class;
	}

	/**
	 * Reward the user.
	 *
	 * @param Model_User $user
	 * @return mixed
	 */
	abstract public function reward(Model_User $user);

}