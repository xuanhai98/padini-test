<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';

class Amasty_Xsearch_AjaxController extends Mage_Core_Controller_Front_Action
{
    protected function _getPriceHTML($_product)
    {
        if (Mage::helper("amxsearch")->isHidePriceOn($_product)) {
            return '';
        }

        $layout = Mage::getSingleton('core/layout');
        $catalogProduct = null;

        if ($_product->getTypeId() == 'bundle') {
            $catalogProduct = $layout->createBlock('bundle/catalog_product_price');
            $catalogProduct->setData('product', $_product);
            $catalogProduct->setTemplate('bundle/catalog/product/price.phtml');
        } else {
            $catalogProduct = $layout->createBlock('catalog/product_price');
            $catalogProduct->setData('product', $_product);
            $catalogProduct->setTemplate('catalog/product/price.phtml');
        }

        return $catalogProduct->toHTML();
    }

    protected function _getReviewHTML($_product)
    {
        $layout = Mage::getSingleton('core/layout');
        $review = $layout->createBlock('review/helper');

        $review->setTemplate('review/helper/summary.phtml');

        return $review->getSummaryHtml($_product, 'short', false);
    }

    public function indexAction()
    {
        header('Access-Control-Allow-Origin: *');
        /* @var $hlr Amasty_Xsearch_Helper_Data */
        $hlr = Mage::helper("amxsearch");

        $result = array(
            'items'      => array(),
            'bottomHtml' => ''
        );

        /* @var $query Mage_CatalogSearch_Model_Query */
        $query = Mage::helper('catalogsearch')->getQuery();
        $query->setQueryText(htmlspecialchars($query->getQueryText()));
        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }

                $query->prepare();
            }
        } else {
            $this->getResponse()->setHttpResponseCode(403);
            return;
        }

        $limit = Mage::getStoreConfig('amxsearch/autocomplete/products_limit');
        $nameLength = Mage::getStoreConfig('amxsearch/autocomplete/name_length');
        $descLength = Mage::getStoreConfig('amxsearch/autocomplete/desc_length');
        $showReviews = Mage::getStoreConfig('amxsearch/autocomplete/reviews') == 1;

        $_resultCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        $_resultCollection->addAttributeToSelect('small_image');

        $_resultCollection->setOrder('relevance');

        if ($_resultCollection->getSize() > $limit) {
            $moreResults = Mage::getUrl('catalogsearch/result') . "?q=" . $query->getQueryText();
            $result['bottomHtml'] = '<a class="more_results" href="' . $moreResults . '">'
                . $hlr->__("More results")
                . "<span> ({$_resultCollection->getSize()})</span>"
                . '</a>';
        }

        $_resultCollection->setPageSize($limit);

        $catalogOutputHelper = Mage::helper('catalog/output');
        $catalogImageHelper = Mage::helper('catalog/image');
        $amxsearchHelper = Mage::helper('amxsearch/data');

        foreach ($_resultCollection as $_product) {
            if ($_product->getTypeId() == 'bundle') {
                $_product->setFinalPrice($_product->getMinPrice());
            }

            $desc = $catalogOutputHelper->productAttribute(
                $_product, $_product->getShortDescription(), 'short_description'
            );

            $size = Mage::getStoreConfig('amxsearch/autocomplete/image')?: 150;
            $imageBlockSize = (int)Mage::getStoreConfig('amxsearch/autocomplete/width');
            if ($imageBlockSize) {
                //count image block percentage width
                $imageBlockSize = $size / ($imageBlockSize * 0.35) * 100;
            }

            $result['products']['items'][] = array(
                'price' => $this->_getPriceHTML($_product),
                'reviews' => $showReviews ? $this->_getReviewHTML($_product) : '',
                'description' => $amxsearchHelper->substr($catalogOutputHelper->stripTags($desc, null, true), $descLength),
                'name' => $amxsearchHelper->substr($catalogOutputHelper->stripTags($_product->getName(), null, true), $nameLength),
                'url' => $_product->getProductUrl(),
                'add_to_cart' => $this->_getAdd2CartHtml($_product),
                'image_block_size' => $imageBlockSize,
                'image' => $catalogImageHelper->init($_product, 'small_image')
                    ->resize($size)->__toString(),
            );
        }
        $result['products']['title'] = $hlr->__('Products');

        /** @var $categoryHelper Amasty_Xsearch_Helper_Category */
        $categoryHelper = Mage::helper('amxsearch/category');
        if ($categoryHelper->includeInSearch()) {
            $result['categories'] = $categoryHelper->search(
                $query->getQueryText(),
                $categoryHelper->numberToInclude()
            );
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getAdd2CartHtml($_product)
    {
        $hlr = Mage::helper("amxsearch");
        $html = '';

        if ($hlr->isHideCartButton($_product)) {
            return $html;
        }

        if (Mage::getStoreConfig('amxsearch/autocomplete/add2cart')) {
            if ($_product->isSaleable() || $_product->getTypeId() == 'downloadable') {
                $url = $hlr->getAddToCartUrl($_product, array())
                    . '?return_url=' . Mage::getUrl('checkout/cart');

                $html = '<div class="add2cart">'
                    . '<button type="button" title="' . $hlr->__('Add to Cart')
                    . '" class="button btn-cart" onclick="setLocation(\'' . $url . '\'); return false;"><span><span>'
                    . $hlr->__('Add to Cart')
                    . '</span></span></button></div>';
            } else {
                $html = '<div class="add2cart availability out-of-stock"><span>'
                    . $hlr->__('Out of stock')
                    . '</span></div>';
            }
        }

        return $html;
    }
}
