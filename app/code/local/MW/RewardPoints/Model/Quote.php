<?php

class MW_RewardPoints_Model_Quote extends Mage_Core_Model_Abstract
{
    protected function _getSession()
    {
    	return Mage::getSingleton('checkout/session');
    }
    
    protected function _getCustomer()
    {
    	return Mage::getModel('rewardpoints/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }
	public function collectTotalBefore($argv)
    {
    	if(Mage::helper('rewardpoints')->moduleEnabled())
		{
			$store_id = Mage::app()->getStore()->getId();
	    	$quote = $argv->getQuote();
	    	$address = $quote->isVirtual()?$quote->getBillingAddress():$quote->getShippingAddress();
			
			$spend_point = (int)Mage::helper('rewardpoints')->getMaxPointToCheckOut();
			
			//mage::log('dddd'.$quote->getBaseGrandTotal().'  '.$address->getBaseGrandTotal().'max'.$spend_point.'ddddddd');
			
			$mw_rewardpoints = (int)$quote->getMwRewardpoint();
			$min = (int)Mage::helper('rewardpoints/data')->getMinPointCheckoutStore($store_id);
			
			if($min > 0 && $mw_rewardpoints < $min && $min <= $spend_point){
				$quote->setMwRewardpoint(0);
				$quote->setMwRewardpointDiscount(0)->save();
			} 
			
			$max_points_discount = Mage::helper('rewardpoints')->exchangePointsToMoneys($spend_point,$store_id);
			//echo $max_points_discount.'aaaaaaaa'.$spend_point;die();
			$rewardpoint_discount = (double)$quote->getMwRewardpointDiscount();
			
			$grandtotal_after_rewardpoint = $quote->getBaseGrandTotal() + $rewardpoint_discount;
			if($rewardpoint_discount > $grandtotal_after_rewardpoint) 
			{
				$quote->setMwRewardpointDiscount($grandtotal_after_rewardpoint);
				$points = Mage::helper('rewardpoints')->exchangeMoneysToPoints($grandtotal_after_rewardpoint,$store_id);
				$quote->setMwRewardpoint(Mage::helper('rewardpoints')->roundPoints($points,$store_id))->save();
			}
			if($rewardpoint_discount > $max_points_discount){
				$quote->setMwRewardpointDiscount($max_points_discount);
				$quote->setMwRewardpoint(Mage::helper('rewardpoints')->roundPoints($spend_point,$store_id))->save();
				
				if($max_points_discount > $grandtotal_after_rewardpoint){
					
					$quote->setMwRewardpointDiscount($grandtotal_after_rewardpoint);
					$points = Mage::helper('rewardpoints')->exchangeMoneysToPoints($grandtotal_after_rewardpoint,$store_id);
					$quote->setMwRewardpoint(Mage::helper('rewardpoints')->roundPoints($points,$store_id))->save();
				}
			}
			
		}else{
			$quote = $argv->getQuote();
			$quote->setMwRewardpointDiscount(0);
			$quote->setMwRewardpoint(0)->save();
		}
    }
}