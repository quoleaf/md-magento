<?php
class Vsourz_Deals_Block_Deals extends Mage_Catalog_Block_Product_Abstract{
	public function getProducts(){
		$prodCount = $this->getProductCount();
		$catId = $this->getCategoryId();
		if(!$catId){
			$catId = Mage::getStoreConfig('deals/settings/category');
		}
		$prodCnt = $this->getProductCount();
		$_productCollection = Mage::getModel("vsourz_deals/deals")->getItemCollection($prodCnt,$catId, $prodCount);
		return $_productCollection;
	}
	public function getDiscountPer($_product){
		$price = $_product->getPrice();
		$specialPrice = $_product->getSpecialPrice();
		$discountPer = 100-($specialPrice/$price)*100;
		$discountPer = round($discountPer,2);
		return $discountPer.'%';
	}
	public function getDiscountAmt($_product){
		$price = Mage::helper('core')->currency($_product->getPrice(),false,false);
		$specialPrice = Mage::helper('core')->currency($_product->getSpecialPrice(),false,false);
		$discountAmt = round($price-$specialPrice,2);
		return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().$discountAmt;
	}
}