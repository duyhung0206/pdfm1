<?php

class Magestore_Pdfinvoiceplus_Model_Entity_Additional_Info extends Magestore_Pdfinvoiceplus_Model_Entity_Pdfgenerator {

    public function getStoreId() {
        return $this->getHelper()->getOrder()->getStoreId();
    }

    public function getHelper() {
        return Mage::helper('pdfinvoiceplus/pdf');
    }

    public function getTheOrderVariables() {
        $order = $this->getOrder(); // change by Jack 25/12
        $variables = array();
        $store = Mage::app()->getStore($this->getStoreId());
		$totalQty=$order->getData('total_qty_ordered');
        $totalTTC = 0;
        /*caculate total TTC*/
        foreach ($order->getAllItems() as $item) {
//                $this->setItem($item);
            if ($item->getParentItem()) {
                continue;
            }
//            Zend_Debug::dump($item->getData('tax_amount'));
//            die();
            $totalTTC += $item->getData('price')*$item->getData('qty_ordered') + $item->getData('tax_amount') - $item->getData('discount_amount');


        }
        $variables['order_totalTTC'] = array(
            'value' => $order->formatPriceTxt(($totalTTC+$order->getData('shipping_amount')), true, false)
        );


		// zend_debug::dump();die;
        foreach ($order->getData() as $key => $value) {
            $variables['order_' . $key] = array('value' => $value);
            if ($key == 'grand_total') {
                $variables['order_grand_total'] = array(
                    // 'value' => Mage::helper('core')->currency($order->getGrandTotal())
                    'value' => $order->formatPriceTxt($order->getGrandTotal(), true, false)
                );
            }
            if ($key == 'subtotal_incl_tax') {
                $variables['order_subtotal_incl_tax'] = array(
                    // 'value' => Mage::helper('core')->currency($order->getShippingAmount())
                    'value' => $order->formatPriceTxt($order->getData('subtotal_incl_tax'), true, false)
                );				
            }
            if ($key == 'shipping_amount') {
                $variables['order_shipping_amount'] = array(
                    // 'value' => Mage::helper('core')->currency($order->getShippingAmount())
                    'value' => $order->formatPriceTxt($order->getShippingAmount(), true, false)
                );
            }
			$variables['order_total_qty'] = array(
                    'value' => (int)$totalQty
                );
            if ($key == 'tax_amount') {
                $variables['order_tax_amount'] = array(
                    // 'value' => Mage::helper('core')->currency($order->getTaxAmount())
                    'value' => $order->formatPriceTxt($order->getTaxAmount(), true, false)
                );
            }
            if ($key == 'subtotal') {
                $variables['order_subtotal'] = array(
                    // 'value' => Mage::helper('core')->currency($order->getSubtotal())
                    'value' => $order->formatPriceTxt($order->getSubtotal(), true, false)
                );
            }
            if ($key == 'created_at') {
                $variables['order_created_at'] = array(
                    'value' => Mage::helper('core')->formatDate($order->getCreatedAt(), 'short', true)
                );
            }
            if ($key == 'reward_salesrule_points') {
                $variables['order_reward_salesrule_points'] = array(
                    'value' => Mage::helper('core')->currency($order->getRewardSalesrulePoints())
                );
            }
            if ($key == 'webpos_cash') {
                $variables['order_webpos_cash'] = array(
                    'value' => Mage::helper('core')->currency($order->getWebposCash())
                );
            }
            if ($key == 'webpos_discount_amount') {
                $variables['order_webpos_discount_amount'] = array(
                    'value' => Mage::helper('core')->currency($order->getWebposDiscountAmount())
                );
            }
            if ($key == 'total_due') {
                $variables['order_total_due'] = array(
                    // 'value' => Mage::helper('core')->currency($order->getTotalDue())
                    'value' => $order->formatPriceTxt($order->getTotalDue(), true, false)
                );
            }
            if ($key == 'rewardpoints_earn') {
                $variables['order_rewardpoints_earn'] = array(
                    'value' => Mage::helper('core')->currency($order->getRewardpointsEarn())
                );
            }
            if ($key == 'rewardpoints_discount') {
                $variables['order_rewardpoints_discount'] = array(
                    'value' => Mage::helper('core')->currency($order->getRewardpointsDiscount())
                );
            }
            if ($key == 'rewardpoints_spent') {
                $variables['order_rewardpoints_spent'] = array(
                    'value' => Mage::helper('core')->currency($order->getRewardpointsSpent())
                );
            }
        }
       // zend_debug::dump($variables);die;
        return $variables;
    }

