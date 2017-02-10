<?php
$this->startSetup();
Mage::register('isSecureArea', 1);

// Force the store to be admin
Mage::app()->setUpdateMode(false);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$category = Mage::getModel('catalog/category');
$category->setPath('1/2') // set parent to be root category
    ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
    ->setName('Product Deals')
	->setIsActive(1)
	->setIsAnchor(1)
	->setIncludeInMenu(0)
    ->save();
$this->endSetup();
?>

