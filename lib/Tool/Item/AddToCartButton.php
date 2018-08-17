<?php

namespace xepan\commerce;

class Tool_Item_AddToCartButton extends \View{
	public $options=[
				"show_multi_step_form"=>false,
				"show_price"=>false, // show either unit price or total amount
				"form_layout"=>'stacked', // or vertical
				"show_original_price"=>true,
				"checkout_page"=>"index",
				"continue_shopping_page"=>"index",
				"success_message"=>"Added to cart successfully",
				"show_addtocart_button"=>true,
				"button_name"=>"Add to Cart",
				"show_shipping_charge"=>true,
				"shipping_charge_with_item_amount"=>false,
				"amount_group_in_multistepform"=>null,
				"show_buynowbtn"=>false,
				"pay_now_button_name"=>'Buy Now',
				"show_qty_input"=>true,
				"qty_label"=>"Qty"
				];
	public $item_member_design;
	function init(){
		parent::init();
		
		if($this->owner instanceof \AbstractController) return;
		
		$form_layout = "form/stacked";
		if( isset($this->options['form_layout']) and $this->options['form_layout'] === "vertical")
			$form_layout = "form";
		$this->form = $form = $this->add('Form',null,null,[$form_layout]);

	}

	function setModel($model){

		$this->model = $model;

		$default_currency_json_mdl = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'currency_id'=>'DropDown'
							],
					'config_key'=>'FIRM_DEFAULT_CURRENCY_ID',
					'application'=>'accounts'
			]);
		$default_currency_json_mdl->tryLoadAny();

		$defaultCurrency = $this->recall($this->app->epan->id.'_defaultCurrency',
						$this->memorize(
							$this->app->epan->id.'_defaultCurrency',
							$this->add('xepan\accounts\Model_Currency')->tryLoadBy('id',$default_currency_json_mdl['currency_id'])
							)
						);

		$form = $this->form;

		$custom_fields = $model->activeAssociateCustomField();
		$custom_fields->setOrder('order','asc');

		$groups = [];

		//price section added
		$fieldset = $groups[$this->options['amount_group_in_multistepform']];
		if($this->options['amount_group_in_multistepform'] && !isset($fieldset)){
			$fieldset = $groups[$this->options['amount_group_in_multistepform']] = $form->add('HtmlElement')->setElement('fieldset');
			$fieldset->add('HtmlElement')->setElement('legend')->set($this->options['amount_group_in_multistepform']);
			$price_view = $fieldset->add('View',null,null,['view/tool/addtocartbutton','amount_section']);
			$this->template->tryDel('amount_section');
		}else
			$this->template->tryDel('amount_section');

		//Populating custom fields
		// $price_added = 0;
		$count = 1;
		foreach ($custom_fields as $custom_field) {
			if(!isset($groups[$custom_field['group']])){
				$fieldset = $groups[$custom_field['group']] = $form->add('HtmlElement')->setElement('fieldset');
				$fieldset->add('HtmlElement')->setElement('legend')->set($custom_field['group']);
			}

			$fieldset = $groups[$custom_field['group']];
			if(strtolower($custom_field['display_type']) === "dropdown" ){
				$field = $fieldset->addField('xepan\commerce\DropDown',"f_".$count,$custom_field['name']);
				$field->setModel($this->add('xepan\commerce\Model_Item_CustomField_Value',['id_field'=>'name','title_field'=>'name'])->addCondition('customfield_association_id',$custom_field->id));
				$field->setEmptyText("Please Select");
				$field->addClass("required");
			}else if(strtolower($custom_field['display_type']) === 'color'){
				$field = $fieldset->addField('xepan\commerce\DropDown',"f_".$count,$custom_field['name']);
				$field->setModel($this->add('xepan\commerce\Model_Item_CustomField_Value',['id_field'=>'name'])->addCondition('customfield_association_id',$custom_field->id));
				
			}else if(strtolower($custom_field['display_type']) === "line"){
				$field = $fieldset->addField('Line',"f_".$count,$custom_field['name']);
				
			}else if(strtolower($custom_field['display_type']) === "date"){
				$field = $fieldset->addField('DatePicker',"f_".$count,$custom_field['name']);
				
			}else if(strtolower($custom_field['display_type']) === "dateandtime"){
				$field = $fieldset->addField('DateTimePicker',"f_".$count,$custom_field['name'])->set($this->app->now);
			}

			$count++;
		}

		// add Quantity Set in respective group
		$fieldset = $groups[$model['quantity_group']];
		if(!isset($fieldset)){
			// $fieldset = $form;
			$fieldset = $form->add('HtmlElement')->setElement('fieldset');
			$fieldset->add('HtmlElement')->setElement('legend')->set($model['quantity_group']);
		}
		
		if($this->options['show_qty_input']){
			$caption = $this->options['qty_label'];
			if($model['qty_from_set_only']){
				$qty_set_model = $this->add('xepan\commerce\Model_Item_Quantity_Set',['id_field'=>'qty']);
				$qty_set_model->addCondition('item_id',$model->id);
				$qty_set_model->setOrder('qty','asc');
				$qty_set_model->_dsql()->group('name');
				$field_qty = $fieldset->addField('xepan\commerce\DropDown','qty',$caption);
				$field_qty->setModel($qty_set_model);
				$field_qty->setEmptyText('Please Select');
			}else
				$field_qty = $fieldset->addField('Number','qty',$caption)->set(1);
			$field_qty->validate('required');
		}

		// add File Upload into respective groups

		if($model['is_allowuploadable'] and $model['upload_file_label']){

			$images_count = 0;
			$upload_array = explode(',', $model['upload_file_label']);
			$images_count = count($upload_array);
			if(!$images_count)
				return;

			// file upload hint

			$fieldset = $groups[$model['upload_file_group']];
			if(!isset($fieldset)){
				$fieldset = $form->add('HtmlElement')->setElement('fieldset');
				$fieldset->add('HtmlElement')->setElement('legend')->set($model['upload_file_group']);
			}

			$fieldset->add('View')->setHtml($model['item_specific_upload_hint']);
			foreach ($upload_array as $field_label) {

				$field_mandatory = explode(":", $field_label);
				$field_label = $field_mandatory[0];

				$field_name = $this->app->normalizeName($field_label);

				$multi_upload_field = $fieldset->addField('xepan\base\Upload',$field_name,$field_label)
						->allowMultiple(1)
						->setFormatFilesTemplate('view/tool/xepan_commerce_file_upload');
				// $multi_upload_field->setAttr('accept','.jpeg,.png,.jpg');
				$file_model = $this->add('xepan\filestore\Model_Image',['policy_add_new_type'=>true]);

				$multi_upload_field->setModel($file_model);
				$multi_upload_field->addClass('required');
				// $multi_upload_field->template->set('after_field','Max size: 500k');
			}
		}

		//submit button
		$getprice_btn = $form->addSubmit('get price')->addStyle('display','none')->addClass('btn-block btn btn-primary');

		$addtocart_btn = $form->addSubmit($this->options['button_name']?:'Add To Cart')->addClass('btn-block btn btn-primary');
		
		if(!$this->options['show_addtocart_button'])
			$addtocart_btn->addStyle('display','none');

		$paynow_btn = "";
		if($this->options['show_buynowbtn']){
			$paynow_btn = $form->addSubmit($this->options['pay_now_button_name']?:"Buy Now");
			
			$addtocart_btn->addStyle('display','none');
		}


		//change event handeling
		$form->on('change','select, input:not([type="file"])',$getprice_btn->js()->click());
		// $fields_qty->js('change',$getprice_btn->js(true)->trigger('click'));
		// $field_qty->js('change',$form->js()->submit());

		// Show modal popup
		$popup = $this->add('xepan\base\View_ModelPopup',['addSaveButton'=>false]);
		$popup->setTitle($this->options['success_message']?:"Added to cart successfully");
		$popup->addClass('xepan-commerce-itemadded-popup');
		
		$popup->add('View')->setHtml('<div class="xepan-cart-model-body alert alert-success"></div>');

		$continue_shopping_btn = $popup->add('Button',null,"footer")->set("Continue Shopping")->addClass(' btn btn-primary atk-button');
		$checkout_btn = $popup->add('Button',null,"footer")->set("Checkout")->addClass(' btn btn-primary atk-button');
		$continue_shopping_btn->js('click',$this->js()->univ()->redirect($this->app->url($this->options['continue_shopping_page'])));
		$checkout_btn->js('click',$this->js()->univ()->redirect($this->app->url($this->options['checkout_page'])));
	

		if($form->isSubmitted()){
			//get price according to selected custom field
			//$custom_field_array = [];

			$department_custom_field = $this->model->getProductionDepartment();
			$count = 1;
			foreach ($custom_fields as $custom_field) {
				// $custom_field_array[$custom_field['name']] = $form[$count];
				$department_id = $custom_field['department_id']?:0;

				if(!isset($department_custom_field[$department_id]))
					$department_custom_field[$department_id] = ['department_name'=>$custom_field['department']];

				if(!isset($department_custom_field[$department_id][$custom_field['customfield_generic_id']])){
					$value_id = $this->add('xepan\commerce\Model_Item_CustomField_Value')
									->addCondition('customfield_association_id',$custom_field->id)
									->addCondition('name',$form["f_".$count])
									->tryLoadAny()->id;
					$temp = [
						"custom_field_name"=>$custom_field['name'],
						"custom_field_value_id"=>$value_id?$value_id:$form["f_".$count],
						"custom_field_value_name"=>$form["f_".$count],
						];
					$department_custom_field[$department_id][$custom_field['customfield_generic_id']] = $temp;
				}
				
				$count++;
			}

			//populate price according to selected customfield
			$price_array = $model->getAmount($department_custom_field,$form['qty']);

			if($form->isClicked($addtocart_btn) OR $form->isClicked($paynow_btn)){

				$count = 1;
				foreach ($custom_fields as $custom_field) {
					// echo "id = ".$custom_field['id']." optional = ".$custom_field['is_optional']." foem value = ".$form["f_".$count];
					$field_name = "f_".$count;
					if(!$custom_field['is_optional']){
						if(!$form[$field_name]){
							$form->error($field_name,$custom_field['name']." is a mandatory ".$custom_field['is_optional']);
							break;
						}
					}
					$count++;
				}
				
				if(!$this->item_member_design)
					$this->item_member_design = 0;

				//selected custom field options array
				$other_fields=null;
				$file_upload_id=0;

				// Custom Field Uploaded Image management
				$upload_images_array = [];
				if(isset($upload_array)){
					foreach ($upload_array as $field_label) {
						
						$field_mandatory = explode(":", $field_label);
						$field_label = $field_mandatory[0];

						$field_name = $this->app->normalizeName($field_label);
						if( (isset($field_mandatory[1]) && $field_mandatory[1] == 'mandatory') && !$form[$field_name])
							$form->error($field_name,'mandatory');
						
						$upload_images_array[] = $form[$field_name];
					}
				}


				$cart = $this->add('xepan\commerce\Model_Cart');
				if($_GET['edit_cartitem_id']){
					$cart->deleteItem($_GET['edit_cartitem_id']);
				}

				// update cart values according single click sale
				$qty = $form['qty'];
				if($this->options['show_buynowbtn']){
					$cart->deleteAll();
				}

				if(!$this->options['show_qty_input'])
					$qty = 1;
				
				$cart->addItem($model->id,$qty,$this->item_member_design,$department_custom_field,$upload_images_array);
				
				$modal_body = 'Added to your cart : '.$model['name']. " with Quantity : ".$form['qty'];
				$js = [
						$form->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload'),
						$form->js()->_selector("#".$popup->name)->find('.xepan-cart-model-body')->text($modal_body),
						$form->js()->_selector("#".$popup->name)->modal()
					];

				if($this->options['show_buynowbtn']){
					$js = [$form->js()->univ()->redirect($this->app->url($this->options['checkout_page']))];
				}

				// add text after model popup render so used jquery here
				$form->js(true,$js)->execute();
				
				// $form->js(null,$js)->univ()->successMessage('Added into your cart ')->execute();
				// $form->js(null,$js)->execute();
			}else{

				// foreach ($price_array as $key => $value) {
				// 	if($key == "taxation"){
				// 		echo "<pre>";
				// 		print_r($value->getRows());
				// 		echo "</pre>";
				// 	}else
				// 		echo $key." = ".$value."<br/>";

				// }
				// die();
				//shipping price added on item amount if option setted from item list options
				if($this->options['show_shipping_charge'] and $this->options['shipping_charge_with_item_amount']){
					$price_array['sale_amount'] = $price_array['sale_amount'] + $price_array['shipping_charge'];
					$price_array['original_amount'] = $price_array['original_amount'] + $price_array['shipping_charge'];
				}

				$sale = $price_array['sale_amount'];
				$original = $price_array['original_amount']?:0;
				$shipping = $price_array['shipping_charge']?:0;
				

				if($this->options['show_price']){
					$sale = $price_array['raw_sale_price']?:0;
					$original = $price_array['raw_original_price']?:0;		
				}

				// $unit = "";
				// if($model['qty_unit']){
				// 	if($this->options['show_price'])
				// 		$unit .= "per ";
				// 	else
				// 		$unit .= $form['qty'];
				// 	$unit .= " ".$model['qty_unit'];
				// }

				if($defaultCurrency['icon'])
					$currency_icon = '<i class="'.$defaultCurrency['icon'].'"></i>';
				else
					$currency_icon = '<i>'.$defaultCurrency['name'].'</i>';

				// ." ".(($unit)?$unit:"")
				$sale = $currency_icon ." ". $sale;
				$shipping = $currency_icon ." ". $shipping;
				$original = $currency_icon ." ". $original;

				$js = [
						$form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-sale-price')->html($sale),
						$form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-shipping-charge')->html($shipping),
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
				// throw new \Exception($this->options['show_original_price']);
				
				if($this->options['show_original_price']){
					$js[] = $form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-original-price')->html($original);
				}else
					$js[] = $form->js()->closest('.xshop-item')->find('.xepan-commerce-tool-item-original-price')->hide();

				$form->js(null,$js)->execute();
			}
		}

		if(count($groups) > 1 or $this->options['show_multi_step_form']){
			$this->js(true)->find('form')->_load('tool/formToWizard')->formToWizard(array("submitButton"=>$addtocart_btn->js(true)->attr('id')));
		}else{
			$this->js(true)->find('form legend')->hide();
		}
		
		return parent::setModel($model);
	}

	function defaultTemplate(){
		return ['view/tool/addtocartbutton'];
	}
}