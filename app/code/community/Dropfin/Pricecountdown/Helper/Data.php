<?php

/**
 * Dropfin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade 
 * this extension to newer versions in the future. 
 *
 * @category    Dropfin
 * @package     Special Price Countdown
 * @copyright   Copyright (c) Dropfin (http://www.dropfin.com)
 */

class Dropfin_Pricecountdown_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_ENABLED = 'dropfin_pricecountdown/configurations/enabled';
    const XML_PATH_SHOW_PLP = 'dropfin_pricecountdown/configurations/show_plp';
    const XML_PATH_SHOW_PLP_TITLE = 'dropfin_pricecountdown/configurations/show_plp_title';
    const XML_PATH_PLP_TITLE = 'dropfin_pricecountdown/configurations/plp_title';
    const XML_PATH_CLOCK_STYLE_PLP = 'dropfin_pricecountdown/configurations/clock_style_plp';
    const XML_PATH_SHOW_PDP = 'dropfin_pricecountdown/configurations/show_pdp';
    const XML_PATH_SHOW_PDP_TITLE = 'dropfin_pricecountdown/configurations/show_pdp_title';
    const XML_PATH_PDP_TITLE = 'dropfin_pricecountdown/configurations/pdp_title';
    const XML_PATH_CLOCK_STYLE_PDP = 'dropfin_pricecountdown/configurations/clock_style_pdp';

    public function getStatus() {
    	return (int) Mage::getStoreConfig(self::XML_PATH_ENABLED);
    }

    public function getProductListPageStatus() {
    	return (int) Mage::getStoreConfig(self::XML_PATH_SHOW_PLP);
    }

    public function getPlpTitle() {
        if(Mage::getStoreConfig(self::XML_PATH_SHOW_PLP_TITLE)) {
            return Mage::getStoreConfig(self::XML_PATH_PLP_TITLE);
        }
        return false;
    }

    public function getProductListPageClockStyle() {
        return (int) Mage::getStoreConfig(self::XML_PATH_CLOCK_STYLE_PLP);
    }

    public function getProductViewPageStatus() {
        return (int) Mage::getStoreConfig(self::XML_PATH_SHOW_PDP);
    }

    public function getPdpTitle() {
        if(Mage::getStoreConfig(self::XML_PATH_SHOW_PDP_TITLE)) {
            return Mage::getStoreConfig(self::XML_PATH_PDP_TITLE);
        }
        return false;
    }

    public function getProductViewPageClockStyle() {
    	return (int) Mage::getStoreConfig(self::XML_PATH_CLOCK_STYLE_PDP);
    }

    public function validatePriceCountDown($fromdate, $todate, $specialPrice){

    	$currentDate = Mage::getModel('core/date')->date('Y-m-d h:i:s A');
    	if($specialPrice != 0 || $specialPrice) {
    		if($todate != null) {
    			if(strtotime($todate) >= strtotime($currentDate) && strtotime($fromdate) <= strtotime($currentDate)){
    				return true;
    			}
    		}
    	}
    	return false;
    	
    }
}
