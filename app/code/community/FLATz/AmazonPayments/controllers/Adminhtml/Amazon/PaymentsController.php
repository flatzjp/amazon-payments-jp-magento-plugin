<?php

/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Amazon_Payments_Adminhtml_Amazon_PaymentsController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Return Amazon API
     */
    protected function _getApi()
    {
        return Mage::getSingleton('amazon_payments/api');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Manually sync order status with Amazon
     */
    public function syncAction()
    {
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order->getId()) {
            Mage::getSingleton('amazon_payments/async')->syncOrderStatus($order, true);

            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view", array('order_id' => $orderId)));
        }

    }
}