<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Block_Catalog_Layer_Filter_Attribute extends Amasty_Shopby_Block_Catalog_Layer_Filter_Attribute_Adapter
{
    public function getItemsAsArray()
    {
        $items = array();
        foreach (parent::getItems() as $itemObject){
            $item = array();
            $item['id'] = $itemObject->getOptionId();
            $item['url']   = $this->htmlEscape($itemObject->getUrl());
            $item['label'] = $itemObject->getLabel();
            $item['descr'] = $itemObject->getDescr();

            $item['count'] = '';
            $item['countValue']  = $itemObject->getCount();
            if (!$this->getHideCounts()) {
                $item['count']  = '&nbsp;<span class="count">(' . $itemObject->getCount() . ')</span>';
            }

            $item['image'] = '';
            if ($itemObject->getImage()){
                $item['image'] = Mage::getBaseUrl('media') . 'amshopby/' . $itemObject->getImage();
            }

            if ($itemObject->getImageHover()) {
                $item['image_hover'] = Mage::getBaseUrl('media') . 'amshopby/' . $itemObject->getImageHover();
            }

            $item['css'] = 'amshopby-attr';
            if (in_array($this->getDisplayType(), array(1,3))) { //dropdown and images
                $item['css'] = '';
            }

            if ($itemObject->getIsSelected()){
                $item['css'] .= '-selected';
                if (3 == $this->getDisplayType()){ //dropdown
                    $item['css'] = 'selected';
                }
            }

            if ($itemObject->getCount() === 0)
            {
                $item['css'] .= ' amshopby-attr-inactive';
            }

            $item['rel'] = $this->getSeoRel() ? ' rel="nofollow" ' : '';

            $items[] = $item;
        }

        $sortBy = $this->getSortBy();
        $functions = array(1 => '_sortByName', 2 => '_sortByCounts');
        if (isset($functions[$sortBy])){
            usort($items, array($this, $functions[$sortBy]));
        }

        // add less/more
        $max = $this->getMaxOptions();
        $i   = 0;
        foreach ($items as $k => $item){
            $style = '';
            if ($max && (++$i > $max)){
                $style = 'style="display:none" class="amshopby-attr-' . $this->getRequestValue() . '"';
            }
            $items[$k]['style'] = $style;
        }
        $this->setShowLessMore($max && ($i > $max));

        return $items;
    }

    public function _sortByName($a, $b)
    {
        $x = trim($a['label']);
        $y = trim($b['label']);

        if ($x == '') return 1;
        if ($y == '') return -1;

        if (is_numeric($x) && is_numeric($y)){
            if ($x == $y)
                return 0;
            return ($x < $y ? 1 : -1);
        }
        else {
            return strcmp($x, $y);
        }
    }

    public function _sortByCounts($a, $b)
    {
        if ($a['countValue'] == $b['countValue']) {
            return 0;
        }

        return ($a['countValue'] < $b['countValue'] ? 1 : -1);
    }

    public function getRequestValue()
    {
        return $this->_filter->getAttributeModel()->getAttributeCode();
    }

    public function getItemsCount()
    {
        $v = Mage::app()->getRequest()->getParam($this->getRequestValue());
        if (isset($v) && $this->getRequestValue() == trim(Mage::getStoreConfig('amshopby/brands/attr'))){
            $cat    = Mage::registry('current_category');
            $rootId = (int) Mage::app()->getStore()->getRootCategoryId();
            if ($cat && $cat->getId() == $rootId){
                // and this is not landing page
                $page = Mage::app()->getRequest()->getParam('am_landing');
                if (!$page) return 0;
            }
        }

        $cnt     = parent::getItemsCount();
        $showAll = !Mage::getStoreConfig('amshopby/general/hide_one_value');
        return ($cnt > 1 || $showAll) ? $cnt : 0;
    }

    public function getRemoveUrl()
    {
        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearPagination();
        $urlBuilder->changeQuery(array(
            $this->getRequestValue() => null,
        ));

        $url = $urlBuilder->getUrl();
        return $url;
    }
}
