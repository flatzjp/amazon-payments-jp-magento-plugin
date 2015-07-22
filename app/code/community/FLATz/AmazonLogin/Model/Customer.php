<?php
/**
 * Login with Amazon Customer Model
 *
 * @category    Amazon
 * @package     Amazon_Login
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Login_Model_Customer extends Mage_Customer_Model_Customer
{
    /**
     * Log user in via Amazon token
     *
     * @param string $token
     *   Amazon Access Token
     * @return object $customer
     */
    public function loginWithToken($token, $redirectOnVerify = '')
    {
        $amazonProfile = $this->getAmazonProfile($token);

        if ($amazonProfile && isset($amazonProfile['user_id']) && isset($amazonProfile['email'])) {
            // Load Amazon Login association
            $row = Mage::getModel('amazon_login/login')->load($amazonProfile['user_id'], 'amazon_uid');
            
            if ($row->getLoginId()) {
                // Load customer by id
                $this->setWebsiteId(Mage::app()->getWebsite()->getId())->load($row->getCustomerId());
            } else {
                // Load customer by email
                $this->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($amazonProfile['email']);
            }

            // If Magento customer account exists and there is no association, then the Magento account
            // must be verified, as Amazon does not verify email addresses.
            if (!$row->getLoginId() && $this->getId()) {
                Mage::getSingleton('customer/session')->setAmazonProfile($amazonProfile);
                Mage::getSingleton('checkout/session')->setAmazonAccessTokenVerify($token);

                Mage::app()->getResponse()
                    ->setRedirect(Mage::helper('amazon_login')->getVerifyUrl() . '?redirect=' . $redirectOnVerify, 301)
                    ->sendResponse();

                exit;

            }
            // Log user in
            else {
                // Create account
                if (!$this->getId()) {
                    $this->createCustomer($amazonProfile);
                }
                Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($this);

                // Use Pay with Amazon for checkout (if Amazon_Payments enabled)
                Mage::getSingleton('checkout/session')->setAmazonAccessToken($token);
            }


        }

        return $this;
    }

    /**
     * Get Amazon Profile
     */
    public function getAmazonProfile($token)
    {
        return Mage::getModel('amazon_login/api')->request('user/profile?access_token=' . urlencode($token));
    }

    /**
     * Get Amazon Name
     *
     * @return array
     */
    public function getAmazonName($name)
    {
        // if the user only has a first name, handle accordingly
        $trimmedName = trim($name);
        if(strpos($trimmedName,' ')===false) {
            return array($trimmedName,'.');
        }

        $firstName = substr($name, 0, strrpos($name, ' '));
        $lastName  = substr($name, strlen($firstName) + 1);
        return array($firstName, $lastName);
    }

    /**
     * Create a new customer
     *
     * @param array $amazonProfile
     *   Associative array containing email and name
     * @return object $customer
     */
    public function createCustomer($amazonProfile)
    {
        list($firstName, $lastName) = $this->getAmazonName($amazonProfile['name']);

        try {
            $this
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->setEmail($amazonProfile['email'])
                ->setPassword($this->generatePassword(8))
                ->setFirstname($firstName)
                ->setLastname($lastName)
                ->setConfirmation(null)
                ->setIsActive(1)
                ->save()
                ->sendNewAccountEmail('registered', '', Mage::app()->getStore()->getId());

            $this->createAssociation($amazonProfile, $this->getId());

        }
        catch (Exception $ex) {
            Mage::logException($ex);
        }

        return $this;
    }

    /**
     * Create association between Amazon Profile and Customer
     */
    public function createAssociation($amazonProfile, $customer_id)
    {
        Mage::getModel('amazon_login/login')
            ->setCustomerId($customer_id)
            ->setAmazonUid($amazonProfile['user_id'])
            ->save();

    }

}

