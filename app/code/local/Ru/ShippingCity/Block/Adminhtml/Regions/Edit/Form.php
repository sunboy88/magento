<?php

class Ru_ShippingCity_Block_Adminhtml_Regions_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset("region_form", array("legend"=>Mage::helper("adminhtml")->__("Region information")));


        $fieldset->addField('default_name', 'text', array(
            'label'     => Mage::helper('adminhtml')->__('Region Name'),
            'name' => 'default_name',
            "class" => "required-entry",
            "required" => true,
        ));
        $form->setValues(Mage::registry("regions_data")->getData());

        $id = (Mage::registry("regions_data") ? Mage::registry("regions_data")->getId() : '');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save', array('id' => $id)));
        $form->setMethod('POST');
        $this->setForm($form);
    }
}
