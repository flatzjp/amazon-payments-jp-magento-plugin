<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Block_Review_Info extends Mage_Sales_Block_Items_Abstract
{
    public function getItems()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
    }

    public function getTotals()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getTotals();
    }
}
