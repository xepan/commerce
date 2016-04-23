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

		/**
		category wise filter
		*/
		$item_join = $item->leftJoin('xshop_category_item.item_id','id');
		$item_join->addField('category_id');
		$item_join->addField('status');
		$item_join->addField('category_assos_item_id','item_id');

		if($_GET['xsnb_category_id'] and is_numeric($_GET['xsnb_category_id'])){
			$item->addCondition('status','Active');
			$item->addCondition('category_id',$_GET['xsnb_category_id']);
		}

		/**
		category filter
		*/
		$q = $item->dsql();
		$group_element = $q->expr('[0]',[$item->getElement('category_assos_item_id')]);
		
		// //Price Range Search
		// if(isset($_GET['xmip']) and is_numeric($_GET['xmip']) and $_GET['xmip']){
		// 	$item_model->addCondition('sale_price','>=',$_GET['xmip']);
		// }

		// if(isset($_GET['xmap']) and is_numeric($_GET['xmap']) and $_GET['xmap']){
		// 	$item_model->addCondition('sale_price','<=',$_GET['xmap']);
		// }

		// //Filter Search
		// if(isset($_GET['filter']) and $_GET['filter']){
		// 	$filter_data = explode(",", $_GET['filter']);
		// 	$array = [];
		// 	foreach ($filter_data as $junk) {
		// 		$temp = explode(":", $junk);
		// 		$array[$temp[0]] = explode("-", $temp[1]);
		// 	}

		// 	$selected_filter_data_array = $array;
	
		// 	$item_spec_j = $item_model->Join('customfield_association.item_id');
		// 	$item_spec_j->addField('customfield_generic_id');
		// 	$item_spec_j->addField('specification_item_id','item_id');
		// 	$item_spec_j->addField('value');

		// 	$cond=[];
		// 	foreach ($selected_filter_data_array as $key => $data) {
		// 		if($data == "" and !$data)
		// 			continue;
		// 		$temp = explode(":", $data);
		// 		$cond[] = $q->andExpr()
		// 							->where('specification_id',$temp[0])
		// 							->where('value','like','%'.$temp[1].'%');
		// 	}

		// 	$or_cond=$q->orExpr();
		// 	foreach ($cond as $and_conds) {
		// 		$or_cond->where($and_conds);
		// 	}

		// 	$item_model->addCondition($or_cond);

		// 	$group_element = $q->expr('[0]',[$item_model->getElement('specification_item_id')]);
		// }

		// $item_model->_dsql()->group($group_element); // Multiple category association shows multiple times item so .. grouped


		//load record according to sequence of order 
		$item->setOrder('display_sequence','desc');
		

		$cl = $this->add('CompleteLister',null,null,['view/tool/item/grid']);
		// $item->addExpression('base_url')->set('"http://localhost/xepan2/"');
		// $item->addExpression('item_detail_url')->set("'Todo'");
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

	function addToolCondition_row_item_detail_page_url($value,$l){
		$url = $this->api->url();
		$detail_page_url = $this->api->url($this->options['item_detail_page_url'],['commerce_item_id'=>$l->model->id]);

		if($this->options['name_redirect_to_detail'] == "true"){
			$l->current_row_html['item_detail_page_url_via_name'] = $detail_page_url;
		}else{			
			$l->current_row_html['item_detail_page_url_via_name'] = $url;
		}

		if($this->options['image_redirect_to_detail'] == "true")
			$l->current_row_html['item_detail_page_url_via_image'] = $detail_page_url;
		else
			$l->current_row_html['item_detail_page_url_via_image'] = $url;
			
	}

}