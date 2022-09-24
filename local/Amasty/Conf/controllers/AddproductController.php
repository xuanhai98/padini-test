<?php
class Amasty_Conf_AddproductController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeUrl()) {
                Mage::getSingleton('customer/session')->setBeforeUrl($this->_getRefererUrl());
            }
        }
    }
    public function stockAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product_id');
        $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($productId);
        if (isset($parentIds[0])) {
            $product = Mage::getModel('catalog/product')->load($parentIds[0]);
            $backUrl = $product->getProductUrl();
        }
        $session = Mage::getSingleton('catalog/session');

        if (!$backUrl || !$productId) {
            $this->_redirect('/');

        }

        if (!$product = Mage::getModel('catalog/product')->load($productId)) {
            /* @var $product Mage_Catalog_Model_Product */
            $session->addError($this->__('Not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return ;
        }

        try {
            $model = Mage::getModel('productalert/stock')
                ->setCustomerId(Mage::getSingleton('customer/session')->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess($this->__('Alert subscription has been saved.'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($backUrl);
    }

}