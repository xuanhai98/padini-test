<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Cart
*/
class Amasty_Cart_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function getTime()
   {
       return Mage::getStoreConfig('amcart/general/time');
   }
   
   public function useProductPage()
   {
       return Mage::getStoreConfig('amcart/general/use_product_page');
   }
   
   public function getProductButton()
   {
       return Mage::getStoreConfig('amcart/general/product_button');
   }
   
   public function getDisplayAlign()
   {
       return Mage::getStoreConfig('amcart/display/align');
   }
   
   public function displayProduct()
   {
       return Mage::getStoreConfig('amcart/display/disp_product');
   } 
   
   public function displayCount()
   {
       return Mage::getStoreConfig('amcart/display/disp_count');
   }
   
   public function displaySumm()
   {
       return Mage::getStoreConfig('amcart/display/disp_sum');
   }

   public function jsParam($obj)
   {
       $param = array(
           'send_url'           =>  $obj->getSendUrl(),
           'update_url'         =>  $obj->getUpdateUrl(),
           'src_image_progress' =>  $obj->getSkinUrl('images/amasty/loading.gif'),
           'enable_minicart'    =>  Mage::getStoreConfig('amcart/general/minicart'),
           'type_loading'       =>  Mage::getStoreConfig('amcart/display/type_loading'),
           'error'              =>  $this->__(' ↑ This is a required field.'),
           'align'              =>  $this->getDisplayAlign(),
           'form_key'           =>  Mage::getSingleton('core/session')->getFormKey(),
           'is_product_view'    =>  Mage::registry('current_product') ? 1 : 0,
           'top_cart_selector'  =>  Mage::getStoreConfig('amcart/reloading/selector'),
           'buttonClass'        =>  Mage::getStoreConfig('amcart/general/button_selector')
       );
       if(Mage::registry('current_product'))
              $param['product_id'] = Mage::registry('current_product')->getId();
	  if(Mage::registry('current_category'))

              $param['current_category'] = Mage::registry('current_category')->getUrl();


      
       return Zend_Json::encode($param);
   }
   
   public function getItemId($_product)
   {
       return Mage::getSingleton('checkout/session')->getQuote()->getItemByProduct($_product)->getId();
   }
}
