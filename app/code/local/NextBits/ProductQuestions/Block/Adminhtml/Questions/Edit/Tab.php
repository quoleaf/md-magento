<?php
// echo "hiii";
class NextBits_ProductQuestions_Block_Adminhtml_Questions_Edit_Tab extends Mage_Adminhtml_Block_Widget_Tabs
{

	public function __construct()
	{
		parent::__construct();
		$this->setId('productquestions_tabs');
		// $this->setName('example_tabs');	
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('productquestions')->__('Question'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('general_section', array(
			'label'     => Mage::helper('productquestions')->__('General Information'),
			'title'     => Mage::helper('productquestions')->__('General Information'),
			'content'   => $this->getLayout()->createBlock('productquestions/adminhtml_questions_edit_tab_forms')
			->toHtml(),
		));

		$this->addTab('products', array(
			'label' => Mage::helper('productquestions')->__('Select Product'),
			'url' 	=> $this->getUrl('*/*/product', array('_current' => true)),
			'class' => 'ajax',
		));

		return parent::_beforeToHtml(); 
	}
}