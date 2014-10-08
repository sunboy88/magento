<?php

class Ru_ShippingCity_Block_Adminhtml_City_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "city_id";
        $this->_blockGroup = "shippingcity";
        $this->_controller = "adminhtml_city";
        $this->_updateButton("save", "label", Mage::helper("adminhtml")->__("Save City"));
        $this->_updateButton("delete", "label", Mage::helper("adminhtml")->__("Delete City"));
    }

    public function getHeaderText()
    {
        if (Mage::registry("city_data") && Mage::registry("city_data")->getId()) {
            return Mage::helper("adminhtml")->__("Edit City '%s'", $this->htmlEscape(Mage::registry("city_data")->getName()));
        } else {
            return Mage::helper("adminhtml")->__("Add City");
        }
    }
}