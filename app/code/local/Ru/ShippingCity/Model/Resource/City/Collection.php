<?php

class Ru_ShippingCity_Model_Resource_City_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    protected function _construct() {
        $this->_init('shippingcity/city');
    }

}
