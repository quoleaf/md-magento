<?php

/**
 * Product:       Xtento_CustomTrackers (1.6.1)
 * ID:            Dc7yPU38CZxYBN+a3mW6MRfqBRg/ZkIO4tMh9SO20MI=
 * Packaged:      2017-02-28T08:07:50+00:00
 * Last Modified: 2012-02-10T00:37:26+01:00
 * File:          app/code/local/Xtento/CustomTrackers/Model/Shipping/Carrier/Tracker7.php
 * Copyright:     Copyright (c) 2016 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_CustomTrackers_Model_Shipping_Carrier_Tracker7 extends Xtento_CustomTrackers_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'tracker7';

    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }

    public function isTrackingAvailable()
    {
        return parent::isTrackingAvailable();
    }
}