<?php

class Ru_ShippingCity_CityController extends Mage_Core_Controller_Front_Action
{

    public function listAction()
    {
        $post = $this->getRequest();
        $regionId = $post->getParam('regionId');
        $optionsArr = Mage::getModel('shippingcity/city')->getCities($regionId);
        $values = array();
        foreach ($optionsArr as $option) {
            $values[] = array(
                'value' => $option['city_id'],
                'label' => $option['name']
            );
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($values));
    }

}
