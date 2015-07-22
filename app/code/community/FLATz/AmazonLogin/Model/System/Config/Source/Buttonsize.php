<?php
/**
 * Amazon Login
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Login_Model_System_Config_Source_Buttonsize
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'medium', 'label'=>Mage::helper('adminhtml')->__('Medium')),
            array('value'=>'large', 'label'=>Mage::helper('adminhtml')->__('Large')),
        );
    }
}