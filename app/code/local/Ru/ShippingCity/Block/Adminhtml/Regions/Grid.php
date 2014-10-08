<?php

class Ru_ShippingCity_Block_Adminhtml_Regions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("regionsGrid");
        $this->setDefaultSort("region_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('shippingcity/region')->getCollection()->addFieldToFilter('country_id', Mage::helper('shippingcity')->getCountryCode());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('region_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'index' => 'region_id',
            'type' => 'text',
        ));
        $this->addColumn('default_name', array(
            'header' => Mage::helper('adminhtml')->__('Region Name'),
            'index' => 'default_name',
            'type' => 'text',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('region_id');
        $this->getMassactionBlock()->setFormFieldName('region_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_regions', array(
            'label' => Mage::helper('adminhtml')->__('Remove Regions'),
            'url' => $this->getUrl('*/region/massRemove'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
        ));
        return $this;
    }
}