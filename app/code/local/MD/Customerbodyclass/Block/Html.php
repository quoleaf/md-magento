<?php
 
class MD_Customerbodyclass_Block_Html extends Mage_Page_Block_Html
{
    public function __construct()
    {
        parent::__construct();
         
        /* @var Mage_Customer_Model_Session */
        $customer = Mage::getSingleton('customer/session');
         
        /**
         * Add logged in class to body if customer is logged in
        */
        if($customer->isLoggedIn()) {
            $this->addBodyClass('customer-logged-in');
        }
    }
}