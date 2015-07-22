<?php
/**
 * Amazon Payments Helper
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get config
     */
    public function getConfig()
    {
        return Mage::getSingleton('amazon_payments/config');
    }

    /**
     * Retrieve seller ID
     *
     * @return string
     */
    public function getSellerId()
    {
        return $this->getConfig()->getSellerId();
    }

    /**
     * Retrieve client ID
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->getConfig()->getClientId();
    }

    /**
     * Retrieve client secret
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->getConfig()->getClientSecret();
    }

    /**
     * Return URL to use for checkout
     *
     * @param $hasToken   Amazon token is passed in query paramaters to log user in
     */
    public function getCheckoutUrl($hasToken = true)
    {
        $_config = $this->getConfig();

        if ($_config->isCheckoutOnepage()) {
            if ($hasToken) {
                return $this->getOnepageCheckoutUrl();
            }
            else {
                return Mage::getUrl('checkout/onepage', array('_secure'=>true));
            }
        }
        else if ($_config->isCheckoutModal()) {
            return $this->getModalUrl();
        }
        else {
            return $this->getStandaloneUrl();
        }
    }

    /**
     * Return onepage checkout URL
     */
    public function getOnepageCheckoutUrl()
    {
        return Mage::getUrl('amazon_payments/onepage', array('_forced_secure'=>true));
    }

    /**
     * Retrieve stand alone URL
     *
     * @return string
     */
    public function getStandaloneUrl()
    {
        return Mage::getUrl('checkout/amazon_payments', array('_secure'=>true));
    }

    /**
     * Retrieve popup modal URL
     *
     * @return string
     */
    public function getModalUrl()
    {
        return Mage::getUrl('checkout/cart?amazon_modal=1', array('_secure'=>true));
    }

    /**
     * Does product attribute allow purchase with Amazon payments?
     */
    public function isEnableProductPayments()
    {
        // Viewing single product
        if ($_product = Mage::registry('current_product')) {
            return !$_product->getDisableAmazonpayments();
        }
        // Check cart products
        else {
            $cart = Mage::getModel('checkout/cart')->getQuote();
            foreach ($cart->getAllItems() as $item) {
                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($_product->getDisableAmazonpayments()) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * Does user have Amazon order reference for checkout?
     *
     * @return string
     */
    public function isCheckoutAmazonSession()
    {
        return (Mage::getSingleton('checkout/session')->getAmazonAccessToken());
    }

    /**
     * Is sandbox mode?
     *
     * @return bool
     */
    public function isAmazonSandbox()
    {
        return $this->getConfig()->isSandbox();
    }

    /**
     * Clear session data
     */
    public function clearSession()
    {
        Mage::getSingleton('checkout/session')->unsAmazonAccessToken();
    }

    /**
     * Change OnePage login block?
     *
     * amazon_payments.xml template helper
     */
    public function switchOnepageLoginTemplateIf($amazonTemplate, $defaultTemplate)
    {
        if ($this->getConfig()->isCheckoutOnepage()) {
            return $amazonTemplate;
        } else {
            return $defaultTemplate;
        }
    }

    /**
     * Show modal?
     */
    public function showModal()
    {
        return (Mage::app()->getRequest()->getParam('amazon_modal') && $this->getConfig()->isCheckoutModal());
    }

    /**
     * Is button bade (acceptance mark) enabled?
     *
     * @return bool
     */
    public function isButtonBadgeEnabled($store = null)
    {
        return ($this->getConfig()->isButtonBadgeEnabled() && $this->getConfig()->isEnabled());
    }
}