<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_Api extends Varien_Object
{
    const ORDER_PLATFORM_ID = 'A2K7HE1S3M5XJ';

    // Amazon Authorization Order States
    const AUTH_STATUS_PENDING   = 'Pending';
    const AUTH_STATUS_OPEN      = 'Open';
    const AUTH_STATUS_DECLINED  = 'Declined';
    const AUTH_STATUS_CLOSED    = 'Closed';
    const AUTH_STATUS_COMPLETED = 'Completed';
    const AUTH_STATUS_SUSPENDED = 'Suspended';

    protected $api;
    protected $logFile = 'amazon.log';

    /**
     * Return and/or initiate Amazon's Client Library API
     *
     * @return OffAmazonPaymentsService_Client
     */
    public function getApi()
    {
        if (!$this->api) {
            $config = array(
                'merchantId'         => $this->getConfig()->getSellerId($this->getStoreId()),
                'accessKey'          => $this->getConfig()->getAccessKey($this->getStoreId()),
                'secretKey'          => $this->getConfig()->getAccessSecret($this->getStoreId()),
                'region'             => $this->getConfig()->getRegion($this->getStoreId()),
                'environment'        => ($this->getConfig()->isSandbox($this->getStoreId())) ? 'sandbox' : 'live',
                'applicationName'    => 'Amazon Payments Magento Extension',
                'applicationVersion' => (string) Mage::getConfig()->getNode('modules/Amazon_Payments/version'),
                'serviceURL'         => '',
                'widgetURL'          => '',
                'caBundleFile'       => '',
                'clientId'           => '',
             );

            $this->api = new OffAmazonPaymentsService_Client($config);
        }
        return $this->api;
    }

    /**
     * Get Amazon Payments config
     */
    public function getConfig()
    {
        return Mage::getSingleton('amazon_payments/config');
    }

    /**
     * Return required request query parameters for Amazon API
     */
    protected function _getRequiredParams()
    {
        return array(
            'SellerId' => $this->getConfig()->getSellerId($this->getStoreId()),
        );
    }

    /**
     * Is transaction/debug logging enabled?
     */
    protected function _isLoggingEnabled()
    {
        return (Mage::getStoreConfig('payment/amazon_payments/debug'));
    }

    /**
     * Perform API request with error handling
     *
     * @param string $method
     * @param array $request
     * @return Amazon Response Object
     */
    protected function request($method, $request)
    {
        $response = null;
        $request += $this->_getRequiredParams();

        $className     = 'OffAmazonPaymentsService_Model_' . ucfirst($method) . 'Request';
        $requestObject = new $className($request);

        $start_time = microtime(TRUE);

        // Execute request
        try {
            $response = $this->getApi()->$method($requestObject);
        }
        catch (Exception $exception) {
            Mage::logException($exception);
            Mage::throwException($exception);
        }

        // Debugging/Logging
        if ($this->_isLoggingEnabled()) {

            Mage::log('Request: ' . $method . "\n" . print_r($request, true), null, $this->logFile);

            $time = round(microtime(TRUE) - $start_time, 2) . ' seconds.';
            Mage::log($method . " Time: " . $time, null, $this->logFile);

            if (isset($exception)) {
                Mage::log($exception->__toString(), Zend_Log::ERR, $this->logFile);
            }
            else {

                $classMethods = get_class_methods(get_class($response));

                $fields = array();
                foreach ($classMethods as $methodName) {
                    if (substr($methodName, 0, 3) == 'get') {
                        $fields[substr($methodName, 3)] = $response->$methodName();
                    }
                }
                Mage::log('Response: ' . print_r($fields, true), null, $this->logFile);
            }
        }

        return $response;
    }

    /**
     * Authorize
     *
     * @param string $orderReferenceId
     * @param string $authorizationAmount
     * @param string $authorizationCurrency
     * @param string $softDescriptor    Description to be shown on the buyer’s payment instrument statement.
     * @param string $sellerAuthorizationNote   A description for the transaction that is displayed in emails to the buyer (also used for Sandbox Simulations).
     * @return OffAmazonPaymentsService_Model_AuthorizeResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_Authorize.html
     */
    public function authorize($orderReferenceId, $authorizationReferenceId, $authorizationAmount, $authorizationCurrency, $captureNow = false, $softDescriptor = null, $sellerAuthorizationNote = null)
    {
        $request = array(
            'AmazonOrderReferenceId' => $orderReferenceId,
            'AuthorizationReferenceId' => $authorizationReferenceId,
            'AuthorizationAmount' => array(
                'Amount'       => $authorizationAmount,
                'CurrencyCode' => $authorizationCurrency
            ),
            'CaptureNow' => $captureNow,
        );

        if (!$this->getConfig()->isAsync($this->getStoreId())) {
            $request['TransactionTimeout'] = 0; // Synchronous Mode
        }

        if ($softDescriptor) {
            $request['SoftDescriptor'] = $softDescriptor;
        }

        if ($sellerAuthorizationNote) {
            $request['SellerAuthorizationNote'] = trim($sellerAuthorizationNote);
        }

        $response = $this->request('authorize', $request);

        if ($response && $response->isSetAuthorizeResult()) {
            $result = $response->getAuthorizeResult();
            if ($result->isSetAuthorizationDetails()) {
                return $result->getAuthorizationDetails();
            }
        }

        return $response;
    }

    /**
     * Capture
     *
     * @param string $authReferenceId
     * @param string $captureReferenceId
     * @param string $captureAmount
     * @param string $captureCurrency
     * @param string $softDescriptor Description to be shown on the buyer’s payment instrument statement.
     * @return OffAmazonPaymentsService_Model_CaptureResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_Capture.html
     */
    public function capture($authReferenceId, $captureReferenceId, $captureAmount, $captureCurrency, $softDescriptor = null)
    {
        $request = array(
            'AmazonAuthorizationId' => $authReferenceId,
            'CaptureReferenceId' => $captureReferenceId,
            'CaptureAmount' => array(
                'Amount' => $captureAmount,
                'CurrencyCode' => $captureCurrency
            ),
        );

        if (!$this->getConfig()->isAsync($this->getStoreId())) {
            $request['TransactionTimeout'] = 0; // Synchronous Mode
        }

        if ($softDescriptor) {
            $request['SoftDescriptor'] = $softDescriptor;
        }

        $response = $this->request('capture', $request);

        if ($response && $response->isSetCaptureResult()) {
            $result = $response->getCaptureResult();
            if ($result->isSetCaptureDetails()) {
                return $result->getCaptureDetails();
            }
        }

        return $response;
    }

    /**
     * GetOrderReferenceDetails
     *
     * @param string $amazonOrderReferenceId
     * @param string $addressConsentToken
     * @return OffAmazonPaymentsService_Model_GetOrderReferenceDetailsResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_GetOrderReferenceDetails.html
     */
    public function getOrderReferenceDetails($amazonOrderReferenceId, $addressConsentToken = null)
    {
        $request = array(
            'AmazonOrderReferenceId' => $amazonOrderReferenceId,
            'AddressConsentToken' => $addressConsentToken,
        );

        $response = $this->request('getOrderReferenceDetails', $request);

        if ($response && $response->isSetGetOrderReferenceDetailsResult()) {
            $result = $response->getGetOrderReferenceDetailsResult();
            if ($result->isSetOrderReferenceDetails()) {
                return $result->getOrderReferenceDetails();
            }
        }

        return $response;
    }

    /**
     * GetAuthorizationDetails
     *
     * @param string $amazonAuthorizationId
     * @return OffAmazonPaymentsService_Model_GetAuthorizationDetails
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_GetAuthorizationDetails.html
     */
    public function getAuthorizationDetails($amazonAuthorizationId)
    {
        $request = array(
            'AmazonAuthorizationId' => $amazonAuthorizationId,
        );

        $response = $this->request('getAuthorizationDetails', $request);

        if ($response && $response->isSetGetAuthorizationDetailsResult()) {
            $result = $response->getGetAuthorizationDetailsResult();
            if ($result->isSetAuthorizationDetails()) {
                return $result->getAuthorizationDetails();
            }
        }

        return $response;
    }

    /**
     * SetOrderReferenceDetails
     *
     * @param string $orderReferenceId
     * @param string $orderAmount
     * @param string $orderCurrency
     * @param string $orderId
     * @param string $storeName
     * @return OffAmazonPaymentsService_Model_SetOrderReferenceDetailsResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_SetOrderReferenceDetails.html
     */
    public function setOrderReferenceDetails($orderReferenceId, $orderAmount, $orderCurrency, $orderId = '', $storeName = '')
    {
        $request = array(
            'AmazonOrderReferenceId' => $orderReferenceId,
            'OrderReferenceAttributes' => array(
                'PlatformId' => Amazon_Payments_Model_Api::ORDER_PLATFORM_ID,
                'OrderTotal' => array(
                    'Amount'       => $orderAmount,
                    'CurrencyCode' => $orderCurrency
                ),
                'SellerOrderAttributes' => array(
                    'SellerOrderId' => $orderId,
                    'StoreName'     => $storeName,
                ),
            )
        );

        $response = $this->request('setOrderReferenceDetails', $request);

        if ($response && $response->isSetSetOrderReferenceDetailsResult()) {
            $result = $response->getSetOrderReferenceDetailsResult();
            if ($result->isSetOrderReferenceDetails()) {
                return $result->getOrderReferenceDetails();
            }
        }

        return $response;
    }

    /**
     * ConfirmOrderReference
     *
     * @param string $orderReferenceId
     * @return OffAmazonPaymentsService_Model_ConfirmOrderResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_ConfirmOrderReference.html
     */
    public function confirmOrderReference($orderReferenceId)
    {
        $request = array(
            'AmazonOrderReferenceId' => $orderReferenceId
        );

        return $this->request('confirmOrderReference', $request);
    }

    /**
     * CancelOrderReference
     *
     * @param string $orderReferenceId
     * @return OffAmazonPaymentsService_Model_CancelOrderReferenceResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_CancelOrderReference.html
     */
    public function cancelOrderReference($orderReferenceId)
    {
        $request = array(
            'AmazonOrderReferenceId' => $orderReferenceId
        );
        return $this->request('cancelOrderReference', $request);
    }

    /**
     * CloseOrderReference
     *
     * @param string $orderReferenceId
     * @return OffAmazonPaymentsService_Model_CloseOrderReferenceResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_CloseOrderReference.html
     */
    public function closeOrderReference($orderReferenceId)
    {
        $request = array(
            'AmazonOrderReferenceId' => $orderReferenceId
        );
        return $this->request('closeOrderReference', $request);
    }

    /**
     * Refund
     *
     * @param string $captureReferenceId
     * @param string $refundReferenceId
     * @param string $refundAmount
     * @param string $refundCurrency
     * @param string $sellerRefundNote
     * @param string $softDescriptor
     * @return OffAmazonPaymentsService_Model_RefundResponse
     * @link http://docs.developer.amazonservices.com/en_US/off_amazon_payments/OffAmazonPayments_Refund.html
     */
    public function refund($captureReferenceId, $refundReferenceId, $refundAmount, $refundCurrency, $sellerRefundNote = null, $softDescriptor = null)
    {
        $request = array(
            'AmazonCaptureId' => $captureReferenceId,
            'RefundReferenceId' => $refundReferenceId,
            'RefundAmount' => array(
                'Amount'       => $refundAmount,
                'CurrencyCode' => $refundCurrency
            ),
        );

        if ($sellerRefundNote) {
            $request['SellerRefundNote'] = $sellerRefundNote;
        }
        if ($softDescriptor) {
            $request['SoftDescriptor'] = $softDescriptor;
        }

        $response = $this->request('refund', $request);

        if ($response && $response->isSetRefundResult()) {
            $result = $response->getRefundResult();
            if ($result->isSetRefundDetails()) {
                return $result->getRefundDetails();
            }
        }
        return $response;
    }

}

