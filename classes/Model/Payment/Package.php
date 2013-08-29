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
class Model_Payment_Package extends ORM {

	protected $_table_columns = array(
		'id'    => NULL,
		'name'  => NULL,
		'price' => NULL
	);

}