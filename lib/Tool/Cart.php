<?php

namespace xepan\commerce;

class Tool_Cart extends \xepan\cms\View_Tool{
	public $options = [
					'layout'=>'short_cart',
					'show_image'=>true,
					"show_qtyform"=>true,
					"show_customfield"=>true,
					"show_design_edit"=>true,
					"show_round_amount"=>true,
					"show_discount_voucher"=>true,
					"checkout_page"=>"",
					"cart_detail_url"=>"",
					"designer_page_url"=>"",
					'show_express_shipping'=>false,
					'show_proceed_to_next_button'=>true,
					'show_cart_item_remove_button'=>true,
					'custom_template'=>'',
					'show_total_tax_amount'=>false
				];

	public $total_count=0;
	function init(){
		parent::init();
		// echo'<pre>';
		// print_r($this->options);
		// exit;	
		$message = $this->validateRequiredOption();
		if($message){
			$this->template->tryDel('lister');
			$this->template->tryDel('footer');
			$this->template->trySet('total_count',0);
			$this->add('View_Warning')->set($message);
			return;
		}
						
		$entered_discount_voucher = $this->app->recall('discount_voucher');
		$implement_express_shipping = $this->app->recall('express_shipping');

		$this->addClass('xshop-cart');
		$this->js('reload')->reload();

		$cart = $this->add('xepan\commerce\Model_Cart');
		$totals = $cart->getTotals();

		$this->total_count = $totals['total_item_count'];
		//if no record found then delete  other spot
		if(!$this->total_count){
			$this->template->trySet('not_found_message','shopping cart is empty');
			$this->template->tryDel('footer');
			$this->template->tryDel('lister');
			$this->template->trySet('total_count',0);
			return;
		}else
			$this->template->tryDel('not_found');

		$lister = $this->add('CompleteLister',null,'lister',["view/tool/cart/".$this->options['layout'],'lister']);
		$lister->setModel($cart);
		//discount voucher implementation

		// if($entered_discount_voucher){
		// 	$discount_voucher_model = $this->add('xepan\commerce\Model_DiscountVoucher')->tryLoadBy('name',$entered_discount_voucher);
			$discount_amount = $totals['row_discount'] + $totals['row_discount_shipping'] + $totals['row_discount_shipping_express'];

		// 	$total = 0;
		// 	if($discount_voucher_model->loaded()){
		// 		//get discount amount based on discount voucher condition
		// 		$discount_amount = $discount_voucher_model->getDiscountAmount($total_amount_raw, $sum_shipping_charge_raw);
		// 		//todo in future individual item wise with discount_voucher based_on condition
		// 	}
		// }
		
		// $net_amount = $total_amount + $sum_shipping_charge - $discount_amount;
		
		$default_currency = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'currency_id'=>'DropDown'
							],
					'config_key'=>'FIRM_DEFAULT_CURRENCY_ID',
					'application'=>'accounts'
			]);
		$default_currency->tryLoadAny();	
		

		$currency_m = $this->add('xepan\accounts\Model_Currency');
		$this->currency_model = $currency_m->load($default_currency['currency_id']);
		$this->template->trysetHTML('currency',$currency_m['name']);

		$this->template->trySet('total_count',$this->total_count?:0);
		$this->template->trySet('total_amount',number_format(round($totals['amount'],2),2));
		
		if($implement_express_shipping){
			$this->template->trySet('total_shipping_amount',number_format(round($totals['express_shipping_charge'],2),2));
			$this->template->trySet('gross_amount',number_format(round($totals['amount']  + $totals['express_shipping_charge'],2),2));
			$this->template->trySet('net_amount',$this->app->round($totals['amount']  + $totals['express_shipping_charge']));
		}else{
			$this->template->trySet('total_shipping_amount',number_format(round($totals['shipping_charge'],2),2));
			$this->template->trySet('gross_amount',number_format(round($totals['amount']  + $totals['shipping_charge'],2),2));
			$this->template->trySet('net_amount',$this->app->round($totals['amount']  + $totals['shipping_charge']));
		}
		$this->template->trySet('discount_amount',number_format(round($totals['row_discount'],2),2));
		
		$count = $this->total_count;

		//cart item remove action
		if($this->options['show_cart_item_remove_button']){
			
			$this->on('click','.xepan-commerce-cart-item-delete')->univ()->confirm('Are you sure?')
				->ajaxec(array(
	            	$this->app->url(),
	            	['remove_cart_item_id'=>$this->js()->_selectorThis()->data('cartid'),'remove_cart_item'=>1]

	        ));

			$remove_cart_item = $this->app->stickyGET('remove_cart_item');	
			$remove_cart_item_id = $this->app->stickyGET('remove_cart_item_id');	
			
			if($remove_cart_item){
				$count = $count - 1;
				$this->add('xepan\commerce\Model_Cart')->deleteItem($remove_cart_item_id);
				$js_event = [
					$this->js()->_selector('.xepan-commerce-cart-item-count')->html($count),
					$this->js()->closest('.xepan-commerce-tool-cart-item-row')->hide(),
					$this->js()->univ()->successMessage('removed successfully'),
					$this->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')
				];
				$this->js(null,$js_event)->execute();
			}

			// $this->on('click','.xepan-commerce-cart-item-delete',function($js,$data)use($count){
				
			// 	if($this->js()->univ()->confirm('Are you sure?')){
			// 		// return $js->univ()->successMessage('hello');
			// 		$count = $count - 1;
			// 		// $this->add('xepan\commerce\Model_Cart')->deleteItem($data['cartid']);
			// 		$js_event = [
			// 			$this->js()->_selector('.xepan-commerce-cart-item-count')->html($count),
			// 			$this->js()->closest('.xepan-commerce-tool-cart-item-row')->hide(),
			// 			$this->js()->univ()->successMessage('removed successfully'),
			// 			$this->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')
			// 		];
			// 		return $js_event;
			// 	}else{
			// 		$js()->univ()->consoleError('false');
			// 	}
			
			// 	// return $js->univ()->consoleError('good choice not delete');
			// 	// return $js->univ()->consoleError("false");
			// });
		}

		$cart_detail_url = $this->api->url($this->options['cart_detail_url']);
		$this->template->trySet('cart_detail_url',$cart_detail_url)	;
		
		// show or hide proceed to next button
		$checkout_page_url = $this->app->url($this->options['checkout_page']);
		if($this->options['show_proceed_to_next_button']){
			$this->on('click',".xepan-cart-proceed-to-next-btn",function($js,$data)use($checkout_page_url){
				return $js->redirect($checkout_page_url);
			});
		}else
			$this->template->tryDel('proceed_to_next_button_wrapper');
			
		// discount voucher 
		if(in_array($this->options['show_discount_voucher'],["true",true,"1",1])){
			$form = $this->add('Form',null,'discount_voucher',['form\empty']);
			$voucher_field = $form->addField('line','discount_voucher');
			// $voucher_field->validate('required');
			if($entered_discount_voucher)
				$voucher_field->set($entered_discount_voucher);
			if(!$discount_amount and $entered_discount_voucher)
				$voucher_field->belowField()->add('View')->addClass('text-danger')->set('Not Effective');

			$voucher_field->js('change',$form->js()->submit());
			if($form->isSubmitted()){
				if($form['discount_voucher'] == "" or is_null($form['discount_voucher'])){
					$this->app->forget('discount_voucher');
					$this->app->forget('discount_voucher_obj');
					$this->add('xepan\commerce\Model_Cart')->reloadCart();
					$this->js()->reload()->execute();
				}

				$discount_voucher = $this->add('xepan\commerce\Model_DiscountVoucher');
				$message = $discount_voucher->isVoucherUsable($form['discount_voucher']);
				if($message=="success"){
					$this->app->memorize('discount_voucher',$form['discount_voucher']);
					$this->app->memorize('discount_voucher_obj',$this->add('xepan\commerce\Model_DiscountVoucher')->tryLoadBy('name',$form['discount_voucher']));
					$this->add('xepan\commerce\Model_Cart')->reloadCart();
					$this->js()->reload()->execute();
				}else{
					$this->app->forget('discount_voucher');
					$this->app->forget('discount_voucher_obj');
					$form->displayError('discount_voucher',$message);
				}

			}

		}else{
			$this->template->tryDel('discount_voucher_wrapper');
		}

		// express shipping
		if(in_array($this->options['show_express_shipping'], ["true",1,"1",true])){
			$express_form = $this->add('Form',null,'express_shipping_checkbox',['form\empty']);
			$field_express_shipping = $express_form->addField('checkbox','express_shipping',"");

			if($implement_express_shipping){
				$field_express_shipping->set(true);
			}

			$field_express_shipping->js('change',$express_form->js()->submit());
			if($express_form->isSubmitted()){
				if($express_form['express_shipping'])
					$this->app->memorize('express_shipping',1);
				else
					$this->app->forget('express_shipping');

				$this->app->redirect();
			}
		}else
			$this->template->tryDel('express_shipping_wrapper');

		if($this->options['show_total_tax_amount']){
			$this->template->trySet('tax_amount',$totals['tax_amount']);
		}else
			$this->template->tryDel('tax_amount_wrapper');

		if($cart->hasItemMemberDssignId()){			
			$this->view_font = $this->add('xepan\commerce\View_Designer_FontCSS');
		}

		$lister->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$cart]);

	}

	function defaultTemplate(){
		$template_name =  $this->options['layout'];

		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/cart/".$this->options['custom_template'].".html";
			if(file_exists($path)){
				$template_name = $this->options['custom_template'];
			}
		}
		
		return ["view/tool/cart/".$template_name];
	}

	function addToolCondition_row_show_express_shipping($value,$l){

		$shipping_charge = $l->model['shipping_charge'];
		$duration = $l->model['shipping_duration'];

		if($this->app->recall('express_shipping')){
			$shipping_charge = $l->model['express_shipping_charge'];
			$duration = $l->model['express_shipping_duration'];
		}

		$l->current_row_html['shipping_charge_amount'] = number_format(round($shipping_charge,2),2);
		$l->current_row_html['shipping_duration_text'] = $duration;
		$l->current_row_html['currency'] = $this->currency_model['name'];
	}

	function addToolCondition_row_show_image($value,$l){
			//Image		
			if(!$value) return;

			$model = $l->model;
			// get preview image of editable items
			if($model['item_member_design_id']){															
				$l->current_row_html['image_wrapper'] = "";
				$unique_value = uniqid();
				$l->current_row['uniq_id'] = $unique_value;

				$this->js(true)
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/webfont.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/fabric.min.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/designer.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/jquery.colorpicker.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/cropper.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/designer/pace.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/addtocart.js')
					->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/slick.js')
					;

				$font_family_config_array = json_encode($this->view_font->getFontList());

				$design_m = $this->add('xepan\commerce\Model_Item_Template_Design')->load($model['item_member_design_id']);
				$design = json_decode($design_m['designs'],true);

				if(!$design_m['item_id'])
					return;

				$item = $design_m->ref('item_id');

				if(!$item->loaded())
					return;

				$specification = $item->getSpecification();				
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['width'],$temp);
				$specification['width']= $temp[1][0];
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['height'],$temp);
				$specification['height']= $temp[1][0];
				$specification['unit']=$temp[2][0];
				preg_match_all("/^([0-9]+)\s*([a-zA-Z]+)\s*$/", $specification['trim'],$temp);
				$specification['trim']= $temp[1][0];
				

				$this->js(true)->_selector('#canvas-cart-'.$unique_value)->xepan_xshopdesigner(
														array(
																'width'=>$specification['width'],
																'height'=>$specification['height'],
																'trim'=>$specification['trim']?:5,
																'unit'=> $specification['unit']?:'mm',
																'designer_mode'=> false,
																'design'=>json_encode($design['design']),
																'show_cart'=>0,
																'selected_layouts_for_print'=>$design['selected_layouts_for_print'],
																'item_id'=>$model['item_id'],
																'item_member_design_id' => $model['item_member_design_id'],
																'item_name' => $model['name'],
																'base_url'=> $this->api->url()->absolute()->getBaseURL(),
																'calendar_starting_month'=> $design['calendar_starting_month'],
																'calendar_starting_year'=> $design['calendar_starting_year'],
																'calendar_event'=> $design['calendar_event'],
																'printing_mode'=>false,
																'show_canvas'=>true,
																'is_start_call'=>1,
																'show_tool_bar'=>0,
																'show_pagelayout_bar'=>0,
																'show_tool_calendar_starting_month'=>0,
																'mode'=>'primary',
																'show_layout_bar'=>0,
																'make_static'=>true,
																'font_family_list'=>$font_family_config_array,
																'show_safe_zone'=>0
														));
			}else if($model['file_upload_ids'] AND count($file_ids = json_decode($model['file_upload_ids']))){
				$file_ids = json_decode($model['file_upload_ids']);
				
				$img_model = $this->add('xepan\filestore\Model_File')->load($file_ids[0]);
				$l->current_row_html['image_url'] = $img_model['url'];
				$l->current_row_html['canvas_wrapper'] = "";
				$l->current_row_html['upload_file_count'] = count($file_ids);
			}else{
				$thumb_url = $model->getImageUrl();
				$l->current_row_html['image_url'] = $thumb_url;
				$l->current_row_html['canvas_wrapper'] = "";
			}

	}

	function addToolCondition_row_show_customfield($value,$l){
		if(!$value){
			$l->current_row_html['custom_field'] = '';
			return;
		}
		
		$lister = $l->add('Lister',null,'custom_field',["view/tool/cart/".$this->options['layout'],'custom_field']);
		$name_value_array = [];
		foreach ($l->model['custom_fields'] as $junk) {

			foreach ($junk as $array) {
				if(!is_array($array) or !count($array))
					continue;

				$name_value_array[] = ['id'=>$array['custom_field_name'],'name'=>$array['custom_field_value_name']];
			}
		}
		$lister->setSource($name_value_array);
		$l->current_row_html['custom_field'] = $lister->getHtml();
	}

	function addToolCondition_row_show_round_amount($value,$l){
		if(!$value) return;

		$l->current_row_html['amount_including_tax'] = number_format(round($l->model['amount'],2),2);
		$l->current_row_html['unit_price'] = number_format(round($l->model['unit_price'],2),2);
	}

	function addToolCondition_row_show_cart_item_remove_button($value,$l){
		if(!$value){
			$l->current_row_html["cart_item_remove_button_wrapper"] = "";
			return;
		}

	}

	function addToolCondition_row_show_design_edit($value,$l){
		if(!$value){
			$l->current_row_html['design_edit_wrapper'] = "";
			return;
		}

		$model = $l->model;
		if(!$model['item_member_design_id']){
			$l->current_row_html['design_edit_wrapper'] = "";
			return;	
		}

		
		$edit_design_page_url = $this->app->url($this->options['designer_page_url'],
								[
									'xsnb_design_item_id'=>$model['item_id'],
									'item_member_design'=>$model['item_member_design_id'],
									'edit_cartitem_id'=>$model['id']
								]);
		$l->current_row_html['design_edit_url'] = $edit_design_page_url;
		
	}

	function addToolCondition_row_show_qtyform($value,$l){
		if(!$value) return;

		$form = $l->add('Form',['name'=>'frm_'.$l->model->id],'qty_form',['form/empty']);
		$form->addField('Hidden','cartid')->set($l->model->id);

		$model = $l->model;
		$item_model = $this->add('xepan/commerce/Model_Item')->load($model['item_id']);

		// if($this->layout == "short_cart"){
		// 	$field_qty = $form->addField('Number','qty')->set($model['qty']);	
		// }else{

			//  temporary todo shifted into options
			if($this->options['layout'] == "short_cart"){
				$field_qty = $form->addField('Readonly','qty')->set($model['qty']);
			}else{
				if($item_model['qty_from_set_only']){
						$field_qty = $form->addField('xepan\commerce\DropDown','qty');
						$field_qty->setModel($item_model->getQuantitySetOnly());
						$field_qty->set($model['qty']);
				}else
					$field_qty = $form->addField('Number','qty')->set($model['qty']);
				
				$field_qty->js('change',$form->js()->submit());
			}
			
		// }



		if($form->isSubmitted()){
			$cart = $this->add('xepan\commerce\Model_Cart')->load($form['cartid']);
			$cart->updateCart($form['cartid'],$form['qty']);
			$this->js()->_selector('.xepan-commerce-tool-cart')->trigger('reload')->execute();
			// $this->js()->_selector('.xshop-cart')->trigger('reload')->reload()->execute();
		}

		$l->current_row_html['qty_form'] = $form->getHtml();
	}

	function validateRequiredOption(){

		if( !trim($this->options['checkout_page'])){
			return "specify checkout page name";
		}
		
		if( !trim($this->options['cart_detail_url'])){
			return "specify cart detail page name";	
		}

		if($this->options['show_design_edit'] === "true" and !trim($this->options['designer_page_url'])){
			return "specify designer page name";					
		}

		if($this->options['custom_template']){
			$path = getcwd()."/websites/".$this->app->current_website_name."/www/view/tool/cart/".$this->options['custom_template'].".html";
			if(!file_exists($path)){
				return "custom template not found";
			}
		}

		return 0;
	}


}