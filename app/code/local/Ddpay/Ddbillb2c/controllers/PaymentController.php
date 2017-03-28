<?php
/*
Ddbillb2c Payment Controller
*/

class Ddpay_Ddbillb2c_PaymentController extends Mage_Core_Controller_Front_Action {
    
	/**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

	public function redirectAction() {
	
	}
	
	public function processAction() {

		$paymentMethod = Mage::getModel('ddbillb2c/standard');
		$session = Mage::getSingleton('checkout/session');
		$ddbillb2c_process = $session->getDdbillb2cProcess();

		if( $ddbillb2c_process == 1 )
		{
		
			$orderId = $session->getDdbillb2cOrderId();

			$session->unsDdbillb2cProcess();
			$session->unsDdbillb2cOrderId();
			
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			$billing = $order->getBillingAddress();
			$shipping = $order->getShippingAddress();
			$client_ip = $_SERVER['REMOTE_ADDR'];
	
			$requestData = array(
				"merchant_code" => $paymentMethod->getConfigData('merchant_code'),
				"sign_type" => $paymentMethod->getConfigData('sign_type'),
				"notify_url" => Mage::getUrl('ddbillb2c/payment/response'),
				"order_amount" => number_format($order->getGrandTotal(), 2, '.', ''),
				"order_no" => $order->getIncrementId(),
				"order_time" => date('Y-m-d H:i:s', time()),
				"product_code" => "",
				"product_desc" => "Products of Order No: ".$order->getIncrementId(),
				"product_name" => "Products of Order No: ".$order->getIncrementId(),
				"product_num" => "",
				"return_url" => Mage::getUrl('checkout/onepage/success/'),
				"show_url" => "",
				
				"customer_first_name" => $billing->getFirstname(),
				"customer_last_name" => $billing->getLastname(),
				"customer_email" => $order->getCustomerEmail(),
				"customer_phone" => $billing->getTelephone(),
				"customer_country" => $billing->getCountry(),
				"customer_state" => $billing->getRegion(),
				"customer_city" => $billing->getCity(),
				"customer_street" => $billing->getStreet(1),
				"customer_zip" => $billing->getPostcode(),
				"customer_cardNumber" => "",
				"customer_idNumber" => "",
				
				"ship_to_firstname" => $shipping->getFirstname(),
				"ship_to_lastname" => $shipping->getLastname(),
				"ship_to_email" => $order->getCustomerEmail(),
				"ship_to_phone" => $shipping->getTelephone(),
				"ship_to_country" => $shipping->getCountry(),
				"ship_to_state" => $shipping->getRegion(),
				"ship_to_city" => $shipping->getCity(),
				"ship_to_street" => $shipping->getStreet(1),
				"ship_to_zip" => $shipping->getPostcode(),
				"extra_return_param" => "",
				"client_ip" => $client_ip,
				"order_button_text" => __("Pay with DDBill B2C"),
			);
	
			$html = $paymentMethod->doDdbillb2cCreditPay( $requestData );
			if( $paymentMethod->getConfigData('debug') )
			{
				$debugData = array('ddbillb2c_request' => $requestData);
				Mage::log(json_encode($debugData), null, "ddbillb2c.log");
			}
			
			echo $html;
		}
		
		exit();			
	}
	
	public function responseAction() {
		$paymentMethod = Mage::getModel('ddbillb2c/standard');

		if( $this->getRequest()->isPost() ){
			
			$data = $this->getRequest()->getPost();
			if( $paymentMethod->getConfigData('debug') )
			{
				$debugData = array('ddbillb2c_response' => $data);
				Mage::log(json_encode($debugData), null, "ddbillb2c.log");
			}

			$validated = $paymentMethod->checkDdbillb2cCreditPayNotify( $data );
			$orderId = $data['order_no'];
			
			if( $validated ) {
				$order = Mage::getModel('sales/order');
				$order->loadByIncrementId($orderId);
				if( $order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING )
				{
					echo "SUCCESS";
					exit;
				}
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
				$order->sendNewOrderEmail();
				$order->setEmailSent(true);
				$order->save();

				echo "SUCCESS";
				exit;
			}
			else {
				echo "FAIL";
				exit;
			}
		}
		else
			exit();
	}
	
	public function cancelAction() {
        if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
            if($order->getId()) {
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
			}
        }
	}
}