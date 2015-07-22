<?php
/**
 * Amazon Payments
 *
 * @category    Amazon
 * @package     Amazon_Payments
 * @copyright   Copyright (c) 2014 Amazon.com
 * @license     http://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Amazon_Payments_Block_Onepage extends Mage_Checkout_Block_Onepage_Abstract
{

    /**
     * Initialize login step, as user may have logged in from cart page.
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('login', array('label'=>Mage::helper('checkout')->__('Checkout Method'), 'allow'=>true));
        parent::_construct();
    }

    /**
     * Get 'one step checkout' step data
     *
     * @return array
     */
    public function getSteps()
    {

        $steps = array();
        $stepCodes = $this->_getStepCodes();

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }

        return $steps;
    }

    /**
     * Get active step
     *
     * @return string
     */
    public function getActiveStep()
    {
        return 'widget';
    }

    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        return array('login', 'widget', 'shipping_method', 'review');
    }
}
