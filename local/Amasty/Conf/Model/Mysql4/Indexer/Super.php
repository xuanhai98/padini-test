<?php
/**
 * @author Amasty Team
 * @copyright Amasty
 * @package Amasty_Conf
 */
class Amasty_Conf_Model_Mysql4_Indexer_Super extends Mage_Index_Model_Indexer_Abstract
{
    const   EVENT_MATCH_RESULT_KEY = 'amconf_match_result';
    var     $noimgUrl;
    var     $imageSizeAtCategoryPageX;
    var     $imageSizeAtCategoryPageY;
    var     $cacheFilePath;
    public static $indexSuperData = array();

    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ));

    protected function _construct()
    {
        $this->_init('amconf/mysql4_indexer_super');
        $this->imageSizeAtCategoryPageX = Mage::getStoreConfig('amconf/list/main_image_list_size_x');
        $this->imageSizeAtCategoryPageY = Mage::getStoreConfig('amconf/list/main_image_list_size_y');
        $this->cacheFilePath            = Mage::getBaseDir('var') . DS . 'cache' . '/indexSuperData';
    }

    public function getName() {
        return Mage::helper('amconf')->__('Amasty Color Swatches Pro');
    }

    public function getDescription() {
        return Mage::helper('amconf')->__('Data from category page');
    }

    public function matchEvent(Mage_Index_Model_Event $event) {
        $data = $event->getNewData();
        if(isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }
        $entity = $event->getEntity();
        $result = true;
        if($entity != Mage_Catalog_Model_Product::ENTITY) {
            return;
        }
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);
        return $result;
    }

    protected function _registerEvent(Mage_Index_Model_Event $event) {
        $dataObj = $event->getDataObject();
        if($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('amconf_update_product_id', $dataObj->getId());
        } else if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
            $event->addNewData('amconf_delete_product_id', $dataObj->getId());
        } else if ($event->getType() == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            $event->addNewData('amconf_mass_action_product_ids', $dataObj->getProductIds());
        }
        return true;
    }

    protected function _processEvent(Mage_Index_Model_Event $event) {
        $data = $event->getNewData();
        if(!empty($data['amconf_update_product_id'])) {
            $this->doSomethingOnUpdateEvent($data['amconf_update_product_id']);
        } else if (!empty($data['amconf_delete_product_id'])) {
            $this->doSomethingOnDeleteEvent($data['amconf_delete_product_id']);
        } else if (!empty($data['amconf_mass_action_product_ids'])) {
            $this->doSomethingOnMassActionEvent($data['amconf_mass_action_product_ids']);
        }
    }

    protected function doReindexAll() {
        $noimgUrl = Mage::helper('amconf')->getNoimgImgUrl();
        $collectionConfigurable = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('small_image')
            ->addAttributeToFilter('type_id', array('eq' => 'configurable'));
        foreach ($collectionConfigurable as $configurableProduct) {
            try {
                $smallImage  = (string)(Mage::helper('catalog/image')->init($configurableProduct, 'small_image')->resize($this->imageSizeAtCategoryPageX, $this->imageSizeAtCategoryPageY));
            } catch(Exception $e) {
                Mage::log("Exception: " . __FILE__ . " function [" . __FUNCTION__ . "] line " . __LINE__ . " " . $e->getMessage());
                $smallImage  = $noimgUrl;
            }

            self::$indexSuperData[$configurableProduct->getId()]= array(
                'sku'             => $configurableProduct->getSku(),
                'small_image_url' => $smallImage,
                'simples'         => array()
            );

            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $configurableProduct);
            foreach($childProducts as $childProduct) {
                $childProduct = Mage::getModel('catalog/product')->load($childProduct->getId());
                self::$indexSuperData[$configurableProduct->getId()]['simples'][$childProduct->getId()] = array(
                    'sku'                  => $childProduct->getSku(),
                    //'size'                 => $childProduct->getAttributeText('size'),
                    //'color'                => $childProduct->getAttributeText('color'),
                    'size_id'              => $childProduct->getSize(),
                    'color_id'             => $childProduct->getColor(),
                   );
                if(!('no_selection' == $childProduct->getSmallImage() || '' == $childProduct->getSmallImage())){
                    try {
                        self::$indexSuperData[$configurableProduct->getId()]['simples'][$childProduct->getId()]['small_image_url' ] =
                            (string)(Mage::helper('catalog/image')->init($childProduct, 'small_image')->resize($this->imageSizeAtCategoryPageX, $this->imageSizeAtCategoryPageY));
                    } catch(Exception $e) {
                        Mage::log("Exception: " . __FILE__ . " function [" . __FUNCTION__ . "] line " . __LINE__ . " " . $e->getMessage());
                    }

                }
            }
        }
        file_put_contents($this->cacheFilePath, serialize(self::$indexSuperData));
        return true;
    }

    public function reindexAll() {
        return $this->doReindexAll();
    }

    protected function loadIndexSuperData() {
        if(! isset(self::$indexSuperData) || count(self::$indexSuperData) < 1) {
            self::$indexSuperData = array();
            if(file_exists($this->cacheFilePath)) {
                self::$indexSuperData = unserialize(file_get_contents($this->cacheFilePath));
            }
            else{
                $this->doReindexAll();
                if(file_exists($this->cacheFilePath)) {
                    self::$indexSuperData = unserialize(file_get_contents($this->cacheFilePath));
                }
            }
        }
    }

    public function getPersistedDataById($productId, $type) {
        $this->loadIndexSuperData();
        if($type == 'configurable') {
            return self::$indexSuperData[$productId];
        }
        else if ($type == 'simple') {
            foreach(self::$indexSuperData as $conf) {
                foreach($conf['simples'] as $key => $value) {
                    if($key == $productId) {
                        return $conf['simples'][$key];
                    }
                }
            }
        } else {
            return NULL;
        }
    }
}