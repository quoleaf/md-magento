<?php

class Dragonfroot_Recentlypurchase_Model_Resource_DefineText
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('recentlypurchase')->__('Random')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('recentlypurchase')->__('Someone in [country] checkout [product name]')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('recentlypurchase')->__('Someone sent [product name] to [full_name customer] in [ country of the shipping address]')
            ),
            array(
                'value' => 3,
                'label' => Mage::helper('recentlypurchase')->__('Someone in [suburb of shipping address] bought a [product name]')
            ),
            array(
                'value' => 4,
                'label' => Mage::helper('recentlypurchase')->__('Custom define text')
            )
        );
    }
}