<?php

class Dragonfroot_Recentlypurchase_Model_Resource_PositionDisplay
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 1,
                'label' => Mage::helper('recentlypurchase')->__('Left bottom')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('recentlypurchase')->__('Right bottom')
            )
        );
    }
}