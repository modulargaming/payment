<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Initial recurring structure
 */
class Migration_Payment_20130908114106 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `payment_subscriptions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `package_id` int(11) NOT NULL,
			  `created` int(10) NOT NULL,
			  `updated` int(10) NOT NULL,
			  `status` enum('pending','active','cancelled','') NOT NULL,
			  `recurring_payment_id` varchar(255) NOT NULL,
			  `expires` int(10) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
		");

		$db->query(NULL, "ALTER TABLE payment_packages ADD `type` ENUM( 'once', 'recurring' ) NOT NULL AFTER `name` ;");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE payment_subscriptions');
		$db->query(NULL, 'ALTER TABLE payment_packages DROP `type`');
	}

}
