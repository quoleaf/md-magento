<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Express Module
 */
class Itfosters_Pallcurrencys_Model_Express  extends Mage_Paypal_Model_Express
{
    
    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Mage_Paypal_Model_Express
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();

        // prepare api call
        $token = $payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);

        $totalamount = ($amount*(Mage::helper('ppfix')->getCurrentExchangeRate()));

        $api = $this->_pro->getApi()
            ->setToken($token)
            ->setPayerId($payment->
                getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))
            //->setAmount($amount)
            ->setAmount($totalamount)
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setInvNum($order->getIncrementId())
            //->setCurrencyCode($order->getBaseCurrencyCode())
            ->setCurrencyCode(Mage::helper('pallcurrencys')->getToCurrency())
            ->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled);

        // call api and get details from it
        $api->callDoExpressCheckoutPayment();

        $this->_importToPayment($api, $payment);
        return $this;
    }
   
}
