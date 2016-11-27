<?php


class Mageplaza_Googleadwords_Model_Config extends Mage_Core_Model_Abstract{

    /**
     * Themepuma
     * Every config should be defined as const here
     */
    const XML_GENERAL_ENABLE = 'googleadwords/general/enable';

    public function isEnabled(){
        return $this->getConfig(self::XML_GENERAL_ENABLE);
    }

    /**
     * @param null $name
     * @return mixed|null
     */
    public function getConfig($name = null){
        if(!$name) return null;
        return Mage::getStoreConfig($name);
    }
}