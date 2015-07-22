<?php
/**
 * Login with Amazon
 *
 * @category    Amazon
 * @package     FLATz_AmazonLogin
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonLogin_Model_Resource_Login extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('flatz_amazon_login/login', 'login_id');
    }
}
