<?php

class Amasty_Shopby_Block_Adminhtml_Value_Edit_Additional_Categories extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/amshopby/value/edit/additional/categories.phtml');
        $this->setId('categoriesGrid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $connection = Mage::getModel('catalog/category')->getResource()->getReadConnection();

        $categoryIds = $connection->fetchCol(
            $connection->select()
                ->from('catalog_category_product', 'category_id')
                ->group('category_id')
        );

        $moreIds = [];

        foreach ($categoryIds as $categoryId) {
            /** @var Mage_Catalog_Model_Category $category **/
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $parent = $category->getParentCategory();
            if ($parent->getId() && $parent->getData('is_anchor'))
                $moreIds[] = $parent->getId();
        }

        $categoryIds = array_unique(array_merge($categoryIds, $moreIds));

        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection **/
        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('*');
        $collection->addIsActiveFilter();
        $collection->addFieldToFilter('entity_id', $categoryIds);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('amshopby')->__('ID'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('amshopby')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
            'width'     => '300px',
        ));

        $this->addColumn('attribute_code', array(
            'header'    => Mage::helper('amshopby')->__('Upload Image'),
            'align'     => 'left',
            'index'     => 'entity_id',
            'renderer'  => 'amshopby/adminhtml_value_edit_additional_categories_renderer',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('amshopby/adminhtml_value/categoriesGrid');
    }

}