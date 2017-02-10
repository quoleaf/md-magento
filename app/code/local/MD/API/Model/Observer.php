<?php
class MD_API_Model_Observer{
     public function collectRewardPoints(Varien_Event_Observer $observer){
         $orderCollection = $observer->getEvent()->getOrderCollection();

         foreach ($orderCollection as $order) {
             $customerId = $order->getCustomerId();
             //$customer = Mage::getModel('customer/customer')->load($customerId);
             //Zend_Debug::dump($customer->getName());
             $md_rewards = Mage::getModel('rewardpoints/order')->load($customerId);
             $order->setData('mdfactor_reward_points', $md_rewards->mw_rewardpoint_discount); 
         }

         return $this;
     }
}