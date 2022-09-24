<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `comment` TEXT NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `block_pos` VARCHAR(255) NOT NULL;
"); 

$this->endSetup();