<?php

namespace xepan\commerce;

class Tool_Cart extends \xepan\cms\View_Tool{
	public $options = [
					'layout'=>'short_cart',
					'show_name'=>true,
					'template'=>'short',
					'show_customfield'=>true,
					'image'=>'yes',
					"show_qtyform"=>true,
					"show_discount_voucher"=>false
				];
	public $total_count=0;
	function init(){
		parent::init();

		$entered_discount_voucher = $this->app->recall('discount_voucher');
		$this->addClass('xshop-cart');
		$this->js('reload')->reload();

		$cart = $this->add('xepan\commerce\Model_Cart');
		
		$lister=$this->add('CompleteLister',null,'lister',["view/tool/".$this->options['layout'],'lister']);
		$lister->setModel($cart);

		$sum_amount_excluding_tax=0;
		$sum_amount_including_tax=0;
		$sum_tax_amount=0;
		$sum_shipping_charge = 0;
		$discount_amount = 0;
		$net_amount=0;
		$count = 0;

		foreach ($cart as $item) {
			$sum_amount_excluding_tax += $item['amount_excluding_tax'];
			$sum_tax_amount += $item['tax_amount'];
			$sum_amount_including_tax += $item['amount_including_tax'];
			$sum_shipping_charge += $item['shipping_charge'];
			$count++;
		}

		if($entered_discount_voucher){
			$discount_voucher_model = $this->add('xepan\commerce\Model_DiscountVoucher')->loadBy('name',$entered_discount_voucher);
			$discount_amount = ($sum_amount_including_tax * $discount_voucher_model['discount_percentage'] /100);
		}
		
		$net_amount = $sum_amount_including_tax + $sum_shipping_charge - $discount_amount;

		$this->total_count = $count;



		$this->template->trySet('total_count',$this->total_count);
		$this->template->trySet('sum_amount_excluding_tax',$this->app->round($sum_amount_excluding_tax));
		$this->template->trySet('tax_amount',$this->app->round($sum_tax_amount));
		$this->template->trySet('net_amount',$this->app->round($net_amount));
		$this->template->trySet('gross_amount',$this->app->round($sum_amount_including_tax));
		$this->template->trySet('total_shipping_amount',$this->app->round($sum_shipping_charge));
		$this->template->trySet('discount_amount',$this->app->round($discount_amount));
		
		$count = $this->total_count;
		//if no record found then delete  other spot
		if(!$this->total_count){			
			$this->template->trySet('not_found_message','shopping cart is empty');
			$this->template->tryDel('footer');
			$this->template->tryDel('lister');
			return;
		}else
			$this->template->tryDel('not_found');
			
		$this->on('click','.xepan-commerce-cart-item-delete',function($js,$data)use($count){
			$count = $count - 1;
			$this->add('xepan\commerce\Model_Cart')->deleteItem($data['cartid']);
			$js_event = [
				$this->js()->_selector('.xepan-commerce-cart-item-count')->html($count),
				$js->closest('.xepan-commerce-tool-cart-item-row')->hide(),
				$this->js()->univ()->successMessage('removed successfully'),
				$this->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')
				// $this->js()->univ()->location($this->api->url())
			];
			return $js_event;
		});

		$cart_detail_url = $this->api->url($this->options['cart-detail-url']);
		$this->template->trySet('cart_detail_url',$cart_detail_url)	;
		
		$place_order_button = $this->add('View',null,'place_order')->set($this->options['place_order_button_name']);
		$place_order_button->js('click')->redirect($this->api->url($this->options['checkout_page']));

		if($this->options['show_discount_voucher'] == "true"){			
			$form = $this->add('Form',null,'discount_voucher',['form\empty']);
			$voucher_field = $form->addField('line','discount_voucher');
			// $voucher_field->validate('required');
			if($entered_discount_voucher)
				$voucher_field->set($entered_discount_voucher);

			$voucher_field->js('change',$form->js()->submit());
			if($form->isSubmitted()){
				if($form['discount_voucher'] == "" or is_null($form['discount_voucher'])){
					$this->app->forget('discount_voucher');
					$this->app->redirect();
				}

				$discount_voucher = $this->add('xepan\commerce\Model_DiscountVoucher');
				$message = $discount_voucher->isVoucherUsable($form['discount_voucher']);
				if($message=="success"){
					$this->app->memorize('discount_voucher',$form['discount_voucher']);
					$this->app->redirect();
				}else{
					$this->app->forget('discount_voucher');
					$form->displayError('discount_voucher',$message);
				}

			}

		}

		$lister->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$cart]);
	}

	function defaultTemplate(){
		return ["view/tool/".$this->options['layout']];
	}

	function addToolCondition_row_image($value,$l){
			//Image
			
			$model = $l->model;
			// get preview image of editable items
			if($model['item_member_design_id']){
				$thumb_url = $this->api->url('xepan_commerce_designer_thumbnail',
								[
									'xsnb_design_item_id'=>$model['item_id'],
									'item_member_design_id'=>$model['item_member_design_id'],
									'width'=>100
								]);
			}else if($model['file_upload_id']){
				$thumb_url = $model['file_upload_id'];
			}else
				$thumb_url = $model->getImageUrl();

			$l->current_row_html['image_url'] = $thumb_url;
	}

	function addToolCondition_row_show_customfield($value,$l){
		$lister = $l->add('Lister',null,'custom_field',["view/tool/".$this->options['layout'],'custom_field']);
		$name_value_array = [];
		foreach ($l->model['custom_fields'] as $junk) {
			foreach ($junk as $array) {
				if(!count($array))
					continue;
				$name_value_array[] = ['id'=>$array['custom_field_name'],'name'=>$array['custom_field_value_name']];
			}
		}
		$lister->setSource($name_value_array);
		$l->current_row_html['custom_field'] = $lister->getHtml();
	}

	function addToolCondition_row_show_round_amount($value,$l){

		$l->current_row_html['amount_including_tax'] = $this->app->round($l->model['amount_including_tax']);
		$l->current_row_html['amount_excluding_tax'] = $this->app->round($l->model['amount_excluding_tax']);
		$l->current_row_html['unit_price'] = $this->app->round($l->model['unit_price']);
	}

	function addToolCondition_row_show_design_edit($value,$l){
		$model = $l->model;
		if($value){
			$edit_design_page_url = $this->app->url($this->options['designer_page_url'],
									[
										'xsnb_design_item_id'=>$model['item_id'],
										'item_member_design'=>$model['item_member_design_id']
									]);
			$l->current_row_html['design_edit_url'] = $edit_design_page_url;

		}else{
			$l->current_row_html['design_edit_wrapper'] = "";
		}
		
	}

	function addToolCondition_row_show_qtyform($value,$l){
		
		$form = $l->add('Form',null,'qty_form',['form/empty']);
		$form->addField('Hidden','cartid')->set($l->model->id);

		$model = $l->model;
		$item_model = $this->add('xepan/commerce/Model_Item')->load($model['item_id']);

		if($item_model['qty_from_set_only']){
			$field_qty = $form->addField('xepan\commerce\DropDown','qty');
			$field_qty->setModel($item_model->getQuantitySetOnly());
			$field_qty->set($model['qty']);
		}else
			$field_qty = $form->addField('Number','qty')->set($model['qty']);

		$field_qty->js('change',$form->js()->submit());

		if($form->isSubmitted()){

			$cart = $this->add('xepan\commerce\Model_Cart')->load($form['cartid']);
			$cart->updateCart($form['cartid'],$form['qty']);
						
			$js = [
				$form->js()->univ()->location()
				];
			$form->js(null,$js)->execute();
		}

		$l->current_row_html['qty_form'] = $form->getHtml();
	}
}