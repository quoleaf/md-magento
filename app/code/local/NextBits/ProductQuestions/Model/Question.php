<?php
class NextBits_ProductQuestions_Model_Question
{
	protected $_options;
	
	public function toOptionArray(){
		return array(
            array('value' => 1, 'label'=>Mage::helper('productquestions')->__('Anyone')),
            array('value' => 2, 'label'=>Mage::helper('productquestions')->__('Registered Customers')),
        );
	}
}
?>