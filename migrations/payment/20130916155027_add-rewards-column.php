<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Add Rewards column
 */
class Migration_Payment_20130916155027 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE `payment_packages` ADD `rewards` TEXT NOT NULL');
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'ALTER TABLE `payment_packages` DROP `rewards`');
	}

}
