<?php
namespace xepan\commerce;

class Tool_Designer extends \xepan\cms\View_Tool{
	public $options = [
						'watermark_text'=>'xepan',
						"show_addtocart_button"=>true,
						"show_original_price"=>false,
						"checkout_page"=>"index",
						"continue_shopping_page"=>"index",
						"success_message"=>"Added to cart successfully",
						"show_shipping_charge"=>false,
						"shipping_charge_with_item_amount"=>false,
						'amount_group_in_multistepform'=>null
					];

	function init(){
		parent::init();

		$item_member_design_id = $this->api->stickyGET('item_member_design');
		$item_id = $this->api->stickyGET('xsnb_design_item_id');
		$want_to_edit_template_item = $this->api->stickyGET('xsnb_design_template');
		$this->api->stickyGET('show_cart');
		$this->api->stickyGET('show_preview');
		

		//display cart tool
		if($_GET['show_cart'] and $item_id){
			$item = $this->add('xepan\commerce\Model_Item')->load($item_id);

			// display the name of item
			$this->template->trySet('name',$item['name']. " ( ".$item['sku']." ) ");

			$this->template->trySet('step1_class','xepan-designer-step-deactive');
			$this->template->trySet('step2_class','xepan-designer-step-deactive');
			$this->template->trySet('step3_class','xepan-designer-step-active');

			$this->template->tryDel('designer_tool_wrapper');
			$this->template->tryDel('design_preview_wrapper');

			$previous_button = $this->add('Button',null,'previous_button')->addClass('xepan-designer-previous-step-button');
			$previous_button->set('previous');
			if($previous_button->isclicked()){
				$this->api->stickyForget('show_cart');
				$this->js()->univ()->location($this->app->url(null,['show_preview'=>1]))->execute();
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
				$form_design_approved->js()->submit()->execute();
			}

			//load designs
			$model_template_design = $this->add('xepan\commerce\Model_Item_Template_Design');
			$model_template_design
					->addCondition('item_id',$item_id)
					->addCondition('id',$item_member_design_id)
					;
			
			$customer = $this->add('xepan\base\Model_Contact');
			$customer_logged_in = $customer->loadLoggedIn();
			
			if(!$model_template_design->count()->getOne() and $customer_logged_in){
				throw new \Exception("some thing happen wrong, design not found");
			}


			$this->add('xepan\commerce\Tool_Item_Designer',[
												'options'=>$this->options,
												'item_member_design'=>$item_member_design_id,
												'xsnb_design_item_id'=>$item_id,
												'printing_mode'=>false,
												'show_canvas'=>false,
												'is_start_call'=>1,
												'show_tool_bar'=>false,
												'show_pagelayout_bar'=>true,
												'show_layout_bar'=>false,
												'show_paginator'=>0
											],
									'design_preview');

			// $form_design_approved->addSubmit('Next');
			if($form_design_approved->isSubmitted()){
				$this->app->stickyForget('show_preview');
				$form_design_approved->js()->univ()->location($this->api->url(['show_cart'=>1]))->execute();
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

			if($next_btn->isclicked()){
				//check for the designed is saved or not
				if(!$item_member_design_id)
					$this->js()->univ()->errorMessage('save your design first')->execute();
				
				$template_design = $this->add('xepan/commerce/Model_Item_Template_Design')->tryLoad($item_member_design_id);
				if(!$template_design->loaded())
					$this->js()->univ()->errorMessage('member not found')->execute();
				
				$contact_model = $this->add('xepan\base\Model_Contact')->tryLoad($template_design['contact_id']);
				if(!$contact_model->loadLoggedIn())
					$this->js()->univ()->errorMessage('not authorize users')->execute();

				$this->js()->univ()->location($this->app->url(null,['show_preview'=>1]))->execute();
			}

			$designer_tool = $this->add('xepan\commerce\Tool_Item_Designer',['options'=>$this->options],'designer_tool');
		}
	}

	function defaultTemplate(){
		return ['view\tool\designer'];
	}
}