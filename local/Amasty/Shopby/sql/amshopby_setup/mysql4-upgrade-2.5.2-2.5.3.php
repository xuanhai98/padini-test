<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("
ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `include_in` VARCHAR(256) NOT NULL;
");

$this->endSetup();