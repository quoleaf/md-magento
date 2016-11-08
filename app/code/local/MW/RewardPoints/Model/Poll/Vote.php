<?php
class MW_RewardPoints_Model_Poll_Vote extends Mage_Poll_Model_Poll_Vote
{
    protected $_eventPrefix = 'poll_vote';
    protected $_eventObject = 'vote';
    
	protected function _construct()
    {
        return parent::_construct();
    }
    
    public function voteAfterSave($argv)
    {
    	$vote = $argv->getVote();
    	if($vote->getCustomerId())
    	{
    		$store_id = Mage::app()->getStore()->getId();
    		
    		$customer_group_id = Mage::getModel('customer/customer')->load($vote->getCustomerId())->getGroupId();
    		$type_of_transaction = MW_RewardPoints_Model_Type::SUBMIT_POLL;
	        //$points = (double)Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id);
	        $points = (int)Mage::helper('rewardpoints')->getPointVotingPollConfigStore($store_id);
    		Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($vote->getCustomerId(), 0);	
    		
    		if($points)
    		{
    			$_customer = Mage::getModel("rewardpoints/customer")->load($vote->getCustomerId());
    			$_customer->addRewardPoint($points);
    			
    			$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SUBMIT_POLL, 
					    			 'amount'=>(int)$points,
					    			 'balance'=>$_customer->getMwRewardPoint(), 
					    			 'transaction_detail'=>$vote->getPollId(), 
					    			 'transaction_time'=>now(), 
    								 'expired_day'=>0,
						    		 'expired_time'=>null,
						    		 'point_remaining'=>0,
					    			 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
    			
	            $_customer->saveTransactionHistory($historyData);
	            
	            // send mail when points changed
				Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($_customer->getId(),$historyData, $store_id);
						
	            Mage::getSingleton('core/session')->addSuccess(Mage::helper("rewardpoints")->__("You have been rewarded %s %s for submitting poll",$points,Mage::helper('rewardpoints')->getPointCurency($store_id)));
    		}
    	}
    }
}
