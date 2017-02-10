<?php
class NextBits_ProductQuestions_ProductquestionsController extends Mage_Core_Controller_Front_Action
{
	public function savequestionsAction() 
	{
		$questions = $this->getRequest()->getPost();
		$questionCollection = array();
		$model = Mage::getModel('productquestions/questions'); 
		// echo "<pre>";
		// print_r($questions);exit;
		$id = $this->getRequest()->getParam('product_id');
		$model->setData('author_name',$questions['author_name']);
		$model->setData('author_email',$questions['author_mail']);
		$model->setData('product_id',$id);
		$model->setData('questions',$questions['content']);
		$status = Mage::helper("productquestions")->approveQueAutomatically();
		if($status == 1){
			$model->setData('status','approved');
		} else {
			$model->setData('status','pending');
		}
		if(array_key_exists('is_private',$questions)){
			$model->setData('visibility','private');
		}
		else {
			$model->setData('visibility','public');
		}
		
		$model->setData('created_at',now());
		$model->setData('asked_from',$questions['asked_from']);
		if($questions['asked_from'] == 'customer'){
			$customer = Mage::getModel('customer/customer');
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
			$customer->loadByEmail($questions['author_mail']);
			
			$customer_id = $customer->getId();
			$model->setData('customer_id',$customer_id);
		}
		$model->save();
		$questionCollection[] = $model;
		if(Mage::helper('productquestions')->getQuestionNotification()) {
			Mage::helper('productquestions')->sendMail($questionCollection);
		}
		$obj = Mage::getModel('catalog/product');
		$_product = $obj->load($id);
		$prdurl = $_product->getProductUrl(); 
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$_prdurl = explode($url,$prdurl); 
		Mage::getSingleton("core/session")->addSuccess(Mage::helper("productquestions")->__("Your question has been received and Notification will be send when answer is published"));
		$this->_redirect($_prdurl[1]);
	}
	
	public function saveanswersAction() 
	{
		$answers = $this->getRequest()->getPost();
		$answerCollection = array();
		$model = Mage::getModel('productquestions/answers'); 
		$id = $this->getRequest()->getParam('product_questions_id');
		// echo $id;
		// echo "<pre>";
		// print_r($answers);exit;
		
		$model->setData('author_name',$answers['author_name']);
		$model->setData('author_email',$answers['author_mail']);
		$model->setData('product_questions_id',$id);
		$model->setData('answers',$answers['content']);
		$status = Mage::helper("productquestions")->approveAnsAutomatically();
		if($status == 1){
			$model->setData('status','approved');
		} else {
			$model->setData('status','pending');
		}
		$model->setData('created_at',now());
		$model->setData('answers_from',$answers['answers_from']);
		if($answers['answers_from'] == 'customer'){
			$customer = Mage::getModel('customer/customer');
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
			$customer->loadByEmail($answers['author_mail']);
			
			$customer_id = $customer->getId();
			$model->setData('customer_id',$customer_id);
		}
		$model->save();
		$answerCollection[] = $model;
		if(Mage::helper('productquestions')->getAnswerNotification()) {
			Mage::helper('productquestions')->sendEmail($answerCollection);
		}
		$que = Mage::getModel('productquestions/questions')->load($id);
		$proId = $que->getProductId();
		$obj = Mage::getModel('catalog/product');
		$_product = $obj->load($proId);
		$prdurl=$_product->getProductUrl(); 
		$url=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$_prdurl=explode($url,$prdurl); 
		Mage::getSingleton("core/session")->addSuccess(Mage::helper("productquestions")->__("Your answer has been received "));
		$this->_redirect($_prdurl[1]);
	}
	
	public function setLikeAction()
	{
		$customerId = $this->getRequest()->getPost('customerId');
		$queId = $this->getRequest()->getPost('qaId');
		$type = $this->getRequest()->getPost('typeId');
		
		$FirstItemcollection = Mage::getModel('productquestions/likedislike')->getCollection()->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('qa_id',$queId)->addFieldToFilter('type',$type);			
		if(count($FirstItemcollection)>0)
		{
			$likeModelLoad = Mage::getModel('productquestions/likedislike')->load($FirstItemcollection->getFirstItem()->getLikeDislikeId());
			if($likeModelLoad->getQueLike()==0)
			{
				$likeModelLoad->setQueLike(1);
				$likeModelLoad->setDislike(0);
				$likeModelLoad->save();
			}
			else
			{
				$likeModelLoad->setQueLike(0);				
				$likeModelLoad->save();						
			}
			
		}
		else
		{		
			$likeModel = Mage::getModel('productquestions/likedislike');	
			$likeModel->setQaId($queId);
			$likeModel->setQueLike(1);
			$likeModel->setCustomerId($customerId);
			$likeModel->setType($type);
			$likeModel->save();			
		}
		
		$block=$this->getLayout()->createBlock('productquestions/product_view_productquestions');

		$like=$block->getQALikes($queId,$type);
		$dislike = $block->getQADisLikes($queId,$type);
		$arr = array('dislike' => $dislike, 'like' => $like ,'type' => $type);
		echo json_encode($arr);
	}
	public function setDisLikeAction()
	{
		$customerId = $this->getRequest()->getPost('customerId');
		$queId = $this->getRequest()->getPost('qaId');
		$type = $this->getRequest()->getPost('typeId');
		
		$FirstItemcollection = Mage::getModel('productquestions/likedislike')->getCollection()->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('qa_id',$queId)->addFieldToFilter('type',$type);	
		
		if(count($FirstItemcollection)>0)
		{
			$likeModelLoad = Mage::getModel('productquestions/likedislike')->load($FirstItemcollection->getFirstItem()->getLikeDislikeId());
			if($likeModelLoad->getDislike()==0)
			{
				$likeModelLoad->setDislike(1);
				$likeModelLoad->setQueLike(0);
				$likeModelLoad->save();
			}
			else
			{
				$likeModelLoad->setDislike(0);				
				$likeModelLoad->save();				
			}				
		}
		else
		{		
			$likeModel = Mage::getModel('productquestions/likedislike');	
			$likeModel->setQaId($queId);
			$likeModel->setDislike(1);
			$likeModel->setCustomerId($customerId);
			$likeModel->setType($type);
			$likeModel->save();			
		}	
		$block=$this->getLayout()->createBlock('productquestions/product_view_productquestions');

		$like=$block->getQALikes($queId,$type);
		$dislike = $block->getQADisLikes($queId,$type);
		
		
		$arr = array('dislike' => $dislike, 'like' => $like,'type' => $type);
		echo json_encode($arr);
	}
}
?>