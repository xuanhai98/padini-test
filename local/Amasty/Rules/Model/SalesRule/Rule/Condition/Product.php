<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */
class Amasty_Rules_Model_SalesRule_Rule_Condition_Product extends Mage_SalesRule_Model_Rule_Condition_Product
{

    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_sku'] = Mage::helper('amrules')->__('Custom Options SKU');
        $attributes['stock_item_qty'] = Mage::helper('reports')->__('Stock Qty');
        $attributes['weight'] = Mage::helper('sales')->__('Weight');

        if (Mage::getStoreConfig('amrules/general/options_values'))
            $attributes['quote_item_value'] = Mage::helper('amrules')->__('Custom Options Values');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $product = false;
        if ($object->getProduct() instanceof Mage_Catalog_Model_Product) {
            $product = $object->getProduct();
        } else {
            $product = Mage::getModel('catalog/product')
                ->load($object->getProductId());
        }

        if ($this->getAttribute() == 'stock_item_qty') {
            $productStockQty = $product->getStockItem()->getStockQty();
            if ($product->getTypeId() == 'configurable') {
                $children = $object->getChildren();
                $simple = $children[0];
                $productStockQty = $simple->getProduct()->getStockItem()->getQty();
            }

            $product->setStockItemQty($productStockQty);
        }

        if (Mage::getStoreConfig('amrules/general/options_values')) {
            $options = $product->getTypeInstance(true)->getOrderOptions($product);
            $values = '';
            if (isset($options['options']))
                foreach ($options['options'] as $option)
                    $values .= '|'.$option['value'];

            $product->setQuoteItemValue($values);
        }

        $product->setQuoteItemSku($object->getSku());

        $object->setProduct($product);
        
        return parent::validate($object);
    }
}
