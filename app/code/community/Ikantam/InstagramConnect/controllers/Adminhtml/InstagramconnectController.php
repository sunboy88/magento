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
        $dataHashtags = $this->getRequest()->getPost('hashtags');
        $numUpdate = (int)$this->getRequest()->getPost('numupdate');
        if($numUpdate > 20){
            $numUpdate = 20;
        }
        
        $hashtags = explode(',',$dataHashtags);
         //var_dump($hashtags);
        // die('111');
        $result = Mage::helper('instagramconnect/image')->update($hashtags,$numUpdate);
        //var_dump($result);die('123');
        $message = $this->__('An error occured. Make sure you are authenticated with Instagram.');
        if(!$result){
            Mage::getSingleton('adminhtml/session')->addError($message);
        }else{
            $collectionImages = Mage::getModel('instagramconnect/instagramimage')->getCollection()
                                ->addFilter('is_approved', 0)
                                ->addFilter('tag', $hashtags)
                                ->setOrder('image_id', 'DESC')
                                ->addFilter('is_visible', 1);
            //var_dump(count($collectionImages));die('123');
            $html = '';
            $max = count($collectionImages);
            if($max > 20){
                $html.= '<div style="text-align:center;font-size:20px;margin:20px;">Page: ';
                $numPage = $max/20 + 1;
                for($num=1;$num <= $numPage;$num++) {
                    if($num==1){
                        $html.='  <a style="color:red;margin-left:5px;" class="paging-'.$num.'"  id="paging-'.$num.'" href="javascript:void(0);" onclick="paging('.$num.');this.style.color=\'red\'">'.$num.'</a>  ';
                    }else{
                        $html.='  <a style="margin-left:5px;" class="paging-'.$num.'" id="paging-'.$num.'" href="javascript:void(0);" onclick="paging('.$num.');this.style.color=\'red\'">'.$num.'</a>  ';    
                    }
                    
                }
                //$html.= '  <a>Next</a> ';
                $html.= '</div>';
            }
            $count = 0;
            $checkDiv = 0;
            $numberPage = 0;
            foreach ($collectionImages as $image){
                $count++;
                if($max > 20){
                    if($count==1){
                        $html.= '<div class="item-images" id="item-image-'.$count.'">';
                    }
                    if($count > 20){
                        $num1 = floor($count/20);
                        $num2 = $count%20;
                        if($num2 == 1){
                            $checkDiv = 1;
                            $numberPage = $num1 + 1;
                            //var_dump($num1);
                            $html.= '<div style="display:none" class="item-images"  id="item-image-'.$numberPage.'">';
                        }
                            
                    }
                        $html.= '<div class="item" id="'.$image->getImageId() .'" style="width:150px;margin:10px; text-align:center; float:left;">';
                        $html.= '<p>'.Mage::helper('core')->escapeHtml($image->getTag()).'</p>';
                        $html.= '<img src="'. $image->getThumbnailUrl().'" />';
                        $html.= '<br>';
                        $html.= ' <a style="float:left;" onclick="return approveImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Approve</a>';
                        $html.= '<a style="float:right;" onclick="return deleteImage(\''. $image->getImageId().'\');" href="javascript:void(0);">Delete</a>';
                        $html.= '</div>';
                    if($checkDiv && ($count/20 == $numberPage) || $count == $max ){
                        Mage::log('test: '.$count/20);
                        $html.= '</div>';
                        $checkDiv = 0;
                    }
                    if($count==20){
                        $html.= '</div>';
                    }
                }else{
                    $html.= '<div class="item-image">';
                        $html.= '<div class="item" id="'.$image->getImageId() .'" style="width:150px;margin:10px; text-align:center; float:left;">';
                        $html.= '<p>'.Mage::helper('core')->escapeHtml($image->getTag()).'</p>';
                        $html.= '<img src="'. $image->getThumbnailUrl().'" />';
                        $html.= '<br>';
                        $html.= ' <a style="float:left;" onclick="return approveImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Approve</a>';
                        $html.= '<a style="float:right;" onclick="return deleteImage(\''. $image->getImageId().'\');" href="javascript:void(0);">Delete</a>';
                        $html.= '</div>';
                    $html.= '</div>';
                }
            }
            
            if($max > 20){
                $html.= '<div style="clear:both"></div><div style="text-align:center;font-size:20px;margin:20px;">Page: ';
                $numPage = $max/20 + 1;
                for($num=1;$num <= $numPage;$num++) {
                    if($num==1){
                        $html.='  <a style="color:red" class="paging-'.$num.'"  id="paging-'.$num.'" href="javascript:void(0);" onclick="paging('.$num.');this.style.color=\'red\'">'.$num.'</a>  ';
                    }else{
                        $html.='  <a class="paging-'.$num.'"  id="paging-'.$num.'" href="javascript:void(0);" onclick="paging('.$num.');this.style.color=\'red\'">'.$num.'</a>  ';    
                    }
                    
                }
                //$html.= '  <a>Next</a> ';
                $html.= '</div>';
            }
            $this->getResponse()->setBody(json_encode(array('success' => true, 'data' => $html))); 
        }
        
        //$this->renderLayout();
        //return $html;
        //$this->getResponse()->setBody($html);
    }
    public function updateApprovedAction(){
        $productId = $this->getRequest()->getPost('product_insta');
        $collectionApproved =  Mage::getModel('instagramconnect/instagramimage')->getCollection()
                        ->addFilter('is_approved', 1)
                        ->addFilter('is_visible', 1)
                        ->setOrder('image_id', 'DESC')
                        ->addFilter('product_instagram',$productId);
        foreach ($collectionApproved as $image){
            $html.= '<div class="item" id="'.$image->getImageId().'" style="width:150px;margin:10px; text-align:center; float:left;">';
            $html.= '<img src="'.$image->getThumbnailUrl().'" />';
            $html.= '<br>';
            $html.= '<a style="float:right;" onclick="return deleteApprovedImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Delete Image</a>';
            $html.= '</div>';
            
        }
        $this->getResponse()->setBody(json_encode(array('success' => true, 'data' => $html))); 
    }
    public function updateApprovedAjaxAction(){
        $productId = Mage::app()->getRequest()->getParam('product_insta');
        $collectionApproved =  Mage::getModel('instagramconnect/instagramimage')->getCollection()
                        ->addFilter('is_approved', 1)
                        ->addFilter('is_visible', 1)
                        ->addFilter('product_instagram',$productId);
        $html = '';
        $html.= '<div class="content-header">';
        $html.= '     <table cellspacing="0">';
        $html.= '        <tbody><tr>';
        $html.= '            <td style="width:50%;"><h3 class="icon-head head-sales-order">Approved Instagram Images</h3></td>';
        $html.= '<td class="a-right">
                    <a style="float:right;" href="javascript:void(0);" id="update-image-approved">Update Images</a>
                </td>';
        $html.= '        </tr>';
        $html.= '    </tbody></table>';
        $html.= '</div>';
        $html.= '<div id="content-images-approved">';
         foreach ($collectionApproved as $image){
            $html.= '<div class="item" id="'.$image->getImageId().'" style="width:150px;margin:10px; text-align:center; float:left;">';
            $html.= '<img src="'.$image->getThumbnailUrl().'" />';
            $html.= '<br>';
            $html.= '<a style="float:right;" onclick="return deleteApprovedImage(\''.$image->getImageId().'\');" href="javascript:void(0);">Delete Image</a>';
            $html.= '</div>';
            
         }
         $html.= '</div>';
         $html.='<script>';
         $html.='$("update-image-approved").observe("click", function(event) { 
                        new Ajax.Request("'.$this->getUrl("instagramconnect/adminhtml_instagramconnect/updateApproved") .'", {
                        parameters: {
                            isAjax: 1, 
                            method: "POST",
                            product_insta : '.$productId.'
                        },
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
                });
        </script>';
         $this->getResponse()->setBody($html);
    }

    public function clearAllAction(){
        $hashtags = $this->getRequest()->getPost('hashtags');
        $modelInstagram = Mage::getModel('instagramconnect/instagramimage');
        $collectionImages = $modelInstagram->getCollection()
                            ->addFilter('tag', $hashtags)
                            ->addFilter('is_approved', 0);

        //var_dump(count($collectionImages));die('111');
        foreach ($collectionImages as $images) {
            try { 
                    $modelInstagram->setId($images->getImageId())->delete();    
                } catch (Exception $e){
                    $this->getResponse()->setBody(json_encode(array('success' => false))); 
                    return false;
                }
        }
        $this->getResponse()->setBody(json_encode(array('success' => true))); 
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
        $productId = $this->getRequest()->getPost('product_id');
    	
    	$image = Mage::getModel('instagramconnect/instagramimage')->load($imageId);
    	
    	if ($image->getId()) {
    		$image->setIsApproved(1);
            $image->setProductInstagram($productId);
            $image->save();
    	}
    	
    	$this->getResponse()->setBody(json_encode(array('success' => true)));
    }
    
    public function deleteAction()
    {
        $imageId = $this->getRequest()->getPost('id');
        $image = Mage::getModel('instagramconnect/instagramimage')->load($imageId);
        
        if ($image->getId()) {
            $image->setIsVisible(0);
            $image->save();
        }
        
        $this->getResponse()->setBody(json_encode(array('success' => true)));
    }

    public function deleteApprovedAction()
    {
    	$imageId = $this->getRequest()->getPost('id');
    	$productId = $this->getRequest()->getPost('product_id');
    	$image = Mage::getModel('instagramconnect/instagramimage')->load($imageId);
    	
    	if ($image->getId()) {
    		$image->setIsApproved(0);
            $image->setProductInstagram(0);
            $image->save();
    	}
    	
    	$this->getResponse()->setBody(json_encode(array('success' => true)));
    }
}
