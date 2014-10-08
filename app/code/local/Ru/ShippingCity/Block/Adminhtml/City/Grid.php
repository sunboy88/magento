<?php

class Ru_ShippingCity_Block_Adminhtml_City_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("cityGrid");
        $this->setDefaultSort("city_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('shippingcity/city')->getCollection();
        $collection->getSelect()->join(array('region' => Mage::getSingleton('core/resource')->getTableName('directory/country_region')),
        'region.region_id = main_table.region_id');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('city_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'index' => 'city_id',
            'type' => 'text',
        ));
        $this->addColumn('region_name', array(
            'header' => Mage::helper('adminhtml')->__('Region Name'),
            'index' => 'region_id',
            'filter_index' => 'region.region_id',
            'type' => 'options',
            'options' => Mage::helper('shippingcity')->getRegionOptions(false)
        ));
        $this->addColumn('city_name', array(
            'header' => Mage::helper('adminhtml')->__('City Name'),
            'index' => 'name',
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
        $this->setMassactionIdField('city_id');
        $this->getMassactionBlock()->setFormFieldName('city_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_cities', array(
            'label' => Mage::helper('adminhtml')->__('Remove Cities'),
            'url' => $this->getUrl('*/city/massRemove'),
            'confirm' => Mage::helper('adminhtml')->__('Are you sure?')
        ));
        return $this;
    }
}