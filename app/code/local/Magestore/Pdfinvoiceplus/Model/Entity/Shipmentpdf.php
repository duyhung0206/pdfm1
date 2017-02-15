<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Shipmentpdf extends Magestore_Pdfinvoiceplus_Model_Entity_Shipmentgenerator
{
    public $shipmentId;
    
    public $templateId;

    public function getTheShipment()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load($this->shipmentId);
        return $shipment;
    }

    public function getThePdf($shipmentId, $templateId = NULL)
    {
        $this->templateId = $templateId;
        $this->shipmentId = $shipmentId;
        $this->setVars(Mage::helper('pdfinvoiceplus')->processAllVars($this->collectVars()));
        return $this->getPdf();
    }
    public function collectVars()
    {
           /* Change By Jack 25/12 */
         $vars = Mage::getModel('pdfinvoiceplus/entity_additional_info')
                ->setSource($this->getTheShipment())
                ->setOrder($this->getTheShipment()->getOrder())
                ->setShipment($this->getTheShipment())
                ->getTheInfoMergedVariables();   
         /* End Change */
         return $vars;
    }
}
