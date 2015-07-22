<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */
class Amazon_Payments_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();

        // Acceptance Mark/Badge
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('amazon_payments/mark.phtml');

        $this->setTemplate('amazon_payments/form.phtml')
            ->setMethodLabelAfterHtml($mark->toHtml())
            ->setMethodTitle('');
    }
}