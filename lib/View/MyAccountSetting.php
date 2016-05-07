<?php

namespace xepan\commerce;

class View_MyAccountSetting extends \View{
	public $options = [];

	function init(){
		parent::init();

		$customer = $this->add('xepan/commerce/Model_Customer');
		if(!$customer->loadLoggedIn()){
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

	// //================================Address======================
		$form=$this->add('Form',null,'address');
		$form->setLayout($this->options['address_form_layout']);

		$form->setModel($customer,array('address','city','state','country','pin_code','billing_address','billing_city','billing_state','billing_country','billing_pincode','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode','same_as_billing_address'));
		$form->addSubmit('Update');

		$same_billing_field = $form->getElement('same_as_billing_address');
		
		$field_b_address = $form->getElement('billing_address');
		$field_b_city = $form->getElement('billing_city');
		$field_b_state = $form->getElement('billing_state');
		$field_b_country = $form->getElement('billing_country');
		$field_b_pincode = $form->getElement('billing_pincode');

		$field_s_address = $form->getElement('shipping_address');
		$field_s_city = $form->getElement('shipping_city');
		$field_s_state = $form->getElement('shipping_state');
		$field_s_country = $form->getElement('shipping_country');
		$field_s_pincode = $form->getElement('shipping_pincode');
		
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
					&& ($form['billing_state']==$form['shipping_state'])
					&& ($form['billing_country']==$form['shipping_country'])
					&& ($form['billing_pincode']==$form['shipping_pincode'])
				  )
					return $this->js()->univ()->errorMessage('Billing and shipping address not same')->execute();							
				}

			$form->update();
			$this->js(null,$form->js()->univ()->successMessage('Update Information Successfully'))->reload()->execute();
		}
		

	// //===========================Deactivate=====================
		$deactive_form = $this->add('Form',null,'deactivate');
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