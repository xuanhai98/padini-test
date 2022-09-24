<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_InvisibleCaptcha
 */


class Amasty_InvisibleCaptcha_Model_Captcha
{
    /**
     * @var array
     */
    protected $_supportedExtensionUrl;

    protected $_errorCodes = array(
        'missing-input-secret' => 'The secret parameter is missing.',
        'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
        'missing-input-response' => 'The response parameter is missing.',
        'invalid-input-response' => 'The response parameter is invalid or malformed.',
        'bad-request' => 'The request is invalid or malformed.',
        'invalid-keys' => 'The Site Key and the Secret Key are incorrect or does not match.'
    );

    protected $_urls = array();
    protected $_selectors = array();

    /**
     * Amasty_InvisibleCaptcha_Model_Captcha constructor.
     * Set Urls for supported extensions
     */
    public function __construct()
    {
        $this->_supportedExtensionUrl = array(
            'amasty_customform' => 'customform/form/submit'
        );

        if (0 < Mage::getStoreConfig('aminvisiblecaptcha/amasty/magpleasure_blogpro')) {
            $this->_supportedExtensionUrl['magpleasure_blogpro'] = 'mpblog/index/postForm';
        }

        if (0 < Mage::getStoreConfig('aminvisiblecaptcha/amasty/amasty_faq')) {
            $faqLink = Mage::getStoreConfig('amfaq/general/url_prefix');
            $this->_supportedExtensionUrl['amasty_faq'] = '/' . $faqLink . '/';
        }
    }

    /**
     * Validation of token from Google
     *
     * @param string $token
     * @return bool
     * @throws Zend_Http_Client_Exception
     */
    public function verify($token)
    {
        $verification = array(
            'success' => false,
            'error' => ''
        );
        $googleVerify = new Varien_Http_Client('https://www.google.com/recaptcha/api/siteverify');
        $googleVerify->setMethod(Varien_Http_Client::POST);
        $googleVerify->setParameterPost('secret', Mage::getStoreConfig('aminvisiblecaptcha/general/captcha_secret'));
        $googleVerify->setParameterPost('response', $token);
        try {
            $googleResponse = $googleVerify->request();
            if ($googleResponse->isSuccessful()
                && !empty($googleResponse)
            ) {
                $headers = $googleResponse->getHeaders();
                if (array_key_exists('Content-encoding', $headers)
                    && 'gzip' == $headers['Content-encoding']
                ) {
                    $body = $googleResponse->decodeGzip($googleResponse->getRawBody());
                } else {
                    $body = $googleResponse->getBody();
                }
                $answer = json_decode($body);
                if (array_key_exists('success', $answer)) {
                    $success = $answer->success;
                    if ($success) {
                        $verification['success'] = true;
                    } elseif (array_key_exists('error-codes', $answer)) {
                        $error = $answer->{'error-codes'};
                        $verification['error'] = $this->_errorCodes[$error[0]];
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log($e->__toString(), null, 'Amasty_InvisibleCaptcha.log');
        }
        return $verification;
    }

    /**
     * Return URLs to verify
     *
     * @return array
     */
    public function getUrls()
    {
        if (empty($this->_urls)) {
            $urls = trim(Mage::getStoreConfig('aminvisiblecaptcha/advanced/captcha_urls'));
            $urlsList = preg_split('|\s*[\r\n]+\s*|', $urls, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($this->_supportedExtensionUrl as $configKey => $extensionUrl) {
                if (0 < Mage::getStoreConfig('aminvisiblecaptcha/amasty/' . $configKey)) {
                    $urlsList[] = $extensionUrl;
                }
            }
            $this->_urls = $urlsList;
        }
        return $this->_urls;
    }

    /**
     * Return selectors of forms to protect
     *
     * @return array
     */
    public function getSelectors()
    {
        if (empty($this->_selectors)) {
            $selectors = trim(Mage::getStoreConfig('aminvisiblecaptcha/advanced/captcha_selectors'));
            $selectors = preg_split('|\s*[\r\n]+\s*|', $selectors, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($this->_supportedExtensionUrl as $configKey => $extensionUrl) {
                if (0 < Mage::getStoreConfig('aminvisiblecaptcha/amasty/' . $configKey)) {
                    switch ($configKey) {
                        case 'amasty_customform':
                            $selectors[] = 'form[action*="' . $extensionUrl . '"]';
                            break;
                        case 'amasty_faq':
                            $selectors = array_merge(array('#amfaq-ask-form-inline', '#amfaq-ask-form'), $selectors);
                            break;
                        case 'magpleasure_blogpro' :
                            $postId = Mage::app()->getRequest()->getParam('id', null);
                            if ($postId) {
                                $selectors = array_merge(array('#mpblog-form-' . $postId), $selectors);
                            }
                            break;
                    }
                }
            }
            $this->_selectors = $selectors;
        }
        return $this->_selectors;
    }
}
