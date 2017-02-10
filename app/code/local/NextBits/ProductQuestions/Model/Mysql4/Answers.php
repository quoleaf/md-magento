<?php 
class NextBits_ProductQuestions_Model_Mysql4_Answers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('productquestions/answers','answers_id');
    }
}
?>