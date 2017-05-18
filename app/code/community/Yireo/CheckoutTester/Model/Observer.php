<?php
/**
 * Yireo CheckoutTester
 *
 * @author Yireo
 * @package CheckoutTester
 * @copyright Copyright 2016
 * @license Open Source License (OSL v3)
 * @link https://www.yireo.com
 */

/**
 * CheckoutTester observer to various Magento events
 */
class Yireo_CheckoutTester_Model_Observer
{
    /**
     * Method fired on the event <controller_action_predispatch>
     *
     * @param Varien_Event_Observer $observer
     * @return Yireo_CheckoutTester_Model_Observer
     * @deprecated
     */
    public function controllerActionPredispatch($observer)
    {
        return $this;
    }
}
