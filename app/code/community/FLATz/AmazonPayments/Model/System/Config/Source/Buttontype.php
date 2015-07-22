<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_System_Config_Source_Buttontype
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