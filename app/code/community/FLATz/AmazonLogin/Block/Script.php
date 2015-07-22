<?php
/**
 * Amazon Login
 *
 * @category    Amazon
 * @package     FLATz_AmazonLogin
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonLogin_Block_Script extends Mage_Core_Block_Template
{

    /**
     * Is popup window?
     *
     * @return bool
     */
    public function isPopup()
    {
        return ($this->helper('flatz_amazon_login')->isPopup());
    }

    /**
     * Is sandbox mode?
     */
    public function isSandboxEnabled()
    {
        return (Mage::getStoreConfig('payment/flatz_amazon_payments/sandbox'));
    }

    /**
     * Get client ID
     */
    public function getClientId()
    {
        return Mage::getModel('flatz_amazon_login/api')->getClientId();
    }

    /**
     * Get additional scope
     */
    public function getAdditionalScope()
    {
         return $this->helper('flatz_amazon_login')->getAdditionalScope();
    }

    /**
     * Get login auth URL
     */
    public function getLoginAuthUrl()
    {
         return $this->helper('flatz_amazon_login')->getLoginAuthUrl();
    }

}
