<?php
/**
 * Login with Amazon
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Login_Model_Resource_Login extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('amazon_login/login', 'login_id');
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        // Obey Account Sharing scope
        if (Mage::getModel('customer/config_share')->isWebsiteScope()) {
            $select->join(
                array('customer' => $this->getTable('customer/entity')),
                $this->getMainTable() . '.customer_id = customer.entity_id AND customer.website_id = ' . Mage::app()->getWebsite()->getId()
            );
        }
        return $select;
    }
}
