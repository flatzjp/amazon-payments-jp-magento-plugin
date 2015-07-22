<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_Config
{
    /**#@+
     * Paths to Payment Method config
     */

    const CONFIG_XML_PATH_ENABLED        = 'payment/amazon_payments/enabled';
    const CONFIG_XML_PATH_CLIENT_ID      = 'amazon_login/settings/client_id';
    const CONFIG_XML_PATH_CLIENT_SECRET  = 'amazon_login/settings/client_secret';
    const CONFIG_XML_PATH_SELLER_ID      = 'payment/amazon_payments/seller_id';
    const CONFIG_XML_PATH_ACCESS_KEY     = 'payment/amazon_payments/access_key';
    const CONFIG_XML_PATH_ACCESS_SECRET  = 'payment/amazon_payments/access_secret';
    const CONFIG_XML_PATH_REGION         = 'payment/amazon_payments/region';
    const CONFIG_XML_PATH_SANDBOX        = 'payment/amazon_payments/sandbox';
    const CONFIG_XML_PATH_DEBUG          = 'payment/amazon_payments/debug';
    const CONFIG_XML_PATH_CHECKOUT_PAGE  = 'payment/amazon_payments/checkout_page';
    const CONFIG_XML_PATH_PAYMENT_ACTION = 'payment/amazon_payments/payment_action';
    const CONFIG_XML_PATH_ORDER_STATUS   = 'payment/amazon_payments/order_status';
    const CONFIG_XML_PATH_SHOW_PAY_CART  = 'payment/amazon_payments/show_pay_cart';
    const CONFIG_XML_PATH_STORE_NAME     = 'payment/amazon_payments/store_name';
    const CONFIG_XML_PATH_SECURE_CART    = 'payment/amazon_payments/secure_cart';
    const CONFIG_XML_PATH_IS_ASYNC       = 'payment/amazon_payments/is_async';
    const CONFIG_XML_PATH_RESTRICTED_IPS = 'payment/amazon_payments/restricted_ips';

    const CONFIG_XML_PATH_BUTTON_TYPE    = 'payment/amazon_payments/button_type';
    const CONFIG_XML_PATH_BUTTON_COLOR   = 'payment/amazon_payments/button_color';
    const CONFIG_XML_PATH_BUTTON_SIZE    = 'payment/amazon_payments/button_size';
    const CONFIG_XML_PATH_BUTTON_BADGE   = 'payment/amazon_payments/button_badge';

    const CONFIG_XML_PATH_LOGIN_ENABLED  = 'amazon_login/settings/enabled';


    /**
     * Retrieve config value for store by path
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    protected function _getStoreConfig($path, $store)
    {
        return Mage::getStoreConfig($path, $store);
    }

    /**
     * Is sandbox?
     *
     * @param   store $store
     * @return  bool
     */
    public function isSandbox($store = null)
    {
        return (bool) $this->_getStoreConfig(self::CONFIG_XML_PATH_SANDBOX, $store);
    }

    /**
     * Is module enabled?
     *
     * @param   store $store
     * @return  bool
     */
    public function isEnabled($store = null)
    {
        // Check for IP Restriction
        if ($this->_getStoreConfig(self::CONFIG_XML_PATH_RESTRICTED_IPS, $store)) {
            if ( !Mage::helper('core')->isDevAllowed() ) {
                return false;
            }
        }

        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_ENABLED, $store) && $this->getClientId($store) && $this->getClientSecret($store));
    }

    /**
     * Is guest checkout/pay only? (does not create customer account)
     *
     * @param   store $store
     * @return  bool
     */
    public function isGuestCheckout($store = null)
    {
        return ! (bool) $this->_getStoreConfig(self::CONFIG_XML_PATH_LOGIN_ENABLED, $store);
    }

    /**
     * Is debug mode enabled?
     *
     * @param   store $store
     * @return  bool
     */
    public function isDebugMode($store = null)
    {
        return (bool) $this->_getStoreConfig(self::CONFIG_XML_PATH_DEBUG, $store);
    }

    /**
     * Get client ID
     *
     * @param   store $store
     * @return  string
     */
    public function getClientId($store = null)
    {
        return trim($this->_getStoreConfig(self::CONFIG_XML_PATH_CLIENT_ID, $store));
    }

    /**
     * Get client secret
     *
     * @param   store $store
     * @return  string
     */
    public function getClientSecret($store = null)
    {
        return trim($this->_getStoreConfig(self::CONFIG_XML_PATH_CLIENT_SECRET, $store));
    }

    /**
     * Get seller/merchant ID
     *
     * @param   store $store
     * @return  string
     */
    public function getSellerId($store = null)
    {
        return trim($this->_getStoreConfig(self::CONFIG_XML_PATH_SELLER_ID, $store));
    }

    /**
     * Get API access key
     *
     * @param   store $store
     * @return  string
     */
    public function getAccessKey($store = null)
    {
        return trim($this->_getStoreConfig(self::CONFIG_XML_PATH_ACCESS_KEY, $store));
    }

    /**
     * Get API secret access key
     *
     * @param   store $store
     * @return  string
     */
    public function getAccessSecret($store = null)
    {
        return trim($this->_getStoreConfig(self::CONFIG_XML_PATH_ACCESS_SECRET, $store));
    }

    /**
     * Get API region
     *
     * @param   store $store
     * @return  string
     */
    public function getRegion($store = null)
    {
        $region = $this->_getStoreConfig(self::CONFIG_XML_PATH_REGION, $store);
        if (!$region) {
            $region = 'us';
        }
        return $region;
    }

    /**
     * Get Checkout Page type
     *
     * @param   store $store
     * @return  string
     */
    public function getCheckoutPage($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_CHECKOUT_PAGE, $store);
    }

    /**
     * Get payment action
     *
     * @param store $store
     * @return string
     */
    public function getPaymentAction($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_PAYMENT_ACTION, $store);
    }

    /**
     * Get new order status
     *
     * @param store $store
     * @return string
     */
    public function getNewOrderStatus($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_ORDER_STATUS, $store);
    }

    /**
     * Get customzied store name, if used
     *
     * @param   store $store
     * @return  string
     */
    public function getStoreName($store = null)
    {
        $storeName = $this->_getStoreConfig(self::CONFIG_XML_PATH_STORE_NAME, $store);
        if ($storeName) {
            return $storeName;
        }
        else {
            return Mage::app()->getStore()->getName();
        }
    }

    /**
     * Is Checkout using OnePage?
     *
     * @param   store $store
     * @return  bool
     */
    public function isCheckoutOnepage($store = null)
    {
        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_CHECKOUT_PAGE, $store) == 'onepage');
    }

    /**
     * Is Checkout modal?
     *
     * @param   store $store
     * @return  bool
     */
    public function isCheckoutModal($store = null)
    {
        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_CHECKOUT_PAGE, $store) == 'modal');
    }

    /**
     * Show Pay button on Cart?
     *
     * @param   store $store
     * @return  string
     */
    public function showPayOnCart($store = null)
    {
        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_SHOW_PAY_CART, $store));
    }

    /**
     * Is secure cart?
     *
     * @param   store $store
     * @return  bool
     */
    public function isSecureCart($store = null)
    {
        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_SECURE_CART, $store));
    }

    /**
     * Is async mode?
     *
     * @param   store $store
     * @return  bool
     */
    public function isAsync($store = null)
    {
        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_IS_ASYNC, $store));
    }

    /**
     * Get button type
     *
     * @param   store $store
     * @return  string
     */
    public function getButtonType($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_BUTTON_TYPE, $store);
    }

    /**
     * Get button color
     *
     * @param   store $store
     * @return  string
     */
    public function getButtonColor($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_BUTTON_COLOR, $store);
    }

    /**
     * Get button size
     *
     * @param   store $store
     * @return  string
     */
    public function getButtonSize($store = null)
    {
        return $this->_getStoreConfig(self::CONFIG_XML_PATH_BUTTON_SIZE, $store);
    }

    /**
     * Is button bade (acceptance mark) enabled?
     *
     * @param   store $store
     * @return  bool
     */
    public function isButtonBadgeEnabled($store = null)
    {
        return ($this->_getStoreConfig(self::CONFIG_XML_PATH_BUTTON_BADGE, $store));
    }

}
