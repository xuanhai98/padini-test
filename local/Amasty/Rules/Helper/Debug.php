<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


class Amasty_Rules_Helper_Debug extends Mage_Core_Helper_Abstract
{
    const LOCAL_IP = '127.0.0.1';

    /**
     * List of all directives, where we can find real ip address
     * @var array
     */
    private $addressPath = array(
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR'
    );

    /**
     * Return real ip address
     * @return string
     */
    public function getIpAddress()
    {
        foreach ($this->addressPath as $path) {
            if (array_key_exists($path, $_SERVER) && !empty($_SERVER[$path])) {
                if (strpos($_SERVER[$path], ',') !== false) {
                    $addresses = explode(',', $_SERVER[$path]);
                    foreach ($addresses as $address) {
                        if (trim($address) != self::LOCAL_IP) {
                            return trim($address);
                        }
                    }
                } elseif ($_SERVER[$path] != self::LOCAL_IP) {
                    return $_SERVER[$path];
                }
            }
        }

        return self::LOCAL_IP;
    }

    /**
     * @return bool
     */
    public function isDebugDisplayAllowed()
    {
        return Mage::getStoreConfig('amrules/debugger_settings/enable_debug') && $this->isCurrentIpAllow();
    }

    /**
     * @return bool
     */
    private function isCurrentIpAllow()
    {
        $ip = $this->getIpAddress();
        $allowedIps = Mage::getStoreConfig('amrules/debugger_settings/allowed_ip');

        return strpos($allowedIps, $ip) !== false;
    }
}
