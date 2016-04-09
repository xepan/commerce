<?php 

 namespace xepan\commerce;

 use Omnipay\Common\GatewayFactory;
 use Omnipay\Omnipay;

 class page_paymentgateway extends \xepan\commerce\page_configurationsidebar{

	public $title='Payment Gate Way';

	function init(){
		parent::init();
		

		$btn = $this->add('Button')->set('Update');
		$crud =$this->app->layout->add('xepan\base\CRUD');
		$crud->setModel('xepan\commerce\PaymentGateway',array('is_active','name','processing','gateway_image_id'));

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
			// $composer_json = file_get_contents('../composer.json');
			// $composer_array = json_decode($composer_json,true);
			// $require_array = $composer_array['require'];

			// foreach ($require_array as $name => $version) {
			// 	//check only for the gateway of omnipay
			// 	$name = explode("/", $name);
			// 	if(!isset($name[1]))
			// 		continue;

			// 	$gateway = explode("_", $name[1]);
			// 	if($gateway[0] != "omnipay" or !isset($gateway[1]))
			// 		continue;

			// 	$gateway_name = $gateway[1];

				$pg_model = $this->add('xepan/commerce/Model_PaymentGateway');
				$pg_model->addCondition('name','ccavenue');
				$pg_model->tryLoadAny();
				
				try {
					//create OmniPay Object
					$gatewayfactory = new GatewayFactory;
					
					$gateway_factory = $gatewayfactory->create('ccavenue');
					$pg_model['default_parameters'] = $gateway_factory->getDefaultParameters();//getDefault Params
					$pg_model['processing'] = $pg_model['processing']?: "OffSite";
					$pg_model->saveAndUnload();
 				} catch (\Exception $e) {
 					throw $e;
 				}
			// }

			$crud->grid->js()->reload()->execute();

		}			
	}
} 