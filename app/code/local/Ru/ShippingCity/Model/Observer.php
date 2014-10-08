<?php

class Ru_ShippingCity_Model_Observer
{
    public function convertCityId($observer)
    {
        $address = $observer->getAddress();
        $cityId = $address->getCity();
        if(is_numeric($cityId)) {
            $city = Mage::getModel('shippingcity/city')->load($cityId);
            $address->setCity($city->getName());
        }
    }
}
