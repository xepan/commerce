<?php
namespace xepan\commerce;

class Tool_Designer extends \xepan\cms\View_Tool{
	public $options = [
						'watermark_text'=>'xepan',
						"show_addtocart_button"=>true,
						"show_original_price"=>false,
						"show_shipping_charge"=>false,
						"shipping_charge_with_item_amount"=>false,
						"show_qtyform"=>false,
						"show_multi_step_form"=>false,
						"show_price"=>false, // show either unit price or total amount
						"checkout_page"=>"index",
						"continue_shopping_page"=>"index",
						"success_message"=>"Added to cart successfully",
						"button_name"=>"Add to Cart",
						"show_qty_input"=>true,
						"qty_label"=>"Qty"
					];

	function init(){
		parent::init();

		$edit_cartitem_id = $this->app->stickyGET('edit_cartitem_id');
		$item_member_design_id = $this->api->stickyGET('item_member_design');
		$item_id = $this->api->stickyGET('xsnb_design_item_id');
		$want_to_edit_template_item = $this->api->stickyGET('xsnb_design_template');
		$this->api->stickyGET('show_cart');
		$this->api->stickyGET('show_preview');
		
		
		//display cart tool
		if($_GET['show_cart'] and $item_id){
			$item = $this->add('xepan\commerce\Model_Item')->load($item_id);

			// display the (name) of item
			$this->template->trySet('name',$item['name']);
			$this->template->trySet('sku',$item['sku']);

			$this->template->trySet('step1_class','xepan-designer-step-deactive');
			$this->template->trySet('step2_class','xepan-designer-step-deactive');
			$this->template->trySet('step3_class','xepan-designer-step-active');

			$this->template->tryDel('designer_tool_wrapper');
			$this->template->tryDel('design_preview_wrapper');

			$previous_button = $this->add('Button',null,'previous_button')->addClass('xepan-designer-previous-step-button');
			$previous_button->set('previous');
			if($previous_button->isclicked()){
				$this->api->stickyForget('show_cart');
				$this->js()->univ()->location($this->app->url(null,['show_preview'=>1,'edit_cartitem_id'=>$_GET['edit_cartitem_id']]))->execute();
			}

			$v = $this->add('View',null,'add_to_cart',['view/tool/designer/addtocart'])->addClass('xshop-item');
			$v1 = $v->add('View',null,'sale_price')->setElement('span')->addClass('xepan-commerce-tool-item-sale-price')->set($item['sale_price']);
			if($this->options['show_original_price']){
				$v2 = $v->add('View',null,'original_price')->addClass('xepan-commerce-tool-item-original-price')->set($item['original_price']);
			}else
				$this->template->tryDel('original_price');
			
			if($this->options['show_shipping_charge'] and !$this->options['shipping_charge_with_item_amount']){
				$v->add('View',null,'shipping_price')->setElement('span')->addClass('xepan-commerce-tool-item-shipping-charge')->set(0);
			}else
				$v->template->tryDel('shipping_price_wrapper');


			$cart_tool = $v->add('xepan\commerce\Tool_Item_AddToCartButton',[
																		'options'=>$this->options,
																		'item_member_design'=>$item_member_design_id
																	],'price_addtocart_tool');
			$cart_tool->setModel($item);
			$this->template->tryDel('next_button');

		}elseif ($_GET['show_preview']) {
			$this->template->trySet('step1_class','xepan-designer-step-deactive');
			$this->template->trySet('step2_class','xepan-designer-step-active');
			$this->template->trySet('step3_class','xepan-designer-step-deactive');

			$this->template->tryDel('designer_tool_wrapper');
			$this->template->tryDel('add_to_cart_wrapper');
			//next button for addto cart button
			$form_design_approved = $this->add('Form',null,'check_and_approved_design');
			$approved_checkbox = $form_design_approved->addField('checkbox','approved',$this->options['approved_design_checkbox_label']);
			$approved_checkbox->validate('required');
			
			$previous_button = $this->add('Button',null,'previous_button')->addClass('xepan-designer-previous-step-button');
			$previous_button->set('previous');
			if($previous_button->isclicked()){
				$this->app->stickyForget('show_preview');				
				$this->js()->univ()->location(
								$this->api->url(
											[
												'item_member_design'=>$item_member_design_id,
												'xsnb_design_item_id'=>$item_id,
												'xsnb_design_template'=>$want_to_edit_template_item,
											]
										))->execute();
			}

			$next_button = $this->add('Button',null,'next_button')->addClass('xepan-designer-next-step-button');
			$next_button->set('next');
			if($next_button->isclicked()){
				// throw new \Exception("first Next".$_GET['edit_cartitem_id'], 1);
				
				$form_design_approved->js()->submit()->execute();
			}

			//load designs
			// $item_model = $this->add('xepan\commerce\Model_Item')->tryLoad($item_id);
			// if(!$item_model->loaded()){
			// 	$this->add('View_Error',null,'design_preview')->set('Design Not loaded ');
			// 	return;
			// }

			$model_template_design = $this->add('xepan\commerce\Model_Item_Template_Design');
			$model_template_design
					->addCondition('item_id',$item_id)
					->addCondition('id',$item_member_design_id)
					;
			
			$customer = $this->add('xepan\base\Model_Contact');
			$customer_logged_in = $customer->loadLoggedIn("Customer");
			
			if(!$model_template_design->count()->getOne() and $customer_logged_in){
				throw new \Exception("some thing happen wrong, design not found");
			}


			$this->add('xepan\commerce\Tool_Item_Designer',[
												'options'=>$this->options,
												'item_member_design'=>$item_member_design_id,
												'xsnb_design_item_id'=>$item_id,
												'printing_mode'=>false,
												'show_canvas'=>false,
												'is_start_call'=>0,
												'show_tool_bar'=>false,
												'show_pagelayout_bar'=>true,
												'show_layout_bar'=>false,
												'show_paginator'=>0,
												'model'=>'primary',
												'is_preview_mode'=>1,
												'generating_image'=>true,
												'show_safe_zone'=>1
											],
									'design_preview');


			// $form_design_approved->addSubmit('Next');
			if($form_design_approved->isSubmitted()){
				$this->app->stickyForget('show_preview');
				$form_design_approved->js()->univ()->location($this->api->url(['show_cart'=>1,'edit_cartitem_id'=>$_GET['edit_cartitem_id']]))->execute();
			}

		}
		else{
			//add class
			$this->template->trySet('step1_class','xepan-designer-step-active');
			$this->template->trySet('step2_class','xepan-designer-step-deactive');
			$this->template->trySet('step3_class','xepan-designer-step-deactive');
			
			$this->template->tryDel('add_to_cart_wrapper');
			$this->template->tryDel('design_preview_wrapper');			
			//step 1
			$next_btn = $this->add('Button',null,'next_button')->addClass('xepan-designer-next-step-button');
			$next_btn->set('Next');
			$next_btn->js('click','if(design_dirty) {ev.stopImmediatePropagation(); alert("save your design first"); return false;}')->_enclose();
			
			if($next_btn->isclicked()){
				// throw new \Exception($_GET['edit_cartitem_id'], 1);
				
				//check for the designed is saved or not
				if(!$item_member_design_id)
					$this->js()->univ()->errorMessage('save your design first')->execute();
				
				$template_design = $this->add('xepan/commerce/Model_Item_Template_Design')->tryLoad($item_member_design_id);
				if(!$template_design->loaded())
					$this->js()->univ()->errorMessage('member not found')->execute();
				
				$contact_model = $this->add('xepan\base\Model_Contact')->tryLoad($template_design['contact_id']);
				if(!$contact_model->loadLoggedIn("Customer"))
					$this->js()->univ()->errorMessage('not authorize users')->execute();

				$this->js()->univ()->location($this->app->url(null,['show_preview'=>1,'edit_cartitem_id'=>$_GET['edit_cartitem_id']]))->execute();
			}

			$designer_tool = $this->add('xepan\commerce\Tool_Item_Designer',['options'=>$this->options],'designer_tool');
		}
	}

	function defaultTemplate(){
		return ['view\tool\designer'];
	}
}