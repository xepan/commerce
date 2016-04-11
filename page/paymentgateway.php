<?php 

 namespace xepan\commerce;

 use Omnipay\Common\GatewayFactory;
 use Omnipay\Omnipay;

 class page_paymentgateway extends \xepan\commerce\page_configurationsidebar{

	public $title='Payment Gate Way';
	public $xepan_custom_gateway = ['ccavenue'];

	function init(){
		parent::init();
		
		
		$btn = $this->add('Button')->set('Update');
		$crud =$this->app->layout->add('xepan\base\CRUD');
		$crud->setModel('xepan\commerce\PaymentGateway',array('is_active','name','processing','gateway_image_id'));

		$crud->grid->add('VirtualPage')
	      ->addColumn('Update_data')
	      ->set(function($page){
	          $id = $_GET[$page->short_name.'_id'];
				
			$payment_gateway = $page->add('xepan/commerce/Model_PaymentGateway')->load($id);

			$form = $page->add('Form');

			$fields = json_decode($payment_gateway['default_parameters'],true);
			$values = json_decode($payment_gateway['parameters'],true);
		foreach ($fields as $field => $value) {
			if(is_array($value))
				$form->addField('DropDown',$field)->setValueList($value)->set($values[$field]);
			else
				$form->addField('line',$field)->set($values[$field]);
		}

		// echo "<pre>";
		// print_r($fields);
		// echo "</pre>";
		// exit;
		$form->addSubmit('Update');

		if($form->isSubmitted()){
			$fields = json_decode($payment_gateway['default_parameters'],true);
			foreach ($fields as $field => $value) {
				if(is_array($value))
					$fields[$field] = $value[$form[$field]];
				else
					$fields[$field] = $form[$field];
			}
			$payment_gateway['parameters'] = json_encode($fields);

			$payment_gateway->save();

			$form->js(null,$form->js()->reload())->univ()->successMessage('Update Information')->execute();
		}

	    });

		//update all Paymentgateway with there default parameters
		if($btn->isClicked()){
			$gateway = new GatewayFactory();
			//Get Omnipay Gateway
			$payment_gateway = $gateway->getSupportedGateways();
			//Save in SQL Model
			foreach ($payment_gateway as $gateway) {
				//tryload  PaymentGateway Model with name
				$pg_model = $this->add('xepan/commerce/Model_PaymentGateway');
				$pg_model->addCondition('name',$gateway);
				$pg_model->tryLoadAny();
				try {
					//create OmniPay Object
					$gateway_factory = GatewayFactory::create($gateway);
					$pg_model['default_parameters'] = $gateway_factory->getDefaultParameters();//getDefault Params
					$pg_model['processing'] = $pg_model['processing']?: "OffSite";
					$pg_model->saveAndUnload();
 				} catch (\Exception $e) {
 					// throw $e;
 				}
			}

			//xepan payment gateway
			foreach ($this->xepan_custom_gateway as $gateway_name) {
				
				$pg_model = $this->add('xepan/commerce/Model_PaymentGateway');
				$pg_model->addCondition('name',$gateway_name);
				$pg_model->tryLoadAny();
				
				try {
					//create OmniPay Object
					$gatewayfactory = new GatewayFactory;
					
					$gateway_factory = $gatewayfactory->create($gateway_name);
					$pg_model['default_parameters'] = $gateway_factory->getDefaultParameters();//getDefault Params
					$pg_model['processing'] = $pg_model['processing']?: "OffSite";
					$pg_model->saveAndUnload();
 				} catch (\Exception $e) {
 					// throw $e;
 				}
			}
			
			$crud->grid->js()->reload()->execute();
		}			
	}

	

	// 	}


} 