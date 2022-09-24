<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


class Amasty_InvisibleCaptcha_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigOptions($extension)
    {
        if (Mage::helper('core')->isModuleEnabled($extension)) {
            $options = array(
                array(
                    'value' => 1,
                    'label' => $this->__('Enable')
                ),
                array(
                    'value' => 0,
                    'label' => $this->__('Disable')
                )
            );
        } else {
            $options = array(
                array(
                    'value' => -1,
                    'label' => $this->__('Not Installed')
                )
            );
        }
        return $options;
    }
}
