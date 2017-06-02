<?php

class newcustomerattr_NewCustomerCoupon_Model_Observer
{
    public function customerRegistered(Varien_Event_Observer $observer) {
        $customer = $observer->getCustomer();
        $customer->setIsCustomerNew(1)->save();

        return $this;
    }

    public function orderPlaced(Varien_Event_Observer $observer) {
        $customer = $observer->getOrder()->getCustomer();
        $customer->setIsCustomerNew(0)->save();

        return $this;
    }


    public function addCustomerCondition(Varien_Event_Observer $observer) {

        $conditions = array(
            array(
                'value' => 'newcustomerattr_newcustomercoupon/rule_condition_customer_newCustomer',
                'label' => Mage::helper('newcustomerattr_newcustomercoupon')->__('Is Customer New'),
            ),
        );

        $additional = $observer->getEvent()->getAdditional();
        $additional->setConditions($conditions);

        return $this;

    }
}