<?php
class NextBits_ProductQuestions_Block_Adminhtml_Questions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'product_questions_id';
        $this->_blockGroup = 'productquestions';
        $this->_controller = 'adminhtml_questions';
        
        $this->_updateButton('save', 'label', Mage::helper('productquestions')->__('Save Question'));
        $this->_updateButton('delete', 'label', Mage::helper('productquestions')->__('Delete Question'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productquestions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'questions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'questions_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('questions_data') && Mage::registry('questions_data')->getId() ) {
            return Mage::helper('productquestions')->__("Edit Question", $this->htmlEscape(Mage::registry('questions_data')->getTitle()));
        } 
		else {
			return Mage::helper('productquestions')->__('New Question');
		}
    }
}