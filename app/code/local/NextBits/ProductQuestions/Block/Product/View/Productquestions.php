<?php
class NextBits_ProductQuestions_Block_Product_View_Productquestions extends Mage_Core_Block_Template
{
    protected $_product = null;

    function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('current_product');
        }
        return $this->_product;
    }
	
	public function makeClickableLinks($s) 
	{
		return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
	}
	
	public function getQALikes($id,$type)
	{
		$countDisLike = Mage::getModel('productquestions/likedislike')->getCollection()->addFieldToFilter('qa_id',$id)->addFieldToFilter('que_like',1)->addFieldToFilter('type',$type);		
		if(count($countDisLike)>0)
		{
			return count($countDisLike);
		}
		return 0;
	}
	
	public function getQADisLikes($id,$type)
	{
		$countDisLike = Mage::getModel('productquestions/likedislike')->getCollection()->addFieldToFilter('qa_id',$id)->addFieldToFilter('dislike',1)->addFieldToFilter('type',$type);
		if(count($countDisLike)>0)
		{
			return count($countDisLike);
		}
		return 0;		
	}

	public function getQuestions($id)
	{
		return Mage::getModel('productquestions/questions')->getCollection()
					->addFieldToFilter('product_id', array('like' => '%' . 	$id . '%'))
					->addFieldToFilter('status', 'approved')
					->addFieldToFilter('visibility', 'Public');
	}
	
	public function getAnswers($qid)
	{
		return Mage::getModel('productquestions/answers')->getCollection()
					->addFieldToFilter('product_questions_id',$qid)
					->addFieldToFilter('status', 'approved');
	}
}
?>