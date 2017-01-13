<?php

namespace xepan\commerce;

class View_Credit extends \View{
	public $customer_id;
	public $order;

	function init(){
		parent::init();

		$customer_model = $this->add('xepan\commerce\Model_CustomerCredit');
		
		if(!$customer_model->loadLoggedIn("Customer")){
			$this->add('View_Error',null,'error')->set('you logout or session out try again');
			return;
		}

		if($this->customer_id and $customer_model->id != $this->customer_id){
			$this->add('View_Error',null,'error')->set('customer not found');
			return;
		}

		if( $this->order['net_amount'] > $customer_model['remaining_credit_amount']){
			$this->add('View_Error',null,'error')->set('your have insufficient credit amount');
			return;
		}else{
			
			$this->setModel($customer_model);

			$this->template->trySet('available_credit_amount',$customer_model['remaining_credit_amount']);
			$this->template->trySet('order_amount',$this->order['net_amount']);

			$form = $this->add('Form');
			$form->addSubmit('Credit Pay Now');

			if($form->isSubmitted()){
				$customer_model->consumeCredit($this->order['net_amount'],$this->order);
				$form->js()->univ()->redirect($this->app->url(null,array('step'=>"Complete",'paynow'=>true,'paid'=>true,'order_id'=>$this->order->id)))->execute();
			}
		}

		
		$this->add('Button',null,'pay_via_online')->set('Continue with Online Payment')->js('click')->univ()->redirect($this->app->url(null,array('step'=>"Payment",'paynow'=>true)));
	}

	function defaultTemplate(){
		return ['view/tool/checkout/steppayment/credit'];
	}

}