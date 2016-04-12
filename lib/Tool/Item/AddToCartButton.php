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

		if($model['qty_from_set_only']){
			$qty_set_model = $this->add('xepan\commerce\Model_Item_Quantity_Set')->addCondition('item_id',$model->id)->tryLoadAny();//->dsql()->group('qty');
			
			$field_qty = $form->addField('xepan\commerce\DropDown','qty')->setModel($qty_set_model);
		}else
			$field_qty = $form->addField('Number','qty')->set(1);
		
		//Populating custom fields
		$count = 1;
		foreach ($custom_fields as $custom_field) {

			if($custom_field['display_type'] =="DropDown" ){
				$field = $form->addField('xepan\commerce\DropDown',$count,$custom_field['name']);
				$field->setModel($this->add('xepan\commerce\Model_Item_CustomField_Value',['id_field'=>'name'])->addCondition('customfield_association_id',$custom_field->id));
				
			}else if($custom_field['display_type'] == 'color'){
				$field = $form->addField('xepan\commerce\DropDown',$count,$custom_field['name']);
				$field->setModel($this->add('xepan\commerce\Model_Item_CustomField_Value',['id_field'=>'name'])->addCondition('customfield_association_id',$custom_field->id));
				
			}else if($custom_field['display_type'] == "line"){
				$field = $form->addField('Line',$count,$custom_field['name']);
				
			}

			$count++;
		}

		//submit button
		$addtocart_btn = $form->addSubmit($this->options['button_name']);
		$getprice_btn = $form->addSubmit('get price')->addStyle('display','none');

		//change event handeling
		$form->on('change','select, input',$form->js()->submit());
		// $fields_qty->js('change',$getprice_btn->js(true)->trigger('click'));
		// $field_qty->js('change',$form->js()->submit());

		if($form->isSubmitted()){

			//get price according to selected custom field
			$custom_field_array = [];
			$d_cf = [];
			$count = 1;
			foreach ($custom_fields as $custom_field) {
				$custom_field_array[$custom_field['name']] = $form[$count];

				$department_id = $custom_field['department_id']?:0;

				if(!isset($d_cf[$department_id]))
					$d_cf[$department_id] = ['department_name'=>$custom_field['department']];

				if(!isset($d_cf[$department_id][$custom_field['customfield_generic_id']])){
					$value_id = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id',$custom_field->id)
									->tryLoadAny()->id;
					$temp = [
						"custom_field_name"=>$custom_field['name'],
						"custom_field_value_id"=>$value_id,
						"custom_field_value_name"=>$form[$count],
						];
					$d_cf[$department_id][$custom_field['customfield_generic_id']] = $temp;
				}
				
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