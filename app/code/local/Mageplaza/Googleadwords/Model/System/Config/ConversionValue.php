<?php
class Mageplaza_Googleadwords_Model_System_Config_ConversionValue extends Varien_Object
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
            0    => Mage::helper('googleadwords')->__('Don\'t assign a value'),
            'same'    => Mage::helper('googleadwords')->__('Each time it happens, the conversion action has the same value'),
            'total'    => Mage::helper('googleadwords')->__('Use Order Total'),
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