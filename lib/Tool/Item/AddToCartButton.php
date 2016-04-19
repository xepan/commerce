<?php

namespace xepan\commerce;

class Tool_Item_AddToCartButton extends \View{
	public $options=[];
	public $item_member_design;
	function init(){
		parent::init();

		$this->form = $form = $this->add('Form');
	}

	function setModel($model){
		
		$form = $this->form;

		$custom_fields = $model->activeAssociateCustomField();

		if($model['qty_from_set_only']){
			$qty_set_model = $this->add('xepan\commerce\Model_Item_Quantity_Set',['id_field'=>'qty']);
			$qty_set_model->addCondition('item_id',$model->id);
			$qty_set_model->setOrder('qty','asc');
			$qty_set_model->_dsql()->group('name');

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
		$addtocart_btn = $form->addSubmit($this->options['button_name']?:'Add To Cart');
		$getprice_btn = $form->addSubmit('get price')->addStyle('display','none');
		
		if(!$this->options['show_addtocart_button'])
			$addtocart_btn->addStyle('display','none');
		//change event handeling
		$form->on('change','select, input',$form->js()->submit());
		// $fields_qty->js('change',$getprice_btn->js(true)->trigger('click'));
		// $field_qty->js('change',$form->js()->submit());

		if($form->isSubmitted()){
			//get price according to selected custom field
			// $custom_field_array = [];
			$department_custom_field = [];
			$count = 1;
			foreach ($custom_fields as $custom_field) {
				// $custom_field_array[$custom_field['name']] = $form[$count];

				$department_id = $custom_field['department_id']?:0;

				if(!isset($department_custom_field[$department_id]))
					$department_custom_field[$department_id] = ['department_name'=>$custom_field['department']];

				if(!isset($department_custom_field[$department_id][$custom_field['customfield_generic_id']])){
					$value_id = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id',$custom_field->id)
									->tryLoadAny()->id;
					$temp = [
						"custom_field_name"=>$custom_field['name'],
						"custom_field_value_id"=>$value_id?$value_id:$form[$count],
						"custom_field_value_name"=>$form[$count],
						];
					$department_custom_field[$department_id][$custom_field['customfield_generic_id']] = $temp;
				}
				
				$count++;
			}
			
			//populate price according to selected customfield
			$price_array = $model->getAmount($department_custom_field,$form['qty']);

			//
			if($form->isClicked($addtocart_btn)){
				if(!$this->item_member_design)
					$this->item_member_design = 0;

				//selected custom field options array
				$other_fields=null;
				$file_upload_id=0;

				$cart = $this->add('xepan\commerce\Model_Cart');
								
				$cart->addItem($model->id,$form['qty'],$this->item_member_design,$department_custom_field,$price_array['shipping_charge'],$file_upload_id);
				$js = [$form->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')];
				$form->js(null,$js)->univ()->successMessage('Added to cart ' . $model['name'])->execute();
			}else{
				$js = [
						$form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-sale-price')->html($price_array['sale_amount']),
						$form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-original-price')->html($price_array['original_amount']),
						$form->js()->_selector('.xepan-commerce-item-image')
								->reload(
										[
											'commerce_item_id'=>$model->id],
											null,
											[
												$this->app->url(null,['custom_field'=>json_encode($department_custom_field)]),
												'cut_object'=>$this->js()->_selector('.xepan-commerce-item-image')->attr('id')
											]
										)
					];
				$form->js(null,$js)->execute();
			}
		}
		
		return parent::setModel($model);
	}


}