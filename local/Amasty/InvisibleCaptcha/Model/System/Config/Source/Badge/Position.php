<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


class Amasty_Invisiblecaptcha_Model_System_Config_Source_Badge_Position
{
    const INLINE = 'inline';
    const BOTTOM_LEFT = 'bottomleft';
    const BOTTOM_RIGHT = 'bottomright';

    public function toOptionArray()
    {
        $hlp = Mage::helper('aminvisiblecaptcha');
        return array(
            array(
                'value' => self::INLINE,
                'label' => $hlp->__('Inline')
            ),
            array(
                'value' => self::BOTTOM_LEFT,
                'label' => $hlp->__('Bottom Left')
            ),
            array(
                'value' => self::BOTTOM_RIGHT,
                'label' => $hlp->__('Bottom Right')
            )
        );
    }
}
