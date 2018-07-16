<?php

namespace xepan\commerce;

class View_MyAccountSetting extends \View{
	public $options = [];

	function init(){
		parent::init();

		$customer = $this->add('xepan/commerce/Model_Customer');

		if(!$customer->loadLoggedIn("Customer")){
			$this->add('View_Error')->set('Not Authorized');
			return;
		}

		$this->app->pathfinder->base_location->addRelativeLocation(
			'epan-components/xShop', array(
				'php'=>'lib',
				'template'=>'templates',
				'css'=>'templates/css',
				'js'=>'templates/js',
				)
			);

		$customer->addCondition('user_id',$this->api->auth->model->id);
		$customer->setLimit(1);
		$customer->tryLoadAny();

	//============================Change Password
		$user = $this->add('xepan\base\Model_User')->load($this->api->auth->model->id);

		$this->api->auth->addEncryptionHook($user);
		$this->add('View',null,'user_name')->set($user['email']);
		$change_pass_form = $this->add('Form',null,"change_password");
		$change_pass_form->setLayout('view\tool\myaccount\form\changepwd');
		$change_pass_form->addField('password','old_password')->validate('required');
		$change_pass_form->addField('password','new_password')->validate('required');
		$change_pass_form->addField('password','retype_password')->validate('required');
		$change_pass_form->addSubmit('Change Password');

		if($change_pass_form->isSubmitted()){
			if( $change_pass_form['new_password'] != $change_pass_form['retype_password'])
				$change_pass_form->displayError('old_password','Password not match');
			
			if(!$this->api->auth->verifyCredentials($user['username'],$change_pass_form['old_password']))
				$change_pass_form->displayError('old_password','Password not match');

			if($user->updatePassword($change_pass_form['new_password'])){

				if($this->options['keep-login-on-password-change'] or $this->options['keep-login-on-password-change']=="true"){
					$this->app->auth->logout();
					$this->app->redirect($this->options['login-page']);
				}

				$change_pass_form->js()->univ()->successMessage('Password Changed Successfully')->execute();
			}

		}

	//================================Address======================
		$form=$this->add('Form',null,'address');
		$form->setLayout('view\tool\myaccount\form\address');

		$form->setModel($customer,array('address','city','state_id','country_id','pin_code','billing_address','billing_city','billing_state_id','billing_country_id','billing_pincode','shipping_address','shipping_city','shipping_state_id','shipping_country_id','shipping_pincode','same_as_billing_address'));
		$form->addSubmit('Update');
		
		$field_state = $form->getElement('state_id');
		$field_country = $form->getElement('country_id');
		$field_country->getModel()->addCondition('status','Active');
		$field_state->dependsOn($field_country);

		// if($_GET['country_id']){
		// 	$field_state->getModel()->addCondition('country_id',$_GET['country_id'])
		// 							  ->addCondition('status','Active');	
		// }

		// $field_country->js('change',$field_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$field_state->name]),'country_id'=>$field_country->js()->val()]));


		$same_billing_field = $form->getElement('same_as_billing_address');
		
		$field_b_address = $form->getElement('billing_address');
		$field_b_city = $form->getElement('billing_city');
		$field_b_state = $form->getElement('billing_state_id');
		$field_b_country = $form->getElement('billing_country_id');
		$field_b_pincode = $form->getElement('billing_pincode');
		$field_b_country->getModel()->addCondition('status','Active');

		// $field_b_state->dependsOn($field_b_country);
		if($_GET['billing_country_id']){	
			$field_b_state->getModel()->addCondition('country_id',$_GET['billing_country_id'])
									  ->addCondition('status','Active');	
		}
		$field_b_country->js('change',$field_b_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$field_b_state->name]),'billing_country_id'=>$field_b_country->js()->val()]));
		
		$field_s_address = $form->getElement('shipping_address');
		$field_s_city = $form->getElement('shipping_city');
		$field_s_state = $form->getElement('shipping_state_id');
		$field_s_country = $form->getElement('shipping_country_id');
		$field_s_pincode = $form->getElement('shipping_pincode');
		$field_s_country->getModel()->addCondition('status','Active');
		
		// $field_s_state->dependsOn($field_s_country);
		if($_GET['shipping_country_id']){	
			$field_s_state->getModel()->addCondition('country_id',$_GET['shipping_country_id'])
									  ->addCondition('status','Active');	
		}
		$field_s_country->js('change',$field_s_state->js()->reload(null,null,[$this->app->url(null,['cut_object'=>$field_s_state->name]),'shipping_country_id'=>$field_s_country->js()->val()]));

		$js = array(
				$field_s_address->js()->val($field_b_address->js()->val()),
				$field_s_city->js()->val($field_b_city->js()->val()),
				$field_s_state->js()->val($field_b_state->js()->val()),
				$field_s_country->js()->val($field_b_country->js()->val()),
				$field_s_pincode->js()->val($field_b_pincode->js()->val())
				);

		$same_billing_field->js('change',$js);


		if($form->isSubmitted()){
			if($form['same_as_billing_address']){				
				if(!($form['billing_address']==$form['shipping_address'])
					&& ($form['billing_city']==$form['shipping_city'])
					&& ($form['billing_state_id']==$form['shipping_state_id'])
					&& ($form['billing_country_id']==$form['shipping_country_id'])
					&& ($form['billing_pincode']==$form['shipping_pincode'])
				  )
					return $this->js()->univ()->errorMessage('Billing and shipping address not same')->execute();							
				}

			$form->update();
			$this->js(null,$form->js()->univ()->successMessage('Update Information Successfully'))->reload()->execute();
		}
		

	// //===========================Deactivate=====================
		$deactive_form = $this->add('Form',null,'deactivate');
		$deactive_form->setLayout('view\tool\myaccount\form\deactivate');
		$deactive_form->addField('password','password')->validate('required');
		$deactive_form->addSubmit('Confirm Deactivation');
		if($deactive_form->isSubmitted()){
			if(!$this->api->auth->verifyCredentials($user['username'],$deactive_form['password']))
				$deactive_form->displayError('password','password must match');
			if($customer->deactivate()){
				$user['status'] = "Inactive";
				$user->save();

				$deactive_form->js(null,$this->js()->univ()->successMessage('deactivate successfully'))->univ()->redirect($deactive_form->app->url('logout'))->execute();				
			}				
		}
	}

	function defaultTemplate(){		
		return['view\\tool\\'.$this->options['customer-setting-layout']];
	}
}