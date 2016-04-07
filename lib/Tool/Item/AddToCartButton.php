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
		//TODO Populating custom fields
		foreach ($custom_fields as $custom_field) {
			
			if($custom_field['display_type'] =="DropDown"){
				$field = $form->addField('xepan\commerce\DropDown',$custom_field['name']);
				$field->setModel($this->add('xepan\commerce\Model_Item_CustomField_Value')->addCondition('customfield_association_id',$custom_field->id));
			}
			else if($custom_field['display_type'] == 'color'){

			}else if($custom_field['display_type'] == "line"){

			}

		}

		$form->addField('line','qty');

		$form->addSubmit($this->options['button_name']);

		if($form->isSubmitted()){

			$item_member_design_id = null;
			if($this->api->auth->model->id)
				$item_member_design_id = $this->api->auth->model->id;

			//selected custom field options array
			$custom_field_array = [];
			$other_fields=null;
			$file_upload_id=null;

			$cart = $this->add('xepan\commerce\Model_Cart');
			$cart->addItem($model->id,$form['qty'],$item_member_design_id,$custom_field_array,$other_fields,$file_upload_id);

			$form->js()->univ()->successMessage('Added to cart ' . $form['qty'])->execute();
		}
		return parent::setModel($model);
	}

}