<?php


namespace xepan\commerce;


class Controller_PaymentGatewayHelper extends \AbstractController {
	public $order;
	public $customer;

	function makeParamData($customer,$order,$gateway){
		$this->order = $order;
		$this->customer = $customer;

		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$return_url =  $protocol.$_SERVER['HTTP_HOST'].$this->api->url(null,array('paid'=>1,'pay_now'=>1,'order_id'=>$this->order->id))->getURL();
		$cancel_url = $protocol.$_SERVER['HTTP_HOST'].$this->api->url(null,array('canceled'=>'true','order_id'=>$this->order->id))->getURL();
		switch ($gateway) {
			case 'CCAvenue':
				$params = array(
				    'amount' => $order['net_amount'],
				    'currency' => 'INR',
				    'description' => 'Invoice Against Order Payment',
				    'transactionId' => $order->id, // invoice no 
				    'headerImageUrl' => 'http://xavoc.com/logo.png',
				    // 'transactionReference' => '1236Ref',
				    'returnUrl' => $return_url,
				    'cancelUrl' => $cancel_url,
					'language' => 'EN',
					'billing_name' => $customer['first_name'],
					'billing_address' => $order['billing_address'],
					'billing_city' => $order['billing_city'],
					'billing_state' => $order['billing_state'],
					'billing_country' => $order['billing_country'],
					'billing_zip' => $order['billing_pincode'],
					'billing_tel' => $customer->getPhones()[0],
					'billing_email' => $customer->getEmails()[0],
					'delivery_address' => $order['shipping_address'],
					'delivery_city' => $order['shipping_city'],
					'delivery_state' => $order['shipping_state'],
					'delivery_country' => $order['shipping_country'],
					'delivery_zip' => $order['shipping_pincode'],
					'delivery_tel' => $customer->getPhones()[0],
					'delivery_email' => $customer->getEmails()[0] //$this->app->auth->model['username']
			 	);

			 	return $params;
				break;
			case "Instamojo":
				return [
					'amount'=>$order['net_amount'],
					'buyer_name'=>$customer['name'],
					'email'=>$customer->getEmails()[0],
					'phone'=>'8559846603',
					'purpose'=>$order->id,
					'currency'=>'INR',
					'redirect_url'=>$return_url
				];
				break;
			default:
				# code...
				break;
		}
		return [];
	}

	function isSuccessful($customer,$order,$response,$gateway){
		switch ($gateway) {
			case 'CCAvenue':
				return $response->isSuccessful();
				break;
			
			default:
				return $response->isSuccessful();
				break;
		}
	}
}