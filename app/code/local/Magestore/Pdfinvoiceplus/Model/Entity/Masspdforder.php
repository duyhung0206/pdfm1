<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Masspdforder extends Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator
{
//    private $_invoiceId = null;
//    public function setInvoiceId($id){
//        $this->_invoiceId = $id;
//    }
    public function getPdfDataOrder($orderIds)
    {
        if (isset($orderIds))
        {
            $pdf = $this->loadPdf();

            foreach ($orderIds as $orderId)
            {
                $order = Mage::getModel('sales/order')->load($orderId);
                if($order->getId())
                    Mage::register('current_order', $order);
//                $pdfData = Mage::getBlockSingleton('pdfinvoiceplus/adminhtml_pdf')->getOrderPdf();
                $pdfData = Mage::getModel('pdfinvoiceplus/entity_orderpdf')->getThePdf((int) $orderId);
                $pagebreak = '<pagebreak>';
                if ($orderId === end($orderIds))
                {
                    $pagebreak = '';
                }

                $templateBody = $pdfData->getData('htmltemplate');
                $templateBody = str_replace("http://yorkshiresoap.labelmediadev.co.uk/index.php/pdfinvoiceplus/index/imagedesign",Mage::helper('pdfinvoiceplus')->getRotatedImage($orderId),$templateBody);

                $pdf->WriteHTML($templateBody . $pagebreak);
                Mage::unregister('current_order');
            }

        }


        return $pdf->Output('', 'S');
    }

}
