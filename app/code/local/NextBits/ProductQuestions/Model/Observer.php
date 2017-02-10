<?php
class NextBits_ProductQuestions_Model_Observer extends Varien_Object
{
   public function prepareLayoutBefore(Varien_Event_Observer $observer)
   {
	
       /* @var $block Mage_Page_Block_Html_Head */
	   if (!Mage::helper('productquestions')->isJqueryEnabled()) {
            return $this;
        }
       $block = $observer->getEvent()->getBlock();

       if ("head" == $block->getNameInLayout()) {
           foreach (Mage::helper('productquestions')->getFiles() as $file) {
               $block->addJs(Mage::helper('productquestions')->getJQueryPath($file));
           }
       }
       return $this;
   }
}