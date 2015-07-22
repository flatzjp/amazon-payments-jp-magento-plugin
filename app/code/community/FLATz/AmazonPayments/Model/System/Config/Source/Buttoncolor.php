<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_System_Config_Source_Buttoncolor
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'Gold', 'label'=>Mage::helper('adminhtml')->__('Gold')),
            array('value'=>'DarkGray', 'label'=>Mage::helper('adminhtml')->__('Dark Gray')),
            array('value'=>'LightGray', 'label'=>Mage::helper('adminhtml')->__('Light Gray')),
        );
    }
}