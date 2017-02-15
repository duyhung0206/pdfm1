<?php

class Magestore_Pdfinvoiceplus_Model_Default_Entity_Shipment extends Magestore_Pdfinvoiceplus_Model_Entity_Abstract
{
    private $_shipmentId = null;
    public function _construct()
    {
        parent::_construct();
        $this->_init('pdfinvoiceplus/default_entity_shipment');
    }
    public function setShipmentId($id){
        $this->_shipmentId = $id;
    }
    
    public function getInstanceSource(){
        $shipment = $this->_shipmentId;
        return Mage::getModel('sales/order_shipment')->load($shipment);
    }
   
    
    public function getFooter(){
        return 'footer';
    }
    public function getCss(){
        return 'css';
    }
    
    public function getPdf($html) {
        return parent::getPdf($html);
    }
}

?>