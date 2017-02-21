<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Masspdforder extends Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator
{
    public function getPdfDataOrder($orderIds)
    {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $storeId = Mage::app()->getStore()->getStoreId();
        $choosetemplate = Mage::getStoreConfig('pdfinvoiceplus/general/choosedesign',$storeId);
        if (isset($orderIds))
        {
            $pdf = $this->loadPdf();
            if($template->getData('template_id') == $choosetemplate){
                foreach ($orderIds as $orderId)
                {
                    $order = Mage::getModel('sales/order')->load($orderId);
                    if($order->getId())
                        Mage::register('current_order', $order);
                    $pdfData = Mage::getModel('pdfinvoiceplus/entity_orderpdf')->getThePdf((int) $orderId);
                    $pagebreak = '<pagebreak>';
                    if ($orderId === end($orderIds))
                    {
                        $pagebreak = '';
                    }
                    $templateBody = $pdfData->getData('htmltemplate');
                    $templateBody = str_replace("/pdfinvoiceplus/index/imagedesign",Mage::helper('pdfinvoiceplus')->getRotatedImage($orderId),$templateBody);

                    $pdf->WriteHTML($templateBody . $pagebreak);
                    Mage::unregister('current_order');
                }
            }else{
                foreach ($orderIds as $orderId)
                {
                    $order = Mage::getModel('sales/order')->load($orderId);
                    if($order->getId())
                        Mage::register('current_order', $order);
                    $pdfData = Mage::getModel('pdfinvoiceplus/entity_orderpdf')->getThePdf((int) $orderId);
                    $pagebreak = '<pagebreak>';
                    if ($orderId === end($orderIds))
                    {
                        $pagebreak = '';
                    }

                    $pdf->WriteHTML($pdfData->getData('htmltemplate') . $pagebreak);
                    Mage::unregister('current_order');
                }
            }

            
        }
        
        
        return $pdf->Output('', 'S');
    }
    
}
