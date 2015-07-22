<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Model_System_Config_Backend_Clientsecret extends Mage_Core_Model_Config_Data
{
    private $_path = 'amazon_login/settings/client_secret';

    /**
     * Use Amazon_Login Client ID
     */
    public function save()
    {
        Mage::getConfig()->saveConfig($this->_path, $this->getValue(), $this->getScope(), $this->getScopeId());
        return parent::save();
    }

    public function afterLoad()
    {
        if ($this->website) {
          $this->value = Mage::app()->getWebsite($this->getWebsite())->getConfig($this->_path);
        }
        else {
          $this->value = Mage::getStoreConfig($this->_path);
        }
        $this->_afterLoad();
    }

}
