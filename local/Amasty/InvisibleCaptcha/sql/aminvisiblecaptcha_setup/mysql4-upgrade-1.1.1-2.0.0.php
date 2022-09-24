<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


$this->startSetup();

$this->run("
DELETE FROM `{$this->getTable('core/config_data')}` WHERE `path`='aminvisiblecaptcha/general/enabled';
UPDATE `{$this->getTable('core/config_data')}` SET `path`='aminvisiblecaptcha/general/enabled' WHERE `path`='aminvisiblecaptcha/general/enabledCaptcha';

DELETE FROM `{$this->getTable('core/config_data')}` WHERE `path`='aminvisiblecaptcha/general/captcha_key';
UPDATE `{$this->getTable('core/config_data')}` SET `path`='aminvisiblecaptcha/general/captcha_key' WHERE `path`='aminvisiblecaptcha/general/captchaKey';

DELETE FROM `{$this->getTable('core/config_data')}` WHERE `path`='aminvisiblecaptcha/general/captcha_secret';
UPDATE `{$this->getTable('core/config_data')}` SET `path`='aminvisiblecaptcha/general/captcha_secret' WHERE `path`='aminvisiblecaptcha/general/captchaSecret';
");

$this->endSetup();
