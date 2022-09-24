<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


class Amasty_RulesPro_Model_Rule_Condition_Items_Sku extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(array(
            '=' => Mage::helper('rule')->__('is'),
            '!=' => Mage::helper('rule')->__('is not'),
            'in' => Mage::helper('rule')->__('is one of'),
            'not in' => Mage::helper('rule')->__('is not one of'),
            'like' => Mage::helper('rule')->__('contains'),
            'not like' => Mage::helper('rule')->__('does not contain'),
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
        $attributes['sku'] = Mage::helper('amrules')->__('SKU');
        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getExplicitApply()
    {
        return true;
    }

    public function validate(Varien_Object $object)
    {
        $query = $this->getOperatorForValidate();
        switch ($query) {
            case 'in':
            case 'not in':
                $query = $query . " (";
                foreach (array_map('trim', explode(',', $this->getValue())) as $elem) {
                    $query = $query . "'" . $elem . "'";
                    $query = $query . ",";
                }
                $query = rtrim($query, ",");
                $query = $query . " )";
                break;
            case 'like':
            case 'not like':
                $query = $query . " '%" . $this->getValue() . "%'";
                break;
            default:
                $query = $query . "'" . $this->getValue() . "'";
        }
        $result = array('sku' => $query);
        return $result;
    }
}
