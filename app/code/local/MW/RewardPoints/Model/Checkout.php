<?php
class MW_RewardPoints_Model_Checkout extends Mage_Core_Model_Abstract
{
    public function placeAfter($argv)
    {
    	if(Mage::helper('rewardpoints')->moduleEnabled())
		{
			$order = $argv->getOrder();
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			
			if($quote->getCheckoutMethod(true) == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER){
    			Mage::getModel('rewardpoints/customer')->customerSaveAfterRegister($order);
			}
			
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
	 	    if($customer->getId()){
	    		$store_id = Mage::app()->getStore()->getId();
	    		Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer->getId(), 0);	
				$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
				$earn_rewardpoint = (int)$quote->getEarnRewardpoint();
				$rewardpoints = (int)$quote->getMwRewardpoint();

	    		//Subtract reward points of customer and save reward points to order if customer use this point to checkout
	            if($rewardpoints || $earn_rewardpoint)
	            {
	            	if($rewardpoints)
	            	{
		            	//Subtract reward points of customer
		            	$_customer->addRewardPoint(-$rewardpoints);
		            	$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::USE_TO_CHECKOUT, 
							            	 'amount'=>(int)$rewardpoints, 
							            	 'balance'=>$_customer->getMwRewardPoint(), 
							            	 'transaction_detail'=>$order->getIncrementId(), 
							            	 'transaction_time'=>now(), 
		            						 'expired_day'=>0,
									    	 'expired_time'=>null,
									         'point_remaining'=>0,
							            	 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
		            	
		           		$_customer->saveTransactionHistory($historyData);
		           		Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($_customer->getId(),$historyData, $store_id);
		           		
		           		
	            	}
	           		$discount = $quote->getMwRewardpointDiscount();
	           		//Save reward point for order
	           		$orderData =  array('order_id'=>$order->getId(),
						           		'reward_point'=>$rewardpoints, 
	           							'rewardpoint_sell_product'=>(int)$quote->getMwRewardpointSellProduct(),
	           							'earn_rewardpoint'=> $earn_rewardpoint,
						           		'money'=>$discount,
						           		'reward_point_money_rate'=>Mage::helper('rewardpoints')->getPointMoneyRateConfig($store_id));
	           		
	           		$_order = Mage::getModel('rewardpoints/rewardpointsorder');
	           		$_order->saveRewardOrder($orderData);
	            }
	            if($earn_rewardpoint >0){
	            	
	            	$detail = array();
	            	$_detail_rule_result = array();
	            	$_detail_products = array();
	            	$_point_cart = (int)$quote->getEarnRewardpointCart();
	            	$order_id = $order->getIncrementId();
	            	
		        	if($_point_cart > 0){
			    		$_detail_rule_result[] = Mage::helper('rewardpoints')->__('%s | %s',$_point_cart,$order_id);
		        	}
		        	
		            foreach($quote->getAllVisibleItems() as $item)
		            {
		            	$product_id = $item->getProduct()->getId();
		            	$mw_reward_point = (int)Mage::getModel('rewardpoints/productpoint')->getPoint($product_id);
		            	if($mw_reward_point > 0)
		            	{
		            		$product_name = Mage::getModel('catalog/product')->load($product_id)->getName();
		            		$_detail_products[] = Mage::helper('rewardpoints')->__('%s |%s',$mw_reward_point * $item->getQty(),$product_name);
		            	}
		            }
    				$detail[0] = $_detail_rule_result;
    				$detail[2] = $_detail_products;
    				
	            	$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::CHECKOUT_ORDER_NEW,
						           		 'amount'=>$earn_rewardpoint,
						           		 'balance'=>$_customer->getMwRewardPoint(), 
						           		 'transaction_detail'=>$order->getIncrementId()."||".serialize($detail), 
						           		 'transaction_time'=>now(),
	            						 'expired_day'=>0,
    									 'expired_time'=>null,
	            						 'point_remaining'=>0,
						           		 'status'=>MW_RewardPoints_Model_Status::PENDING);
	           		
			        $_customer->saveTransactionHistory($historyData);
	            }
	            
	            //Reward points to friend if this is first purchase
	            $orders = Mage::getModel("sales/order")->getCollection()
	            			->addFieldToFilter('customer_id',$customer->getId());
	            if($_customer->getMwFriendId()) Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($_customer->getMwFriendId(), 0);	
	            $friend = $_customer->getFriend();
	            if((sizeof($orders) ==1) && ($friend)){
	            	$customer_group_id = Mage::getModel('customer/customer')->load($customer->getId())->getGroupId();
	            	$type_of_transaction = MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE;
	            	
	            	$point = explode('/',Mage::helper('rewardpoints/data')->getPointReferralFirstPurchaseConfigStore($store_id));
	            	//$point = explode('/',Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id));
	            	
	            	$_point = $point[0]; 
	            	if(sizeof($point)==2)
	            	{
	            		$total = $order->getBaseGrandTotal();
	            		$_point = ((int)($total / $point[1])) * $point[0];
	            	}
	            	if($_point){
		           		$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE,
							           		 'amount'=>(int)$_point, 
							           		 'balance'=>$friend->getMwRewardPoint(), 
							           		 'transaction_detail'=>$customer->getId()."|".$order->getId(), 
							           		 'transaction_time'=>now(),
							           		 'expired_day'=>0,
								    		 'expired_time'=>null,
								    		 'point_remaining'=>0,
							           		 'status'=>MW_RewardPoints_Model_Status::PENDING);
		           		$friend->saveTransactionHistory($historyData);
	            	}
	            }else if($friend)
	            {
	            	//Reward points to friend if this is next purchase
	            	$customer_group_id = Mage::getModel('customer/customer')->load($customer->getId())->getGroupId();
	            	$type_of_transaction = MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE;
	            	
	            	$point = explode('/',Mage::helper('rewardpoints/data')->getPointReferralNextPurchaseConfigStore($store_id));
	            	//$point = explode('/',Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id));
	            	
	            	$_point = $point[0]; 
	            	if(sizeof($point)==2)
	            	{
	            		$total = $order->getBaseGrandTotal();
	            		$_point = ((int)($total / $point[1])) * $point[0];
	            	}
	            	if($_point){
		           		$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE, 
							           		 'amount'=>(int)$_point,
		           						     'balance'=>$friend->getMwRewardPoint(), 
							           		 'transaction_detail'=>$customer->getId()."|".$order->getId(), 
							           		 'transaction_time'=>now(),
							           		 'expired_day'=>0,
								    		 'expired_time'=>null,
								    		 'point_remaining'=>0,
							           		 'status'=>MW_RewardPoints_Model_Status::PENDING);
		           		$friend->saveTransactionHistory($historyData);
	            	}
	            }
	    	}
		}
    }
    // canceled payment
    // cap nhat trang thai khi payment that bai
 	public function cancelOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	
    	$customerId = $order->getCustomerId();
    	$store_id = Mage::getModel('customer/customer') ->load($customerId)->getStoreId();
    	$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
    	
    	
    	$new_transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customerId)
					->addFieldToFilter('transaction_detail',array('like'=>"%".$order->getIncrementId()."%"))
					->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::COMPLETE)))
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC');
		
		foreach($new_transactions as $new_transaction)
		{
			switch($new_transaction->getTypeOfTransaction())
			{
				//Use points to check out
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					if($new_transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$transactions_refund  = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
											    ->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::ORDER_CANCELLED_ADD_POINTS)
												->addFieldToFilter('transaction_detail',$order->getIncrementId());
						
					if(sizeof($transactions_refund) >0) continue;

					$customer->addRewardPoint($new_transaction->getAmount());
					
					$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::ORDER_CANCELLED_ADD_POINTS, 
										 'amount'=>(int)$new_transaction->getAmount(), 
										 'balance'=>$customer->getMwRewardPoint(),
										 'transaction_detail'=>$order->getIncrementId(),
										 'transaction_time'=>now(), 
										 'expired_day'=>0,
							    		 'expired_time'=>null,
							    		 'point_remaining'=>0,
										 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					
					$customer->saveTransactionHistory($historyData);
					
					// send mail when points changed
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
					break;
			}
		}
    	$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customerId)
					->addFieldToFilter('transaction_detail',array('like'=>"%".$order->getIncrementId()."%"))
					->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::PENDING)))
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC');
		
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
					
				//Add points when first purchase, next purchase
				case MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE:
				case MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE:
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getId()) continue;
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getFriend()->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
					
				//Use points to check out
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$transactions_refund  = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
											    ->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::ORDER_CANCELLED_ADD_POINTS)
												->addFieldToFilter('transaction_detail',$order->getIncrementId());
						
					if(sizeof($transactions_refund) >0) continue;
					
					$customer->addRewardPoint($transaction->getAmount());

					$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::ORDER_CANCELLED_ADD_POINTS, 
										 'amount'=>(int)$transaction->getAmount(), 
										 'balance'=>$customer->getMwRewardPoint(),
										 'transaction_detail'=>$order->getIncrementId(),
										 'transaction_time'=>now(), 
										 'expired_day'=>0,
							    		 'expired_time'=>null,
							    		 'point_remaining'=>0,
										 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					
					$customer->saveTransactionHistory($historyData);
					
					// send mail when points changed
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
					break;
			
				//Reward points for order
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER_NEW:
					$detail = explode("||",$transaction->getTransactionDetail());
					if($detail[0] != $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
					
			}
		}
		$customer_friend_id = Mage::getModel('rewardpoints/customer')->load($customerId) ->getMwFriendId();
    	if($customer_friend_id){
			//update transaction status for friend
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
						->addFieldToFilter('customer_id',$customer_friend_id)
						->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
						->addFieldToFilter('type_of_transaction',array('in'=>array(MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE,MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE)))
						->addOrder('transaction_time','ASC')
						->addOrder('history_id','ASC');
			foreach($transactions as $transaction)
			{
				$detail = explode("|",$transaction->getTransactionDetail());
				if($detail[1]!= $order->getId()) continue;
				$status = MW_RewardPoints_Model_Status::UNCOMPLETE;
				$transaction->setBalance(Mage::getModel('rewardpoints/customer')->load($customer_friend_id)->getRewardPoint())->setTransactionTime(now());
				$transaction->setStatus($status)->save();
			}
		}
    }
    public function orderSaveAfter($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$store_id = $order->getStoreId();
    	$status_add_reward_point = Mage::helper('rewardpoints/data')->getStatusAddRewardPointStore($store_id);
	   // $status_subtract_reward_point  = Mage::helper('rewardpoints/data')->getStatusSubtractRewardPointStore($store_id);
    	if($order->getStatus() == 'canceled')
		{
			$this ->cancelOrder($arvgs);
		}
    	if($order->getStatus() == $status_add_reward_point)
		{
			$this ->completeOrder($arvgs);
		}
		// trang thai refund product
		if($order->getStatus() == 'closed')
		{
			$this ->refundOrder($arvgs);	
		}
    }
	public function completeOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$customerId = $order->getCustomerId();
    	$store_id = Mage::getModel('customer/customer') ->load($customerId)->getStoreId();
    	$customer = Mage::getModel('rewardpoints/customer')->load($customerId);
    	
    	$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customerId)
					->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
					->addOrder('transaction_time','ASC')
					->addOrder('history_id','ASC');
		
		foreach($transactions as $transaction)
		{
			switch($transaction->getTypeOfTransaction())
			{
	
				//Use points to check out
				case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
					$order = Mage::getModel("sales/order")->loadByIncrementId($transaction->getTransactionDetail());
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$transaction->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					break;
			
				//Reward points for order
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
					if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$customer->addRewardPoint($transaction->getAmount());
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					
					// send mail when points changed
					$historyData = array('type_of_transaction'=>$transaction->getTypeOfTransaction(),
										 'amount'=>(int)$transaction->getAmount(), 
										 'balance'=>$transaction->getBalance(), 
										 'transaction_detail'=>$transaction->getTransactionDetail(), 
										 'transaction_time'=>$transaction->getTransactionTime(), 
										 'status'=>$transaction->getStatus());
					
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
					break;
					
				case MW_RewardPoints_Model_Type::CHECKOUT_ORDER_NEW:
					
					$detail = explode("||",$transaction->getTransactionDetail());
					if($detail[0] != $order->getIncrementId()) continue;
					
					$status = MW_RewardPoints_Model_Status::COMPLETE;
					$customer->addRewardPoint($transaction->getAmount());
					
					$transaction->setExpiredTime(null)->setPointRemaining(0);
					
					$transaction->setBalance($customer->getRewardPoint())->setTransactionTime(now());
					$transaction->setStatus($status)->save();
					
					// send mail when points changed
					$historyData = array('type_of_transaction'=>$transaction->getTypeOfTransaction(),
										 'amount'=>(int)$transaction->getAmount(), 
										 'balance'=>$transaction->getBalance(), 
										 'transaction_detail'=>$transaction->getTransactionDetail(), 
										 'transaction_time'=>$transaction->getTransactionTime(), 
										 'status'=>$transaction->getStatus());
					
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
					break;
			}
		}
		$customer_friend_id = Mage::getModel('rewardpoints/customer')->load($customerId) ->getMwFriendId();
		if($customer_friend_id){
			//update transaction status for friend
			$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
						->addFieldToFilter('customer_id',$customer_friend_id)
						->addFieldToFilter('status',MW_RewardPoints_Model_Status::PENDING)
						->addFieldToFilter('type_of_transaction',array('in'=>array(MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE,MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE)))
						->addOrder('transaction_time','ASC')
						->addOrder('history_id','ASC');
			foreach($transactions as $transaction)
			{
				$detail = explode("|",$transaction->getTransactionDetail());
				if($detail[1]!= $order->getId()) continue;
				Mage::getModel('rewardpoints/customer')->load($customer_friend_id)->addRewardPoint($transaction->getAmount());
				$status = MW_RewardPoints_Model_Status::COMPLETE;
				
				$transaction->setExpiredTime(null)->setPointRemaining(0);
					
				$transaction->setBalance(Mage::getModel('rewardpoints/customer')->load($customer_friend_id)->getRewardPoint())->setTransactionTime(now());
				$transaction->setStatus($status)->save();
				
				// send mail when points changed
				$historyData = array('type_of_transaction'=>$transaction->getTypeOfTransaction(),
									 'amount'=>(int)$transaction->getAmount(), 
									 'balance'=>$transaction->getBalance(), 
									 'transaction_detail'=>$transaction->getTransactionDetail(), 
									 'transaction_time'=>$transaction->getTransactionTime(), 
									 'status'=>$transaction->getStatus());
				
				$store_id = Mage::getModel('customer/customer')->load($customer_friend_id)->getStoreId();
				Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer_friend_id,$historyData, $store_id);
			}
		}
					
    }
    public function refundOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
			->addFieldToFilter('transaction_detail',array('like'=>"%".$order->getIncrementId()."%"))
			->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::COMPLETE)));

			if(sizeof($transactions)) foreach($transactions as $transaction)
			{
				$customer = $transaction->getCustomer();
				$store_id = Mage::getModel('customer/customer')->load($customer->getId())->getStoreId();
				$subtract_reward_point = Mage::helper('rewardpoints')->getSubtractPointWhenRefundConfigStore($store_id);
				$restore_spent_points = Mage::helper('rewardpoints')->getRestoreSpentPointsWhenRefundConfigStore($store_id);
				
				switch($transaction->getTypeOfTransaction())
				{
					//Use points to check out
					case MW_RewardPoints_Model_Type::USE_TO_CHECKOUT:
						if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						
						$transactions_refund  = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
											    ->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::REFUND_ORDER_ADD_POINTS)
												->addFieldToFilter('transaction_detail',$order->getIncrementId());
						
						if(sizeof($transactions_refund) >0) continue;
						
						if($restore_spent_points){
							
							$customer->addRewardPoint($transaction->getAmount());
						
							$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_ADD_POINTS,
												 'amount'=>(int)$transaction->getAmount(),
												 'balance'=>$customer->getMwRewardPoint(),
												 'transaction_detail'=>$order->getIncrementId(), 
												 'transaction_time'=>now(), 
												 'expired_day'=>0,
									    		 'expired_time'=>null,
									    		 'point_remaining'=>0,
												 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
							$customer->saveTransactionHistory($historyData);
							
							//send mail when points changed
							Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
							
						}
						
						break;
				
					//Reward points for order
					case MW_RewardPoints_Model_Type::CHECKOUT_ORDER:
						if($transaction->getTransactionDetail()!= $order->getIncrementId()) continue;
						
						$transactions_refund  = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
											    ->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::REFUND_ORDER_SUBTRACT_POINTS)
												->addFieldToFilter('transaction_detail',$order->getIncrementId());
						
						if(sizeof($transactions_refund) >0) continue;

						if($transaction->getStatus() == MW_RewardPoints_Model_Status::COMPLETE && $subtract_reward_point){
							$customer->addRewardPoint(-$transaction->getAmount());
							
							$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_SUBTRACT_POINTS,
												 'amount'=>(int)$transaction->getAmount(), 
												 'balance'=>$customer->getMwRewardPoint(), 
												 'transaction_detail'=>$order->getIncrementId(), 
												 'transaction_time'=>now(), 
												 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
							$customer->saveTransactionHistory($historyData);
							
							//send mail when points changed
							Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
							
						}//else
							//$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE)->save();
						break;
					//Reward points for order
					case MW_RewardPoints_Model_Type::CHECKOUT_ORDER_NEW:
						
						$detail = explode("||",$transaction->getTransactionDetail());
						if($detail[0] != $order->getIncrementId()) continue;
						
						$transactions_refund  = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
											    ->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::REFUND_ORDER_SUBTRACT_POINTS)
												->addFieldToFilter('transaction_detail',$order->getIncrementId());
						
						if(sizeof($transactions_refund) >0) continue;

						if($transaction->getStatus() == MW_RewardPoints_Model_Status::COMPLETE && $subtract_reward_point){
							$customer->addRewardPoint(-$transaction->getAmount());
							
							$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_SUBTRACT_POINTS,
												 'amount'=>(int)$transaction->getAmount(), 
												 'balance'=>$customer->getMwRewardPoint(), 
												 'transaction_detail'=>$order->getIncrementId(), 
												 'transaction_time'=>now(), 
												 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
							$customer->saveTransactionHistory($historyData);
							        			
							//send mail when points changed
							Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer->getId(),$historyData, $store_id);
							
							
							
						}//else
							//$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE)->save();
						break;
						
				}
			}
			$customerId = $order->getCustomerId();
			$customer_friend_id = Mage::getModel('rewardpoints/customer')->load($customerId) ->getMwFriendId();
	    	if($customer_friend_id){
				//update transaction status for friend
				$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
							->addFieldToFilter('customer_id',$customer_friend_id)
							->addFieldToFilter('status',MW_RewardPoints_Model_Status::COMPLETE)
							->addFieldToFilter('type_of_transaction',array('in'=>array(MW_RewardPoints_Model_Type::FRIEND_FIRST_PURCHASE,MW_RewardPoints_Model_Type::FRIEND_NEXT_PURCHASE)))
							->addOrder('transaction_time','ASC')
							->addOrder('history_id','ASC');
				foreach($transactions as $transaction)
				{
					$store_id = Mage::getModel('customer/customer')->load($customer_friend_id)->getStoreId();
					$subtract_reward_point = Mage::helper('rewardpoints')->getSubtractPointWhenRefundConfigStore($store_id);
					
					$detail = explode("|",$transaction->getTransactionDetail());
					if($detail[1]!= $order->getId()) continue;					
					//$transaction->setStatus(MW_RewardPoints_Model_Status::UNCOMPLETE)->save();
					
					$transactions_refund  = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
											    ->addFieldToFilter('type_of_transaction',MW_RewardPoints_Model_Type::REFUND_ORDER_FREND_PURCHASE)
												->addFieldToFilter('transaction_detail',$transaction->getTransactionDetail());
						
					if(sizeof($transactions_refund) >0) continue;
					
					if($subtract_reward_point){
						
						Mage::getModel('rewardpoints/customer')->load($customer_friend_id)->addRewardPoint(-$transaction->getAmount());
					
					  	$historyData =  array('type_of_transaction'=>MW_RewardPoints_Model_Type::REFUND_ORDER_FREND_PURCHASE, 
											  'amount'=>(int)$transaction->getAmount(), 
											  'balance'=>Mage::getModel('rewardpoints/customer')->load($customer_friend_id)->getMwRewardPoint(), 
											  'transaction_detail'=>$transaction->getTransactionDetail(), 
											  'transaction_time'=>now(), 
											  'status'=>MW_RewardPoints_Model_Status::COMPLETE);
						Mage::getModel('rewardpoints/customer')->load($customer_friend_id)->saveTransactionHistory($historyData);
											
						//send mail when points changed
						Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer_friend_id,$historyData, $store_id);
						
					}
				}
			}
    }
}