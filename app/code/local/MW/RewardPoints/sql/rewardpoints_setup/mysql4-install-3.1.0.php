<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute('catalog_product', 'reward_point_product', array(
	'label' => 'Reward Points',
	'type' => 'int',
	'input' => 'text',
	'visible' => true,
	'required' => false,
	'position' => 10,
));

$installer = $this;
$resource = Mage::getSingleton('core/resource');
$installer->startSetup();

$installer->run("

	DROP TABLE IF EXISTS {$resource->getTableName('rewardpoints/rewardpointsorder')};
	CREATE TABLE {$resource->getTableName('rewardpoints/rewardpointsorder')} (
	  `order_id` int(11) unsigned NOT NULL,
	  `reward_point` int(11) unsigned NOT NULL,
	  `rewardpoint_sell_product` int(11)  NULL DEFAULT '0',
	  `earn_rewardpoint` int(11)  NULL DEFAULT '0',
	  `money` decimal(12,4) NULL DEFAULT '0.0000',
	  `reward_point_money_rate` varchar(255) NOT NULL,
	  PRIMARY KEY (`order_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	DROP TABLE IF EXISTS {$resource->getTableName('rewardpoints/rewardpointshistory')};
	CREATE TABLE {$resource->getTableName('rewardpoints/rewardpointshistory')} (
	  `history_id` int(11) unsigned NOT NULL auto_increment,
	  `customer_id` int(11) unsigned NOT NULL default '0',
	  `type_of_transaction` int(11) unsigned NOT NULL,
	  `amount` int(11) unsigned NOT NULL,
	  `balance` int(11) NOT NULL,
	  `transaction_detail` text NULL DEFAULT '',
	  `transaction_time` datetime NULL,
	  `expired_day` int(11)  DEFAULT '0',
	  `expired_time` datetime  DEFAULT NULL ,
	  `point_remaining` int(11)  NULL DEFAULT '0',
	  `check_time` int(2)  NULL DEFAULT '1',
	  `status` int(2) NOT NULL,
	  PRIMARY KEY (`history_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	DROP TABLE IF EXISTS {$resource->getTableName('rewardpoints/customer')};
	CREATE TABLE {$resource->getTableName('rewardpoints/customer')} (
	  `customer_id` int(11) unsigned NOT NULL,
	  `mw_reward_point` int(11) unsigned NOT NULL default '0',
	  `mw_friend_id` int(11) unsigned NOT NULL default '0',
	  `subscribed_balance_update` int(2)  DEFAULT '1',
	  `subscribed_point_expiration` int(2)  DEFAULT '1', 
	  PRIMARY KEY (`customer_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	DROP TABLE IF EXISTS {$resource->getTableName('rewardpoints/productpoint')};
	CREATE TABLE {$resource->getTableName('rewardpoints/productpoint')} (
	  `id` int(11) unsigned NOT NULL auto_increment,
	  `rule_id` int(11) NOT NULL default '0',
	  `product_id` int(11) NOT NULL default '0',
	  `reward_point` int(11) NOT NULL default '0',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	
	
");


$sql_quote = "
		ALTER TABLE `{$resource->getTableName('sales/quote')}` 
		
		ADD `mw_rewardpoint` int(11) NULL DEFAULT '0',
		ADD `mw_rewardpoint_discount` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `mw_rewardpoint_discount_show` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `earn_rewardpoint` int(11) NULL DEFAULT '0',
		ADD `earn_rewardpoint_cart` int(11) NULL DEFAULT '0',
		ADD `spend_rewardpoint_cart` int(11) NULL DEFAULT '0',
		ADD `mw_rewardpoint_sell_product` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `mw_rewardpoint_detail` text NULL DEFAULT '',
		ADD `mw_rewardpoint_rule_message` text NULL DEFAULT ''
		
		";

$installer->run($sql_quote);

$sql_quote_address = "
		ALTER TABLE `{$resource->getTableName('sales/quote_address')}` 
		
		ADD `mw_rewardpoint` int(11) NULL DEFAULT '0',
		ADD `mw_rewardpoint_discount` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `mw_rewardpoint_discount_show` decimal(12,4) NULL DEFAULT '0.0000'
		
		";

$installer->run($sql_quote_address);


$sql_order = "
		ALTER TABLE `{$resource->getTableName('sales/order')}` 
		
		ADD `mw_rewardpoint` int(11) NULL DEFAULT '0',
		ADD `mw_rewardpoint_discount` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `mw_rewardpoint_discount_show` decimal(12,4) NULL DEFAULT '0.0000'
		
		";

$installer->run($sql_order);

$sql_invoice = "
		ALTER TABLE `{$resource->getTableName('sales/invoice')}` 
		
		ADD `mw_rewardpoint` int(11) NULL DEFAULT '0',
		ADD `mw_rewardpoint_discount` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `mw_rewardpoint_discount_show` decimal(12,4) NULL DEFAULT '0.0000'
		
		";

$installer->run($sql_invoice);

$sql_creditmemo = "
		ALTER TABLE `{$resource->getTableName('sales/creditmemo')}` 
		
		ADD `mw_rewardpoint` int(11) NULL DEFAULT '0',
		ADD `mw_rewardpoint_discount` decimal(12,4) NULL DEFAULT '0.0000',
		ADD `mw_rewardpoint_discount_show` decimal(12,4) NULL DEFAULT '0.0000'
		
		";

$installer->run($sql_creditmemo);




$installer->endSetup();
