<?php
class Ru_ShippingCity_Adminhtml_RegionController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu("system/shippingcity")
            ->_addBreadcrumb(Mage::helper("adminhtml")->__("Regions Manager"), Mage::helper("adminhtml")->__("Regions Manager"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("Manage Regions"));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__("Regions"));
        $this->_title($this->__("Edit Region"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("shippingcity/region")->load($id);
        if ($model->getId()) {
            Mage::register("regions_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("system/shippingcity");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Regions Manager"), Mage::helper("adminhtml")->__("Regions Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Regions Description"), Mage::helper("adminhtml")->__("Regions Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("shippingcity/adminhtml_regions_edit"));
            $this->renderLayout();
        }
        else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("modulename")->__("Region does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {
        $this->_title($this->__("Regions"));
        $this->_title($this->__("New Region"));

        $id   = $this->getRequest()->getParam("id");
        $model  = Mage::getModel("shippingcity/region")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("regions_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("system/shippingcity");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Regions Manager"), Mage::helper("adminhtml")->__("Regions Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Regions Description"), Mage::helper("adminhtml")->__("Regions Description"));


        $this->_addContent($this->getLayout()->createBlock("shippingcity/adminhtml_regions_edit"));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();
        if ($post_data) {
            try {
                Mage::getModel('shippingcity/region')
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->saveRegion();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Region was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setRegionsData(false);
                $this->_redirect("*/*/");
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setRegionsData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam("id") > 0 ) {
            try {
                $model = Mage::getModel("shippingcity/region");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Region was successfully deleted"));
                $this->_redirect("*/*/");
            }
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }

    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('region_ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("shippingcity/region");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Region(s) was successfully removed"));
        }
        catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}