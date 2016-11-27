<?php
class Mageplaza_Googleadwords_Model_System_Config_Format extends Varien_Object
{


    public function toOptionArray(){
        return $this->getOptionHash();
    }
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            3    => Mage::helper('googleadwords')->__('No notification'), //default value
            1    => Mage::helper('googleadwords')->__('One line notification'),
            2    => Mage::helper('googleadwords')->__('Two lines notification'),
        );
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}