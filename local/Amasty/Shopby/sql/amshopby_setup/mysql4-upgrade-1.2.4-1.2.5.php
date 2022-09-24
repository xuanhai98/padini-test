<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("

CREATE TABLE `{$this->getTable('amshopby/range')}` (
  `range_id` mediumint(8) unsigned NOT NULL auto_increment,
  `price_frm` int  unsigned NOT NULL,
  `price_to`  int  unsigned NOT NULL,
  PRIMARY KEY  (`range_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

");

$this->endSetup();