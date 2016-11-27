<?php

class Mageplaza_Googleadwords_Block_Googleadwords extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Mageplaza_Googleadwords_Block_Googleadwords
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getConfig($name)
    {
        return Mage::getStoreConfig($name);
    }

    public function getGeneral($name)
    {
        return $this->getConfig('googleadwords/general/' . $name);
    }

    public function getConversionValue()
    {
        switch ($this->getGeneral('enable_conversion_value')) {
            case '0':
                return null;
                break;

            case 'same':
                return (float) $this->getGeneral('conversion_value');
                break;

            case 'total':
                if($this->getOrder()){
                    return  (float) $this->getOrder()->getGrandTotal();
                } else{
                    return 0;
                }
                break;

        }

        return 0;
    }

    /**
     * Returns the current order
     *
     * @return Mage_Sales_Model_Order|null
     */
    public function getOrder()
    {
        if (!$this->hasOrder()) {
            /* @var $session Mage_Checkout_Model_Session */
            /* @var $order Mage_Sales_Model_Order */
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $order = null;

            if ($orderId) {
                $order = Mage::getModel('sales/order');
                $order->load($orderId);

                if (!$order->getId()) {
                    $order = null;
                }
            }

            $this->setOrder($order);
        }

        return $this->_getData('order');
    }

    public function getCurrentCurrency()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }


}