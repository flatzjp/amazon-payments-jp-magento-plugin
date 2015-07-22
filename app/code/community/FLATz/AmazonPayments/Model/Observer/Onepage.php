<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_Observer_Onepage
{
    protected $_quote;

    /**
     * Event: controller_action_layout_load_before
     *
     * Load layout handle override for OnePage
     */
    public function beforeLoadLayout(Varien_Event_Observer $observer)
    {
        $_helper = Mage::helper('amazon_payments/data');
        $fullActionName = $observer->getEvent()->getAction()->getFullActionName();


        if ($fullActionName == 'checkout_onepage_index' && $_helper->getConfig()->isEnabled() && $_helper->isCheckoutAmazonSession() && $_helper->isEnableProductPayments()) {
            // If One Page is disable and user has active Amazon Session, redirect to standalone checkout
            if (!$_helper->getConfig()->isCheckoutOnepage()) {
                Mage::app()->getFrontController()->getResponse()->setRedirect($_helper->getStandaloneUrl());
            }

            // Use custom checkout layout
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('checkout_onepage_index_amazon_payments');
        }
    }


    /**
     * Event: custom_quote_process
     *
     * Clear address if user switches from Amazon Checkout to third-party checkout
     */
    public function clearShippingAddress(Varien_Event_Observer $observer)
    {
        $_helper = Mage::helper('amazon_payments/data');
        $session = $observer->getEvent()->getCheckoutSession();

        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();
        $action_reset = array('opc_index_index', 'firecheckout_index_index');

        if (in_array($action, $action_reset) && $session && $session->getCheckoutState() == 'begin' && $session->getAmazonAddressId() && $session->getQuoteId() && $this->_quote === null) {

            $quote = $this->_quote = Mage::getModel('sales/quote')->setStoreId(Mage::app()->getStore()->getId())->load($session->getQuoteId());
            $address = $quote->getShippingAddress();

            if ($address->getId() == $session->getAmazonAddressId()) {

                $reset = array(
                    'firstname'   => '',
                    'lastname'    => '',
                    'street'      => '',
                    'city'        => '',
                    'region_id'   => '',
                    'postcode'    => '',
                    'country_id'  => '',
                    'telephone'   => '',
                );

                $address->setData($reset);
                $quote->setShippingAddress($address);
                $quote->setBillingAddress($address);

                $quote->collectTotals()->save();

                $session->unsAmazonAddressId();
            }

        }
    }

}