<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_System_Config_Source_Paymentaction
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'authorize_capture', 'label'=>Mage::helper('adminhtml')->__('Authorize and Capture')),
            array('value'=>'authorize', 'label'=>Mage::helper('adminhtml')->__('Authorize Only')),
        );
    }
}
