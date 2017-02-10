<?php
class NextBits_ProductQuestions_Block_Customer_Questionview extends Mage_Core_Block_Template
{
	protected function _construct() 
	{
		parent::_construct();
	}
	
	public function makeClickableLinks($s) 
	{
		return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
	}
	
	public function getBackUrl()
	{
		return Mage::getUrl('productquestions/account/list');
	}
}