<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_System_Config_Source_Paymentaction
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'authorize_capture', 'label'=>Mage::helper('adminhtml')->__('Authorize and Capture')),
            array('value'=>'authorize', 'label'=>Mage::helper('adminhtml')->__('Authorize Only')),
            array('value'=>'new', 'label'=>Mage::helper('adminhtml')->__('None')),
        );
    }
}
