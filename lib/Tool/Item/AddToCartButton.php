<?php

namespace xepan\commerce;

class Tool_Item_AddToCartButton extends \View{
	public $options=[];

	function init(){
		parent::init();

		$this->form = $form = $this->add('Form');

	}

	function setModel($model){
		
		$form = $this->form;

		$custom_fields = $model->stockEffectCustomFields();

		// $field_price = $form->addField('Line','price');
		$field_qty = $form->addField('line','qty')->set(1);
		
		//Populating custom fields
		$count = 1;
		foreach ($custom_fields as $custom_field) {
			
			if($custom_field['display_type'] =="DropDown"){
				$field = $form->addField('xepan\commerce\DropDown',$count,$custom_field['name']);
				$field->setModel($this->add('xepan\commerce\Model_Item_CustomField_Value',['id_field'=>'name'])->addCondition('customfield_association_id',$custom_field->id));
				
			}else if($custom_field['display_type'] == 'color'){

			}else if($custom_field['display_type'] == "line"){

			}

			$count++;
		}

		//submit button
		$addtocart_btn = $form->addSubmit($this->options['button_name']);
		// $getprice_btn = $form->addSubmit('get price');

		//change event handeling
		$form->on('change','select, input',$form->js()->submit());
		// $fields_qty->js('change',$getprice_btn->js(true)->trigger('click'));
		// $field_qty->js('change',$form->js()->submit());

		if($form->isSubmitted()){

			//get price according to selected custom field
			$custom_field_array = [];
			$count = 1;
			foreach ($custom_fields as $custom_field) {
				 $custom_field_array[$custom_field['name']] = $form[$count];
				$count++;
			}

			//populate price according to selected customfield
			$price_array = $model->getAmount($custom_field_array,$form['qty']);

			//
			if($form->isClicked($addtocart_btn)){
				$item_member_design_id = 0;
				if($this->api->auth->model->id)
					$item_member_design_id = $this->api->auth->model->id;

				//selected custom field options array
				$other_fields=null;
				$file_upload_id=null;

				$cart = $this->add('xepan\commerce\Model_Cart');
				$cart->addItem($model->id,$form['qty'],$item_member_design_id,$custom_field_array,$other_fields,$file_upload_id);
				$js = [$form->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')];
				$form->js(null,$js)->univ()->successMessage('Added to cart ' . $model['name'])->execute();
			}else{
				$js = [
						$form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-sale-price')->html($price_array['sale_amount']),
						$form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-original-price')->html($price_array['original_amount']),
						// $form->getElement('price')->js()->val($price_array['sale_amount'])
					];

				$form->js(null,$js)->execute();
			}
			

		}
		return parent::setModel($model);
	}

}