<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_Observer_Order
{
    /**
     * Event: sales_order_place_after
     *
     * Programmatically update customer address book with Amazon address
     */
    public function updateCustomerAddress(Varien_Event_Observer $observer)
    {
        $order    = $observer->getEvent()->getOrder();
        $customer = $order->getCustomer();
        $payment  = $order->getPayment();

        if ($customer && $customer->getId() && $payment->getMethodInstance()->getCode() == 'amazon_payments') {
            $customerAddress = $order->getShippingAddress() ? $order->getShippingAddress() : $order->getBillingAddress();

            $newAddress = Mage::getModel('customer/address')
                ->addData($customerAddress->getData())
                ->setCustomerId($customer->getId())
      			    ->setSaveInAddressBook('1');

            // Create new default shipping address
            if (!$customer->getDefaultShipping()) {
                $newAddress->setIsDefaultShipping('1');
            }
            // Check for duplicate addresses
            else {
                foreach ($customer->getAddresses() as $address) {
                    if ($address->getPostcode() == $newAddress->getPostcode() && $address->getStreet() == $newAddress->getStreet()) {
                        return;
                    }
                }
            }

            try {
                $newAddress->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }

        }
    }
}
