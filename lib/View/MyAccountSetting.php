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
		$form->setModel($customer,array('address','city','state','country','pincode','billing_address','billing_city','billing_state','billing_country','billing_pincode','shipping_address','shipping_city','shipping_state','shipping_country','shipping_pincode'));
		$form->addSubmit('Update');
		if($form->isSubmitted()){
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