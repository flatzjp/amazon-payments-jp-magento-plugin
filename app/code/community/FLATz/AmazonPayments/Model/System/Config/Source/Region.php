<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @copyright   Copyright (c) 2015 FLATz Inc.
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_System_Config_Source_region
{
    public function toOptionArray()
    {
        return array(
//            array('value'=>'us', 'label'=>Mage::helper('adminhtml')->__('United States')), 
            array('value'=>'jp', 'label'=>Mage::helper('adminhtml')->__('Japanese')),
//            array('value'=>'eu', 'label'=>Mage::helper('adminhtml')->__('Europe')), 
        );
    }
}

