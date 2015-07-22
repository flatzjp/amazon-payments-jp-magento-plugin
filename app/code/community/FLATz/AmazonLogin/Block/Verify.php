<?php
/**
 * Amazon Login
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Login_Block_Verify extends Mage_Core_Block_Template
{
    public function getEmail()
    {
        $profile = $this->helper('amazon_login')->getAmazonProfileSession();
        return $profile['email'];
    }

    public function getPostActionUrl()
    {
        return $this->helper('amazon_login')->getVerifyUrl() . '?redirect=' . htmlentities($this->getRequest()->getParam('redirect'));
    }

    public function getForgotPasswordUrl()
    {
         return $this->helper('customer')->getForgotPasswordUrl();
    }

}