    public function getTheInvoiceVariables() {
        $invoice = $this->getInvoice(); // Change by Jack
         /* change by Zeus 04/12 */
        $variables[] = NULL;
        /* end change */
        if($invoice){  // Change by Jack 26/12
            $order = $invoice->getOrder();
			$totalQty=$order->getData('total_qty_ordered');

            $totalTTC = 0;
            /*caculate total TTC*/
            foreach ($this->getSource()->getAllItems() as $item) {
//                $this->setItem($item);
                if ($item->getOrderItem()->getParentItem()) {
                    $theParent = $item->getOrderItem()->getParentItem();
                    if (Mage::helper('pdfinvoiceplus/product')->isConfigurable($theParent->getProductId())) {
                        continue;
                    }
                }
                $totalTTC += $item->getData('price')*$item->getData('qty') + $item->getData('tax_amount') - $item->getData('discount_amount');


            }

            $variables['invoice_totalTTC'] = array(
                'value' => $order->formatPriceTxt(($totalTTC+$invoice->getShippingAmount()), true, false)
            );
            foreach ($invoice->getData() as $key => $value) {
                    $variables['invoice_' . $key] = array(
                        'value' => $value,
                    );
                    if ($key == 'subtotal_incl_tax') {
                        $variables['invoice_subtotal_incl_tax'] = array(
                            'value' => $order->formatPriceTxt($invoice->getData('subtotal_incl_tax'), true, false)
                        );
                    }
                    if($key == 'order_id'){
                        $variables['invoice_' . $key] = array(
                        'value' => $order->getIncrementId()
                    );
                    }
                    if ($key == 'grand_total') {
                        $variables['invoice_grand_total'] = array(
                            // 'value' => Mage::helper('core')->currency($invoice->getGrandTotal())
                            'value' => $order->formatPriceTxt($invoice->getGrandTotal(), true, false)
                        );						
                    }
					$variables['invoice_total_qty'] = array(
                            'value' => (int)$totalQty
                        );
                    if ($key == 'shipping_amount') {
                        $variables['invoice_shipping_amount'] = array(
                            // 'value' => Mage::helper('core')->currency($invoice->getShippingAmount())
                            'value' => $order->formatPriceTxt($invoice->getShippingAmount(), true, false)
                        );
                    }
                    if ($key == 'tax_amount') {
                        $variables['invoice_tax_amount'] = array(
                            // 'value' => Mage::helper('core')->currency($invoice->getTaxAmount())
                            'value' => $order->formatPriceTxt($invoice->getTaxAmount(), true, false)
                        );
                    }
                    if ($key == 'subtotal') {
                        $variables['invoice_subtotal'] = array(
                            // 'value' => Mage::helper('core')->currency($invoice->getSubtotal())
                            'value' => $order->formatPriceTxt($invoice->getSubtotal(), true, false)
                        );
                    }
                if ($key == 'subtotal') {
                    $variables['invoice_subtotal'] = array(
                        // 'value' => Mage::helper('core')->currency($invoice->getSubtotal())
                        'value' => $order->formatPriceTxt($invoice->getSubtotal(), true, false)
                    );
                }
                    if ($key == 'created_at') {
                        $variables['invoice_created_at'] = array(
                            'value' => Mage::helper('core')->formatDate($invoice->getCreatedAt(), 'short', true)
                        );
                    }
                    if ($key == 'reward_salesrule_points') {
                        $variables['invoice_reward_salesrule_points'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getRewardSalesrulePoints())
                        );
                    }
                    if ($key == 'webpos_cash') {
                        $variables['invoice_webpos_cash'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getWebposCash())
                        );
                    }
                    if ($key == 'webpos_discount_amount') {
                        $variables['invoice_webpos_discount_amount'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getWebposDiscountAmount())
                        );
                    }
                    if ($key == 'total_due') {
                        $variables['invoice_total_due'] = array(
                            // 'value' => Mage::helper('core')->currency($invoice->getTotalDue())
                            'value' => $order->formatPriceTxt($invoice->getTotalDue(), true, false)
                        );
                    }
                    if ($key == 'rewardpoints_earn') {
                        $variables['invoice_rewardpoints_earn'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getRewardpointsEarn())
                        );
                    }
                    if ($key == 'rewardpoints_discount') {
                        $variables['invoice_rewardpoints_discount'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getRewardpointsDiscount())
                        );
                    }
                    if ($key == 'rewardpoints_spent') {
                        $variables['invoice_rewardpoints_spent'] = array(
                            'value' => Mage::helper('core')->currency($invoice->getRewardpointsSpent())
                        );
                    }
                    if ($key == 'state') {
                        if ($value == 1) {
                            $variables['invoice_state'] = array(
                                'value' => Mage::helper('core')->__('Pendding')
                            );
                        } else if ($value == 2) {
                            $variables['invoice_state'] = array(
                                'value' => Mage::helper('core')->__('Paid')
                            );
                        } else {
                            $variables['invoice_state'] = array(
                                'value' => Mage::helper('core')->__('Closed')
                            );
                        }
                    }
                }
        }  // End Change
		
        return $variables;
    }

    public function getTheCreditmemoVariables() {
        $creditmemo = $this->getCreditmemo(); // Change By Jack
        /* change by Zeus 04/12 */
        $variables[] = NULL;
        /* end change */
        if($creditmemo){  // Change by Jack 26/12
                $order = $creditmemo->getOrder();
				$totalQty=$order->getData('total_qty_ordered');
				// zend_debug::dump($order->getData());die;
                foreach ($creditmemo->getData() as $key => $value) {
                    $variables['creditmemo_' . $key] = array('value' => $value);
                    if ($key == 'grand_total') {
                        $variables['creditmemo_grand_total'] = array(
                            // 'value' => Mage::helper('core')->currency($creditmemo->getGrandTotal())
                            'value' => $order->formatPriceTxt($creditmemo->getGrandTotal(), true, false)
                        );
                    }
                    if($key == 'order_id'){
                        if($order->getIncrementId() != ''){
                        $variables['creditmemo_' . $key] = array(
                        'value' => $order->getIncrementId()
                    );
                    }
                    }
                /*End Change*/
                    if ($key == 'shipping_amount') {
                        $variables['creditmemo_shipping_amount'] = array(
                            // 'value' => Mage::helper('core')->currency($creditmemo->getShippingAmount())
                            'value' => $order->formatPriceTxt($creditmemo->getShippingAmount(), true, false)
                        );
                    }
                    if ($key == 'tax_amount') {
                        $variables['creditmemo_tax_amount'] = array(
                            // 'value' => Mage::helper('core')->currency($creditmemo->getTaxAmount())
                            'value' => $order->formatPriceTxt($creditmemo->getTaxAmount(), true, false)
                        );
                    }
                    if ($key == 'subtotal') {
                        $variables['creditmemo_subtotal'] = array(
                            // 'value' => Mage::helper('core')->currency($creditmemo->getSubtotal())
                            'value' => $order->formatPriceTxt($creditmemo->getSubtotal(), true, false)
                        );
                    }
                    if ($key == 'created_at') {
                        $variables['creditmemo_created_at'] = array(
                            'value' => Mage::helper('core')->formatDate($creditmemo->getCreatedAt(), 'short', true)
                        );
                    }
                    if ($key == 'reward_salesrule_points') {
                        $variables['creditmemo_reward_salesrule_points'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getRewardSalesrulePoints())
                        );
                    }
                    if ($key == 'webpos_cash') {
                        $variables['creditmemo_webpos_cash'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getWebposCash())
                        );
                    }
                    if ($key == 'webpos_discount_amount') {
                        $variables['creditmemo_webpos_discount_amount'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getWebposDiscountAmount())
                        );
                    }
                    if ($key == 'total_due') {
                        $variables['creditmemo_total_due'] = array(
                            // 'value' => Mage::helper('core')->currency($creditmemo->getTotalDue())
                            'value' => $order->formatPriceTxt($creditmemo->getTotalDue(), true, false)
                        );
                    }
                    if ($key == 'rewardpoints_earn') {
                        $variables['creditmemo_rewardpoints_earn'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getRewardpointsEarn())
                        );
                    }
                    if ($key == 'rewardpoints_discount') {
                        $variables['creditmemo_rewardpoints_discount'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getRewardpointsDiscount())
                        );
                    }
                    if ($key == 'rewardpoints_spent') {
                        $variables['creditmemo_rewardpoints_spent'] = array(
                            'value' => Mage::helper('core')->currency($creditmemo->getRewardpointsSpent())
                        );
                    }
                    if ($key == 'state') {
                        if ($value == 1) {
                            $variables['creditmemo_state'] = array(
                                'value' => Mage::helper('core')->__('Pendding')
                            );
                        } else if ($value == 2) {
                            $variables['creditmemo_state'] = array(
                                'value' => Mage::helper('core')->__('Paid')
                            );
                        } else {
                            $variables['creditmemo_state'] = array(
                                'value' => Mage::helper('core')->__('Closed')
                            );
                        }
                    }
                }
        } // End Change
        return $variables;
    }
	public function getTheShipmentVariables() {
        $shipment = $this->getShipment(); // Change By Jack
        /* change by Zeus 04/12 */
        $variables[] = NULL;
        /* end change */
        if($shipment){  // Change by Jack 26/12
                $order = $shipment->getOrder();
				// zend_debug::dump($order->getData());die;
                foreach ($shipment->getData() as $key => $value) {
                    $variables['shipment_' . $key] = array('value' => $value);
                  
                    if($key == 'order_id'){
                        if($order->getIncrementId() != ''){
                        $variables['shipment_' . $key] = array(
                        'value' => $order->getIncrementId()
                    );
                    }
                    }
                /*End Change*/
                   
                    if ($key == 'created_at') {
                        $variables['shipment_created_at'] = array(
                            'value' => Mage::helper('core')->formatDate($shipment->getCreatedAt(), 'short', true)
                        );
                    }
					$variables['shipment_status'] = array(
                            'value' => $order->getData('status')
                        );

                }
        } // End Change
        return $variables;
    }

    public function getTheCustomerVariables() {
        $order = $this->getOrder(); // change by Jack 25/12
        $store = Mage::app()->getStore($this->getStoreId());
        $customerId = $order->getCustomerId();
        $getCustomer = Mage::getModel('customer/customer')->load($customerId);
        $getCustomerGroup = Mage::getModel('customer/group')->load((int) $order->getCustomerGroupId())->getCode();
        $variables = array();
        foreach ($getCustomer->getData()  as $key =>$value){
            $variables['customer_'.$key] = array('value' => $value);
            if($key =='created_at'){
                $variables['customer_created_at']=array(
                    'value' =>Mage::helper('core')->formatDate($getCustomer->getData('created_at'), 'short', true)
                );
            }
             if($key =='created_in'){
                $variables['customer_created_in']=array(
                    'value' =>$getCustomer->getData('created_in')
                );
            }
            if($key =='dbo'){
                $variables['customer_created_at']=array(
                    'value' =>Mage::helper('core')->formatDate($getCustomer->getData('dob'), 'short', true)
                );
            }
        }
        return $variables;
    }

    public function getThePaymentInfo($type) {
        $order = $this->getOrder(); // change by Jack 25/12
        $paymentInfo = $order->getPayment()->getMethodInstance()->getTitle();

        $variables = array(
            $type.'_payment_method' => array(
                'value' => $paymentInfo,
                'label' => Mage::helper('sales')->__('Billing Method'),
            ),
            $type.'_billing_method_currency' => array(
                'value' => $order->getOrderCurrencyCode(),
                'label' => Mage::helper('sales')->__('Order was placed using'),
            ),
        );
        return $variables;
    }

    public function getTheShippingInfo($type) {
        $order = $this->getOrder(); // change by Jack 25/12
        if ($order->getShippingDescription()) {
            $shippingInfo = $order->getShippingDescription();
        } else {
            $shippingInfo = '';
        }

        $variables = array(
            $type.'_shipping_method' => array(
                'value' => $shippingInfo,
                'label' => Mage::helper('sales')->__('Shipping Information'),
            ),
        );
        return $variables;
    }

    public function getTheAddresInfo($type) {
        $order = $this->getOrder();  // change by Jack 25/12
        $billingInfo = $order->getBillingAddress()->getFormated(true);
        if ($order->getShippingAddress()) {
            $shippingInfo = $order->getShippingAddress()->getFormated(true);
        } else {
            $shippingInfo = '';
        }
        $billingInfo = Mage::helper('pdfinvoiceplus')->handleStringAddress($billingInfo);
        $shippingInfo = Mage::helper('pdfinvoiceplus')->handleStringAddress($shippingInfo);

        $variables = array(
            $type.'_billing_address' => array(
                'value' => $billingInfo,
                'label' => Mage::helper('sales')->__('Billing Address'),
            ),
            $type.'_shipping_address' => array(
                'value' => $shippingInfo,
                'label' => Mage::helper('sales')->__('Shipping Address'),
            )
        );
        return $variables;
    }
    /* change by Jack 27/12 */
    public function detectType(){
		// zend_debug::dump(Mage::getSingleton('core/session')->getType());die;
        if(Mage::getSingleton('core/session')->getType() == 'invoice')
            return 'invoice';
        else if(Mage::getSingleton('core/session')->getType() == 'creditmemo')
            return 'creditmemo';
		else if(Mage::getSingleton('core/session')->getType() == 'shipment')
            return 'shipment';
        else
            return 'order';
    }
    public function getTheInfoMergedVariables() {
        $type = $this->detectType();
		// zend_debug::dump($this->detectType());die;
        if ($type == 'invoice') {
			$vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('invoice')
                    , $this->getThePaymentInfo('invoice')
                    , $this->getTheShippingInfo('invoice')                    
                    , $this->getTheInvoiceVariables()
            );
        } else if ($type == 'creditmemo') {
			$vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('creditmemo')
                    , $this->getThePaymentInfo('creditmemo')
                    , $this->getTheShippingInfo('creditmemo')
                    , $this->getTheCreditmemoVariables()
            );
        } else if ($type == 'shipment') {
			$vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('shipment')
                    , $this->getThePaymentInfo('shipment')
                    , $this->getTheShippingInfo('shipment')
                    , $this->getTheShipmentVariables()
            );
        } else {
            $vars = array_merge(
                    $this->getTheOrderVariables()
                    , $this->getTheCustomerVariables()
                    , $this->getTheAddresInfo('order')
                    , $this->getThePaymentInfo('order')
                    , $this->getTheShippingInfo('order')
            );
        }
        $processedVars = Mage::helper('pdfinvoiceplus')->arrayToStandard($vars);
        return $processedVars;
    }
    // end change
}