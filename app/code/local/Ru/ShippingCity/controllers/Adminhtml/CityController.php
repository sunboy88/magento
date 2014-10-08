<?php

class Ru_ShippingCity_Adminhtml_CityController extends Mage_Adminhtml_Controller_Action {

    public function listAction() {
        $post = $this->getRequest();
        $regionId = $post->getParam('regionId');
        $values = Mage::getModel('shippingcity/city')->toOptionArray($regionId);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($values));
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu("system/shippingcity")
            ->_addBreadcrumb(Mage::helper("adminhtml")->__("Cities Manager"), Mage::helper("adminhtml")->__("Cities Manager"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("Manage Cities"));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__("Cities"));
        $this->_title($this->__("Edit City"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("shippingcity/city")->load($id);
        if ($model->getId()) {
            Mage::register("city_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("system/shippingcity");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cities Manager"), Mage::helper("adminhtml")->__("Cities Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cities Description"), Mage::helper("adminhtml")->__("Cities Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("shippingcity/adminhtml_city_edit"));
            $this->renderLayout();
        }
        else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("modulename")->__("City does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {
        $this->_title($this->__("Cities"));
        $this->_title($this->__("New City"));

        $id   = $this->getRequest()->getParam("id");
        $model  = Mage::getModel("shippingcity/city")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("city_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("system/shippingcity");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cities Manager"), Mage::helper("adminhtml")->__("Cities Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Cities Description"), Mage::helper("adminhtml")->__("Cities Description"));


        $this->_addContent($this->getLayout()->createBlock("shippingcity/adminhtml_city_edit"));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();
        if ($post_data) {
            try {
                Mage::getModel('shippingcity/city')
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("City was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setCitiesData(false);
                $this->_redirect("*/*/");
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setCitiesData($this->getRequest()->getPost());
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
                $model = Mage::getModel("shippingcity/city");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("City was successfully deleted"));
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
            $ids = $this->getRequest()->getPost('city_ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("shippingcity/city");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("City(s) was successfully removed"));
        }
        catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
