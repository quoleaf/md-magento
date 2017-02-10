<?php //echo "hii";exit;
class NextBits_ProductQuestions_Block_Adminhtml_Questions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_questions';
		$this->_blockGroup = 'productquestions';
		$this->_headerText = Mage::helper('productquestions')->__('Manage Questions');
		$this->_addButtonLabel = Mage::helper('productquestions')->__('New Question');
		parent::__construct();
	}
}