<?php
class NextBits_ProductQuestions_AccountController extends Mage_Core_Controller_Front_Action
{
	protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
	public function listAction() 
	{
		$this->loadLayout();											
		$this->renderLayout();
	}
	
	public function viewAction() 
	{
		$this->loadLayout();											
		$this->renderLayout();
	}
	
	public function viewansAction() 
	{
		$this->loadLayout();											
		$this->renderLayout();
	}
}
?>