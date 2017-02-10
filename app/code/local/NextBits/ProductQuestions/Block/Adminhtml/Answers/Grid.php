<?php
// echo "hiii";
class NextBits_ProductQuestions_Block_Adminhtml_Answers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('answersGrid');
		$this->setDefaultSort('answers_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	

	protected function _prepareCollection()
	{  
		$collection = Mage::getModel("productquestions/answers")->getCollection();
		$collection->getSelect()
		->joinLeft(array('ce1' => $collection->getTable('productquestions/questions')), 'ce1.product_questions_id=main_table.product_questions_id', array('questions'));
		// echo $collection->getSelect()->__toString();exit;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('answers_id', array(
			'header'    => Mage::helper('productquestions')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'answers_id',
		));
		
		$this->addColumn('author_name', array(
			'header'    => Mage::helper('productquestions')->__('Author Name'),
			'width'     => '100px',
			'index'     => 'author_name',
		));
		
		$this->addColumn('questions', array(
			'header'    => Mage::helper('productquestions')->__('Questions'),
			'align'     =>'left',
			'index'     => 'questions',
		));
		
		$this->addColumn('answers', array(
			'header'    => Mage::helper('productquestions')->__('Answers'),
			'align'     =>'left',
			'index'     => 'answers',
		));
		
		$this->addColumn('created_at', array(
			'header'    => Mage::helper('productquestions')->__('Created At'),
			'align'     =>'left',
			'width'     => '150px',
			'index'     => 'created_at',
		));
		
		$this->addColumn('status', array(
			'header'    => Mage::helper('productquestions')->__('Status'),
			'align'     =>'left',
			'width'     => '90px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
			    "approved" => Mage::helper('productquestions')->__('Approved'),
			    "pending" => Mage::helper('productquestions')->__('Pending')
				  
			),
		));
		
		$this->addColumn('action',array(
			'header'    =>  Mage::helper('productquestions')->__('Action'),
			'width'     => '100',
			'type'      => 'action',
			'getter'    => 'getId',
			'actions'   => array(
				array(
					'caption'   => Mage::helper('productquestions')->__('Edit'),
					'url'       => array('base'=> '*/*/edit'),
					'field'     => 'id'
				)
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
		));

		return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction() 
	{ 
		
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('page');

		$this->getMassactionBlock()->addItem('delete', array(
			'label' => Mage::helper('productquestions')->__('Delete'),
			'url' => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('productquestions')->__('Are you sure?')
		));

		$statuses = array(
			array('value' => 'pending', 'label' => Mage::helper('productquestions')->__('Pending')),
			array('value' => 'approved', 'label' => Mage::helper('productquestions')->__('Approved')),
		);

		array_unshift($statuses, array('label' => '', 'value' => ''));
		$this->getMassactionBlock()->addItem('status', array(
			'label' => Mage::helper('productquestions')->__('Change status'),
			'url' => $this->getUrl('*/*/massStatusChange', array('_current' => true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('productquestions')->__('Status'),
					'values' => $statuses
				)
			)
		));
		
		return $this;  
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}


}