<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */
class Amasty_Rules_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Widget_Form
{
    public function getSettingsUrl()
    {
        return $this->getUrl("adminhtml/catalog_product_attribute/index");
    }
}
