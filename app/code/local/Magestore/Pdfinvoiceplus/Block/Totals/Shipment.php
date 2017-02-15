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
 * Pdfinvoiceplus Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Pdfinvoiceplus
 * @author      Magestore Developer
 */
class Magestore_Pdfinvoiceplus_Block_Totals_Shipment extends Mage_Sales_Block_Order_Shipment_Totals
{
    protected function _prepareLayout() {
        parent::__construct();
        $taxBlock = $this->getLayout()->createBlock('pdfinvoiceplus/totals_tax')
                ->setTemplate('pdfinvoiceplus/tax/order/tax.phtml');
        $this->setChild('tax',$taxBlock);
    }
    public function getSource(){
        return $this ->getShipment();
    }
    public function getShipment()
    {
        if ($this->_shipment === null) {
            if (Mage::registry('current_shipment')) {
                $this->_shipment = Mage::registry('current_shipment');
            } else{
                $this->_shipment = $this->getInstance();
                Mage::register('current_shipment', $this->_shipment);
            }
        }
        return $this->_shipment;
    }
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }
}