<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_Observer_Action
{
    /**
     * Event: controller_action_predispatch_checkout_cart_index
     *
     * Redirect to HTTPS cart page
     */
    public function secureCart(Varien_Event_Observer $observer)
    {
        if (!Mage::app()->getStore()->isCurrentlySecure() && strpos(Mage::getStoreConfig('web/secure/base_url'), 'https') !== false && Mage::getSingleton('flatz_amazon_payments/config')->isSecureCart()) {
            $redirectUrl = Mage::getUrl('checkout/cart/', array('_forced_secure' => true));
            Mage::app()->getResponse()->setRedirect($redirectUrl)->sendResponse();
        }
    }
}