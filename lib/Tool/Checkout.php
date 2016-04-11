<?php
 
namespace xepan\commerce;

use Omnipay\Common\GatewayFactory;

class Tool_Checkout extends \xepan\cms\View_Tool{
	
	public $options=array(); // ONLY Available in server side components
	public $order;
	public $gateway="";

	function init(){
		parent::init();
		
		//Memorize checkout page if not logged in
		$this->api->memorize('next_url',array('page'=>$_GET['page'],'order_id'=>$_GET['order_id']));
		
		//Check for the authtentication
		//Redirect to Login Page
		if($this->options['xshop_checkout_noauth_subpage_url']=='on'){
			if(!$this->options['xshop_checkout_noauth_subpage'] or $this->options['xshop_checkout_noauth_subpage'] ==""){
				$this->add('View_Error')->set('Subpage Name Cannot be Empty');
				return;
			}
			
			$auth = $this->add('xepan\commerce\Controller_Auth',array('redirect_subpage'=>$this->options['xshop_checkout_noauth_subpage']));
			$auth->checkCredential();
		}

		// add Login View if not loggedIn
		if($this->options['xshop_checkout_noauth_view'] == "on"){
			$auth = $this->add('xepan\commerce\Controller_Auth',array('substitute_view'=>"xepan\base\Tool_UserPanel"));
			if(!$auth->checkCredential())
				return;
		}

		// Check if order is owned by current member ??????
		
		// $order=$this->order = $this->api->memorize('checkout_order',$this->api->recall('checkout_order',$this->add('xepan/commerce/Model_SalesOrder')->tryLoad($_GET['order_id']?:0)));
		// if(!$order->loaded()){
		// 	$this->api->forget('checkout_order');
		// 	$this->add('View_Error')->set('Order not found');
		// 	return;
		// }

		$member = $this->add('xepan\commerce\Model_Customer');
		$member->loadLoggedIn();

		//temporary comment5
		// if($order['contact_id'] != $member->id){
		// 	$this->add('View_Error')->set('Order does not belongs to your account. ' . $order->id);
		// 	return;
		// }

		
		$this->api->stickyGET('step');
		
		$step =isset($_GET['step'])? $_GET['step']:1;
		try{
			call_user_method("step$step", $this);
		}catch(Exception $e){
			// remove all database tables if exists or connetion available
			// remove config-default.php if exists
			throw $e;
		}

		// ================================= PAYMENT MANAGEMENT =======================
		if($_GET['pay_now']=='true'){
			
			if(!($this->app->recall('checkout_order') instanceof \xepan\commerce\Model_SalesOrder))
				throw new \Exception("order not found");
			
			$order=$this->order = $this->app->recall('checkout_order');
			$this->order->reload();
			// create gateway
			$gateway = $this->gateway;
			
			$gateway= GatewayFactory::create($order['paymentgateway']);
			
			$gateway_parameters = $order->ref('paymentgateway_id')->get('parameters');
			$gateway_parameters = json_decode($gateway_parameters,true);

			// fill default values from database
			foreach ($gateway_parameters as $param => $value) {
				$param =ucfirst($param);
				$fn ="set".$param;
				$gateway->$fn($value);
			}

			$params = array(
			    'amount' => $order['net_amount'],
			    'currency' => 'INR',
			    'description' => 'Invoice Against Order Payment',
			    'transactionId' => $order->id, // invoice no 
			    'headerImageUrl' => 'http://xavoc.com/logo.png',
			    // 'transactionReference' => '1236Ref',
			    'returnUrl' => 'http://'.$_SERVER['HTTP_HOST'].$this->api->url(null,array('paid'=>'true','pay_now'=>'true'))->getURL(),
			    'cancelUrl' => 'http://'.$_SERVER['HTTP_HOST'].$this->api->url(null,array('canceled'=>'true','pay_now'=>'true'))->getURL(),
				'language' => 'EN',
				'billing_name' => $order->customer()->get('name'),
				'billing_address' => $order['billing_address'],
				'billing_city' => $order['billing_city'],
				'billing_state' => $order['billing_state'],
				'billing_country' => $order['billing_country'],
				'billing_zip' => $order['billing_zip'],
				'billing_tel' => $order['billing_contact'],
				'billing_email' => $order['billing_email'],
				'delivery_address' => $order['shipping_address'],
				'delivery_city' => $order['shipping_city'],
				'delivery_state' => $order['shipping_state'],
				'delivery_country' => $order['shipping_country'],
				'delivery_zip' => $order['shipping_zip'],
				'delivery_tel' => $order['shipping_tel'],
				'delivery_email' => $order['shipping_email']
		 	);

			// Step 2. if got returned from gateway ... manage ..
		
			if($_GET['paid']){
				$response = $gateway->completePurchase($params)->send($params);
			    if ( ! $response->isSuccessful()){
			    	$order_status = $response->getOrderStatus();
			    	var_dump($order_status);

			  //   	if(in_array($order_status, ['Failure']))
			  //   		$order_status = "onlineFailure";
			  //   	elseif(in_array($order_status, ['Aborted']))
			  //   		$order_status = "onlineAborted";
			  //   	else
			  //   		$order_status = "onlineFailure";
					// $order->setStatus($order_status);
			        throw new \Exception($response->getMessage());
			    }
		    	
			    $order->invoice()->PayViaOnline($response->getTransactionReference(),$response->getData());
				
				//send email after payment id paid successfully
				//Change Order Status onlineUnPaid to Submitted
				$invoice = $order->invoice();
				$email_body = $invoice->parseEmailBody();
				$customer = $invoice->customer();
				$customer_email=$customer->get('customer_email');
				$emails = explode(',', $customer_email['customer_email']);
				$to_email = $emails[0];
				unset($emails[0]);

				$subject = "Your ".$order['name']." Paid Successfully";
				$invoice->sendEmail($to_email,$subject,$email_body,$emails);
				
				$order->setStatus('submitted');

			    $this->api->forget('checkout_order');
			    $this->api->redirect($this->api->url(null,array('step'=>4,'pay_now'=>true,'paid'=>true)));
			    exit;
			}

			// Step 1. initiate purchase ..
			try {
				//Sending $param with send function for passing value to gateway
				//dont know it's right way or no
			    $response = $gateway->purchase($params)->send($params);

			    if ($response->isSuccessful() /* OR COD */) {
			        // mark order as complete if not COD
			        // Not doing onsite transactions now ...
					$responsereturn=$response->getData();
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

		//Cart model
		// $cart=$this->add('xShop/Model_Cart');
		// $item=$this->add('xShop/Model_Item');
	}

	function step1(){
		$this->api->auth->model->id = 3;

		$this->add('View')->setHTML('<div class="atk-push"><span class="xcheckout-step label label-success">Step 1</span> / <span class=" label label-info">Step 2</span> / <span class=" xcheckout-step stepgray label label-default">Step 3</span> / <span class="xcheckout-step label label-default">Finish</span></div>')->addClass('text-center');
		$personal_form=$this->add('Form');
		$personal_form->setLayout(['view/form/'.$this->options['checkout-step1-form-layout']]);

		$customer = $this->add('xepan\commerce\Model_Customer');
		if($this->api->auth->model->id){
			$customer->addCondition('user_id',$this->api->auth->model->id);
			$customer->tryLoadAny();
		}
		$personal_form->setModel($customer,array('billing_address','billing_city','billing_state','billing_country','billing_pincode','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode'));
			
		//get all Field of billing address
		$f_b_address = $personal_form->getElement('billing_address')->validate('required|to_trim');
		$f_b_city = $personal_form->getElement('billing_city')->validate('required|to_trim');
		$f_b_state = $personal_form->getElement('billing_state')->validate('required|to_trim');
		$f_b_country = $personal_form->getElement('billing_country')->validate('required|to_trim');
		$f_b_pincode = $personal_form->getElement('billing_pincode')->validate('required|to_trim');
		$f_b_contact = $personal_form->addField('billing_contact')->validate('required|to_trim');
		$f_b_email = $personal_form->addField('billing_email')->validate('required|to_trim');

		// get all Field of shipping address
		$f_s_address = $personal_form->getElement('shipping_address')->validate('required|to_trim');
		$f_s_city = $personal_form->getElement('shipping_city')->validate('required|to_trim');
		$f_s_state = $personal_form->getElement('shipping_state')->validate('required|to_trim');
		$f_s_country = $personal_form->getElement('shipping_country')->validate('required|to_trim');
		$f_s_pincode = $personal_form->getElement('shipping_pincode')->validate('required|to_trim');
		$f_s_contact = $personal_form->addField('shipping_contact')->validate('required|to_trim');
		$f_s_email = $personal_form->addField('shipping_email')->validate('required|email');


		$personal_form->addField('Checkbox','i_read','<a target="_blank" href="index.php?page='.$this->options['xshop_checkout_tnc_subpage'].'">I have Read All trems & Conditions<a/>')->validate('required')->js(true)->closest('div.atk-form-row');

		$copy_address = $personal_form->add('Button')->set('Copy Address')->addClass('copy-address');
		$js = array(
				$f_s_address->js()->val($f_b_address->js()->val()),
				$f_s_city->js()->val($f_b_city->js()->val()),
				$f_s_state->js()->val($f_b_state->js()->val()),
				$f_s_country->js()->val($f_b_country->js()->val()),
				$f_s_pincode->js()->val($f_b_pincode->js()->val()),
				$f_s_contact->js()->val($f_b_contact->js()->val()),
				$f_s_email->js()->val($f_b_email->js()->val())
			);

		$copy_address->js('click',$js);

		$personal_form->addSubmit('Place Order');

		if($personal_form->isSubmitted()){
			if(!$personal_form['i_read'])
				$personal_form->displayError('i_read','It is Must');

			
			$billing_detail = [
							'billing_address' => $personal_form['billing_address'],
							'billing_city'=>$personal_form['billing_city'],
							'billing_state'=>$personal_form['billing_state'],
							'billing_country'=>$personal_form['billing_country'],
							'billing_pincode'=>$personal_form['billing_pincode'],
							'billing_contact'=>$personal_form['billing_contact'],
							'billing_email' => $personal_form['billing_email'],

							'shipping_address' => $personal_form['shipping_address'],
							'shipping_city'=>$personal_form['shipping_city'],
							'shipping_state'=>$personal_form['shipping_state'],
							'shipping_country'=>$personal_form['shipping_country'],
							'shipping_pincode'=>$personal_form['shipping_pincode'],
							'shipping_contact'=>$personal_form['shipping_contact'],
							'shipping_email' => $personal_form['shipping_email'],
							];
			$order = $this->add('xepan\commerce\Model_SalesOrder');
			$order = $order->placeOrderFromCart($billing_detail);
			$this->order = $order;
			// Update order in session :: checkout_order
			$this->api->memorize('checkout_order',$order);
			
			//empty the cart after order has been placed successfully
			$this->add('xepan\commerce\Model_Cart')->emptyCart();

			$personal_form->owner->js(null,$personal_form->js()->univ()->successMessage("Update Personal Section Information"))->univ()->redirect($this->api->url(null,array('step'=>2)))->execute();
		}
	}

	function step2(){
		if(!($this->app->recall('checkout_order') instanceof \xepan\commerce\Model_SalesOrder))
			throw new \Exception("order not found");
			
		$order=$this->order = $this->app->recall('checkout_order');
		$this->order->reload();

		// add all active payment gateways
		$this->add('View')->setHTML('<div class="atk-push"><span class="xcheckout-step label label-success">Step 1</span> / <span class="xcheckout-step label label-success">Step 2</span> / <span class=" xcheckout-step stepgray label label-info">Step 3</span> / <span class="xcheckout-step label label-default">Finish</span></div>')->addClass('text-center');
		$pay_form=$this->add('Form');

		$payment_model=$this->add('xepan/commerce/Model_PaymentGateway');
		$payment_model->addCondition('is_active',true);
		
		$pay_gate_field = $pay_form->addField('xepan\base\Radio','payment_gateway_selected');
		$pay_gate_field->setImageField('gateway_image');
		$pay_gate_field->setModel($payment_model);
		
		$prev=$pay_form->add('Button')->set('Previous')->addClass('atk-swatch-tomato');//->js('click',$form->js()->submit());

		if($prev->isClicked()){
			$pay_form->owner->js(null,$pay_form->js())->univ()->redirect($this->api->url(null,array('step'=>1)))->execute();
		}


		$btn_label = $this->options['xshop_checkout_btn_label']?:'Proceed';
		
		$pay_form->addSubmit($btn_label);
		
		if($pay_form->isSubmitted()){
			if(!$pay_form['payment_gateway_selected'])
				throw new \Exception("must select payment gateway", 1);
				
			$order['paymentgateway_id'] = $pay_form['payment_gateway_selected'];
			$order->save();

			$this->js(null, $this->js()->univ()->successMessage('Order Placed Successfully'))
				->redirect($this->api->url(null,array('pay_now'=>'true','step'=>3)))->execute();
		}
	}

	function step3(){
		if(!($this->app->recall('checkout_order') instanceof \xepan\commerce\Model_SalesOrder))
			throw new \Exception("order not found");
			
		$order = $this->order = $this->app->recall('checkout_order');

		$this->order->reload();

		$message = "Payment Processed Successfully";

		$class="";
		$this->add('View')->setHTML('<div style="margin-bottom:30px;"class="atk-push"><span class="xcheckout-step label label-success">Step 1</span> / <span class="xcheckout-step label label-success">Step 2</span> / <span class=" xcheckout-step stepgray label label-success">Step 3</span> / <span class="xcheckout-step label label-info">Finish</span></div>')->addClass('text-center');
		//Payment Calceled 	by User from CCAvenue
		if($_GET['canceled'] == "true"){
			$message = "Payment Processed Canceled";
			$this->order->setStatus('OnlineCanceled');
			$class = "atk-box atk-align-center atk-size-giga atk-effect-danger";
			$_GET['pay_now'] = false;
		}

		$col = $this->add('Columns');
		$col->addColumn(3);
		$m = $col->addColumn(6);
		$m->add('View')->set($message)->addClass($class);

		$cont_shop_btn = $m->add('Button')->set('Continue Shopping');
		//Get Continue Shopping button url from config
		$cont_shop_btn->js('click')->univ()->location($this->api->url(null,array('subpage'=>'home')));
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