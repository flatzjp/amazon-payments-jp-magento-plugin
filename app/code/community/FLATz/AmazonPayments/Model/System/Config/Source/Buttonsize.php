<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_System_Config_Source_Buttonsize
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'small', 'label'=>Mage::helper('adminhtml')->__('Small')),
            array('value'=>'medium', 'label'=>Mage::helper('adminhtml')->__('Medium')),
            array('value'=>'large', 'label'=>Mage::helper('adminhtml')->__('Large')),
            array('value'=>'x-large', 'label'=>Mage::helper('adminhtml')->__('X-large')),
        );
    }
}