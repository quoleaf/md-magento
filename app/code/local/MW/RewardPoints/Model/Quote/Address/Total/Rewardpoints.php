<?php
class MW_RewardPoints_Model_Quote_Address_Total_Rewardpoints extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	
	public function __construct(){
        $this->setCode('reward_points');
    }
	public function collect(Mage_Sales_Model_Quote_Address $address)
    {
    	
    	parent::collect($address);
    	
    	$store_id = Mage::app()->getStore()->getId();
    	$quote = $address->getQuote();
		$baseTotalDiscountAmount = $quote->getMwRewardpointDiscount();
		$totalDiscountAmount = Mage::helper('core')->currency($baseTotalDiscountAmount,false,false);
        
    	$items = $address->getAllVisibleItems();
        if (!count($items)) {
            return $this;
        }
        
        $spend_reward_point_cart = 0;
    	$earn_reward_point = 0;
    	$earn_reward_point_cart = 0;
    	$product_sell_point = 0;
    	$_point_order  = 0;
		$reward_point_detail = array();	
		$array_rule_show = array();

		$point_orders = explode('/',trim(Mage::helper('rewardpoints')->getOrderPurchaseStore($store_id)));
    	$_point_order = (int)$point_orders[0]; 
        if(sizeof($point_orders)== 2 )
        {
            $total = $address->getBaseGrandTotal();
            $_point_order = ((int)($total / $point_orders[1])) * ((int)$point_orders[0]);
        }
        
		$earn_reward_point =  $_point_order;
		$earn_reward_point_cart =  	$_point_order;
        foreach ($items as $item) {
        	
        	$reward_point = 0;
        	$mw_reward_point = 0;
        	$product_id = $item->getProductId();
    	 	$qty = $item->getQty();
    	 	
    	 	$mw_reward_point = $qty * Mage::getModel('rewardpoints/productpoint')->getPoint($product_id);
    	 	$earn_reward_point = $earn_reward_point + $mw_reward_point;
        }
        
        $quote->setMwRewardpointDiscountShow($totalDiscountAmount);
        
        $quote->setSpendRewardpointCart($spend_reward_point_cart)->save();
        $quote->setEarnRewardpoint($earn_reward_point)->save();
        $quote->setEarnRewardpointCart($earn_reward_point_cart)->save();
        $quote->setMwRewardpointSellProduct($product_sell_point)->save();
        // show rule form reward point checkout
        $quote->setMwRewardpointDetail(serialize($reward_point_detail))->save();
        // rule show message when not add to cart 
        $quote->setMwRewardpointRuleMessage(serialize($array_rule_show))->save();
        
        $address->setMwRewardpoint($quote->getMwRewardpoint());
        $address->setMwRewardpointDiscountShow($totalDiscountAmount);
        // base discount
        $address->setMwRewardpointDiscount($baseTotalDiscountAmount);
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getMwRewardpointDiscountShow());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-($address->getMwRewardpointDiscount()));
        //$address->setBaseDiscountAmount($address->getBaseDiscountAmount()- ($address->getMwRewardpointDiscount()));
        
      
    	return $this;
    	
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
       $store_id = Mage::app()->getStore()->getId();
       $amount = $address->getMwRewardpointDiscountShow();
       $point = $address->getMwRewardpoint();
       $point_show = Mage::helper('rewardpoints')->formatPoints($point,$store_id);
       //$amount = $address->getRewardPointsEarn();
        if ($amount!=0) {
        	$title = Mage::helper('rewardpoints')->__('You Redeem (%s)',$point_show);
            $address->addTotal(array(
                'code'=> $this->getCode(),
                'title'=>$title,
                'value'=>-$amount,
            	'strong'    => false
            	//'area'    => '1'
            ));
        }
        return $this;
    }

}
