<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */


class Amasty_RulesPro_Model_Rule_Condition_Items extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect
{

    public function __construct()
    {
        parent::__construct();
        $this->setType('amrulespro/rule_condition_items')
            ->setValue(null);;
    }

    public function loadAttributeOptions()
    {
        $this->setAttributeOption(array(
            'total_sales_qty' => Mage::helper('amrules')->__('Total Sales Qty'),
            'total_sales_amount' => Mage::helper('amrules')->__('Total Sales Amount'),
            'of_placed_orders' => Mage::helper('amrules')->__('Number of Comleted Orders'),
        ));
        return $this;
    }

    public function getNewChildSelectOptions()
    {
        $conditions = array(
            array('label' => Mage::helper('amrules')->__('Please choose condition'), 'value' => ''),
            array('label' => Mage::helper('amrules')->__('SKU'), 'value' => 'amrulespro/rule_condition_items_sku'),
            array('label' => Mage::helper('amrules')->__('Attribute Set'), 'value' => 'amrulespro/rule_condition_items_attributeSet'),
        );
        return $conditions;
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            Mage::helper('amrules')->__('If %s %s %s for a subselection of items matching %s of these conditions:',
                $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(),
                $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function validate(Varien_Object $object)
    {
        $quote = $object;
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            $quote = $object->getQuote();
        }

        // order history conditions are valid for customers only, not for visitors.
        $id = $quote->getCustomerId();
        if (!$id) {
            return false;
        }

        $condArray = array();

        foreach ($this->getConditions() as $condObj) {
            $condArray[] = $condObj->validate($object);
        }

        $fieldName = $this->getAttributeElement()->getValue();

        $fieldValue = Mage::getSingleton('amrulespro/calculator')
            ->getSingleTotalFieldItems($id, $fieldName, $condArray, $this->getAggregator());

        if (is_null($fieldValue)) {
            $fieldValue = 0;
        }

        return $this->validateAttribute($fieldValue);
    }
}
