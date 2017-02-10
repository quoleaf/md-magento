<?php
class NextBits_ProductQuestions_Block_Adminhtml_Answers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'answers_id';
        $this->_blockGroup = 'productquestions';
        $this->_controller = 'adminhtml_answers';
        
        $this->_updateButton('save', 'label', Mage::helper('productquestions')->__('Save Answer'));
        $this->_updateButton('delete', 'label', Mage::helper('productquestions')->__('Delete Answer'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productquestions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'answers_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'answers_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('answers_data') && Mage::registry('answers_data')->getId() ) {
            return Mage::helper('productquestions')->__("Edit Answer", $this->htmlEscape(Mage::registry('answers_data')->getTitle()));
        } 
		else {
			return Mage::helper('productquestions')->__('New Answer');
		}
    }
}