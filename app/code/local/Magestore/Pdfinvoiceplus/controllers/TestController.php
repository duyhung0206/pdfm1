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
 * Pdfinvoiceplus Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_TestController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    public function indexAction() {
        $product = Mage::getModel('catalog/product')->load(336);
        Zend_debug::dump($product->getData());
        die();
    }

    public function allVarsAction() {
        $helper = Mage::helper('pdfinvoiceplus/variable');
        $variables = array(
            'order' => array(
                'customer' => $helper->getCustomerVars(),
                'order' => $helper->getOrderVars(),
                'order_item' => $helper->getOrderItemVars()
            ),
            'invoice' => array(
                'customer' => $helper->getCustomerVars(),
                'invoice' => $helper->getInvoiceVars(),
                'invoice_item' => $helper->getInvoiceItemVars()
            ),
            'creditmemo' => array(
                'customer' => $helper->getCustomerVars(),
                'creditmemo' => $helper->getCreditmemoVars(),
                'creditmemo_item' => $helper->getCreditmemoItemVars()
            ),
			'shipment' => array(
                'customer' => $helper->getCustomerVars(),
                'shipment' => $helper->getShipmentVars(),
                'shipment_item' => $helper->getShipmentItemVars()
            )
        );

        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }

    public function imageAction() {
        
    }

    public function htmlDomAction() {
        $html = Mage::getModel('pdfinvoiceplus/template')->load(1);
        $dom = new SimpleHtmlDoom_SimpleHtmlDoomLoad;
        $dom->load($html->getInvoiceHtml());
        //$col = $dom->find(".col-total-label");

        $col = $dom->find(".col-total-label");
        $col = $col[0];
        //$col->appendChild($childs[3]);
        //$child[0]->appendChild($child[0]->childNodes[3]);
        //echo print_r($col->childNodes(3)->outertext);
        //echo "============";
        //echo $col->__toString();
        $child = $col->children();
        //$col->appendChild($col->childNodes(3));

        echo $child[0]->__toString();
    }

}
