<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


class Amasty_Xsearch_Model_Backend_Noroute extends Mage_Core_Model_Config_Data
{
    const CMS_NOROUTE = 'cms/index/noRoute';
    const SEARCH_NOROUTE = 'amxsearch/noroute/index';

    /**
     * Change no_route setting if need.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        if ($value == 0) {
            $noRouteAction = self::CMS_NOROUTE;
        } else {
            $noRouteAction = self::SEARCH_NOROUTE;
        }
        if ($this->getScope()) {
            Mage::getConfig()->saveConfig(
                'web/default/no_route', $noRouteAction, $this->getScope(), $this->getScopeId()
            );
        }
        parent::setValue($value);

        return $this;
    }
}
