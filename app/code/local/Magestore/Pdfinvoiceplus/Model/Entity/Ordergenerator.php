<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Ordergenerator extends Magestore_Pdfinvoiceplus_Model_Entity_Abstractextend {

    const THE_START = '##productlist_start##';
    const THE_END = '##productlist_end##';
    public $pdfCollection;

    /**
     * The pdf proceesed template
     * @var string
     */
    protected $_pdfProcessedTemplate;

    /**
     * Load the pdf system
     * @return Mpdf Object - lib
     */
    protected function _construct()
    {
        $this->setPdfCollection();
        parent::_construct();
    }

    public function setTheSourceId($orderId)
    {
        $this->_sourceId = $orderId;
        return $this->_sourceId;
    }

    public function getTheSourceId()
    {
        return $this->_sourceId;
    }
     public function setPdfCollection()
    {
        $this->pdfCollection = Mage::getModel('pdfinvoiceplus/template')->getCollection();
        return $this;
    }

    public function getPdfCollection()
    {
        return $this->pdfCollection;
    }

    public function getTheStoreId()
    {
        if ($storeId = $this->getTheOrder()->getStore()->getId())
        {
            return array(0, $storeId);
        }
        return array(0);
    }

    public function getFilteredCollection()
    {
        try {
            $pdfGeneratorTemplate = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
//            if ($this->templateId)
//            {
//                $pdfGeneratorTemplate = $this->getPdfCollection()
//                        ->addFieldToSelect('*')
//                        ->addFieldToFilter('template_id',$this->templateId);
//            }
//            else
//            {
//                $pdfGeneratorTemplate = $this->getPdfCollection()
//                        ->addFieldToSelect('*')
//                        ->addFieldToFilter('status', 1);
////                        ->addFieldToFilter('template_store_id', $this->getTheStoreId());
//            }
            
        } catch (Exception $e) {
            Mage::log($e->getMessage());
            return null;
        }
        return $pdfGeneratorTemplate;
    }

    public function createTemplate()
    {
        $pdfCollection = $this->getFilteredCollection();
        if ($pdfCollection)
        {
            return $pdfCollection;
        }
//        foreach ($pdfCollection as $pdf)
//        {
//            $dataTemplate = $pdf;
//        }

//        if ($dataTemplate)
//        {
//            return $dataTemplate;
//        }
        return false;
    }

    public function getFileName() {
        if ($fileName = $this->createTemplate()->getData('order_filename'))
        {
            $templateVars = $this->getVars();
            $headerTemplate = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($fileName);
            $processedTemplate = $headerTemplate->getProcessedTemplate($templateVars);

            $cleanString = Mage::helper('core/string')->cleanString($processedTemplate);
            $cleanString = str_replace(array(' ', '.', ':'), '-', $processedTemplate);
            return $cleanString;
        }
        return 'order - ';
    }
     public function getBody()
    {

        if ($body = $this->createTemplate()->getData('order_html'))
        {
            return $body;
            
        }
        return false;
    }

    /**
     * Get the template body from used in the backend with the varables and add the item variables.
     * @return string
     */
    public function getTheTemplateBodyWithItems()
    {
        $templateToProcessForItems = $this->getBody();
          /* Change by Zeus 08/12 */
        $finalItems = NULL;
        /* End change */
        $items = Mage::getModel('pdfinvoiceplus/entity_itemsorder')
                        ->setSource($this->getTheOrder())->setOrder($this->getTheOrder());
        $itemsData = $items->processAllVars();
//        Zend_Debug::dump($itemsData);
//        die();
        $result = Mage::helper('pdfinvoiceplus/items')
                ->getTheItemsFromBetwin($templateToProcessForItems,self::THE_START, self::THE_END);
        $i = 1;
        $j = 0;
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $storeId = Mage::app()->getStore()->getStoreId();
        $choosetemplate = Mage::getStoreConfig('pdfinvoiceplus/general/choosedesign',$storeId);
        if($template->getData('template_id') == $choosetemplate){
            $templateExplode = explode('<div class="split"></div>',$templateToProcessForItems);
            /*get content table item order*/
            $templateTable = $templateExplode[1];
            $templateTableFinal = '';
            $maxSizeRow = 6.5;
            $arrayItems = Mage::getSingleton('core/session')->getArrayItems();
            $arrayHight = Mage::getSingleton('core/session')->getHightRow();
//            echo htmlentities($templateTable).'<br/>';
//            echo htmlentities($result);
//            Zend_Debug::dump($arrayItems);
            Zend_Debug::dump($arrayHight);
//            die();
            $bundleProduct = array();
            $productSubBundle = array();
            $currenySizeRow = floatval(0);
            foreach ($itemsData as $key => $templateVars)
            {
                if($templateVars['items_product_type'] == 'bundle'){
                    $bundleProduct[] =  $templateVars['items_item_id'];
                }
                if(in_array($templateVars['parent_item_id'], $bundleProduct)){
                    $productSubBundle[$templateVars['parent_item_id']][] = $templateVars['items_item_id'];
                }
            }
            $rowSizeBundleProduct = array();
            foreach ($productSubBundle as $key => $rowBundleProduct){
                $totalSize = 0;
                foreach ($rowBundleProduct as $temp){
                    $totalSize += $arrayHight[$temp];
                }
                $rowSizeBundleProduct[$key] = $totalSize;
            }
//            Zend_Debug::dump($productSubBundle);
//            Zend_Debug::dump($itemsData);
//            die();
            foreach ($itemsData as $key => $templateVars)
            {
                if($templateVars['items_product_type'] == 'bundle'){
                    $currenySizeRow += $rowSizeBundleProduct[$templateVars['items_item_id']];
//                    echo $templateVars['items_item_id']. 'bundle:'.$rowSizeBundleProduct[$templateVars['items_item_id']].'<br/>';
                }
//                Zend_Debug::dump($templateVars['items_item_id']);
//                Zend_Debug::dump($arrayHight[$templateVars['items_item_id']]);
//                Zend_Debug::dump($currenySizeRow);
//                die();
                if($currenySizeRow > $maxSizeRow){
//                    echo ($currenySizeRow-$rowSizeBundleProduct[$templateVars['items_item_id']]).'<br/>';
                    $templateTableFinal .= str_replace($result, $finalItems, $templateTable);
                    $finalItems = null;
                    $currenySizeRow = 0;
                }
                $itemPosition = array('items_position' => $i++);
                if(isset($templateVars['parent_item_id']) && in_array($templateVars['parent_item_id'], $bundleProduct)) {
                    $itemProcess =
                        '--><tr class="style-border-color"> 
<td class="color-text contenteditable background-items" title="Click to edit, right-click to insert variable" contextmenu-type="item" contenteditable="true" align="center" placeholder="Click to edit!" style="padding: 0.03in;font-family: \'centurygothic\';color: #4A453F;font-style: normal;font-size: 9px"></td> 
<td class="color-text contenteditable background-items" title="Click to edit, right-click to insert variable" contextmenu-type="item" contenteditable="true" align="left" placeholder="Click to edit!" style="padding: 0.03in;padding-left: 0.2in;font-family: \'centurygothic\';color: #4A453F;font-style: normal;font-size: 9px">'.$templateVars['items_qty_ordered'].' <span>x</span> '.$templateVars['items_name'].'</td> 
<td class="color-text contenteditable background-items" title="Click to edit, right-click to insert variable" contextmenu-type="item" contenteditable="true" align="left" placeholder="Click to edit!" style="padding: 0.03in;padding-left: 0.4in;color: #4A453F;font-style: normal;font-family: \'centurygothic\';font-size: 9px">'.$templateVars['items_name'].'</td> 
</tr><!-- ';
                }else{
                    $currenySizeRow += $arrayHight[$templateVars['items_item_id']];
//                    echo 'simple:'.$arrayHight[$templateVars['items_item_id']].'<br/>';
                    if($currenySizeRow > $maxSizeRow){
//                        echo ($currenySizeRow-$arrayHight[$templateVars['items_item_id']]).'<br/>';
                        $templateTableFinal .= str_replace($result, $finalItems, $templateTable);
                        $finalItems = null;
                        $currenySizeRow = 0;
                    }
                    $templateVars = array_merge($itemPosition, $templateVars);
                    $pdfProcessTemplate = Mage::getModel('core/email_template');
                    $itemProcess = $pdfProcessTemplate->setTemplateText($result)->getProcessedTemplate($templateVars);
                }

                $finalItems .= $itemProcess . '<br>';
                $j++;
//                if(in_array($j, $arrayItems)){
//                    $templateTableFinal .= str_replace($result, $finalItems, $templateTable);
//                    $finalItems = null;
//                }
            }
//            die();


            if(count($arrayItems) == 0){
                $templateTableFinal .= str_replace($result, $finalItems, $templateTable);
            }
            $templateWithItemsProcessed = $templateExplode[0] . $templateTableFinal . $templateExplode[2];

            $tempmplateForHtmlProcess = '<html>' . $templateWithItemsProcessed . '</html>';
        }else{

            foreach ($itemsData as $key => $templateVars)
            {
                $itemPosition = array('items_position' => $i++);
                $templateVars = array_merge($itemPosition, $templateVars);
                $pdfProcessTemplate = Mage::getModel('core/email_template');
                $itemProcess = $pdfProcessTemplate->setTemplateText($result)->getProcessedTemplate($templateVars);

                $finalItems .= $itemProcess . '<br>';
            }
            $templateWithItemsProcessed = str_replace($result, $finalItems, $templateToProcessForItems);

            $tempmplateForHtmlProcess = '<html>' . $templateWithItemsProcessed . '</html>';
        }

// $start = microtime();
        return $tempmplateForHtmlProcess;
    }

    /**
     * Load the default information for the template processing
     * @return object Mail template object
     */
    public function mainVariableProcess()
    {
        $templateText = $this->getTheTemplateBodyWithItems();
        //auto insert totals
        $total_order = Mage::getModel('pdfinvoiceplus/entity_totalsRender_order');
        $total_order->setSource($this->getTheOrder())
            ->setHtml($templateText)->setTemplateId($this->createTemplate()->getId());
        $templateText = $total_order->renderHtml();
        
        $theVariableProcessor = Mage::helper('pdfinvoiceplus')->setTheTemplateLayout($templateText);
        return $theVariableProcessor;
    }

    /**
     * The vars for the entity
     * @return type
     */
    public function getTheProcessedTemplate()
    {
        $templateVars = $this->getVars();
        $processedTemplate = $this->mainVariableProcess()->getProcessedTemplate($templateVars);
        return $processedTemplate;
    }

    /**
     * get system template in use
     * @return system template
     */
    public function getSystemTemplate() {
        $activeTemplate = Mage::getModel('pdfinvoiceplus/template')->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
        $systemTemplate = Mage::getModel('pdfinvoiceplus/systemtemplate')
            ->load($activeTemplate->getSystemTemplateId());
        return $systemTemplate;
    }

    public function getOrientation() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $orientation = $template->getOrientation();
        if ($orientation == 1)
            return 'L';
        return 'P';
    }
    /* Change by Jack 26/12 */
    public function isMassPDF(){
        $orderIds = Mage::app()->getRequest()->getPost('order_ids');
        if($orderIds)
            return true;
        return false;
    }

    public function getPdf($html = '') {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $storeId = Mage::app()->getStore()->getStoreId();
        $backgroundOn = Mage::getStoreConfig('pdfinvoiceplus/general/switchbg',$storeId);
        $choosetemplate = Mage::getStoreConfig('pdfinvoiceplus/general/choosedesign',$storeId);
        $templateBody = $this->getTheProcessedTemplate();
//        echo $templateBody;
//        die();

        $isMassPDF = $this->isMassPDF();
        $mailPdf = new Varien_Object;
        if($isMassPDF){
            $templateBody = $this->getTheProcessedTemplate();
            if($template->getData('template_id') == $choosetemplate){
                if($backgroundOn == 1){
                    $templateBody = str_replace("/media/bg.jpg",'./media/bg.jpg',$templateBody);
                }else{
                    $templateBody = str_replace("background: url(/media/bg.jpg)",'background: transparent;background-color: transparent;',$templateBody);
                }
                
            }
            
            $mailPdf->setData('htmltemplate', $templateBody);
            $mailPdf->setData('filename', $this->getFileName());
        }
        else{
            $pdf = $this->loadPdf();

            $templateBody = $this->getTheProcessedTemplate();
            if($template->getData('template_id') == $choosetemplate){

                $templateBody = str_replace("/pdfinvoiceplus/index/imagedesign",Mage::helper('pdfinvoiceplus')->getRotatedImage(),$templateBody);
                // echo $end-$start;
                // die('test time');
                if($backgroundOn == 1){
                    $templateBody = str_replace("/media/bg.jpg",'./media/bg.jpg',$templateBody);
                }else{
                    $templateBody = str_replace("background: url(/media/bg.jpg)",'background: transparent;background-color: transparent;',$templateBody);
                }
            }
            $pdf->WriteHTML($this->getCss(), 1);
            $pdf->WriteHTML($templateBody);

            $mailPdf->setData('htmltemplate', $templateBody);
            // $start = microtime();
            $output = $pdf->Output($this->getFileName(), 'S');
              $output = $pdf->Output($this->getFileName(), 'I');
            $mailPdf->setData('pdfbody', $output);
            $mailPdf->setData('filename', $this->getFileName());
        }
        return $mailPdf;
    }
    // End Change
    public function pdfPaperFormat() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $format = $template->getFormat();
        $format = $format ? $format : 'A4';
        return $format;
    }

    public function loadPdf() {
        $template = Mage::helper('pdfinvoiceplus/pdf')->getUsingTemplate();
        $storeId = Mage::app()->getStore()->getStoreId();
        $choosetemplate = Mage::getStoreConfig('pdfinvoiceplus/general/choosedesign',$storeId);

        $bottom = '0';
        $left = '0';
        $right = '0';
        if($template->getData('template_id') == $choosetemplate){
            $top = '0';
            $pdf = new Mpdf_Magestorepdf('', 'A5', 8, '', $left, $right, $top, $bottom);
        }else{
            $top = '5';
            $pdf = new Mpdf_Magestorepdf('', $this->pdfPaperFormat(), 8, '', $left, $right, $top, $bottom);
        }
        
        $orientation = $this->getOrientation();
        $pdf->AddPage($orientation);
        //Change by Jack 29/12 - add page number

            $isEnablePageNumbering = Mage::getStoreConfig('pdfinvoiceplus/general/page_numbering',$storeId);
            if($isEnablePageNumbering)
               if($isEnablePageNumbering)
                $pdf->SetHTMLFooter('<div style = "float:right;z-index:16000 !important; width:30px;">{PAGENO}/{nb}</div>');
        // End Change  
        return $pdf;
    }
}
