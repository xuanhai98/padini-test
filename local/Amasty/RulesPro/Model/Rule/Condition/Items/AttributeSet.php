<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


class Amasty_RulesPro_Model_Rule_Condition_Items_AttributeSet extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=' => Mage::helper('rule')->__('is'),
            '!=' => Mage::helper('rule')->__('is not'),
        ));

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function loadAttributeOptions()
    {
        $attributes = array();
        $attributes['attribute_set_id'] = Mage::helper('amrules')->__('Attribute Set');
        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    public function getExplicitApply()
    {
        return true;
    }

    public function validate(Varien_Object $object)
    {
        $query = $this->getOperatorForValidate();
        $query = $query . "'" . $this->getValue() . "'";
        $result = array('attribute_set' => $query);
        return $result;
    }
}
