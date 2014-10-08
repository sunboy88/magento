<?php

class Ru_Shippingcity_Model_Resource_City extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('shippingcity/city', 'city_id');
    }

    public function loadByRegionId($regionId)
    {
        $adapter = $this->_getReadAdapter();
        if (!is_null($regionId)) {
            $select = $adapter->select()
                    ->from(array('city' => $this->getMainTable()))
                    ->joinLeft(
                            array('region' => $this->getTable('directory/country_region')), 'city.region_id = region.region_id', array('city.name'))
                    ->where('region.region_id = ?', $regionId)
                    ->order('city.name ASC');
        } else {
            $select = $adapter->select()
                    ->from(array('city' => $this->getMainTable()))
                    ->joinLeft(
                    array('region' => $this->getTable('directory/country_region')), 'city.region_id = region.region_id', array('city.name'))
                    ->order('city.name ASC');
        }
        $data = $adapter->fetchAll($select);
        return $data;
    }

}
