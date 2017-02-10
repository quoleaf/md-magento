<?php
class NextBits_ProductQuestions_Block_Customer_Productquestionlist extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$customer_email = $customer->getEmail();
		}
		$questions = Mage::getModel('productquestions/questions')->getCollection()
			->addFieldToFilter('author_email',$customer_email)
			->setOrder('created_at','DESC');
		$this->setQuestions($questions);
	}
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$pager = $this->getLayout()->createblock('page/html_pager','questions.customer.list.pager')
			->setCollection($this->getQuestions());
		$this->setChild('pager',$pager);
		return $this;
	}
	
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
}
?>