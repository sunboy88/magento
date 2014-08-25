<?php
 
class Ikantam_InstagramConnect_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    private $parent;
 
    protected function _prepareLayout()
    {
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        //add new tab


        $this->addTab('new', array(
                'label'     => Mage::helper('catalog')->__('New'),
                //'url'       => $this->getUrl('instagramconnect/adminhtml_instagramconnect/updateFilter', array('_current' => true)),
               // 'class'     => 'ajax',
                //'content'   => $this->getLayout()
             //->createBlock('instagramconnect/adminhtml_catalog_product_new')->toHtml(),
                'active' => true
            ));

        return $this->parent;
    }
}