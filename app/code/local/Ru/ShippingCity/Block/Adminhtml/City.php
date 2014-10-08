<?php
class Ru_ShippingCity_Block_Adminhtml_City extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = "adminhtml_city";
        $this->_blockGroup = "shippingcity";
        $this->_headerText = Mage::helper("adminhtml")->__("Cities Manager");
        $this->_addButtonLabel = Mage::helper("adminhtml")->__("Add New City");
        parent::__construct();
    }

}