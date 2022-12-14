<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


class Amasty_Rules_Model_Rule_Setof extends Amasty_Rules_Model_Rule_Abstract
{
    protected function prepareSetRule($rule, $address, $quote)
    {
        if (!$rule->getPromoSku() && !$rule->getPromoCats()) {
            return array();
        }

        $sortedProducts = array();
        $qtySkus = array();
        $qtyCats = array();

        if ($rule->getPromoSku()) {
            $skus = $this->getSkus($rule->getPromoSku() );
            foreach ($skus as $sku) {
                $qtySkus[$sku] = 0;
                $priceSkus [$sku] = array();
            }
        }

        if ($rule->getPromoCats()) {
            $cats = $this->getCats($rule->getPromoCats());
            foreach ($cats as $cat) {
                $qtyCats[$cat] = 0;
                $priceCats[$cat] = array();
            }
        }

        foreach ($this->getAllItems($address) as $item) {
            //we must skip 0 price items when calculate set
            if (!$item->getId() || $this->_getItemPrice($item) == 0) continue;

            if ($rule->getPromoSku() && in_array($item->getSku(), $skus)) {
                $qtySkus[$item->getSku()] += $this->_getItemQty($item);
            }

            if ($rule->getPromoCats() && array_intersect($item->getProduct()->getCategoryIds(), $cats)) {
                foreach (array_intersect($item->getProduct()->getCategoryIds(), $cats) as $category) {
                    $qtyCats[$category] += $this->_getItemQty($item);
                }
            }
            $sortedProducts[$item->getId()] = $this->_getItemPrice($item);
        }

        $qtySkus = $this->_setMinValue($qtySkus, $rule->getDiscountQty());
        $qtyCats = $this->_setMinValue($qtyCats, $rule->getDiscountQty());

        asort($sortedProducts);

        return array ($qtySkus, $qtyCats, $sortedProducts);
    }

    protected function _setMinValue($array, $discountQty)
    {
        if (!$array) return $array;
        $min = min($array);
        if ($min==0) return array();

        if ($discountQty==0) $discountQty = $min;
        $min = min($min, (int)$discountQty);
        foreach($array as $key=>$value) {
            $array[$key] = $min;
        }
        return $array;
    }

    /**
     * @param $promoSku
     * @return array
     */
    protected function getSkus($promoSku)
    {
        return $this->getPromoData($promoSku);
    }

    /**
     * @param $promoCat
     * @return array
     */
    protected function getCats($promoCat)
    {
        return $this->getPromoData($promoCat);
    }

    /**
     * @param $data
     * @return array
     */
    protected function getPromoData($data)
    {
        $trim = function ($value) {
            return trim($value);
        };

        return array_map($trim, explode(',', $data));
    }
}