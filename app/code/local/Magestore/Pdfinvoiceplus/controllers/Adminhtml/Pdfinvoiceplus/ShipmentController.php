<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Pdfinvoiceplus Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Adminhtml_Pdfinvoiceplus_ShipmentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Pdfinvoiceplus_Adminhtml_PdfinvoiceplusController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('pdfinvoiceplus/pdfinvoiceplus')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('pdfinvoiceplus');
    }
    public function printMassShipmentAction(){
        Mage::getSingleton('core/session')->setData('type','shipment'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('order_ids');
        //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
              //
        $shipmentId = array();
        foreach($ids as $id){
            $order = Mage::getModel('sales/order')->load($id);
            if($order->hasShipments()){
                foreach ($order->getShipmentsCollection() as $shipmentCollection){
                    $shipmentId[] = $shipmentCollection->getData('entity_id');
                }
            }
        }
        if(empty($shipmentId)){
            $this->_redirect('adminhtml/sales_order');
            $error = Mage::helper('sales')->__('You have no files to get');
            Mage::getSingleton('core/session')->addError($error);
            return;
        }
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status',array('eq'=>1));
        if(!$template->getSize()){
            $this->_redirect('adminhtml/sales_order');
            $message = Mage::helper('sales')->__('Template not found');
            Mage::getSingleton('core/session')->addError($message);
        }else{
            $pdfData= Mage::getSingleton('pdfinvoiceplus/entity_masspdfshipment')->getPdfDataShipments($shipmentId);
            $this->_prepareDownloadResponse('Shipment'.$date_curent.'.pdf',$pdfData,'application/pdf');
        }
        //end by zeus 08/01  
    }
    public function printMassShipmentGridAction(){
        Mage::getSingleton('core/session')->setData('type','shipment'); // Change By Jack 27/12
        $ids = $this->getRequest()->getPost('shipment_ids');
        
          //edit by zeus 08/01
		$date_curent =  Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
		//zend_debug::dump($date_curent); die('vao day');
		// End edit Adminhtml/controller/sale/invoice
        
        $template = Mage::getModel('pdfinvoiceplus/template')->getCollection()->addFieldToFilter('status',array('eq'=>1));
        if(!$template->getSize()){
            $this->_redirect('adminhtml/sales_shipment');
            $message = Mage::helper('sales')->__('Template not found');
            Mage::getSingleton('core/session')->addError($message);
        }else{
            $pdfData = Mage::getSingleton('pdfinvoiceplus/entity_masspdfshipment')->getPdfDataShipments($ids);
            $this->_prepareDownloadResponse('Shipment'.$date_curent.'.pdf',$pdfData,'application/pdf');
        }
        // End edit Adminhtml/controller/sale/invoice
    }
    
    public function printAction(){
        Mage::getSingleton('core/session')->setData('type','shipment'); // Change By Jack 27/12
         if (!$shipmentId = $this->getRequest()->getParam('shipment_id'))
        {
            return false;
        }
        try {
            $pdfFile = Mage::getSingleton('pdfinvoiceplus/entity_shipmentpdf')->getThePdf((int) $shipmentId, false);
            $this->_prepareDownloadResponse($pdfFile->getData('filename') .
                    '.pdf', $pdfFile->getData('pdfbody'), 'application/pdf');
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }       
    }
    
}