<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Driver for Points Rewards.
 *
 * @package    MG/Payment
 * @category   Reward
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Payment_Reward_Points extends Payment_Reward {

	private $_reward;

	/**
	 * Create the Points Reward.
	 *
	 * @param $reward
	 * @throws Exception
	 */
	public function __construct($reward)
	{
		if ( ! is_numeric($reward))
		{
			throw new Exception('Points reward should be numeric!');
		}
		$this->_reward = $reward;
	}

	/**
	 * Reward the user with the specified amount of points.
	 *
	 * @param Model_User $user
	 * @return mixed|void
	 */
	public function reward(Model_User $user)
	{
		$user->set_property('points', $user->get_property('points') + $this->_reward);
	}

}