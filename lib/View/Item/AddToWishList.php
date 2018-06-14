<?php

namespace xepan\commerce;

class View_Item_AddToWishList extends \View{
	public $options=[
					'show_add_button'=>true,
					'button_name'=>'Add to wish list',
					'not_login_error_message'=>'login first to add in your wish',
					'not_customer_error_message'=>'you are not a customer',
					'success_message'=>'Added in your wish list',
				];
	
	public $model; // model actulay the item model

	function init(){
	parent::init();

			if($this->options['show_add_button']){
			$this->form = $this->add('Form');
		}
	}

	function setModel($model){
	$this->model = $model;
		
		if($this->options['show_add_button']){
			$this->form->addSubmit($this->options['button_name'])->setIcon(' fa fa fa-heart');

			if($this->form->isSubmitted()){

				if(!$this->app->auth->model->id){
					$this->form->js()->univ()->errorMessage($this->options['not_login_error_message'])->execute();
				}

				$this->customer = $customer = $this->add('xepan\commerce\Model_Customer');
		        $customer->loadLoggedIn("Customer");
		        if(!$customer->loaded()){
					$this->form->js()->univ()->errorMessage($this->options['not_customer_error_message'])->execute();
		        }

				$wish_model = $this->add('xepan\commerce\Model_Wishlist');
				$wish_model->addCondition('item_id',$model->id);
				$wish_model->addCondition('contact_id',$this->customer->id);
				$wish_model->addCondition('status','Due');
				$wish_model->tryLoadAny();

				if(!$wish_model->loaded())
					$wish_model->save();

				$this->form->js()->univ()->successMessage($this->options['success_message'])->execute();
			}
		}
		return parent::setModel($model);
	}
}