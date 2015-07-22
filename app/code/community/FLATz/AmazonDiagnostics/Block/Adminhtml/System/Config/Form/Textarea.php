<?php

/**
 * Amazon Diagnostics
 *
 * @category    Amazon
 * @package     Amazon_Diagnostics
 * @copyright   Copyright (c) 2015 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
class Amazon_Diagnostics_Block_Adminhtml_System_Config_Form_Textarea extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _construct() {

        parent::_construct();
        $this->setTemplate('amazon_payments/textarea.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {

        return $this->_toHtml();
    }

}
