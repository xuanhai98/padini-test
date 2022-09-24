<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


require_once Mage::getModuleDir('controllers', 'Mage_Cms').DS.'IndexController.php';
class Amasty_Xsearch_NorouteController extends Mage_Cms_IndexController
{
    /**
     * @var null|string
     */
    protected $_extension = null;

    public function indexAction($coreRoute = null)
    {
        $pathInfo = $this->_retrieveRequestInfo();
        if ($this->_isNeedRedirect()) {
            $searchUrl = Mage::helper('catalogsearch')->getResultUrl($this->_getSearchQuery($pathInfo));
            $this->getResponse()->setRedirect($searchUrl);
        } else {
            $this->noRouteAction();
        }
    }

    /**
     * @param string $pathInfo
     * @return string
     */
    protected function _getSearchQuery($pathInfo)
    {
        $query = trim($pathInfo, '/');
        $query = str_replace('/', ' ', $query);
        $query = str_replace('.html', '', $query);

        return $query;
    }

    /**
     * Retrieve path info, save extension of requested data.
     *
     * @return string
     */
    protected function _retrieveRequestInfo()
    {
        $pathInfo = $this->getRequest()->getPathInfo();
        $urlParts = explode('.', $pathInfo);
        if (count($urlParts) > 1) {
            $this->_extension = end($urlParts);
        }

        return $pathInfo;
    }

    /**
     * @return bool
     */
    protected function _isNeedRedirect()
    {
        return (!$this->_extension || $this->_extension == 'html')
            && !$this->getRequest()->isXmlHttpRequest();
    }
}
