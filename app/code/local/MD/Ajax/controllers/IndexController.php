<?php
class MD_Ajax_IndexController extends Mage_Core_Controller_Front_Action{
 public function ajaxAction(){
    $customer = Mage::getModel('customer/customer');
    $websiteId = Mage::app()->getWebsite()->getId();

    if (array_key_exists('email', $_POST)) {
        $email = $_POST['email'];
    } else {
        $this->getResponse()->setBody(false);
        return;
    }
    if ($websiteId) {
        $customer->setWebsiteId($websiteId);
    }
    $customer->loadByEmail($email);
    if ($customer->getId()) {
        $this->getResponse()->setBody(true);
        return;
    }
    $this->getResponse()->setBody(false);
    return;
}   
}