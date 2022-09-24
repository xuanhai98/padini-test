<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


abstract class Amasty_Xsearch_Helper_Abstract extends Mage_Core_Helper_Abstract
{
    protected abstract function getCode();

    /**
     * Include in search results.
     *
     * @return bool
     */
    public function includeInSearch()
    {
        return Mage::getStoreConfigFlag('amxsearch/' . $this->getCode() . '/search');
    }

    /**
     * Number to include.
     *
     * @return int
     */
    public function numberToInclude()
    {
        return (int) Mage::getStoreConfig('amxsearch/' . $this->getCode() . '/limit');
    }

    /**
     * Search action.
     *
     * @param  string $keyword
     * @param  int $limit
     * @return array
     */
    public abstract function search($keyword, $limit);
}
