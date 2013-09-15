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
class Model_Payment_Transaction extends ORM {

	const STATUS_PENDING   = 'pending';
	const STATUS_COMPLETED = 'completed';

	protected $_table_columns = array(
		'id'         => NULL,
		'user_id'    => NULL,
		'package_id' => NULL,
		'created'    => NULL,
		'updated'    => NULL,
		'token'      => NULL,
		'status'     => NULL,
		'email'      => NULL,
		'fist_name'  => NULL,
		'last_name'  => NULL,
		'country'    => NULL,
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