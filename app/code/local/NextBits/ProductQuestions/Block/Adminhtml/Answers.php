<?php //echo "hii";exit;
class NextBits_ProductQuestions_Block_Adminhtml_Answers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_answers';
		$this->_blockGroup = 'productquestions';
		$this->_headerText = Mage::helper('productquestions')->__('Manage Answers');
		$this->_addButtonLabel = Mage::helper('productquestions')->__('New Answer');
		parent::__construct();
	}
}