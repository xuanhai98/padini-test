<?php

class Amasty_Shopby_Block_Adminhtml_Value_Edit_Additional_Categories_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $category)
    {

        $html = '<input type="file" data-value-id="' . $this->getRequest()->getParam('id') . '" data-id="' . $category->getId() . '" id="category_image[' . $category->getId() . ']" name="category_image" onchange="saveImage(this)" />';

        return $html;
    }
}