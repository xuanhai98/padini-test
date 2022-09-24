<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("

ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `show_search` TINYINT( 1 ) NOT NULL ,
ADD `slider_decimal` TINYINT( 1 ) NOT NULL ;


");
 
$this->endSetup();