<?php

class Ru_ShippingCity_Model_City extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('shippingcity/city');
    }

    public function getCities($regionId = null)
    {
        $cities = $this->_getResource()->loadByRegionId($regionId);
        return $cities;
    }

    public function toOptionArray($regionId)
    {
        $optionsArr = $this->getCities($regionId);
        $values = array();
        foreach ($optionsArr as $option) {
            $values[] = array(
                'value' => $option['city_id'],
                'label' => $option['name']
            );
        }
        return $values;
    }

}
