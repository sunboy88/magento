<?php

class Ru_ShippingCity_Block_Adminhtml_City_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset("city_form", array("legend"=>Mage::helper("adminhtml")->__("City information")));

        $fieldset->addField('region_id', 'select', array(
            'label'     => Mage::helper('adminhtml')->__('Region Name'),
            'name' => 'region_id',
            "class" => "required-entry",
            'options' => Mage::helper('shippingcity')->getRegionOptions(),
            "required" => true,
        ));
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('City Name'),
            'name' => 'name',
            "class" => "required-entry",
            "required" => true,
        ));
        $form->setValues(Mage::registry("city_data")->getData());

        $id = (Mage::registry("city_data") ? Mage::registry("city_data")->getId() : '');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save', array('id' => $id)));
        $form->setMethod('POST');
        $this->setForm($form);
    }
}
