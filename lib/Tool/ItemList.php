<?php

namespace xepan\commerce;

class Tool_ItemList extends \xepan\cms\View_Tool{
	public $options = [

					// 'show_name'=>true,
					// 'show_image'=>true,
					// 'show_sku'=>true,/* true, false*/
			 	// 	'sku'=>"%",
					// 'show_sale_price'=>true,/* true, false*/
					// 'show_original_price'=>true,/* true, false*/
					// 'show_description'=>true, /*true, false*/ 
					// 'show_tags'=>true, true, false 
					// 'show_Specification'=>true,
					// 'show_customfield_type'=>true,
					// 'show_qty_unit'=>true,
					// 'show_stock_availability'=>false,
					// 'show_is_enquiry_allow'=>false,
					// 'show_is_mostviewed'=>false,
					// 'show_is_new'=>true,
					// 'show_paginator'=>true,
					// 'show_personalized'=>true,
					// 'personalized_page_url'=>'detail',
					// 'show_addtocart'=>true
					// 'personalized_button_name'=>'Designer'
					// 'layout'=>'grid',
					// 'base_url'
					// 'show_how_many_item in a row in grid'
					// 'detail page clicked on image'
					// 'detail page clicked on side anywhere'
					// item detail sub page
					// open details in frame (Enquiry form)
					// sorting of item by date /asscending on their name/ code wise
					// personalize page on button click
					// add to cart page on button click
					// redmore.... link on description 


				];

	function init(){
		parent::init();

		$item = $this->add('xepan\commerce\Model_Item_WebsiteDisplay');
		//load record according to sequence of order 
		$item->setOrder('display_sequence','desc');
		

		$cl = $this->add('CompleteLister',null,null,['view/tool/item/grid']);
		$item->addExpression('base_url')->set('"http://localhost/xepan2/"');
		$item->addExpression('item_detail_url')->set("'Todo'");
		//not record found
		if(!$item->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');


		$cl->setModel($item);

		if($this->options['show_paginator']){
			$paginator = $cl->add('Paginator');
			$paginator->setRowsPerPage(4);		
		}

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$item]);

		$self = $this;
		$url = $this->app->url($this->options['personalized_page_url']);
		// $url = $this->app->url($this->options['detail_page_url']);


		//click in personilize btn redirect to personilize pag

		$cl->on('click','.xshop-item-personalize',function($js,$data)use($url,$self){
			$url = $self->app->url($url,['xsnb_design_item_id'=>$data['xsnbitemid']]);
			return $js->univ()->location($url);
		});

		// $cl->on('click','.xshop-item-addtocart',function($js,$data){
		// 	// $cart = $this->add('xepan\commerce\Model_Cart');
		// 	// $cart->addItem();
		// 	return $js->univ()->successMessage($data['name']." added to cart");
		// });
	}

	function render(){

		$this->js(true)
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery-elevatezoom.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery.fancybox.js');
		
		parent::render();
	}

	function addToolCondition_show_is_new($value,$model){
		$model->addCondition('is_new',$value);
	}

	function addToolCondition_show_is_feature($value,$model){		
		$model->addCondition('is_feature',true)->setOrder('display_sequence','desc');

	}

	function addToolCondition_show_is_mostviewed($value,$model){
		$model->addCondition('is_mostviewed',true);
	}

	// function addToolCondition_show_is_saleable(){
	// 	throw new \Exception("Error Processing Request", 1);
		
	// }
	function addToolCondition_row_show_personalizedbtn($value,$l){
		
		if($l->model['is_designable']){
			$btn = $l->add('Button',null,'personalizedbtn')
				->addClass('xepan-commerce-personalized-btn')
				->setAttr('xsnbitemid',$l->model->id)
				;
			$btn->set($this->options['personalized_button_name']?:'Personilize');
			$l->current_row_html['personalizedbtn'] = $btn->getHtml();
		}else
			$l->current_row_html['personalizedbtn'] = "";

	}

	function addToolCondition_row_addtocart($value,$l){

		if($value != "yes"){
			$l->current_row_html['addtocart_wrapper'] = "";
			return;
		}

		if($l->model['is_saleable']){
			$options = [
						'button_name'=>$this->options['addtocart_name']
						];

			$cart_btn = $l->add('xepan\commerce\Tool_Item_AddToCartButton',
				[
					'name' => "addtocart_view_".$l->model->id,
					'options'=>$options
				],'Addtocart'
				);
			$item = $this->add('xepan\commerce\Model_Item')->load($l->model->id);
			$cart_btn->setModel($item);
			$l->current_row_html['Addtocart'] = $cart_btn->getHtml();
		}else
			$l->current_row_html['Addtocart'] = "";




	}

}