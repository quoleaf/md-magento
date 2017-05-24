<?php

class Dragonfroot_Recentlypurchase_Model_Resource_InformationDisplay
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('recentlypurchase')->__('Define text')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('recentlypurchase')->__('Image product')
            ),
            array(
                'value' => 3,
                'label' => Mage::helper('recentlypurchase')->__('Vote of the product')
            ),
            array(
                'value' => 4,
                'label' => Mage::helper('recentlypurchase')->__('Time ago')
            )
        );
    }
}