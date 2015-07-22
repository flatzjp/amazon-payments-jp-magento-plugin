<?php

/**
 * Amazon Payments Helper
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
class FLATz_AmazonPayments_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get config
     */
    public function getConfig() {
        return Mage::getSingleton('flatz_amazon_payments/config');
    }

    /**
     * Retrieve seller ID
     *
     * @return string
     */
    public function getSellerId() {
        return $this->getConfig()->getSellerId();
    }

    /**
     * Retrieve client ID
     *
     * @return string
     */
    public function getClientId() {
        return $this->getConfig()->getClientId();
    }

    /**
     * Retrieve client secret
     *
     * @return string
     */
    public function getClientSecret() {
        return $this->getConfig()->getClientSecret();
    }

    /**
     * Return URL to use for checkout
     *
     * @param $hasToken   Amazon token is passed in query paramaters to log user in
     */
    public function getCheckoutUrl($hasToken = true) {
        $_config = $this->getConfig();

        if ($_config->isCheckoutOnepage()) {
            if ($hasToken) {
                return $this->getOnepageCheckoutUrl();
            } else {
                return Mage::getUrl('checkout/onepage', array('_secure' => true));
            }
        } else if ($_config->isCheckoutModal()) {
            return $this->getModalUrl();
        } else {
            return $this->getStandaloneUrl();
        }
    }

    /**
     * Return onepage checkout URL
     */
    public function getOnepageCheckoutUrl() {
        return Mage::getUrl('flatz_amazon_payments/onepage', array('_forced_secure' => true));
    }

    /**
     * Retrieve stand alone URL
     *
     * @return string
     */
    public function getStandaloneUrl() {
        return Mage::getUrl('checkout/flatz_amazon_payments', array('_secure' => true));
    }

    /**
     * Retrieve popup modal URL
     *
     * @return string
     */
    public function getModalUrl() {
        return Mage::getUrl('checkout/cart?amazon_modal=1', array('_secure' => true));
    }

    /**
     * Does product attribute allow purchase with Amazon payments?
     */
    public function isEnableProductPayments() {
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
    public function isCheckoutAmazonSession() {
        return (Mage::getSingleton('checkout/session')->getAmazonAccessToken());
    }

    /**
     * Is sandbox mode?
     *
     * @return bool
     */
    public function isAmazonSandbox() {
        return $this->getConfig()->isSandbox();
    }

    /**
     * Clear session data
     */
    public function clearSession() {
        Mage::getSingleton('checkout/session')->unsAmazonAccessToken();
    }

    /**
     * Change OnePage login block?
     *
     * flatz_amazon_payments.xml template helper
     */
    public function switchOnepageLoginTemplateIf($amazonTemplate, $defaultTemplate) {
        if ($this->getConfig()->isCheckoutOnepage()) {
            return $amazonTemplate;
        } else {
            return $defaultTemplate;
        }
    }

    /**
     * Show modal?
     */
    public function showModal() {
        return (Mage::app()->getRequest()->getParam('amazon_modal') && $this->getConfig()->isCheckoutModal());
    }

    /**
     * Is button bade (acceptance mark) enabled?
     *
     * @return bool
     */
    public function isButtonBadgeEnabled($store = null) {
        return ($this->getConfig()->isButtonBadgeEnabled() && $this->getConfig()->isEnabled());
    }

    /**
     * 
     * @param string $name
     * @return string
     */
    public function getJpRegionNameToRegionName($name) {
        if (isset($this->_regionMap[$name])) {
            return $this->_regionMap[$name];
        } else {
            return null;
        }
    }

    protected $_regionMap = array(
        '北海道' => 'Hokkaido',
        '青森県' => 'Aomori',
        '岩手県' => 'Iwate',
        '宮城県' => 'Miyagi',
        '秋田県' => 'Akita',
        '山形県' => 'Yamagata',
        '福島県' => 'Fukushima',
        '茨城県' => 'Ibaragi',
        '栃木県' => 'Tochigi',
        '群馬県' => 'Gunma',
        '埼玉県' => 'Saitama',
        '千葉県' => 'Chiba',
        '東京都' => 'Tokyo',
        '神奈川県' => 'Kanagawa',
        '新潟県' => 'Niigata',
        '富山県' => 'Toyama',
        '石川県' => 'Ishikawa',
        '福井県' => 'Fukui',
        '山梨県' => 'Yamanashi',
        '長野県' => 'Nagano',
        '岐阜県' => 'Gifu',
        '静岡県' => 'Shizuoka',
        '愛知県' => 'Aichi',
        '三重県' => 'Mie                    ',
        '滋賀県' => 'Shiga',
        '京都府' => 'Kyoto',
        '大阪府' => 'Osaka',
        '兵庫県' => 'Hyogo',
        '奈良県' => 'Nara',
        '和歌山県' => 'Wakayama',
        '鳥取県' => 'Tottori',
        '島根県' => 'Shimane',
        '岡山県' => 'Okayama',
        '広島県' => 'Hiroshima',
        '山口県' => 'Yamaguchi',
        '徳島県' => 'Tokushima',
        '香川県' => 'Kagawa',
        '愛媛県' => 'Ehime',
        '高知県' => 'Kochi',
        '福岡県' => 'Fukuoka',
        '佐賀県' => 'Saga',
        '長崎県' => 'Nagasaki',
        '熊本県' => 'Kumamoto',
        '大分県' => 'Oita',
        '宮崎県' => 'Miyazaki',
        '鹿児島県' => 'Kagoshima',
        '沖縄県' => 'Okinawa'
    );

}
