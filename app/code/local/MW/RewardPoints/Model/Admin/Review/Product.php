<?php
class MW_RewardPoints_Model_Admin_Review_Product
{
	public function save($argv)
	{
			$review = $argv->getObject();
			if(Mage::helper('rewardpoints')->moduleEnabled() && $review->getData('customer_id'))
			{ 
					$transactions = Mage::getResourceModel('rewardpoints/rewardpointshistory_collection')
					->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::SUBMIT_PRODUCT_REVIEW)
					->addFieldToFilter('transaction_detail',$review->getId()."|".$review->getEntityPkValue());
					if(!sizeof($transactions))
					{
						$store_id = $review->getStoreId();
						Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($review->getData('customer_id'), 0);	
	                   	$_customer = Mage::getModel('rewardpoints/customer')->load($review->getData('customer_id'));
	                   	$customer_group_id = Mage::getModel('customer/customer')->load($review->getData('customer_id'))->getGroupId();
	                   	$type_of_transaction = MW_RewardPoints_Model_Type::SUBMIT_PRODUCT_REVIEW;
	                   	
	                    $points = (int)Mage::helper('rewardpoints')->getPointPostingProductConfigStore($store_id);
	                    //$points = (double)Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id);
						
	                   	if($review->getStatusId() == Mage_Review_Model_Review::STATUS_APPROVED && $points)
	                    {
	                    	$status = MW_RewardPoints_Model_Status::COMPLETE;
	                    	$_customer->addRewardPoint($points);
	                    	
	                    	$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SUBMIT_PRODUCT_REVIEW, 
						                    	 'amount'=>$points, 
						                    	 'balance'=>$_customer->getMwRewardPoint(), 
						                    	 'transaction_detail'=>$review->getId()."|".$review->getEntityPkValue(), 
						                    	 'transaction_time'=>now(),
						                    	 'expired_day'=>0,
									    		 'expired_time'=>null,
									    		 'point_remaining'=>0,
						                    	 'status'=>$status);
	                    	$_customer->saveTransactionHistory($historyData);
	                    	
	                    	// send mail when points changed
							Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($_customer->getId(),$historyData, $store_id);
	                    }
					}
			}
	}
}