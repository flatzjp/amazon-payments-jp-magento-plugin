<?php
/**
 * Login with Amazon Helper
 *
 * @category    Amazon
 * @package     FLATz_AmazonLogin
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonLogin_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Retrieve customer verify url
     *
     * @return string
     */
    public function getVerifyUrl()
    {
        return $this->_getUrl('flatz_amazon_login/customer/verify');
    }

    /**
     * Retrieve Amazon Profile in session
     */
    public function getAmazonProfileSession()
    {
        return Mage::getSingleton('customer/session')->getAmazonProfile();
    }

    /**
     * Retreive additional login access scope
     */
    public function getAdditionalScope()
    {
        $scope = trim(Mage::getStoreConfig('flatz_amazon_login/settings/additional_scope'));
        return ($scope) ? ' ' . $scope : '';
    }

    /**
     * Return login authorize URL
     *
     * @return string
     */
    public function getLoginAuthUrl()
    {
        return $this->_getUrl('flatz_amazon_login/customer/authorize', array('_forced_secure' => true));
    }

    /**
     * Is login a popup or full-page redirect?
     */
    public function isPopup()
    {
        return (Mage::getStoreConfig('flatz_amazon_login/settings/popup'));
    }

    /**
     * Is FLATz_AmazonLogin enabled in config?
     */
    public function isEnabled()
    {
        return (Mage::getStoreConfig('flatz_amazon_login/settings/enabled'));
    }


}
