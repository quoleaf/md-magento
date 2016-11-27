<?php
class Mageplaza_Googleadwords_Helper_Data extends Mage_Core_Helper_Abstract
{
 
 	protected function _initConfig()
    {
        return Mage::getSingleton('googleadwords/config');
    }

    public function isEnabled(){
        return $this->_initConfig()->isEnabled();
    }

}