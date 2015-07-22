<?php
/**
 * Validate Client ID and Client Secret
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Login_Model_System_Config_Backend_Enabled extends Mage_Core_Model_Config_Data
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
          $_api = Mage::getModel('amazon_login/api');

          // REST API params
          $params = array(
              'grant_type' => 'authorization_code',
              'code' => 'SplxlOBeZQQYbYS6WxSbIA', // Dummy code from docs
              'client_id' => trim($data['client_id']),
              'client_secret' => trim($data['client_secret']),
          );

          $response = $_api->request('auth/o2/token', $params);

          if (!$response) {
              Mage::getSingleton('core/session')->addError('Error: Unable to perform HTTP request to Amazon API.');
          }
          else if ($response && isset($response['error'])) {
              if ($response['error'] == 'invalid_client') {
                  Mage::getSingleton('core/session')->addError('Client authentication failed. Please verify your Client ID and Client Secret.');
                  $this->setValue(0); // Set "Enabled" to "No"
              }
              else {
                  Mage::getSingleton('core/session')->addSuccess('Successfully connected to Amazon API with Client ID and Client Secret.');
              }
          }
        }


        return parent::save();
    }
}
?>