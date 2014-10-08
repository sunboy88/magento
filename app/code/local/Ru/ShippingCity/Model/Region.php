<?php
class Ru_ShippingCity_Model_Region extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('shippingcity/region');
    }

    public function saveRegion()
    {
        if(!$this->getId()) {
            $this->setCountryId(Mage::helper('shippingcity')->getCountryCode());
            $this->save();
        } else {
            $name = $this->getDefaultName();
            $this->load($this->getId());
            $this->setDefaultName($name);
        }
        $this->setDataChanges(true);

        if(!$this->getCode()) {
            $this->setCode(Mage::helper('shippingcity')->getCountryCode() . '-' . $this->getId());
        }
        $this->save();

        $resource = Mage::getSingleton('core/resource');
        $resource->getConnection('core_write')->query(
            "INSERT INTO {$resource->getTableName('directory/country_region_name')} (locale, region_id, name) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE name = ?",
            array(Mage::helper('shippingcity')->getLocale(), $this->getId(), $this->getDefaultName(), $this->getDefaultName()));
    }
}