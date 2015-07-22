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

class FLATz_AmazonPayments_Model_Async extends Mage_Core_Model_Abstract
{

    /**
     * Return Amazon API
     */
    protected function _getApi()
    {
        return Mage::getSingleton('flatz_amazon_payments/api');
    }

    /**
     * Create invoice
     */
    protected function _createInvoice(Mage_Sales_Model_Order $order, $captureReferenceIds)
    {
        if ($order->canInvoice()) {
            $transactionSave = Mage::getModel('core/resource_transaction');

            // Create invoice
            $invoice = $order
                ->prepareInvoice()
                ->register();
            $invoice->setTransactionId(current($captureReferenceIds));

            $transactionSave
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            return $transactionSave->save();
        }

        return false;
    }

    /**
     * Poll Amazon API to receive order status and update Magento order.
     */
    public function syncOrderStatus(Mage_Sales_Model_Order $order, $isManualSync = false)
    {
        $_api = $this->_getApi();
        $message = '';

        try {

            $amazonOrderReference = $order->getPayment()->getAdditionalInformation('order_reference');

            $orderReferenceDetails = $_api->getOrderReferenceDetails($amazonOrderReference);

            if ($orderReferenceDetails) {

                // Retrieve Amazon Authorization Details

                // Last transaction ID is Amazon Authorize Reference ID
                $lastAmazonReference = $order->getPayment()->getLastTransId();
                $resultAuthorize = $this->_getApi()->getAuthorizationDetails($lastAmazonReference);
                $amazonAuthorizationState = $resultAuthorize->getAuthorizationStatus()->getState();
                $reasonCode = $resultAuthorize->getAuthorizationStatus()->getReasonCode();

                // Re-authorize if holded, an Open order reference, and manual sync
                if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED && $orderReferenceDetails->getOrderReferenceStatus()->getState() == 'Open' && $isManualSync) {
                    $payment = $order->getPayment();
                    $amount = $payment->getAmountOrdered();
                    $method = $payment->getMethodInstance();

                    // Re-authorize
                    $payment->setTransactionId($amazonOrderReference);

                    switch ($method->getConfigData('payment_action')) {
                        case $method::ACTION_AUTHORIZE:
                            $method->authorize($payment, $amount, false);
                            break;

                        case $method::ACTION_AUTHORIZE_CAPTURE:
                            $method->authorize($payment, $amount, true);
                            break;
                        default:
                            break;
                    }

                    // Resync
                    $this->syncOrderStatus($order);
                    return;
                }

                $message = Mage::helper('flatz_amazon_payments')->__('Sync with Amazon: Authorization state is %s.', $amazonAuthorizationState);

                switch ($amazonAuthorizationState) {
                  // Pending (All Authorization objects are in the Pending state for 30 seconds after Authorize request)
                  case FLATz_AmazonPayments_Model_Api::AUTH_STATUS_PENDING:
                      $message .= Mage::helper('flatz_amazon_payments')->__(' (Payment is currently authorizing. Please try again momentarily.)');
                      break;

                  // Declined
                  case FLATz_AmazonPayments_Model_Api::AUTH_STATUS_DECLINED:
                      if ($order->getState() != Mage_Sales_Model_Order::STATE_HOLDED) {
                          $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true);
                      }

                      $message .= Mage::helper('flatz_amazon_payments')->__(" Order placed on hold due to %s. Please direct customer to Amazon Payments site to update their payment method.", $reasonCode);
                      break;

                  // Open (Authorize Only)
                  case FLATz_AmazonPayments_Model_Api::AUTH_STATUS_OPEN:
                      $order->setState(Mage_Sales_Model_Order::STATE_NEW);
                      $order->setStatus($_api->getConfig()->getNewOrderStatus());
                      break;

                  // Closed (Authorize and Capture)
                  case FLATz_AmazonPayments_Model_Api::AUTH_STATUS_CLOSED:

                      // Payment captured; create invoice
                      if ($reasonCode == 'MaxCapturesProcessed') {
                          $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
                          $order->setStatus($_api->getConfig()->getNewOrderStatus());

                          if ($this->_createInvoice($order, $orderReferenceDetails->getIdList()->getmember())) {
                              $message .= ' ' . Mage::helper('flatz_amazon_payments')->__('Invoice created.');
                          }
                      }
                      else {
                          $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true);

                          $message .= Mage::helper('flatz_amazon_payments')->__(' Unable to create invoice due to Authorization Reason Code: %s', $reasonCode);
                      }

                      break;
                }

                // Update order
                if ($amazonAuthorizationState != FLATz_AmazonPayments_Model_Api::AUTH_STATUS_PENDING) {
                    $order->addStatusToHistory($order->getStatus(), $message, false);
                    $order->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess($message);
            }
        } catch (Exception $e) {
            // Change order to "On Hold"
            if ($order->getState() != Mage_Sales_Model_Order::STATE_HOLDED) {
                $message = Mage::helper('flatz_amazon_payments')->__('Error exception during sync. Please check exception.log');
                $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true);
                $order->addStatusToHistory($order->getStatus(), $message, false);
                $order->save();
            }

            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    /**
     * Magento cron to sync Amazon orders
     */
    public function cron()
    {
        if ($this->_getApi()->getConfig()->isAsync()) {

            $orderCollection = Mage::getModel('sales/order_payment')
                ->getCollection()
                ->join(array('order'=>'sales/order'), 'main_table.parent_id=order.entity_id', 'state')
                ->addFieldToFilter('method', 'flatz_amazon_payments')
                ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) // Async
                ;

            foreach ($orderCollection as $orderRow) {
                $order = Mage::getModel('sales/order')->load($orderRow->getParentId());
                $this->syncOrderStatus($order);
            }
        }
    }
}
