<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_Observer_Adminhtml
{
    /**
     * Event: adminhtml_widget_container_html_before
     */
    public function addAsyncButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $order = $block->getOrder();

            if (($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT || $order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) && $order->getPayment()->getMethodInstance()->getCode() == 'amazon_payments') {
                $block->addButton('sync_amazon', array(
                    'label'     => Mage::helper('amazon_payments')->__('Sync with Amazon'),
                    'onclick'   => "setLocation('{$block->getUrl('*/amazon_payments/sync/order_id/{$order->getId()}')}')",
                ));

                $block->removeButton('order_invoice');
                $block->removeButton('order_ship');
                $block->removeButton('order_reorder');
            }
        }

    }

}