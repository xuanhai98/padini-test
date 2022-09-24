<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


class Amasty_Xsearch_Model_Config_Backend_Attribute extends Mage_Core_Model_Config_Data
{
    public function _beforeSave()
    {
        parent::_beforeSave();
        $value = $this->getValue();
        if ($value) {
            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $value);
            if ($attributeModel) {
                $attributeModel->setIsSearchable(true)->save();
            }
        }
    }
}
