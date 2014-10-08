<?php

class Ru_ShippingCity_Block_Adminhtml_Regions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "region_id";
        $this->_blockGroup = "shippingcity";
        $this->_controller = "adminhtml_regions";
        $this->_updateButton("save", "label", Mage::helper("adminhtml")->__("Save Region"));
        $this->_updateButton("delete", "label", Mage::helper("adminhtml")->__("Delete Region"));
    }

    public function getHeaderText()
    {
        if (Mage::registry("regions_data") && Mage::registry("regions_data")->getId()) {
            return Mage::helper("adminhtml")->__("Edit Region '%s'", $this->htmlEscape(Mage::registry("regions_data")->getDefaultName()));
        } else {
            return Mage::helper("adminhtml")->__("Add Region");
        }
    }
}