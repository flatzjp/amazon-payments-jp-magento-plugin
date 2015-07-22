<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Block_Checkout extends Mage_Core_Block_Template
{

    /**
     * Return seller ID
     */
    public function getSellerId()
    {
        return $this->helper('amazon_payments')->getSellerId();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal()
    {
        return (float)Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal();
    }

    /**
     * Is debug mode?
     */
    public function isDebugMode()
    {
        return Mage::getSingleton('amazon_payments/config')->isDebugMode();
    }

}
