<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Block_Link extends Mage_Core_Block_Template
{
    public function getCheckoutUrl()
    {
        return Mage::helper('amazon_payments/data')->getStandaloneUrl();
    }

    public function isDisabled()
    {
        return !Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount();
    }

    public function isAmazonPayButtonEnabled()
    {
        return true;
    }

    public function getAmazonPayButtonId() {
        return $this->getNameInLayout();
    }

    public function getSellerId()
    {
        return $this->helper('amazon_payments')->getSellerId();
    }

}
