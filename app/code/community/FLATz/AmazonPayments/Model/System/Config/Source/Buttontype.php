<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_System_Config_Source_Buttontype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'PwA', 'label'=>Mage::helper('adminhtml')->__('Pay with Amazon')),
            array('value'=>'Pay', 'label'=>Mage::helper('adminhtml')->__('Pay')),
            array('value'=>'A', 'label'=>Mage::helper('adminhtml')->__('Amazon logo')),
        );
    }
}