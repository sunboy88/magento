<?php
class Ru_ShippingCity_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_regionOptions = null;

    public function getCountryCode()
    {
        return 'VN';
    }

    public function getLocale()
    {
        return 'vn_VN';
    }

    public function getWebsiteId()
    {
        //return Mage::getModel('core/website')->load('ndg_ru')->getId();
    }

    public function getRegionOptions($insertBlankOption = true)
    {
        if(is_null($this->_regionOptions)) {
            if($insertBlankOption) {
                $this->_regionOptions[''] = $this->__('Please select a region');
            }
            $collection = Mage::getModel('shippingcity/region')->getCollection()->addFieldToFilter('country_id', $this->getCountryCode());
            foreach($collection as $region) {
                $this->_regionOptions[$region->getRegionId()] = $region->getDefaultName();
            }
        }

        return $this->_regionOptions;
    }
}