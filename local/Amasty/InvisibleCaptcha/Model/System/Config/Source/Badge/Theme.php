<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


class Amasty_Invisiblecaptcha_Model_System_Config_Source_Badge_Theme
{
    const LIGHT = 'light';
    const DARK = 'dark';

    public function toOptionArray()
    {
        $hlp = Mage::helper('aminvisiblecaptcha');
        return array(
            array(
                'value' => self::LIGHT,
                'label' => $hlp->__('Light')
            ),
            array(
                'value' => self::DARK,
                'label' => $hlp->__('Dark')
            )
        );
    }
}
