<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */
class Amasty_Number_Block_Adminhtml_Rma_Grid extends Enterprise_Rma_Block_Adminhtml_Rma_Grid
{
    protected function _prepareColumns()
    {
        $ret = parent::_prepareColumns();
        
        $this->getColumn("increment_id")->setType('text');
        
        $this->getColumn("order_increment_id")->setType('text');
        
        return $ret;
    }
}
