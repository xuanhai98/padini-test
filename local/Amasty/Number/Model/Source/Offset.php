<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Number
 */
class Amasty_Number_Model_Source_Offset
{
    public function toOptionArray()
    {
        $options = array(); 
        
        for ($i = -12; $i <= 12; $i++){
            $v = $i > 0 ? "+$i" : $i;
            $hours = ($i==1 || $i==-1) ? '%s hour': '%s hours';
            
            $options[] = array(
                'value' => $v,
                'label' => Mage::helper('amnumber')->__($hours, $v),
            );
        }
        return $options;
    }
}