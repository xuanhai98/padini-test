<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


class Amasty_Rules_Model_Rule_Buyxgetnfixdisc extends Amasty_Rules_Model_Rule_Buyxgety
{
    /**
     * @param Mage_SalesRule_Model_Rule $rule
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return array
     */
    public function calculateDiscount($rule, $address, $quote)
    {
        // no conditions for Y elements
        if (!$rule->getPromoSku() && !$rule->getPromoCats()) {
            return array();
        }

        $arrX = $this->getTriggerElements($address, $rule);
        $realQty = $this->getTriggerElementQty($arrX);
        $maxQty =  $this->_getNQty($rule, $realQty);
        // find all allowed Y (discounted) elements and calculate total discount
        $currQty = 0; // there can be less elemnts to discont than $maxQty

        $passedItems = array();
        $r = array();
        $lastId = 0;
        //we must get all items from sorted
        foreach ($this->_getSortedCartPices($rule, $address) as $item) {
            $item = $this->getItemById($item['id'], $address);
            if ($currQty >= $maxQty) {
                break;
            }

            // what should we do with bundles when we treat them as
            // separate items
            $passedItems[$item->getId()] = $item;
            // we always skip child items and calculate discounts inside parents

            if (!$this->canProcessItem($item, $arrX, $passedItems)) {
                continue;
            }

            if (!$this->isDiscountedItem($rule, $item)) {
                continue;
            }

            if ($lastId == $item->getId()) {
                continue;
            }

            $qty = $this->_getItemQty($item);
            $qty = min($maxQty - $currQty, $qty);
            $currQty += $qty;

            $fixed = $rule->getDiscountAmount(); // in base currency

            $discount =  $qty *$quote->getStore()->convertPrice($fixed);

            $baseDiscount = $qty * $fixed;

            if (isset($r[$item->getId()])) {
                $r[$item->getId()]['discount'] += $discount;
                $r[$item->getId()]['base_discount'] += $baseDiscount;
            } else {
                $r[$item->getId()] = array();
                $r[$item->getId()]['discount'] = $discount;
                $r[$item->getId()]['base_discount'] = $baseDiscount;
            }

            $lastId = $item->getId();
        }
        return $r;
    }
}
