<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


class Amasty_Rules_Model_SalesRule_Rule_Condition_Product_Subselect
    extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect
{
    /**
     * @param Varien_Object $object
     * @param bool $triggered
     * @return bool
     */
    public function validate(Varien_Object $object, $triggered = false)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $attr  = $this->getAttribute();
        $total = 0;

        if ($object instanceof Mage_Sales_Model_Quote_Item || (Mage::helper('ambase')->isModuleActive('Amasty_Promo')
            && Mage::getStoreConfig('ampromo/limitations/skip_promo_item_add') && $triggered)
        ) {
           $items = array($object);
        } else {
            $items = $object->getQuote()->getAllItems();
        }

        foreach ($items as $item) {
            // fix magento bug
            if ($item->getParentItemId()) {
                continue;
            }

            // for bundle we need to add a loop here
            // if we treat them as set of separate items

            $validator = new Amasty_Rules_Model_SalesRule_Rule_Condition_Product_Combine();
            $validator->setData($this->getData());
            $result = $validator->validate($item);
            $this->setData($validator->getData());

            if ($result) {
                $total += $item->getData($attr);
            }
        }

        return $this->validateAttribute($total);
    }
}