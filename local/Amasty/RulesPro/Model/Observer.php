<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_RulesPro
 */
class Amasty_RulesPro_Model_Observer
{

    /**
     * Adds new conditions
     *
     * @param   Varien_Event_Observer $observer
     */
    public function handleNewConditions($observer)
    {
        $transport = $observer->getAdditional();
        $cond = $transport->getConditions();
        if (!is_array($cond)) {
            $cond = array();
        }

        $types = array(
            'address'   => 'Advanced Cart Attribute',
            'customer' => 'Customer attributes',
            'orders'   => 'Purchases history',
        );

        if ('true' == (string)Mage::getConfig()->getNode('modules/Amasty_Orderattr/active')){
            $types ['oattr'] = 'Order Attrributes';
        }
        
        foreach ($types as $typeCode => $typeLabel) {
            $condition = Mage::getModel('amrulespro/rule_condition_' . $typeCode);
            $conditionAttributes = $condition->loadAttributeOptions()->getAttributeOption();

            $attributes = array();
            foreach ($conditionAttributes as $code => $label) {
                $attributes[] = array(
                    'value' => 'amrulespro/rule_condition_' . $typeCode . '|' . $code,
                    'label' => $label,
                );
            }
            $cond[] = array(
                'value' => $attributes,
                'label' => Mage::helper('amrules')->__($typeLabel),
            );
        }

        $cond[] = array(
            'value' => 'amrulespro/rule_condition_total',
            'label' => Mage::helper('amrules')->__('Orders Subselection')
        );

        $cond[] = array(
            'value' => 'amrulespro/rule_condition_items',
            'label' => Mage::helper('amrules')->__('Items Subselection')
        );

        $transport->setConditions($cond);

        return $this;
    }
}
