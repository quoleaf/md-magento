<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('nbpq_product_questions')};
CREATE TABLE {$this->getTable('nbpq_product_questions')} (
	`product_questions_id` INT(11) NOT NULL AUTO_INCREMENT,
	`customer_id` VARCHAR(255) NULL,
	`questions`   VARCHAR(255),
	`created_at` datetime,
	`visibility` VARCHAR(255) NULL,
	`product_id` VARCHAR(255) NULL,
	`author_name` VARCHAR(255) NULL,
	`author_email` VARCHAR(255) NULL,
	`asked_from` VARCHAR(255) NULL,
	`status` VARCHAR(255) NULL,
	PRIMARY KEY (`product_questions_id`)
);

DROP TABLE IF EXISTS {$this->getTable('nbpq_product_answers')};
CREATE TABLE {$this->getTable('nbpq_product_answers')} (
	`answers_id` INT(11) NOT NULL AUTO_INCREMENT,
	`product_questions_id` INT(11) NOT NULL,
	`created_at` datetime,
	`customer_id` VARCHAR(255) NULL,
	`answers`   VARCHAR(255),
	`author_name` VARCHAR(255) NULL,
	`author_email` VARCHAR(255) NULL,
	`answers_from` VARCHAR(255) NULL,
	`status` VARCHAR(255) NULL,
	PRIMARY KEY (`answers_id`)
);

DROP TABLE IF EXISTS {$this->getTable('nbpq_like_dislike')};
CREATE TABLE {$this->getTable('nbpq_like_dislike')} (  
  `like_dislike_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `qa_id` int(11) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',  
  `que_like` enum('0','1') NOT NULL default '0',
  `dislike` enum('0','1') NOT NULL default '0',
  `type` VARCHAR(255) NULL,
   PRIMARY KEY (`like_dislike_id`)
);

");

$installer->endSetup();
?>