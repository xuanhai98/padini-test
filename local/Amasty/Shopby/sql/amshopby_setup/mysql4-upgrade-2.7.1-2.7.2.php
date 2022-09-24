<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("ALTER TABLE `{$this->getTable('amshopby/value')}` ADD `additional_images` TEXT NULL AFTER `descr`");

$this->endSetup();
