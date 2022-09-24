<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Lib_Varien_Data_Form_Element_Grid extends Varien_Data_Form_Element_Abstract
{

    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('text');
    }

    public function getElementHtml()
    {
        $this->addClass('grid');
        $html = "<section>";

        $html .= $this->getBeforeElementHtml();

        $value = $this->getValue() ? json_encode(json_decode($this->getValue(), true)) : json_encode([]);

        $html .= '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getId() . '" value=\'' . $value . '\' />';

        /** @var Amasty_Shopby_Block_Adminhtml_Value_Edit_Additional_Categories $customBlock **/
        $customBlock = $this->getData('custom_block');
        if ($customBlock instanceof Amasty_Shopby_Block_Adminhtml_Value_Edit_Additional_Categories) {
            $html .= $customBlock->toHtml();
        }

        $html .= $this->getAfterElementHtml();
        $html .='<br style="clear:both;" /></section>';
        return $html;
    }

}