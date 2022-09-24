<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


class Amasty_InvisibleCaptcha_Model_Observer
{
    const PARAM_NAME_REFERER_URL = 'referer_url';
    const PARAM_NAME_BASE64_URL = 'r64';
    const PARAM_NAME_URL_ENCODED = 'uenc';

    protected $_forbiddenUrls = array(
        'checkout',
        'onepage',
        'cart'
    );

    public function handleControllers($observer)
    {
        /** @var Mage_Core_Controller_Front_Action $action */
        $action = $observer->getControllerAction();
        $request = $action->getRequest();

        if (Mage::getStoreConfig('aminvisiblecaptcha/general/enabled')
            && !$this->_isAdmin()
        ) {
            foreach (Mage::getSingleton('aminvisiblecaptcha/captcha')->getUrls() as $captchaUrl) {
                if (false !== strpos($request->getRequestUri(), $captchaUrl)) {
                    if ('customer/account/loginPost' == $captchaUrl
                        && false !== strpos($_SERVER['HTTP_REFERER'],'checkout')
                    ) {
                        break;
                    }
                    if ($request->isPost()) {
                        $token = $request->getPost('amasty_invisible_token');
                        if (!$token) {
                            $token = $request->getPost('g-recaptcha-response');
                        }
                        $validation = Mage::getSingleton('aminvisiblecaptcha/captcha')->verify($token);
                        if (!$validation['success']) {
                            $errorText = $validation['error'];
                            if (!$errorText) {
                                $errorText = 'Something is wrong.';
                            }
                            Mage::getSingleton('core/session')->addError(
                                Mage::helper('aminvisiblecaptcha')->__($errorText)
                            );

                            $action
                                ->getResponse()
                                ->setRedirect($this->_getRefererUrl($action))
                                ->sendResponse();

                            Mage::helper('ambase/utils')->_exit();
                        }
                    }
                    break;
                }
            }
        }
    }

    protected function _getRefererUrl($action)
    {
        $refererUrl = $action->getRequest()->getServer('HTTP_REFERER');
        if ($url = $action->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $action->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }
        if ($url = $action->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    protected function _isUrlInternal($url)
    {
        if (false !== strpos($url, 'http')) {
            if (0 === strpos($url, Mage::app()->getStore()->getBaseUrl())
                || 0 === strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true))
            ) {
                return true;
            }
        }
        return false;
    }

    public function settingsSaving($observer)
    {
        if ('aminvisiblecaptcha' == $observer->getObject()->getSection()) {
            $settings = $observer->getObject()->getData();
            if (isset($settings['groups']['advanced']['fields']['captcha_urls']['value'])) {
                $urls = $settings['groups']['advanced']['fields']['captcha_urls']['value'];
                $urls = trim($urls);
                $urlsList = preg_split('|\s*[\r\n]+\s*|', $urls, -1, PREG_SPLIT_NO_EMPTY);
                $forbidden = array();
                foreach ($urlsList as $url) {
                    if ($this->_isUrlForbidden($url)) {
                        $forbidden[] = $url;
                    }
                }
                if (!empty($forbidden)) {
                    $urlsList = array_diff($urlsList, $forbidden);
                    $urlsList = implode("\r\n", $urlsList);
                    $settings['groups']['advanced']['fields']['captcha_urls']['value'] = $urlsList;
                    $observer->getObject()->setData($settings);
                    $message = Mage::helper('aminvisiblecaptcha')->__('Notice: Module is not compatible with the forms at checkout page: appropriate urls have been deleted from the `URLs to Enable` setting.');
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
            }
        }
    }

    protected function _isUrlForbidden($url)
    {
        $found = false;
        foreach ($this->_forbiddenUrls as $forbidden) {
            if (false !== strpos($url, $forbidden)) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    protected function _isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }
        // for some reason isAdmin does not work here
        if ('sales_order_create' == Mage::app()->getRequest()->getControllerName()) {
            return true;
        }

        return false;
    }
}
