<?php
/**
 * Validate Client ID and Client Secret
 *
 * @category    Amazon
 * @package     FLATz_AmazonLogin
 * @copyright   Copyright (c) 2014 Amazon.com
 * @copyright   Copyright (c) 2015 FLATz Inc.
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonLogin_Model_System_Config_Backend_Enabled extends Mage_Core_Model_Config_Data
{
    /**
     * Perform API call to Amazon to validate Client ID/Secret
     *
     */
    public function save()
    {
        $data = $this->getFieldsetData();
        $isEnabled = $this->getValue();

        if ($data['client_id'] && $data['client_secret']) {
          $_api = Mage::getModel('flatz_amazon_login/api');

          // REST API params
          $params = array(
              'grant_type' => 'authorization_code',
              'code' => 'SplxlOBeZQQYbYS6WxSbIA', // Dummy code from docs
              'client_id' => trim($data['client_id']),
              'client_secret' => trim($data['client_secret']),
          );

          $response = $_api->request('auth/o2/token', $params);

          if (!$response) {
              Mage::getSingleton('core/session')->addError(Mage::helper('flatz_amazon_login')->__('Error: Unable to perform HTTP request to Amazon API.'));
          }
          else if ($response && isset($response['error'])) {
              if ($response['error'] == 'invalid_client') {
                  Mage::getSingleton('core/session')->addError(Mage::helper('flatz_amazon_login')->__('Client authentication failed. Please verify your Client ID and Client Secret.'));
                  $this->setValue(0); // Set "Enabled" to "No"
              }
              else {
                  Mage::getSingleton('core/session')->addSuccess(Mage::helper('flatz_amazon_login')->__('Successfully connected to Amazon API with Client ID and Client Secret.'));
              }
          }
        }


        return parent::save();
    }
}
?>