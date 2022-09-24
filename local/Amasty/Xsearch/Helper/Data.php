<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */
class Amasty_Xsearch_Helper_Data extends Mage_Core_Helper_Abstract
{
    function substr($val, $max = 100)
    {
        $ret = $val;

        if (function_exists("mb_strlen")) {
            if (mb_strlen($val, 'UTF-8') > $max) {
                $max -= mb_strlen('...', 'UTF-8');
                $ret = mb_substr($val, 0, $max, 'UTF-8') . '...';
            }
        } else {
            if (strlen($val) > $max) {
                $max -= strlen('...');
                $ret = substr($val, 0, $max) . ' ...';
            }
        }

        return $ret;
    }

    public function getAddToCartUrl($product, $additional = array())
    {
        if (!$product->getTypeInstance(true)->hasRequiredOptions($product)) {
            return Mage::helper('checkout/cart')->getAddUrl($product, $additional);
        }
        $additional = array_merge(
            $additional,
            array('form_key' => $this->_getSingletonModel('core/session')->getFormKey())
        );
        if (!isset($additional['_escape'])) {
            $additional['_escape'] = true;
        }
        if (!isset($additional['_query'])) {
            $additional['_query'] = array();
        }
        $additional['_query']['options'] = 'cart';
        return $this->getProductUrl($product, $additional);
    }

    public function getProductUrl($product, $additional = array())
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }
        return '#';
    }

    protected function _getSingletonModel($className, $arguments = array())
    {
        return Mage::getSingleton($className, $arguments);
    }

    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        return false;
    }

    public function isHidePriceOn($product)
    {
        $hidePrice = false;
        if (Mage::helper('core')->isModuleEnabled('Mageplace_Callforprice')) {
            $hidePrice = $this->isCallforprice($product);
        } elseif (Mage::helper('core')->isModuleEnabled('Mageplace_Hideprice')) {
            $groupsString = Mage::getStoreConfig('hide_price/options/choose_customer_groups');
            $groupsArray = !empty($groupsString) ? explode(",", $groupsString) : array();
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            if (!in_array($groupId, $groupsArray)) {
                $hidePrice = true;
            }
        }

        return $hidePrice;
    }

    /**
     *  Check is cart button will be hidden
     *
     * @param Mage/Catalog/Model/Product $product
     * @return bool
     */
    public function isHideCartButton($product)
    {
        $result = false;
        if (Mage::helper('core')->isModuleEnabled('Mageplace_Callforprice')) {
            $result = $this->isCallforprice($product);
        } elseif (Mage::helper('core')->isModuleEnabled('Mageplace_Hideprice')) {
            $result = $this->isHideprice($product);
        }

        return $result;
    }

    /**
     * Check is callforprice enabled for product
     *
     * @param Mage/Catalog/Model/Product $product
     * @return bool
     */
    private function isCallforprice($product)
    {
        $result = true;
        $isGlobalEnabled = Mage::getStoreConfig('callforprice/options/global');

        if (!$isGlobalEnabled
            && Mage::helper('mageplace_callforprice')->isEnabledForProduct($product) !== true
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check is hideprice enabled for product
     *
     * @param Mage/Catalog/Model/Product $product
     * @return bool
     */
    private function isHideprice($product)
    {
        $result = false;
        $storeId = Mage::app()->getStore()->getStoreId();
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $productEnabled = Mage::getStoreConfig('hide_price/options/product_enable');

        if ($productEnabled) {
            $hideModel = Mage::getModel('mageplace_hideprice/hideprice');
            $hideModel->loadByProductId($product->getId(), array(0, $storeId));
            if ($hideModel->getId() && $hideModel->isHideprice($groupId)) {
                $result = true;
            }
        } else {
            $groupsString = Mage::getStoreConfig('hide_price/options/choose_customer_groups');
            $groupsArray = !empty($groupsString) ? explode(",", $groupsString) : array();

            if (!in_array($groupId, $groupsArray)) {
                $result = true;
            }
        }

        return $result;
    }
}
