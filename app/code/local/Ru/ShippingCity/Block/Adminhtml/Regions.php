<?php
class Ru_ShippingCity_Block_Adminhtml_Regions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = "adminhtml_regions";
        $this->_blockGroup = "shippingcity";
        $this->_headerText = Mage::helper("adminhtml")->__("Regions Manager");
        $this->_addButtonLabel = Mage::helper("adminhtml")->__("Add New Region");
        parent::__construct();
    }

}