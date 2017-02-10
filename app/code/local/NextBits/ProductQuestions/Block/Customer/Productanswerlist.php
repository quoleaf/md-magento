<?php
class NextBits_ProductQuestions_Block_Customer_Productanswerlist extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$customer_email = $customer->getEmail();
		}
		$answers = Mage::getModel('productquestions/answers')->getCollection()
			->addFieldToFilter('author_email',$customer_email)
			->setOrder('created_at','DESC');
		$this->setAnswers($answers);
	}
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$pager = $this->getLayout()->createblock('page/html_pager','answers.customer.list.pager')
			->setCollection($this->getAnswers());
		$this->setChild('pager',$pager);
		return $this;
	}
	
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}
	
	public function makeClickableLinks($s) 
	{
		return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
	}
}
?>