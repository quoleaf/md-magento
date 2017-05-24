<?php

class DragonFroot_Recentlypurchase_Block_Recentlypurchase extends Mage_Catalog_Block_Product_Abstract
{
    const DISPLAY_DEFINE_TEXT = 1;
    const DISPLAY_IMAGE_PRODUCT = 2;
    const DISPLAY_VOTE = 3;
    const DISPLAY_TIME_AGO = 4;
    const POSITION_LEFT_BOTTOM = 1;
    const POSITION_RIGHT_BOTTOM = 2;
    const DEFINE_TEXT_RANDOM = 0;
    const DEFINE_TEXT_CHECKOUT = 1;
    const DEFINE_TEXT_SENT_TO = 2;
    const DEFINE_TEXT_BOUGHT = 3;
    const DEFINE_TEXT_CUSTOM = 4;

    public function __construct()
    {
        parent::__construct();
    }

    public function enableModule()
    {
        $enable = Mage::getStoreConfig('recentlypurchase/params/enabled');
        if (trim(strtolower($this->enabled)) == 'yes' || $enable == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getLastestOrder()
    {
        $limit_order = Mage::getStoreConfig('recentlypurchase/params/limit_order');
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
			->setOrder('increment_id','DESC')
            //->addFieldToFilter('DATE(created_at)', array('eq' => date('Y-m-d')))
            //->setOrder('rand()')
            ->setPageSize($limit_order)
            ->load();
        return $orders;
    }

    public function timeAgo($timebefore = 0)
    {
        $diff = time() - $timebefore;
        $years = intval($diff / 31536000);
        if ($years) {
            return $years . ' ' . (($years > 1) ? Mage::helper('recentlypurchase')->__('years') : Mage::helper('recentlypurchase')->__('year'));
        }
        $months = intval($diff / 2592000);
        if ($months) {
            return $months . ' ' . (($months > 1) ? Mage::helper('recentlypurchase')->__('months') : Mage::helper('recentlypurchase')->__('month'));
        }
        $weeks = intval($diff / 604800);
        if ($weeks) {
            return $weeks . ' ' . (($weeks > 1) ? Mage::helper('recentlypurchase')->__('weeks') : Mage::helper('recentlypurchase')->__('week'));
        }
        $days = intval($diff / 86400);
        if ($days) {
            return $days . ' ' . (($days > 1) ? Mage::helper('recentlypurchase')->__('days') : Mage::helper('recentlypurchase')->__('day'));
        }
        $hours = intval($diff / 3600);
        if ($hours) {
            return $hours . ' ' . (($hours > 1) ? Mage::helper('recentlypurchase')->__('hours') : Mage::helper('recentlypurchase')->__('hour'));
        }
        $minutes = intval($diff / 60);
        if ($minutes) {
            return $minutes . ' ' . (($minutes > 1) ? Mage::helper('recentlypurchase')->__('minutes') : Mage::helper('recentlypurchase')->__('minute'));
        }
        $seconds = intval($diff / 1);
        if ($seconds) {
            return $seconds . ' ' . (($seconds > 1) ? Mage::helper('recentlypurchase')->__('seconds') : Mage::helper('recentlypurchase')->__('second'));
        }
        return Mage::helper('recentlypurchase')->__('Just now');
    }
}