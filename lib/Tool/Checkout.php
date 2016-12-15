<?php
 
namespace xepan\commerce;

use Omnipay\Common\GatewayFactory;

class Tool_Checkout extends \xepan\cms\View_Tool{
	public $options = ['checkout_tnc_page'=>"",
					   'send_order'=>true,
					  ];
	public $order;
	public $gateway="";
	public $merge_model_array=[];

	function init(){
		parent::init();

		//Memorize checkout page if not logged in	
		$this->api->memorize('next_url',array('page'=>$_GET['page'],'order_id'=>$_GET['order_id']));

		//Check for the authtentication
		if(!$this->app->auth->model->id){
			$this->stepLogin();			
			return;
		}
		
		$customer = $this->add('xepan\commerce\Model_Customer');
		if(!$customer->loadLoggedIn()){
			$this->add('View_Error')->set("customer not found");
			// $this->app->redirect("logout");
			return;
		}


		// Check if order is owned by current member ??????
		if(isset($_GET['order_id'])){
			$order = $this->order = $this->api->memorize('checkout_order',$this->api->recall('checkout_order',$this->add('xepan/commerce/Model_SalesOrder')->tryLoad($_GET['order_id']?:0)));
			if(!$order->loaded()){
				$this->api->forget('checkout_order');
				$this->add('View_Error')->set('Order not found');
				return;
			}		

			if($order['contact_id'] != $customer->id){
				$this->add('View_Error')->set('Order does not belongs to your account. ' . $order->id);
				return;
			}
			
		}

		if($_GET['canceled']){
			$this->stepFailure();
			return;
		}
		
		//


		$this->api->stickyGET('step');
		
		$step =isset($_GET['step'])? $_GET['step']:"address";
		try{
			$this->{"step$step"}();
		}catch(Exception $e){
			// remove all database tables if exists or connetion available
			// remove config-default.php if exists
			throw $e;
		}
		
		// ================================= PAYMENT MANAGEMENT =======================
		if($_GET['pay_now']=='true'){
			if(!($this->app->recall('checkout_order') instanceof \xepan\commerce\Model_SalesOrder))
				throw new \Exception("order not found"+$this->app->recall('checkout_order'));
			
			
			$order = $this->order = $this->app->recall('checkout_order');
			$this->order->reload();
			// create gateway
			$gateway = $this->gateway;
			$gateway_factory = new GatewayFactory;
			throw new \Exception("Error Processing Request", 1);
			
			$gateway  = $gateway_factory->create($order['paymentgateway']);
			
			
			$gateway_parameters = $order->ref('paymentgateway_id')->get('parameters');
			$gateway_parameters = json_decode($gateway_parameters,true);
			// fill default values from database
			foreach ($gateway_parameters as $param => $value) {
				
				$param =ucfirst($param);
				$fn ="set".$param;
				$gateway->$fn($value);
			}
			
			$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
			$params = array(
			    'amount' => $order['net_amount'],
			    'currency' => 'INR',
			    'description' => 'Invoice Against Order Payment',
			    'transactionId' => $order->id, // invoice no 
			    'headerImageUrl' => 'http://xavoc.com/logo.png',
			    // 'transactionReference' => '1236Ref',
			    'returnUrl' => $protocol.$_SERVER['HTTP_HOST'].$this->api->url(null,array('paid'=>'true','pay_now'=>'true','order_id'=>$this->order->id))->getURL(),
			    'cancelUrl' => $protocol.$_SERVER['HTTP_HOST'].$this->api->url(null,array('canceled'=>'true','order_id'=>$this->order->id))->getURL(),
				'language' => 'EN',
				'billing_name' => $customer['first_name'],
				'billing_address' => $order['billing_address'],
				'billing_city' => $order['billing_city'],
				'billing_state' => $order['billing_state'],
				'billing_country' => $order['billing_country'],
				'billing_zip' => $order['billing_pincode'],
				'billing_tel' => $customer['contacts_str'],
				'billing_email' => $this->app->auth->model['username'],
				'delivery_address' => $order['shipping_address'],
				'delivery_city' => $order['shipping_city'],
				'delivery_state' => $order['shipping_state'],
				'delivery_country' => $order['shipping_country'],
				'delivery_zip' => $order['shipping_pincode'],
				'delivery_tel' => $customer['contacts_str'],
				'delivery_email' => $this->app->auth->model['username']
		 	);
	

			
			// Step 2. if got returned from gateway ... manage ..
			if($_GET['paid']){
				$response = $gateway->completePurchase($params)->send($params);
			    if ( ! $response->isSuccessful()){
			    	$order_status = $response->getOrderStatus();
			    		throw new \Exception("Failed");
			  //   	if(in_array($order_status, ['Failure']))
			  //   		$order_status = "onlineFailure";
			  //   	elseif(in_array($order_status, ['Aborted']))
			  //   		$order_status = "onlineAborted";
			  //   	else
			  //   		$order_status = "onlineFailure";
					// $order->setStatus($order_status);
			    	$this->api->redirect($this->api->url(null,array('step'=>"Failure",'message'=>$order_status,'order_id'=>$_GET['order_id'])));
			    }
		    	
			    $invoice = $order->invoice();
			    $invoice->PayViaOnline($response->getTransactionReference(),$response->getData());
					
				//Change Order Status onlineUnPaid to Submitted
				$order->submit();

				//send email after payment id paid successfully
				try{
						
					$salesorder_m = $this->add('xepan\base\Model_ConfigJsonModel',
						[
							'fields'=>[
										'from_email'=>'Dropdown',
										'subject'=>'line',
										'body'=>'xepan\base\RichText',
										'master'=>'xepan\base\RichText',
										'detail'=>'xepan\base\RichText',
										],
								'config_key'=>'SALESORDER_LAYOUT',
								'application'=>'commerce'
						]);
					$salesorder_m->add('xepan\hr\Controller_ACL');
					$salesorder_m->tryLoadAny();
						
					$config = $this->app->epan->config;
					$email_setting = $this->add('xepan\communication\Model_Communication_EmailSetting');
					$email_setting->load($salesorder_m['from_email']);
					
					$customer = $invoice->customer();
					$to_email=implode(',',$customer->getEmails());/*To Maintain the complability to send function*/


					$subject = $salesorder_m['subject'];
					$body = $salesorder_m['body'];

					// $merge_model_array=[];
					$this->merge_model_array = array_merge($this->merge_model_array,$invoice->get());
					$this->merge_model_array = array_merge($this->merge_model_array,$order->get());
					$this->merge_model_array = array_merge($this->merge_model_array,$customer->get());	
					$temp_subject=$this->add('GiTemplate');
					$temp_subject->loadTemplateFromString($subject);
					$subject_v=$this->add('View',null,null,$temp_subject);
					$subject_v->template->set($this->merge_model_array);
					
					$email_subject=$subject_v->getHtml();
					
					$temp_body=$this->add('GiTemplate');
					$temp_body->loadTemplateFromString($body);
					$body_v=$this->add('View',null,null,$temp_body);
					$body_v->template->set($this->merge_model_array);
					
					$email_body=$body_v->getHtml();
					
					$invoice->acl = false;
					$invoice->send($email_setting->id,$to_email,null,null,$email_subject,$email_body);
					// $subject_v->destroy();
					// $body_v->destroy();

				}catch(Exception $e){

				}

			    $this->api->forget('checkout_order');
			    // $this->stepComplete();
			    $this->api->redirect($this->api->url(null,array('step'=>"Complete",'pay_now'=>true,'paid'=>true,'order_id'=>$_GET['order_id'])));
			    exit;
			    // return;
			}

			// Step 1. initiate purchase ..
			try {
				//Sending $param with send function for passing value to gateway
				//dont know it's right way or no
			    $response = $gateway->purchase($params)->send($params);
			    if ($response->isSuccessful() /* OR COD */) {
			        // mark order as complete if not COD
			        // Not doing onsite transactions now ...
					$responsereturn = $response->getData();
			    } elseif ($response->isRedirect()) {
			        $response->redirect();
			    } else {
			        // display error to customer
			        exit($response->getMessage());
			    }
			} catch (\Exception $e) {
				throw $e;
			    // internal error, log exception and display a generic message to the customer
			    exit('Sorry, there was an error processing your payment. Please try again later.'. $e->getMessage(). " ". get_class($e));
			}


		}
		// ================================= PAYMENT MANAGEMENT END ===================

	}

