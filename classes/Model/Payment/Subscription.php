<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 *
 * @package    MG/Payment
 * @category   Model
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Model_Payment_Subscription extends ORM {

	// Subscription statuses
	const PENDING   = 'pending';
	const ACTIVE    = 'active';
	const CANCELLED = 'cancelled';

	protected $_table_columns = array(
		'id'                   => NULL,
		'user_id'              => NULL,
		'package_id'           => NULL,
		'created'              => NULL,
		'updated'              => NULL,
		'status'               => NULL,
		'recurring_payment_id' => NULL,
		'expires'              => NULL,
	);

	protected $_created_column = array(
		'column' => 'created',
		'format' => TRUE
	);

	protected $_updated_column = array(
		'column' => 'updated',
		'format' => TRUE
	);

	protected $_belongs_to = array(
		'user' => array(
			'model'       => 'User',
			'foreign_key' => 'user_id',
		),
		'package' => array(
			'model'       => 'Payment_Package',
			'foreign_key' => 'package_id',
		),
	);

}