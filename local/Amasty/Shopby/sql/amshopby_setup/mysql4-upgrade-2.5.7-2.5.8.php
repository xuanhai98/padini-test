<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD `cms_block_bottom` VARCHAR(255);
");

$this->endSetup();