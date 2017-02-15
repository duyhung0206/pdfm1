<?php

class Magestore_Pdfinvoiceplus_Block_Sales_Order_Shipment_Items extends Mage_Sales_Block_Order_Shipment_Items {


    public function getCustomPrintUrl($shipment)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('pdfinvoiceplus/shipment/print', array('shipment_id' => $shipment->getId()));
        }
        return $this->getUrl('pdfinvoiceplus/shipment/print', array('shipment_id' => $shipment->getId()));
    }
}

?>
