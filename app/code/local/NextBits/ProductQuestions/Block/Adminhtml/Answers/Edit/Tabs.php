<?php
// echo "hiii";
class NextBits_ProductQuestions_Block_Adminhtml_Answers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('productquestions_tabs');
		// $this->setName('example_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('productquestions')->__('Answer'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('general_section', array(
			'label'     => Mage::helper('productquestions')->__('General Information'),
			'title'     => Mage::helper('productquestions')->__('General Information'),
			'content'   => $this->getLayout()->createBlock('productquestions/adminhtml_answers_edit_tab_form')
			->toHtml(),
		));
		
		return parent::_beforeToHtml(); 
	}
}