<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amshopby/page')}` ADD COLUMN `cats` TEXT NOT NULL;
"); 

$this->endSetup();