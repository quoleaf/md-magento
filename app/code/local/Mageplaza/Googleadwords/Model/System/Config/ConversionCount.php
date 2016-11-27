<?php
class Mageplaza_Googleadwords_Model_System_Config_ConversionCount extends Varien_Object
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
            'all'    => Mage::helper('googleadwords')->__('All'),
            'unique'    => Mage::helper('googleadwords')->__('Unique'),
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