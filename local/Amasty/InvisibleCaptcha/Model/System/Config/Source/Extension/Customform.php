<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


class Amasty_InvisibleCaptcha_Model_System_Config_Source_Extension_Customform
{
    const EXTENSION = 'Amasty_Customform';

    public function toOptionArray()
    {
        return Mage::helper('aminvisiblecaptcha')->getConfigOptions(self::EXTENSION);
    }
}
