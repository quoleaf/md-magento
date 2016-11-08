<?php

class MW_RewardPoints_Model_Invitation extends Mage_Core_Model_Abstract
{
	public function dispathClickLink($observer)
	{
		$invite = Mage::app()->getRequest()->getParam('mw_reward');
		if($invite)
		{    
			Mage::dispatchEvent('invitation_referral_link_click',array('invite'=>$invite,'request'=>Mage::app()->getRequest()));
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('Thank you for visiting our site'));
		}
	}
    public function referralLinkClick($argv)
    {
    	$invite = $argv->getInvite();
    	//$referral_by = $argv->getReferralBy();
    	$request = $argv->getRequest();
    	$customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->getCollection();
        $customer->getSelect()->where("md5(email)='".$invite."'");
        $customer_id = $customer->getFirstItem()->getId();

		
		if($customer_id)
		{
			if(method_exists($request,'getClientIp'))
				$clientIP = $request->getClientIp(true);
			else
			$clientIP = $request->getServer('REMOTE_ADDR');
			
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('transaction_detail',$clientIP)
			->addFieldToFilter('customer_id',$customer_id);
			
			if(!sizeof($transactions))
			{
				Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer_id, 0);	
				$_customer = Mage::getModel('rewardpoints/customer')->load($customer_id);
				
				$customer_group_id = Mage::getModel('customer/customer')->load($customer_id)->getGroupId();
				$store_id = Mage::app()->getStore()->getId();
				$type_of_transaction = MW_RewardPoints_Model_Type::INVITE_FRIEND;
	           // $points = (int)Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id);
				$points = (int)Mage::helper('rewardpoints')->getPointReferralVisitorConfigStore($store_id);
				
				if($points){
					$_customer->addRewardPoint($points);
					$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::INVITE_FRIEND,
										 'amount'=>$points, 
										 'balance'=>$_customer->getMwRewardPoint(),
										 'transaction_detail'=>$clientIP, 
										 'transaction_time'=>now(), 
										 'expired_day'=>0,
							    		 'expired_time'=>null,
							    		 'point_remaining'=>0,
										 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					$_customer->saveTransactionHistory($historyData);
					
					// send mail when points changed
					Mage::helper('rewardpoints')->sendEmailCustomerPointChangedNew($_customer->getId(),$historyData, $store_id);
				}
			}
			Mage::getModel('core/cookie')->set('friend', $customer_id, 3600*24);
		}
		
    }
}