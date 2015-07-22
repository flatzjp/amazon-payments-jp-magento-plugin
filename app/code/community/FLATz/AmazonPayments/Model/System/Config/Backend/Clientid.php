<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     FLATz_AmazonPayments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonPayments_Model_System_Config_Backend_Clientid extends Mage_Core_Model_Config_Data
{
    private $_path = 'flatz_amazon_login/settings/client_id';

    /**
     * Use FLATz_AmazonLogin Client ID
     */
    public function save()
    {
        Mage::getConfig()->saveConfig($this->_path, $this->value);
        return parent::save();
    }

    public function afterLoad()
    {
        $this->value = Mage::getStoreConfig($this->_path);
        $this->_afterLoad();
    }

}