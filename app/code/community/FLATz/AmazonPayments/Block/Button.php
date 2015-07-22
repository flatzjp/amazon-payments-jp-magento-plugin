<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Block_Button extends Mage_Core_Block_Template
{
    /**
     * Return URL to use for checkout
     */
    public function getCheckoutUrl()
    {
        return $this->helper('flatz_amazon_payments')->getCheckoutUrl();
    }

    /**
     * Return onepage checkout URL
     */
    public function getOnepageCheckoutUrl()
    {
        return $this->helper('flatz_amazon_payments')->getOnepageCheckoutUrl();
    }

    /**
     * Return CSS identifier to use for Amazon button
     */
    public function getAmazonPayButtonId() {
        return $this->getNameInLayout();
    }

    /**
     * Return seller ID
     */
    public function getSellerId()
    {
        return $this->helper('flatz_amazon_payments')->getSellerId();
    }

    /**
     * Get login auth URL
     */
    public function getLoginAuthUrl()
    {
         return $this->getUrl('flatz_amazon_payments/checkout/authorize', array('_forced_secure'=>true));
    }

    /**
     * Get additional login scope
     */
    public function getAdditionalScope()
    {
         return $this->helper('flatz_amazon_login')->getAdditionalScope();
    }

    /**
     * Get button type
     */
    public function getButtonType()
    {
         return Mage::getSingleton('flatz_amazon_payments/config')->getButtonType();
    }

    /**
     * Get button size
     */
    public function getButtonSize()
    {
         return Mage::getSingleton('flatz_amazon_payments/config')->getButtonSize();
    }

    /**
     * Get button color
     */
    public function getButtonColor()
    {
         return Mage::getSingleton('flatz_amazon_payments/config')->getButtonColor();
    }

    /**
     * Is Disabled?
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }
    /**
     * Is Amazon Login enabled?
     *
     * @return bool
     */
    public function isAmazonLoginEnabled()
    {
        return $this->helper('flatz_amazon_login')->isEnabled();
    }

    /**
     * Is button enabled?
     *
     * @return bool
     */
    public function isAmazonPayButtonEnabled()
    {
        if (!Mage::getSingleton('flatz_amazon_payments/config')->isEnabled()) {
            return false;
        }
        // Viewing single product
        else if (Mage::registry('current_product')) {
             return $this->helper('flatz_amazon_payments')->isEnableProductPayments();
        }
        else {
            return ($this->helper('flatz_amazon_payments')->isEnableProductPayments() && (!Mage::getSingleton('flatz_amazon_payments/config')->isCheckoutOnepage() || Mage::getSingleton('flatz_amazon_payments/config')->showPayOnCart()));
        }
    }

    /**
     * Is button badge enabled?
     *
     * @return bool
     */
    public function isButtonBadgeEnabled()
    {
        return $this->helper('flatz_amazon_payments')->isButtonBadgeEnabled();
    }

    /**
     * Is Amazon Payments enabled on product level?
     */
    public function isEnableProductPayments()
    {
        return $this->helper('flatz_amazon_payments')->isEnableProductPayments();
    }

    /**
     * Is popup window?
     *
     * @return bool
     */
    public function isPopup()
    {
        return ($this->helper('flatz_amazon_login')->isPopup());
    }

}