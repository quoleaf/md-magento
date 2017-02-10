<?php //echo "hiii";
class NextBits_ProductQuestions_Block_Adminhtml_Questions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('questions_form', array('legend'=>Mage::helper('productquestions')->__('General Information')));
		
		$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		$fieldset->addField('created_at', 'date', array(
			'name'   	=> 'created_at',
			'label'  	=> Mage::helper('productquestions')->__('Created At'),
			'title'  	=> Mage::helper('productquestions')->__('Created At'),
			'image'  	=> $this->getSkinUrl('images/grid-cal.gif'),
			'format' 	=> $dateFormatIso,
			'time'   	=> true,
			'required'  => true,
			  //'disabled'  => true
		));
		
		$fieldset->addField('status', 'select', array(
			'label'     		=> Mage::helper('productquestions')->__('Status'),
			'class'     		=> 'required-entry',
			'required'  		=> true,
			'name'      		=> 'status',
			'onclick' 			=> "",
			'onchange' 			=> "",
			'values' 			=> array('pending' => 'Pending','approved' => 'Approved'),
			'disabled' 			=> false,
			'readonly' 			=> false,
			'after_element_html'=> '',
			'tabindex' 			=> 1
        ));
		
		$customerObj  = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('firstname')
			->addAttributeToSelect('lastname')
			->addAttributeToSelect('email');
			
		$customername[] = array(
			'value'     => '',
			'label'     => '--Please Select Customer--',
		);
		
		foreach ($customerObj as $item)
		{
			$customername[] = array(
				'value'     => $item->getId(),
				'label'     => $item->getName() . " < ". $item->getEmail() ." >",
			); 
		}
	
		$fieldset->addField('customer_id', 'select', array(
			'label'     		=> Mage::helper('productquestions')->__('Author'),
			'class'     		=> 'required-entry',
			'required'  		=> true,
			'name'      		=> 'customer_id',
			'onclick' 			=> "",
			'onchange' 			=> "",
			'value'  			=> '1',
			'values' 			=> $customername,
			'disabled' 			=> false,
			'readonly' 			=> false,
			'after_element_html'=> '',
			'tabindex' 			=> 1
        ));
		

		
		$fieldset->addField('questions', 'textarea', array(
            'label'     => Mage::helper('productquestions')->__('Question'),
			'class'     => 'required-entry',
			'required'  => true,
            'name'      => 'questions',
        ));
		
		$fieldset->addField('visibility', 'select', array(
			'label'     		=> Mage::helper('productquestions')->__('Visibility'),
			'class'     		=> 'required-entry',
			'required'  		=> true,
			'name'      		=> 'visibility',
			'onclick' 			=> "",
			'onchange' 			=> "",
			'values' 			=> array('public' => 'Public','private' => 'Private'),
			'disabled' 			=> false,
			'readonly' 			=> false,
			'after_element_html'=> '',
			'tabindex' 			=> 1
        ));
		
		if (Mage::getSingleton('adminhtml/session')->getQuestionsData())
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getQuestionsData());
			Mage::getSingleton('adminhtml/session')->setQuestionsData(null);
		} elseif ( Mage::registry('questions_data') ) {
			$form->setValues(Mage::registry('questions_data')->getData());
		}
		
		return parent::_prepareForm();
	}
}