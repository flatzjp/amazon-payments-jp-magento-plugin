<?php
/**
 * Amazon Login
 *
 * @category    Amazon
 * @package     FLATz_AmazonLogin
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class FLATz_AmazonLogin_Block_Verify extends Mage_Core_Block_Template
{
    public function getEmail()
    {
        $profile = $this->helper('flatz_amazon_login')->getAmazonProfileSession();
        return $profile['email'];
    }

    public function getPostActionUrl()
    {
        return $this->helper('flatz_amazon_login')->getVerifyUrl() . '?redirect=' . htmlentities($this->getRequest()->getParam('redirect'));
    }

    public function getForgotPasswordUrl()
    {
         return $this->helper('customer')->getForgotPasswordUrl();
    }

}
