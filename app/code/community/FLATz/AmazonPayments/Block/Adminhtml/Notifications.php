<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{

    /**
     * Is payment config enabled and missing values?
     *
     * @return bool
     */
    public function isMissingPaymentConfig()
    {
        $_config = Mage::getSingleton('amazon_payments/config');

        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            if ($_config->isEnabled($store)) {
                if (
                    !$_config->getClientId($store) ||
                    !$_config->getClientSecret($store) ||
                    !$_config->getSellerId($store) ||
                    !$_config->getAccessKey($store) ||
                    !$_config->getAccessSecret($store)
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get tax management url
     *
     * @return string
     */
    public function getInfoUrl()
    {
        return Mage::app()->getStore()->getConfig(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_URL);
    }

    /**
     * Get payment management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/payment');
    }

    /**
     * Is patch requried for 1.5?
     *
     * @return string
     */
    public function isPaymentPatchRequired()
    {
        $version = Mage::getVersionInfo();

        if ($version['major'] == '1' && $version['minor'] == '5') {
            $payment = Mage::getSingleton('sales/order_payment');

            if (!method_exists($payment, 'lookupTransaction')) {
                return true;
            }

            return false;
        }
    }


}

