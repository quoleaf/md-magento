<?php
class MW_RewardPoints_Model_Obsever 
{
	public function addPaypalRewardItem(Varien_Event_Observer $observer)
    {
        $paypalCart = $observer->getEvent()->getPaypalCart();
        if ($paypalCart && abs($paypalCart->getSalesEntity()->getMwRewardpointDiscount()) > 0.0001) {
            $salesEntity = $paypalCart->getSalesEntity();
            $paypalCart->updateTotal(
                Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,
                (float)$salesEntity->getMwRewardpointDiscount(),
                Mage::helper('rewardpoints')->__('Rewardpoint discount %s',$salesEntity->getMwRewardpointDiscountShow())
            );
        }
    }
    
	public function rewardForBirthdayPoint()
    {
        //this collection get all users which have birthday on today
        $month = date('m', Mage::getModel('core/date')->timestamp(time()));
        $day = date('d', Mage::getModel('core/date')->timestamp(time()));
        $year = date('Y', Mage::getModel('core/date')->timestamp(time()));
        $customer = Mage::getModel("customer/customer")->getCollection();
        $customer->addFieldToFilter('dob', array('like' => '%'.$month.'-'.$day.' 00:00:00'));
        
        $items = $customer->getItems();
        foreach($items as $item)
        {
        	$store_id = $item->getStoreId();
        	$customer_id = $item ->getEntityId();
        	$type_of_transaction = MW_RewardPoints_Model_Type::CUSTOMER_BIRTHDAY;

			$points = (int)Mage::helper('rewardpoints')->getPointBirthDayConfigStore($store_id);

        	if($points > 0){
        		$transactions = Mage::getModel('rewardpoints/rewardpointshistory')->getCollection()
					->addFieldToFilter('customer_id',$customer_id)
					->addFieldToFilter('type_of_transaction',$type_of_transaction)
					->addFieldToFilter('transaction_detail',$year)
					->addFieldToFilter('status',array('in'=>array(MW_RewardPoints_Model_Status::COMPLETE)));
				if(sizeof($transactions) == 0)
				{
					Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer_id, 0);
					$_customer = Mage::getModel('rewardpoints/customer')->load($customer_id);
					$_customer->addRewardPoint($points);
					$historyData = array('type_of_transaction'=>$type_of_transaction, 
										 'amount'=>(int)$points, 
										 'balance'=>$_customer->getMwRewardPoint(), 
										 'transaction_detail'=>$year,
										 'transaction_time'=>now(), 
										 'expired_day'=>0,
							    		 'expired_time'=>null,
							    		 'point_remaining'=>0,
										 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					
					$_customer->saveTransactionHistory($historyData);
					
					// send mail when points changed
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($_customer->getId(),$historyData, $store_id);
					
					// send mail when customer birthday
					Mage::helper('rewardpoints')->sendEmailCustomerPointBirthday($_customer->getId(),$historyData, $store_id);
				}
        		
			}
        }
    }
	public function customerLoginRedirect($observer)
	{
		$mw_redirect = Mage::getModel('core/cookie')->get('mw_redirect');
		if($mw_redirect){
			$session = Mage::getSingleton('customer/session');
			$session->setBeforeAuthUrl(Mage::getUrl('checkout/cart'));
			Mage::getModel('core/cookie')->delete('mw_redirect');
		}
		
	}
	public function customRule($observer)
	{			
		if(strpos(Mage::app()->getRequest()->getPathInfo(),'mw_re_login')) Mage::getModel('core/cookie')->set('mw_redirect',1,120);	
		
		
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules; 
		$modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children()); 
		if(!in_array('MW_Mcore', $modules2) || !$modulesArray['MW_Mcore']->is('active') || Mage::getStoreConfig('mcore/config/enabled')!=1)
		{
			Mage::helper('rewardpoints')->disableConfig();
		}
		
	}
	
	
}