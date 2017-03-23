<?php

class Itfosters_Pallcurrencys_Block_Checkout_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
    public function needDisplayBaseGrandtotal()
    {
        $quote = $this->getQuote();
        if ($quote->getPayment()->getMethodInstance()->getCode() == 'paypal_standard') {
            if (Mage::helper('pallcurrencys')->shouldConvert() && ($quote->getQuoteCurrencyCode() != Mage::helper('pallcurrencys')->getToCurrency())) {
                return true;
            } else {
                return false;
            }
        }
        if ($quote->getBaseCurrencyCode() != $quote->getQuoteCurrencyCode()) {
            return true;
        }
        return false;
    }

    public function displayBaseGrandtotal()
    {
        $firstTotal = reset($this->_totals);
        if (Mage::helper('pallcurrencys')->shouldConvert()) {
            $total = $firstTotal->getAddress()->getBaseGrandTotal();
            $total = Mage::helper('pallcurrencys')->getExchangeRate($total);
            $currency = Mage::getModel('directory/currency')->load(Mage::helper('pallcurrencys')->getToCurrency());
            return $currency->format($total, array(), true);
        }
        if ($firstTotal) {
            $total = $firstTotal->getAddress()->getBaseGrandTotal();
            return Mage::app()->getStore()->getBaseCurrency()->format($total, array(), true);
        }
        return '-';
    }
}