<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


class Amasty_Rules_Model_Rule_Groupndisc extends Amasty_Rules_Model_Rule_Abstract
{
    public function calculateDiscount($rule, $address, $quote)
    {
        $r = array();
        $prices = $this->_getSortedCartPices($rule, $address);
        $qty = $this->_getQty($rule, count($prices));
        if (!$this->hasDiscountItems($prices, $qty)) {
            return $r;
        }

        $percentage = floatVal($rule->getDiscountAmount());
        if (!$percentage) {
            $percentage = 100;
        }
        $percentage = ($percentage / 100.0);
        $currQty = 0;
        $lastId = -1;
        $step = (int)$rule->getDiscountStep();
        if ($step == 0) {
            $step = 1;
        }

        $countPrices = count($prices);

        foreach ($prices as $i => $price) {
            if ($this->_skipBySteps($rule, $step, $i, $currQty, $qty)) {
                continue;
            }

            $currQty++;

            if ($i < $countPrices - ($countPrices % $step)) {
                $discount = $price['price'] * $rule->getDiscountAmount() / 100;
                $baseDiscount = $price['base_price'] * $rule->getDiscountAmount() / 100;
            } else {
                $discount = 0;
                $baseDiscount = 0;
            }

            if ($price['id'] != $lastId) {
                $lastId = intVal($price['id']);
                $r[$lastId] = array();
                $r[$lastId]['discount'] = $discount;
                $r[$lastId]['base_discount'] = $baseDiscount;
                $r[$lastId]['percent'] = $percentage * 100;
            } else {
                $r[$lastId]['discount'] += $discount;
                $r[$lastId]['base_discount'] += $baseDiscount;
                $r[$lastId]['percent'] = $percentage * 100;
            }
        }

        return $r;
    }
}