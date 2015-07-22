<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_System_Config_Source_Checkoutpage
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'onepage', 'label'=>Mage::helper('adminhtml')->__('Magento Core OnePage Checkout')),
            array('value'=>'amazon', 'label'=>Mage::helper('adminhtml')->__('Amazon Standalone Checkout')),
            array('value'=>'modal', 'label'=>Mage::helper('adminhtml')->__('Amazon Standalone Checkout in a Modal Window')),
        );
    }
}
