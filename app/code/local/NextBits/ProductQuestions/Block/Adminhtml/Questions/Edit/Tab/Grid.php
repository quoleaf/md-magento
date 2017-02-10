<?php //echo "hiii";
class NextBits_ProductQuestions_Block_Adminhtml_Questions_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('productGrid');
		$this->setUseAjax(true); // Using ajax grid is important
		$this->setDefaultSort('entity_id');
		$this->setDefaultFilter(array('in_products'=>'')); // By default we have added a filter for the rows, that in_products value to be 1
		$this->setSaveParametersInSession(false);  //Dont save paramters in session or else it creates problems
	}

	protected function _prepareColumns() {

		$this->addColumn('in_products', array(
			'header_css_class'  => 'a-center',
			'class'     		=> 'required-entry',
			'required' 			=> 1,
			'type'              => 'radio',
			'html_name'         => 'product_id',
			'values'            => $this->_getSelectedProducts(),
			'align'             => 'center',
			'index'             => 'entity_id'
		));

		
		$this->addColumn('name', array(
            'header'=> Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));
		
		$this->addColumn('sku', array(
            'header'=> Mage::helper('catalog')->__('SKU'),
            'align' => 'left',
            'index' => 'sku',
        ));
		
		return parent::_prepareColumns();
	}
	
	protected function _getSelectedProducts()   // Used in grid to return selected customers values.
    {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }
	protected function _prepareCollection() {
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('attribute_set_id')
                ->addAttributeToSelect('type_id')
                ->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');

        
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }
    public function getSelectedProducts()
    {
        // Customer Data
        $tm_id = $this->getRequest()->getParam('id');
        if(!isset($tm_id)) {
            $tm_id = 0;
        }
		$conn = Mage::getModel('productquestions/questions')->load($tm_id);
		$data =  $conn->getData('product_id');
		$datas = explode(',',$data);
		
        $products = $datas; 
	
		// This is hard-coded right now, but should actually get values from database.
        $proIds = array();
 
        foreach($products as $product) { 
			$proIds[$product] = array('id'=>$product);
        }
        return $proIds;
    }

	public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/productgrid', array('_current'=>true));
    }
	
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}