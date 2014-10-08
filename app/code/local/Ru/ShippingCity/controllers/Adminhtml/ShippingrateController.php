<?php
class Ru_ShippingCity_Adminhtml_ShippingrateController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu("system/shippingcity")
            ->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping Rates Manager"), Mage::helper("adminhtml")->__("Shipping Rates Manager"));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__("Manage Shipping Rates"));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__("Shipping Rates"));
        $this->_title($this->__("Edit Shipping Rates"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("shippingcity/tablerate")->load($id);
        if ($model->getId()) {
            Mage::register("rate_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("system/shippingcity");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping Rates Manager"), Mage::helper("adminhtml")->__("Shipping Rates Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping Rates Description"), Mage::helper("adminhtml")->__("Shipping Rates Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("shippingcity/adminhtml_tablerate_edit"));
            $this->renderLayout();
        }
        else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("adminhtml")->__("Shipping Rates does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {
        $this->_title($this->__("Shipping Rates"));
        $this->_title($this->__("New Shipping Rates"));

        $id   = $this->getRequest()->getParam("id");
        $model  = Mage::getModel("shippingcity/tablerate")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("rate_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("system/shippingcity");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping Rates Manager"), Mage::helper("adminhtml")->__("Shipping Rates Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Shipping Rates Description"), Mage::helper("adminhtml")->__("Shipping Rates Description"));


        $this->_addContent($this->getLayout()->createBlock("shippingcity/adminhtml_tablerate_edit"));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        $id = $this->getRequest()->getParam("id");
        if ($postData) {
            try {
                Mage::getModel('shippingcity/tablerate')->load($id)
                    ->addData($postData)
                    ->save();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Shipping Rates was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setShippingRateData(false);
                $this->_redirect("*/*/");
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setShippingRateData($postData);
                if($id) {
                    $this->_redirect("*/*/edit", array("id" => $id));
                } else {
                    $this->_redirect("*/*/new");
                }
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction()
    {
        if( $this->getRequest()->getParam("id") > 0 ) {
            try {
                $model = Mage::getModel("shippingcity/tablerate");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Shipping Rates was successfully deleted"));
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
            $ids = $this->getRequest()->getPost('rate_ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("shippingcity/tablerate");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Shipping Rate(s) was successfully removed"));
        }
        catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}