	// step 1
	function stepLogin(){

		$v = $this->add('View',null,null,["view/tool/checkout/steplogin/view"]);

		$login_tool = $v->add('xepan\base\Tool_UserPanel',null,'login_panel');
		$login_tool->options = ['login_success_url'=> $this->app->url(null,['step'=>"Address"])];

	}

	// step 2
	function stepAddress(){
		if(!$this->options['checkout_tnc_page']){
			$this->add('View_Error')->set("specify terms and condition page url");
			return;
		}

		$personal_form=$this->add('Form',null,null,['form/empty']);		
		$personal_form->setLayout(['view/tool/checkout/stepaddress/form']);

		$customer = $this->add('xepan\commerce\Model_Customer');
		if($this->api->auth->model->id){
			$customer->addCondition('user_id',$this->api->auth->model->id);
			$customer->tryLoadAny();
		}

		//apply normal dropdown as commerce/DropDown 
		//billing model conditions
		$field_b_s = $customer->getElement('billing_state_id');
		
		$field_b_s->display(['form'=>"xepan\commerce\DropDown"]);
		$billing_state_model = $field_b_s->getModel();
		$billing_state_model->addExpression('country_status')->set($billing_state_model->refSQL('country_id')->fieldQuery('status'));
		$billing_state_model->addCondition('country_status','Active');
		$billing_state_model->addCondition('status','Active')->setOrder('name','asc');

		$field_b_c = $customer->getElement('billing_country_id');
		$field_b_c->display(['form'=>"xepan\commerce\DropDown"]);
		$billing_country_model = $field_b_c->getModel();
		$billing_country_model->addCondition('status','Active')->setOrder('name','asc');
		
		// shipping model conditions
		$field_s_s = $customer->getElement('shipping_state_id');
		$field_s_s->display(['form'=>"xepan\commerce\DropDown"]);
		$shipping_state_model = $field_s_s->getModel();
		$shipping_state_model->addExpression('country_status')->set($shipping_state_model->refSQL('country_id')->fieldQuery('status'));
		$shipping_state_model->addCondition('country_status','Active');
		$shipping_state_model->addCondition('status','Active');
		$shipping_state_model->setOrder('name','asc');

		$field_s_c = $customer->getElement('shipping_country_id');
		$field_s_c->display(['form'=>"xepan\commerce\DropDown"]);
		$shipping_country_model = $field_s_c->getModel();
		$shipping_country_model->addCondition('status','Active');
		$shipping_country_model->setOrder('name','asc');

		$personal_form->setModel($customer,array('billing_address','billing_city','billing_state_id','billing_country_id','billing_pincode','shipping_address','shipping_city','shipping_state_id','shipping_country_id','shipping_pincode'));
		//get all Field of billing address
		$f_b_address = $personal_form->getElement('billing_address')->validate('required|to_trim');
		$f_b_city = $personal_form->getElement('billing_city')->validate('required|to_trim');
		$f_b_country = $personal_form->getElement('billing_country_id');
		$f_b_country->validate('required');

		$f_b_state = $personal_form->getElement('billing_state_id');
		$f_b_state->validate('required');
		$f_b_pincode = $personal_form->getElement('billing_pincode')->validate('required|to_trim');

		//billing state change according to selected country
		if($this->app->stickyGET('billing_country_id'))
			$f_b_state->getModel()->addCondition('country_id',$_GET['billing_country_id'])->setOrder('name','asc');
		$f_b_country->js('change',$f_b_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$f_b_state->name]),'billing_country_id'=>$f_b_country->js()->val()]));
		// $f_b_country->js('change',$personal_form->js()->atk4_form('reloadField','billing_state_id',[$this->app->url(null,['cut_object'=>$f_b_state->name]),'billing_country_id'=>$f_b_country->js()->val()]));

		// get all Field of shipping address
		$f_s_address = $personal_form->getElement('shipping_address')->validate('required|to_trim');
		$f_s_city = $personal_form->getElement('shipping_city')->validate('required|to_trim');
		$f_s_state = $personal_form->getElement('shipping_state_id');
		$f_s_state->validate('required');
		
		$f_s_country = $personal_form->getElement('shipping_country_id');
		$f_s_country->validate('required');
		
		$f_s_pincode = $personal_form->getElement('shipping_pincode')->validate('required|to_trim');

		if($this->app->stickyGET('shipping_country_id'))
			$f_s_state->getModel()->addCondition('country_id',$_GET['shipping_country_id'])->setOrder('name','asc');

		$f_s_country->js('change',$f_s_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$f_s_state->name]),'shipping_country_id'=>$f_s_country->js()->val()]));

		$personal_form->addField('Checkbox','i_read','<a target="_blank" href="index.php?page='.$this->options['checkout_tnc_page'].'">I have Read All trems & Conditions</a>')->validate('required')->js(true)->closest('div.atk-form-row');
		
		$js_action = array(
				$f_s_address->js()->val($f_b_address->js()->val()),
				$f_s_city->js()->val($f_b_city->js()->val()),
				$f_s_state->js()->val($f_b_state->js()->val()),
				$f_s_country->js()->val($f_b_country->js()->val()),
				$f_s_pincode->js()->val($f_b_pincode->js()->val()),
			);

		$this->on('click','.xepan-checkout-same-as-billing-address',function($js,$data)use($js_action){
			return $js_action;
		});

		// $personal_form->addSubmit('Place Order');
		$this->on('click','.xepan-cart-proceed-next',function($js,$data)use($personal_form){
			return $personal_form->js()->submit();
		});

		if($personal_form->isSubmitted()){
			if(!$personal_form['i_read'])
				$personal_form->displayError('i_read','you must agree with out terms and condition');
		
			//get global config for county and state
			$misc_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'tax_on_shipping'=>'checkbox',
							'tax_as_per'=>'DropDown'
							],
					'config_key'=>'COMMERCE_TAX_AND_ROUND_AMOUNT_CONFIG',
					'application'=>'commerce'
			]);
			$misc_config->tryLoadAny();	

			$misc_tax_as_per = $misc_config['tax_as_per'];

			$billing_state_model->tryLoad($personal_form['billing_state_id']);
			$billing_country_model->tryLoad($personal_form['billing_country_id']);
			$shipping_country_model->tryLoad($personal_form['shipping_country_id']);
			$shipping_state_model->tryLoad($personal_form['shipping_state_id']);
			
			if($misc_tax_as_per === "billing"){
				$this->app->memorize('country',$billing_country_model);
				$this->app->memorize('state',$billing_state_model);
				$this->app->country = $billing_country_model;
				$this->app->state = $billing_state_model;
			}else{
				$this->app->memorize('country',$shipping_country_model);
				$this->app->memorize('state',$shipping_state_model);
				$this->app->country = $shipping_country_model;
				$this->app->state = $shipping_state_model;
			}

			$billing_detail = [
							'billing_address' => $personal_form['billing_address'],
							'billing_city'=>$personal_form['billing_city'],
							'billing_state_id'=>$personal_form['billing_state_id'],
							'billing_state'=>$billing_state_model['name'],
							'billing_country_id'=>$personal_form['billing_country_id'],
							'billing_country'=>$billing_country_model['name'],
							'billing_pincode'=>$personal_form['billing_pincode'],

							'shipping_address' => $personal_form['shipping_address'],
							'shipping_city'=>$personal_form['shipping_city'],
							'shipping_state_id'=>$personal_form['shipping_state_id'],
							'shipping_state'=>$shipping_state_model['name'],
							'shipping_country_id'=>$personal_form['shipping_country_id'],
							'shipping_country'=>$shipping_country_model['name'],
							'shipping_pincode'=>$personal_form['shipping_pincode'],
							];

			$this->app->memorize('billing_detail',$billing_detail);

			$personal_form->owner->js(null)->univ()->redirect($this->api->url(null,array('step'=>"OrderPreview")))->execute();
		}
	}

	// step 3 //verification
	function stepOrderPreview(){
		$view = $this->add('View',null,null,["view/tool/checkout/steporderpreview/view"]);
		$express_shipping = $this->app->recall('express_shipping');
		$billing_detail = $this->app->recall('billing_detail');

		//Todo set Options Checkout Cart Options
		$options = [
					"options"=>[
						'layout'=>'detail_cart',
						'show_image'=>true,
						"show_qtyform"=>false,
						"show_customfield"=>true,
						"show_design_edit"=>true,
						"show_round_amount"=>true,
						"show_discount_voucher"=>true,
						"checkout_page"=>"checkout",
						"cart_detail_url"=>"cart",
						"designer_page_url"=>"design",
						"show_express_shipping"=>$express_shipping,
						"show_proceed_to_next_button"=>false,
						"show_cart_item_remove_button"=>true
					]
				];

		$view->add('xepan\commerce\Tool_Cart',$options,'order_preview');

		$payment_step_url = $this->app->url(null,array('step'=>"Payment"));

		$view->on('click','.xepan-checkout-proceed-to-payment',function($js,$data)use($billing_detail,$payment_step_url){

			$cart_session_model = $this->add('xepan\commerce\Model_Cart');
			$totals = $cart_session_model->getTotals();
			
			if($totals['amount'] === 0){
				return $js->univ()->errorMessage("can't proceed, net amount cannot be Zero");
			}
			
			$order = $this->add('xepan\commerce\Model_SalesOrder');
			$order = $order->placeOrderFromCart($billing_detail,$this->options['send_order']);
			$this->app->hook('order_placed',[$order]);
			$this->app->memorize('checkout_order',$order);
			
			$cart_session_model->emptyCart();

			//forget the memorize variabe
			$this->app->forget('billing_detail');
			$this->app->forget('discount_voucher');
			$this->app->forget('express_shipping');
			
			
			return $js->univ()->redirect($payment_step_url);
		});

	}

	function stepPayment(){
		$view = $this->add('View',null,null,['view/tool/checkout/steppayment/view']);

		$order = $this->order = $this->app->recall('checkout_order');
		
		
		if(!($order instanceof \xepan\commerce\Model_SalesOrder))
			throw new \Exception("order not found");
				
		$this->order->reload();
		$order->reload();

		$customer_model = $this->add('xepan\commerce\Model_CustomerCredit');
		if(!$customer_model->loadLoggedIn())
			throw new \Exception("you logout or session out try again");
		
		if(!$_GET['paynow'] && $customer_model['remaining_credit_amount'] > 0){
			$this->add('xepan\commerce\View_Credit',
						[
							'customer_id'=>$customer_model->id,
							'order'=>$this->order
						]);
			return;
		}

		// add all active payment gateways
		$pay_form=$this->add('Form');

		$payment_model=$this->add('xepan/commerce/Model_PaymentGateway');
		$payment_model->addCondition('is_active',true);
		
		$pay_gate_field = $pay_form->addField('xepan\base\Radio','payment_gateway_selected',"");
		$pay_gate_field->setImageField('gateway_image');
		$pay_gate_field->setModel($payment_model);

		$btn_label = $this->options['xshop_checkout_btn_label']?:'Proceed';
		
		$pay_form->addSubmit($btn_label);
		
						
		if($pay_form->isSubmitted()){
			if(!$pay_form['payment_gateway_selected'])
				$pay_form->error('payment_gateway_selected','please select one payment gate way');
			
			$order['paymentgateway_id'] = $pay_form['payment_gateway_selected'];
			$order->save();

			$next_step_url = $this->app->url(null,array('step'=>"Complete",'pay_now'=>'true'));
			$pay_form->js()->univ()->redirect($next_step_url)->execute();
		}
	}

	function stepComplete(){
		// $this->add('xepan\commerce\Model_SalesOrder')
		$com_view=$this->add('View',null,null,['view/tool/checkout/stepcomplete/view']);
		$merge_model_array=[];
		if($_GET['order_id']){
			$order = $this->add('xepan\commerce\Model_SalesOrder')->addCondition('id',$_GET['order_id']);
			$order->tryLoadAny();
			if($order->loaded()){
				$merge_model_array = array_merge($merge_model_array,$order->invoice()->get());
				$merge_model_array = array_merge($merge_model_array,$order->get());
				$merge_model_array = array_merge($merge_model_array,$order->customer()->get());	
				$com_view->template->set($merge_model_array);	
			}
		}
		
		// $this->api->forget('checkout_order');
	}

	function stepFailure(){
		$v = $this->add('View',null,null,['view/tool/checkout/stepfailure/view']);
		$merge_model_array=[];
		if($_GET['order_id']){
			$order = $this->add('xepan\commerce\Model_SalesOrder')->addCondition('id',$_GET['order_id']);
			$order->tryLoadAny();
			if($order->loaded()){
				$merge_model_array = array_merge($merge_model_array,$order->invoice()->get());
				$merge_model_array = array_merge($merge_model_array,$order->get());
				$merge_model_array = array_merge($merge_model_array,$order->customer()->get());	
				$v->template->set($merge_model_array);	
			}
		}
	}

	function postOrderProcess(){
		if($_GET['order_done'] =='true'){
			
		}
	}

	function render(){
		$this->app->pathfinder->base_location->addRelativeLocation(
		    'epan-components/'.__NAMESPACE__, array(
		        'php'=>'lib',
		        'template'=>'templates',
		        'css'=>array('templates/css','templates/js'),
		        'img'=>array('templates/css','templates/js'),
		        'js'=>'templates/js',
		    )
		);

		// $this->js()->_load('xShop-js');
		// $this->api->jquery->addStylesheet('xShop-js');
		// 	$this->api->template->appendHTML('js_include','<script src="epan-components/xShop/templates/js/xShop-js.js"></script>'."\n");
		parent::render();	
}

	// defined in parent class
	// Template of this tool is view/namespace-ToolName.html
}