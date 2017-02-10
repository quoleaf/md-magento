<?php

class NextBits_ProductQuestions_Adminhtml_QuestionsController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() 
	{
		$this->loadLayout()
			->_setActiveMenu('productquestions/questions')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Question Manager'), Mage::helper('adminhtml')->__('Question Manager'));
		return $this;
	}   
	
	public function productAction() 
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('product.grid')
			->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
	
	public function productgridAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('product.grid')
			->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }

	public function indexAction() 
	{
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() 
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('productquestions/questions')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('questions_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('productquestions/questions');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			//$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_questions_edit'))
			->_addLeft($this->getLayout()->createBlock('productquestions/adminhtml_questions_edit_tab'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestions')->__('Question does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() 
	{
		$this->_title($this->__('New Question'));

		$_model  = Mage::getModel('productquestions/questions');
		Mage::register('questions_data', $_model);
		Mage::register('current_questions', $_model);

		$this->_initAction();
		/* $this->_addBreadcrumb(Mage::helper('adminhtml')->__('I-Banner Manager'), Mage::helper('adminhtml')->__('I-Banner Manager'), $this->getUrl('*'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Banner'), Mage::helper('adminhtml')->__('Add Banner')); */

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_questions_edit'))
		->_addLeft($this->getLayout()->createBlock('productquestions/adminhtml_questions_edit_tabs'));

		$this->renderLayout(); 
	} 

	public function saveAction() 
	{
		if ($data = $this->getRequest()->getPost()) {
			// echo "<pre>";
			// print_r($data);exit;
			// $data['product_id'] = str_replace('&',',',$data['product_id']);
			
			//print_r($data['product_name']);exit;
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
			$data = $this->_filterDateTime ($data, array('created_at'));
			$model = Mage::getModel('productquestions/questions'); 
			  
			$data = $this->_filterDates($data, array('date', 'expiry_date'));
			
			$customerData = Mage::getModel('customer/customer')->load($data['customer_id']);
			$name = $customerData->getFirstname() . ' ' . $customerData->getLastname();
			$email = $customerData->getEmail();
			
			$model->setData($data)
			->setId($this->getRequest()->getParam('id'));
		
			$model->setData('author_name',$name);
			$model->setData('author_email',$email);
			$model->setData('asked_from', 'customer');
			$model->save();
			 
			//print_r($model);exit;
				
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
				$model = Mage::getModel('productquestions/questions');
				
				$model->setId($this->getRequest()->getParam('id'))
				->delete();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Question was successfully deleted'));
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
		$queIds = $this->getRequest()->getParam('page');
		
		try{
			foreach($queIds as $queId){
				$model = Mage::getModel('productquestions/questions')->load($queId);
				$model->delete();
			}
			
			 Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
					'Total of %d record(s) were successfully deleted', count($queIds)
					)
                );
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
	public function massStatusChangeAction()
	{
		$queIds = $this->getRequest()->getParam('page');
		$status = $this->getRequest()->getParam('status');
		try{
			foreach($queIds as $queId){
				$model = Mage::getModel('productquestions/questions')->load($queId);
				$model->setStatus($status);	
				$model->save();
			}
			
			 Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
					'Total of %d record(s) were successfully saved', count($queIds)
					)
                );
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}
}