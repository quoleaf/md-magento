<?php
/**
 * WEB4PRO - Creating profitable online stores
 *
 *
 * @author    WEB4PRO <amutylo@web4pro.com.ua>
 * @category  WEB4PRO
 * @package   Web4pro_ModuleName
 * @copyright Copyright (c) 2014 WEB4PRO (http://www.web4pro.net)
 * @license   http://www.web4pro.net/license.txt
 */
class Web4pro_Abandonedcart_Model_Mailssent extends Mage_Core_Model_Abstract
{
    public function _construct() {
        $this->_init('web4pro_abandonedcart/mailssent','id');
    }

}