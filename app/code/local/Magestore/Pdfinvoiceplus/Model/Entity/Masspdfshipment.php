<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Masspdfshipment extends Magestore_Pdfinvoiceplus_Model_Entity_Shipmentgenerator
{   
    public function getPdfDataShipments($shipmentIds){
        if(isset($shipmentIds)){
            $pdf = $this->loadPdf();
            foreach ($shipmentIds as $shipmentId){
                $shipment = Mage::getSingleton('sales/order_shipment')->load($shipmentId);
                if($shipment->getId())
                    Mage::register('current_shipment',$shipment);
                $pdfData = Mage::getModel('pdfinvoiceplus/entity_shipmentpdf')->getThePdf((int) $shipmentId);
                $pagebreak = '<pagebreak>';
                if ($shipmentId === end($shipmentIds))
                {
                    $pagebreak = '';
                }
                $pdf->WriteHTML($pdfData->getData('htmltemplate') . $pagebreak);
                Mage::unregister('current_shipment');
            }
        }
         return $pdf->Output('', 'S');
        
    }

}

?>