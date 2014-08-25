<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     Ikantam_InstagramConnect
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.ikantam.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Ikantam_InstagramConnect_Adminhtml_InstagramconnectController extends Mage_Adminhtml_Controller_Action
{
    const UPDATE_TYPE_USER  = 1;
    const UPDATE_TYPE_TAG   = 0;


	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Title"));
	   $this->renderLayout();
    }
    
    public function updateAction()
    {

        $updateType = Mage::getStoreConfig('ikantam_instagramconnect/module_options/updatetype');

        switch($updateType){

            case self::UPDATE_TYPE_TAG :
                $result = Mage::helper('instagramconnect/image')->update();
                $message = $this->__('An error occured. Make sure you are authenticated with Instagram.');
                if(!$result){
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                break;

            case self::UPDATE_TYPE_USER :
                if( !Mage::getModel('instagramconnect/instagramauth')->isValid() ){
                    $message = $this->__('Need Instagram user authentification');
                    Mage::getSingleton('adminhtml/session')->addError($message);
                    break;
                }

                $result = Mage::helper('instagramconnect/image_user')->update();
                $message = $this->__('An error occured. Make sure you are authenticated with Instagram.');
                if(!$result){
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }

                break;

        }

        $this->_redirect('instagramconnect/adminhtml_instagramconnect/new');
    }

    public function updateFilterAction()
    {
        //$this->loadLayout();
        $result = Mage::helper('instagramconnect/image')->update();
        $message = $this->__('An error occured. Make sure you are authenticated with Instagram.');
        if(!$result){
            Mage::getSingleton('adminhtml/session')->addError($message);
        }else{
            $collectionImages = Mage::getModel('instagramconnect/instagramimage')->getCollection()
                                ->addFilter('is_approved', 0)
                                ->setOrder('image_id', 'DESC')
                                ->addFilter('is_visible', 1);
            $html = '';
            foreach ($collectionImages as $image){
                $html.= '<div class="item" id="'.$image->getImageId() .'" style="width:150px;margin:10px; text-align:center; float:left;">';
                $html.= '<p>'.Mage::helper('core')->escapeHtml($image->getTag()).'</p>';
                $html.= '<img src="'. $image->getThumbnailUrl().'" />';
                $html.= '<br>';
                $html.= ' <a style="float:left;" onclick="return approveImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Approve</a>';
                $html.= '<a style="float:right;" onclick="return deleteImage(\''. $image->getImageId().'\');" href="javascript:void(0);">Delete</a>';
                $html.= '</div>';
            }
        
            $this->getResponse()->setBody(json_encode(array('success' => true, 'data' => $html))); 
        }
        
        //$this->renderLayout();
        //return $html;
        //$this->getResponse()->setBody($html);
    }
    public function updateAppovedAction(){
        $collectionApproved =  Mage::getModel('instagramconnect/instagramimage')->getCollection()
                        ->addFilter('is_approved', 1)->addFilter('is_visible', 1);
        foreach ($collectionApproved as $image){
            $html.= '<div class="item" id="'.$image->getImageId().'" style="width:150px;margin:10px; text-align:center; float:left;">';
            $html.= '<img src="'.$image->getThumbnailUrl().'" />';
            $html.= '<br>';
            $html.= '<a style="float:right;" onclick="return deleteImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Delete Image</a>';
            $html.= '</div>';
            
        }
        $this->getResponse()->setBody(json_encode(array('success' => true, 'data' => $html))); 
    }
    public function updateApprovedAjaxAction(){
        $collectionApproved =  Mage::getModel('instagramconnect/instagramimage')->getCollection()
                        ->addFilter('is_approved', 1)->addFilter('is_visible', 1);
        $html = '';
        $html.= '<div class="content-header">';
        $html.= '     <table cellspacing="0">';
        $html.= '        <tbody><tr>';
        $html.= '            <td style="width:50%;"><h3 class="icon-head head-sales-order">Approved Instagram Images</h3></td>';
        $html.= '<td class="a-right">
                    <a style="float:right;" onclick="return updateImageAprroved();" href="javascript:void(0);">Update Images</a>
                </td>';
        $html.= '        </tr>';
        $html.= '    </tbody></table>';
        $html.= '</div>';
        $html.= '<div id="content-images-approved">';
         foreach ($collectionApproved as $image){
            $html.= '<div class="item" id="'.$image->getImageId().'" style="width:150px;margin:10px; text-align:center; float:left;">';
            $html.= '<img src="'.$image->getThumbnailUrl().'" />';
            $html.= '<br>';
            $html.= '<a style="float:right;" onclick="return deleteImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Delete Image</a>';
            $html.= '</div>';
            
         }
         $html.= '</div>';
         $html.='<script>';
         $html.='function updateImageAprroved() {
            new Ajax.Request("'.$this->getUrl("instagramconnect/adminhtml_instagramconnect/updateAppoved") .'", {
                        parameters: {isAjax: 1, method: "POST"},
                        onSuccess: function(transport) {

                            try{
                                response = eval("(" + transport.responseText + ")");
                                
                            } catch (e) {
                                response = {};
                            }
                            if (response.success) {
                                $("content-images-approved").replace("<div id=\"content-images-approved\"></div>");
                                $("content-images-approved").insert(response.data);
                            } else {
                                var msg = response.error_messages;
                                if (typeof(msg)=="object") {
                                    msg = msg.join("\n");
                                }
                                if (msg) {
                                    $("review-please-wait").hide();
                                    alert(msg);
                                    return;
                                }
                            }
                            $("review-please-wait").hide();
                            alert("Unknown Error. Please try again later.");
                            return;
                        },
                        onFailure: function(){
                            alert("Server Error. Please try again.");
                            $("review-please-wait").hide();
                        }
                    });
                    return false;
                }
        </script>';
         $this->getResponse()->setBody($html);
    }


    
    public function clearAllAction(){
        $collectionImages = Mage::getModel('instagramconnect/instagramimage')->getCollection()->addFilter('is_approved', 0);
        $modelInstagram = Mage::getModel('instagramconnect/instagramimage');
        foreach ($collectionImages as $images) {
            try {
                        $modelInstagram->setId($images->getImageId())->delete();
                        echo "Data deleted successfully.";
                        return true;
                        
                    } catch (Exception $e){
                        echo $e->getMessage();
                        return false;
                }
        }

    }
    public function newAction()
    {
    	$this->loadLayout();
	   	$this->_title($this->__("New Images"));
	   	$this->renderLayout();
    }
    
    public function approvedAction()
    {
    	$this->loadLayout();
	   	$this->_title($this->__("Approved Images"));
	   	$this->renderLayout();
    }
    
    public function approveAction()
    {
    	$imageId = $this->getRequest()->getPost('id');
    	
    	$image = Mage::getModel('instagramconnect/instagramimage')->load($imageId);
    	
    	if ($image->getId()) {
    		$image->setIsApproved(1)->save();
    	}
    	
    	$this->getResponse()->setBody(json_encode(array('success' => true)));
    }
    
    public function deleteAction()
    {
    	$imageId = $this->getRequest()->getPost('id');
    	
    	$image = Mage::getModel('instagramconnect/instagramimage')->load($imageId);
    	
    	if ($image->getId()) {
    		$image->setIsVisible(0)->save();
    	}
    	
    	$this->getResponse()->setBody(json_encode(array('success' => true)));
    }
}
