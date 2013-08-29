<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Initial payment structure
 */
class Migration_Payment_20130829194303 extends Minion_Migration_Base {

	/**
	 * Run queries needed to apply this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `payment_packages` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `price` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		");

		$db->query(NULL, "
			CREATE TABLE IF NOT EXISTS `payment_transactions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `package_id` int(11) NOT NULL,
			  `created` int(10) NOT NULL,
			  `updated` int(10) NOT NULL,
			  `token` varchar(255) NOT NULL,
			  `status` enum('pending','completed') NOT NULL,
			  `email` varchar(255) NOT NULL,
			  `first_name` varchar(255) NOT NULL,
			  `last_name` varchar(255) NOT NULL,
			  `country` varchar(255) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		");
	}

	/**
	 * Run queries needed to remove this migration
	 *
	 * @param Kohana_Database $db Database connection
	 */
	public function down(Kohana_Database $db)
	{
		$db->query(NULL, 'DROP TABLE payment_packages');
		$db->query(NULL, 'DROP TABLE payment_transactions');
	}

}
