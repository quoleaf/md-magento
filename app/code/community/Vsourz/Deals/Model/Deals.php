<?php
class Vsourz_Deals_Model_Deals extends Mage_Catalog_Model_Product{
	public function getItemCollection($prodCnt,$catId = NULL, $prodCount){
		$categoryId = $catId;
		$todayDate = date('m/d/y');
		$tomorrow = mktime(0, 0, 0, date('m'), date('d'), date('y'));
		$tomorrowDate = date('m/d/y', $tomorrow);
		$visibility = array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG);
		$collection = $this->getCollection()
		->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
		->addAttributeToSelect('*')
		->addAttributeToFilter('category_id', array('in' => $categoryId))
		->addAttributeToFilter('visibility', $visibility)
		->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
		->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
		->addAttributeToFilter('special_to_date', array('or'=> array(
			0 => array('date' => true, 'from' => $tomorrowDate))
			), 'left')
		->setPageSize($prodCount)
		->load();
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		return $collection;
	}
}
?>