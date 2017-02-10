<?php //echo "hiii";
class NextBits_ProductQuestions_Block_Adminhtml_Answers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('answers_form', array('legend'=>Mage::helper('productquestions')->__('General Information')));
		
		$que  = Mage::getModel('productquestions/questions')->getCollection();
		$questions[] = array(
			'value'     => '',
			'label'     => '--Please Select Question--',
		);
		foreach ($que as $item)
		{
		$questions[] = array(
		   'value'     => $item->getProductQuestionsId(),
		   'label'     => $item->getQuestions(),
		); 
		}
		
		$fieldset->addField('product_questions_id', 'select', array(
			'label'     		=> Mage::helper('productquestions')->__('Question'),
			'class'     		=> 'required-entry',
			'required'  		=> true,
			'name'      		=> 'product_questions_id',
			'onclick' 			=> "",
			'onchange' 			=> "",
			'values' 			=> $questions,
			'disabled' 			=> false,
			'readonly' 			=> false,
			'after_element_html'=> '',
			'tabindex' 			=> 1
        ));
		
		$dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
		$fieldset->addField('created_at', 'date', array(
			'name'		=> 'created_at',
			'label'  	=> Mage::helper('productquestions')->__('Created At'),
			'title'  	=> Mage::helper('productquestions')->__('Created At'),
			'image'  	=> $this->getSkinUrl('images/grid-cal.gif'),
			'format' 	=> $dateFormatIso,
			'time'   	=> true,
			'required'  => true
		));
		
		
		
		$fieldset->addField('answers_from', 'select', array(
			'label'     => Mage::helper('productquestions')->__('Answers From'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'answers_from',
			'onclick' => "",
			'onchange' => "",
			'values' => array('admin' => 'Admin','customer' => 'Customer','guest' => 'Guest'),
			'disabled' => false,
			'readonly' => false,
			'after_element_html' => '',
			'tabindex' => 1
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
			'label'     => Mage::helper('productquestions')->__('Author'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'customer_id',
			'onclick' => "",
			'onchange' => "",
			'value'  => '1',
			'values' => $customername,
			'disabled' => false,
			'readonly' => false,
			'after_element_html' => '',
			'tabindex' => 1
        ));
		
		$name = Mage::getStoreConfig('productquestions/general/admin_name');
		$mail = Mage::getStoreConfig('productquestions/general/admin_email');
		
		
		$fieldset->addField('admin_name', 'text', array(
            'label'     => Mage::helper('productquestions')->__('Author Name'),
			'class'     => 'required-entry',
			'required'  => true,
            'name'      => 'admin_name',
        ));
	

		
		$fieldset->addField('admin_email', 'text', array(
            'label'     => Mage::helper('productquestions')->__('Author Mail'),
			'class'     => 'required-entry',
			'required'  => true,
            'name'      => 'admin_email',
        ));
		
		$fieldset->addField('author_name', 'text', array(
            'label'     => Mage::helper('productquestions')->__('Author Name'),
			'class'     => 'required-entry',
			'required'  => true,
            'name'      => 'guest_name',
        ));
		
		
		$fieldset->addField('author_email', 'text', array(
            'label'     => Mage::helper('productquestions')->__('Author Mail'),
			'class'     => 'required-entry',
			'required'  => true,
            'name'      => 'guest_mail',
        ));
		
		$fieldset->addField('answers', 'textarea', array(
            'label'     => Mage::helper('productquestions')->__('Answers'),
			'class'     => 'required-entry',
			'required'  => true,
            'name'      => 'answers',
        ));
		
		$fieldset->addField('status', 'select', array(
			'label'     		=> Mage::helper('productquestions')->__('Status'),
			'class'     		=> 'required-entry',
			'required'  		=> true,
			'name'      		=> 'status',
			'onclick' 			=> "",
			'onchange' 			=> "",
			'values'			=> array('pending' => 'Pending','approved' => 'Approved'),
			'disabled' 			=> false,
			'readonly' 			=> false,
			'after_element_html'=> '',
			'tabindex' 			=> 1
        ));
		
		
		
		$this->setForm($form);

        $this->setChild('form_after',

            // The content of the "form_after" block is a Mage_Adminhtml_Block_Widget_Form_Element_Dependence block typ
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
				->addFieldMap('answers_from', 'answers_from')
                ->addFieldMap('customer_id', 'customer_id')
                ->addFieldMap('admin_name', 'admin_name')
				->addFieldMap('admin_email', 'admin_email')
				->addFieldMap('author_name', 'author_name')
                ->addFieldMap('author_email', 'author_email')
               

                /**
                 * Create dependencies
                 * Each addFieldDependence() can be understood like:
                 * "Show this [dependencies block identifier] if this [dependencies block identifier] is [value]
                 */
              
                ->addFieldDependence('customer_id', 'answers_from', 'customer')
				->addFieldDependence('admin_name', 'answers_from', 'admin')
                ->addFieldDependence('admin_email', 'answers_from', 'admin')
                ->addFieldDependence('author_name', 'answers_from', 'guest')
                ->addFieldDependence('author_email', 'answers_from', 'guest')
		);
		if (Mage::getSingleton('adminhtml/session')->getAnswersData())
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getAnswersData());
			Mage::getSingleton('adminhtml/session')->setAnswersData(null);
		} elseif ( Mage::registry('answers_data') ) {
			$form->setValues(Mage::registry('answers_data')->getData());
			$form->addValues(array('admin_name'=> Mage::getStoreConfig('productquestions/general/admin_name')));
			$form->addValues(array('admin_email'=> Mage::getStoreConfig('productquestions/general/admin_email')));
		}
		return parent::_prepareForm();
	}
}