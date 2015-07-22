<?php

/**
 * Amazon Diagnostics
 *
 * @category    Amazon
 * @package     FLATz_AmazonDiagnostics
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
class FLATz_AmazonDiagnostics_Block_Adminhtml_System_Config_Form_Textarea extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _construct() {

        parent::_construct();
        $this->setTemplate('flatz_amazon_payments/textarea.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        return $this->_toHtml();
    }

}
