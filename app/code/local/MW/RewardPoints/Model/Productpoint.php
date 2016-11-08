<?php

class MW_RewardPoints_Model_Productpoint extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/productpoint');
    }
    public function getPoint($product_id)
	{
		$reward_point_attribute = 0;
		
		if(Mage::getModel('catalog/product')->load($product_id)->getData('reward_point_product'))
				$reward_point_attribute = (int)Mage::getModel('catalog/product')->load($product_id)->getData('reward_point_product');
				
		return $reward_point_attribute;
	}
	
}