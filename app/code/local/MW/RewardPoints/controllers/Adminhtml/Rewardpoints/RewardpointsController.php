<?php

class MW_RewardPoints_Adminhtml_Rewardpoints_RewardpointsController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed()
    {
        return true;
    }
    protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('promo/rewardpoints')
			->_addBreadcrumb(Mage::helper('rewardpoints')->__('Reward Points Manager'), Mage::helper('rewardpoints')->__('Reward Points Manager'));
		
		return $this;
	}
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	public function indexAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function exportCsvAction()
    {
        $fileName   = 'rewardpoints.csv';
        $content    = $this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function importAction()
    {
    	$this->loadLayout()->_setActiveMenu('promo/rewardpoints');
    	$this->_addContent($this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_edit'))
				->_addLeft($this->getLayout()->createBlock('rewardpoints/adminhtml_rewardpoints_edit_tabs'));
		$this->renderLayout();
    }
    
    public function saveAction()
    {
	    if($_FILES['filename']['name'] != '') {
			try {
				/* Starting upload */	
				$uploader = new Varien_File_Uploader('filename');
				
				// Any extention would work
		        $uploader->setAllowedExtensions(array('csv'));
				$uploader->setAllowRenameFiles(false);
				
				// Set the file upload mode 
				// false -> get the file directly in the specified folder
				// true -> get the file in the product like folders 
				//	(file.jpg will go in something like /media/f/i/file.jpg)
				$uploader->setFilesDispersion(false);
						
				// We set media as the upload dir
				$path = Mage::getBaseDir('media').DS;
				$uploader->save($path, $_FILES['filename']['name'] );
				$filename = $path.$uploader->getUploadedFileName();
				
				$fp = @fopen($filename,'r');
				$line = 1;
				$errors = array();
				if($fp){
					$website_id = $this->getRequest()->getParam('website_id');
					
					while (!feof($fp)) {
						
						$tmp = fgets($fp); //Reading a file line by line
						if($line >1){
							$content = str_replace('"','',$tmp);
							$customerInfo = explode(',',$content);
							if(sizeof($customerInfo) ==3)
							{
								$customer = Mage::getModel('customer/customer')->setWebsiteId($website_id)->loadByEmail($customerInfo[1]);
								if($customer->getId())
								{
									Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer->getId(), 0);
								  	$_customer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
								  	$customerInfo[2] = (int)trim($customerInfo[2],"\n");
								  	if(is_numeric($customerInfo[2]))
								  	{
								  		$oldPoints = $_customer->getMwRewardPoint();
								  		$newPoints = $oldPoints + $customerInfo[2];
			    	 
										if($newPoints < 0) $newPoints = 0;
								    	$amount = abs($newPoints - $oldPoints);
								    	
								  		if($amount > 0){
									    	$detail = '';
											$_customer->setData('mw_reward_point',$newPoints);
									    	$_customer->save();
									    	$balance = $_customer->getMwRewardPoint();
									    	$historyData = array('type_of_transaction'=>($customerInfo[2]>0)?MW_RewardPoints_Model_Type::ADMIN_ADDITION:MW_RewardPoints_Model_Type::ADMIN_SUBTRACT, 
														    	 'amount'=>$amount, 
														    	 'balance'=>$balance, 
														    	 'transaction_detail'=>$detail,
														    	 'transaction_time'=>now(), 
														    	 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
									    	$_customer->saveTransactionHistory($historyData);
									    	
									    	// send mail when points changed
								            $store_id = Mage::getModel('customer/customer')->load($_customer->getId())->getStoreId();
											Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($_customer->getId(),$historyData, $store_id);
								    	}
								  		//$_customer->setData('mw_reward_point',$customerInfo[2]);
								  		//$_customer->save();
								  	}else
								  	{
								  		$errors[] = Mage::helper('rewardpoints')->__('At rows %s reward points must be numeric',$line);
								  	}
								}else
								{
									$errors[] = Mage::helper('rewardpoints')->__('At rows %s customer is not avaiable',$line);
								}
							}
						}
						$line  ++;
					}
					
					if(sizeof($errors))
					{
						$err =Mage::helper('rewardpoints')->__("Some errors occur while importing points")."<br>";
						foreach($errors as $error)
							$err .= $error."<br>";
						Mage::getSingleton('adminhtml/session')->addError($err);
					}
					fclose($fp);
					@unlink($filename);
					
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rewardpoints')->__('Your file was imported successfuly'));
					$this->_redirect("*/*/");
				}
			} catch (Exception $e) {
		      	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		      	$this->_redirect("*/*/import");
		    }
    	}else
    	{
    		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rewardpoints')->__("Please select a file to import"));
    		$this->_redirect("*/*/import");
    	}
    }
}