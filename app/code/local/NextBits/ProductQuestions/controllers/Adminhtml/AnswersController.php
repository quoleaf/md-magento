<?php

class NextBits_ProductQuestions_Adminhtml_AnswersController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() 
	{
		$this->loadLayout()
			->_setActiveMenu('productquestions/answers')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Answers Manager'), Mage::helper('adminhtml')->__('Answers Manager'));
		return $this;
	}   

	public function indexAction() 
	{
		$this->_initAction()
		->renderLayout();
	}

	public function editAction() 
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('productquestions/answers')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('answers_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('productquestions/answers');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			//$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_answers_edit'))
			->_addLeft($this->getLayout()->createBlock('productquestions/adminhtml_answers_edit_tab'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestions')->__('Answers does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() 
	{
		$this->_title($this->__('New Answers'));
		$_model  = Mage::getModel('productquestions/answers');
		Mage::register('answers_data', $_model);
		Mage::register('current_answers', $_model);

		$this->_initAction();
		/* $this->_addBreadcrumb(Mage::helper('adminhtml')->__('I-Banner Manager'), Mage::helper('adminhtml')->__('I-Banner Manager'), $this->getUrl('*'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Banner'), Mage::helper('adminhtml')->__('Add Banner')); */

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_answers_edit'))
		->_addLeft($this->getLayout()->createBlock('productquestions/adminhtml_answers_edit_tabs'));

		$this->renderLayout(); 
	} 

	public function saveAction() 
	{
		if ($data = $this->getRequest()->getPost()) {
			// echo "<pre>";
			// print_r($data);exit;
			
			//print_r($data['product_name']);exit;
			$answerCollection = array();
			if (isset($_FILES['file']['name']) && (file_exists($_FILES['file']['tmp_name'])))
			{
				try {	
					$uploader = new Varien_File_Uploader('file');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					
					$path = Mage::getBaseDir('media');
					$uploader->save($path, $_FILES['file']['name'] );
					$data['file'] =$path.$_FILES['file']['name'];
				} catch (Exception $e) {
					
				}//echo "<pre>";print_r($_FILES);exit;
			}
			//$data['file'] = str_replace(' ','_',$_FILES['file']['name']);
		//	$data = $this->_filterDateTime ($data, array('created_at'));
			$model = Mage::getModel('productquestions/answers'); 
			  
			$data = $this->_filterDates($data, array('date', 'expiry_date'));

			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			if($data['answers_from'] == 'admin'){
		
				$model->setData('author_name',$data['admin_name']);
				$model->setData('author_email',$data['admin_email']);
			}
			
			if($data['answers_from'] == 'customer'){
				$customer  = Mage::getModel('customer/customer')->load($data['customer_id']);
				$name = $customer->getName(); 
				$mail =  $customer->getEmail();
				$model->setData('author_name',$name);
				$model->setData('author_email',$mail);
			
			}
		  
			if($data['answers_from'] == 'guest'){
				$model->setData('author_name',$data['guest_name']);
				$model->setData('author_email',$data['guest_mail']);
			}
			$model->save();
			$answerCollection[] = $model;
			//print_r($model);exit;
			if(Mage::helper('productquestions')->getAnswerNotification()) {
				Mage::helper('productquestions')->sendEmail($answerCollection);
			}
				
		}

		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productquestions')->__('Save successfully'));
		$this->_redirect('*/*/');
		try {
			if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
				$model->setCreatedTime(now())
				->setUpdateTime(now());
			} else {
				$model->setUpdateTime(now());
			} 
			$model->save();
			//Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productquestions')->__());
			Mage::getSingleton('adminhtml/session')->setFormData(false);

			if ($this->getRequest()->getParam('back')) {
				$this->_redirect('*/*/edit', array('id' => $model->getId()));
				return;
			}
			$this->_redirect('*/*/');
			return;
		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
	   }
	}

	public function deleteAction() 
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('productquestions/answers');
				
				$model->setId($this->getRequest()->getParam('id'))
				->delete();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Answers was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction()
	{
		$ansIds = $this->getRequest()->getParam('page');
		
		try{
			foreach($ansIds as $ansId){
				$model = Mage::getModel('productquestions/answers')->load($ansId);
				$model->delete();
			}
			
			 Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ansIds)
                        )
                );
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
	public function massStatusChangeAction()
	{
		$ansIds = $this->getRequest()->getParam('page');
		$status = $this->getRequest()->getParam('status');
		try{
			foreach($ansIds as $ansId){
				$model = Mage::getModel('productquestions/answers')->load($ansId);
				$model->setStatus($status);	
				$model->save();
			}
			
			 Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully saved', count($ansIds)
                        )
                );
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
	
	

	public function exportCsvAction()
	{
		$fileName   = 'answers.csv';
		$content    = $this->getLayout()->createBlock('productquestions/adminhtml_answers_grid')
		->getCsv();

		$this->_sendUploadResponse($fileName, $content);
	}

	public function exportXmlAction()
	{
		$fileName   = 'answers.xml';
		$content    = $this->getLayout()->createBlock('productquestions/adminhtml_answers_grid')
		->getXml();

		$this->_sendUploadResponse($fileName, $content);
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
	public function categoriesJsonAction()
    {
        $bannerId     = $this->getRequest()->getParam('id');
        $_model  = Mage::getModel('productquestions/answers')->load($bannerId);
        Mage::register('answers_data', $_model);
        
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('productquestions/adminhtml_answers_edit_tab_category')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}