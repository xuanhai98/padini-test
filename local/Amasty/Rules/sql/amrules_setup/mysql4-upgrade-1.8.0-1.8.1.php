<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */
$this->startSetup();

$this->run("ALTER TABLE `{$this->getTable('salesrule/rule')}` ADD `amskip_rule` tinyint unsigned NOT NULL default '0'");

$this->endSetup();