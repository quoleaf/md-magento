<?php
class Ddpay_Ddbillb2c_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	
	protected $_code = 'ddbillb2c';
	
    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

	protected $_formBlockType = "ddbillb2c/form_ddbillb2c";

    public function getCheckoutRedirectUrl()
    {
		$session = Mage::getSingleton('checkout/session');
		
        return null;
    }

    public function saveOrderAfterSubmit(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
		$session = Mage::getSingleton('checkout/session');
		$Ddbillb2cOrderId = $order->getIncrementId();
		$session->setDdbillb2cOrderId($Ddbillb2cOrderId);
		$session->setDdbillb2cProcess(1);
		
        return $this;
    }

 	public function getOrderPlaceRedirectUrl() 
	{
		return Mage::getUrl('ddbillb2c/payment/process', array('_secure' => true));
	}
	
	public function isAvailable($quote = NULL)
	{
		return true; 
	}
	
    public function getSignTypes()
    {
        return array(
            'RSA' => Mage::helper('ddbillb2c')->__('RSA'),
            'RSA-S' => Mage::helper('ddbillb2c')->__('RSA-S'),
            'MD5' => Mage::helper('ddbillb2c')->__('MD5'),
        );
    }

	public function doDdbillb2cCreditPay( $data ) {
	
		$priKey = openssl_get_privatekey($this->getConfigData('private_key'));
		$pubKey = openssl_get_publickey($this->getConfigData('public_key'));
	
		$merchant_code = $data["merchant_code"];
		$service_type = "direct_pay";	
		$interface_version = "V3.0";
		$sign_type = $data["sign_type"];
		$input_charset = "UTF-8";
		$notify_url = $data["notify_url"];		
		$order_no = $data["order_no"];	
		$order_time = $data["order_time"];	
		$order_amount = $data["order_amount"];	
		$product_name = $data["product_name"];	
		
		$return_url = $data["return_url"];	
		$pay_type = "";
		$redo_flag = "1";	
		$product_code = $data["product_code"];	
		$product_desc = $data["product_desc"];	
		$product_num = $data["product_num"];
		$show_url = $data["show_url"];	
		$client_ip = $data["client_ip"];	
		$bank_code = "";

		$extend_param = "";
		
		$ship_to_name ="";
		if( $data["ship_to_lastname"] != "" )
			$ship_to_name .= $data["ship_to_lastname"];
		if( $data["ship_to_firstname"] != "" )
			$ship_to_name .= $data["ship_to_firstname"];
		
		
		$extend_param .= "ship_to_name^".$ship_to_name
			."|ship_to_email^".$data["ship_to_email"]
			."|ship_to_phone^".$data["ship_to_phone"]
			."|ship_to_state^".$data["ship_to_state"]
			."|ship_to_city^".$data["ship_to_city"]
			."|ship_to_street^".$data["ship_to_street"]
			."|ship_to_zip^".$data["ship_to_zip"]."|";		
		
		
		$customer_name ="";
		if( $data["customer_last_name"] != "" )
			$customer_name .= $data["customer_last_name"];
		if( $data["customer_first_name"] != "" )
			$customer_name .= $data["customer_first_name"];
		
		$extend_param .="customer_email^".$data["customer_email"]
			."|customer_name^".$customer_name
			."|customer_phone^".$data["customer_phone"]
			."|customer_state^".$data["customer_state"]
			."|customer_city^".$data["customer_city"]
			."|customer_street^".$data["customer_street"]
			."|customer_zip^".$data["customer_zip"];
		
		if( $data["customer_cardNumber"] != "" )
			$extend_param .= "|customer_cardNumber^".$data["customer_cardNumber"];
		
		if( $data["customer_idNumber"] != "" )
			$extend_param .= "|customer_idNumber^".$data["customer_idNumber"];
		
		$extra_return_param = $data["extra_return_param"];	
		
		$signStr = "";
		
		if( $bank_code != "" ){
			$signStr = $signStr."bank_code=".$bank_code."&";
		}
		if( $client_ip != "" ){
			$signStr = $signStr."client_ip=".$client_ip."&";
		}
		if( $extend_param != "" ){
			$signStr = $signStr."extend_param=".$extend_param."&";
		}
		if( $extra_return_param != "" ){
			$signStr = $signStr."extra_return_param=".$extra_return_param."&";
		}
		
		$signStr = $signStr."input_charset=".$input_charset."&";	
		$signStr = $signStr."interface_version=".$interface_version."&";	
		$signStr = $signStr."merchant_code=".$merchant_code."&";	
		$signStr = $signStr."notify_url=".$notify_url."&";		
		$signStr = $signStr."order_amount=".$order_amount."&";		
		$signStr = $signStr."order_no=".$order_no."&";		
		$signStr = $signStr."order_time=".$order_time."&";	
	
		if( $pay_type != "" ){
			$signStr = $signStr."pay_type=".$pay_type."&";
		}
		if( $product_code != "" ){
			$signStr = $signStr."product_code=".$product_code."&";
		}	
		if( $product_desc != "" ){
			$signStr = $signStr."product_desc=".$product_desc."&";
		}
		$signStr = $signStr."product_name=".$product_name."&";
		if( $product_num != "" ){
			$signStr = $signStr."product_num=".$product_num."&";
		}	
		if( $redo_flag != "" ){
			$signStr = $signStr."redo_flag=".$redo_flag."&";
		}
		if( $return_url != "" ){
			$signStr = $signStr."return_url=".$return_url."&";
		}		
		$signStr = $signStr."service_type=".$service_type;
		if( $show_url != "" ){	
			$signStr = $signStr."&show_url=".$show_url;
		}
		
		openssl_sign($signStr, $sign_info, $priKey, OPENSSL_ALGO_MD5);
		$sign = base64_encode($sign_info);

		$html = '
		<form name="dinpayFormB2C" id="dinpayFormB2C" method="post" action="https://pay.dinpay.com/gateway?input_charset=UTF-8">
			<input type="hidden" name="sign" value="'.$sign.'" />
			<input type="hidden" name="merchant_code" value="'.$merchant_code.'" />
			<input type="hidden" name="bank_code" value="'.$bank_code.'"/>
			<input type="hidden" name="order_no" value="'.$order_no.'"/>
			<input type="hidden" name="order_amount" value="'.$order_amount.'"/>
			<input type="hidden" name="service_type" value="'.$service_type.'"/>
			<input type="hidden" name="input_charset" value="'.$input_charset.'"/>
			<input type="hidden" name="notify_url" value="'.$notify_url.'">
			<input type="hidden" name="interface_version" value="'.$interface_version.'"/>
			<input type="hidden" name="sign_type" value="'.$sign_type.'"/>
			<input type="hidden" name="order_time" value="'.$order_time.'"/>
			<input type="hidden" name="product_name" value="'.$product_name.'"/>
			<input Type="hidden" Name="client_ip" value="'.$client_ip.'"/>
			<input Type="hidden" Name="extend_param" value="'.$extend_param.'"/>
			<input Type="hidden" Name="extra_return_param" value="'.$extra_return_param.'"/>
			<input Type="hidden" Name="pay_type" value="'.$pay_type.'"/>
			<input Type="hidden" Name="product_code" value="'.$product_code.'"/>
			<input Type="hidden" Name="product_desc" value="'.$product_desc.'"/>
			<input Type="hidden" Name="product_num" value="'.$product_num.'"/>
			<input Type="hidden" Name="return_url" value="'.$return_url.'"/>
			<input Type="hidden" Name="show_url" value="'.$show_url.'"/>
			<input Type="hidden" Name="redo_flag" value="'.$redo_flag.'"/>
			<input type="submit" name="Submit" value="Pay with DDBillB2C" style="display:none;" />
		</form><script type="text/javascript">document.dinpayFormB2C.submit();</script>';

		return $html;
	}
	
	public function checkDdbillb2cCreditPayNotify( $data ) {
	
		$priKey = openssl_get_privatekey($this->getConfigData('private_key'));
		$pubKey = openssl_get_publickey($this->getConfigData('public_key'));
	
		$merchant_code	= $data["merchant_code"];	
		$interface_version = $data["interface_version"];
		$sign_type = $data["sign_type"];
		$dinpaySign = base64_decode($data["sign"]);
		$notify_type = $data["notify_type"];
		$notify_id = $data["notify_id"];
		$order_no = $data["order_no"];
		$order_time = $data["order_time"];	
		$order_amount = $data["order_amount"];
		$trade_status = $data["trade_status"];
		$trade_time = $data["trade_time"];
		$trade_no = $data["trade_no"];
		$bank_seq_no = $data["bank_seq_no"];
		$extra_return_param = $data["extra_return_param"];
		
		$signStr = "";
		
		if( $bank_seq_no != "" ){
			$signStr = $signStr."bank_seq_no=".$bank_seq_no."&";
		}
		if( $extra_return_param != "" ){
			$signStr = $signStr."extra_return_param=".$extra_return_param."&";
		}	
		$signStr = $signStr."interface_version=".$interface_version."&";	
		$signStr = $signStr."merchant_code=".$merchant_code."&";
		$signStr = $signStr."notify_id=".$notify_id."&";
		$signStr = $signStr."notify_type=".$notify_type."&";
		$signStr = $signStr."order_amount=".$order_amount."&";	
		$signStr = $signStr."order_no=".$order_no."&";	
		$signStr = $signStr."order_time=".$order_time."&";	
		$signStr = $signStr."trade_no=".$trade_no."&";	
		$signStr = $signStr."trade_status=".$trade_status."&";
		$signStr = $signStr."trade_time=".$trade_time;
		
		$flag = openssl_verify($signStr, $dinpaySign, $pubKey, OPENSSL_ALGO_MD5);
		if( $flag ){
			return true;
		} else {
			return false;
		}
	}	
}
?>