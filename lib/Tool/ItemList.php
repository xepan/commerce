<?php

namespace xepan\commerce;

class Tool_ItemList extends \xepan\cms\View_Tool{
	public $options = [

					'show_item'=>"all", /* all,new,mostviewed,featured*/
					'layout'=>'grid',/* grid,list*/
					/*show or hide options*/
					'show_name'=>true,
					'show_image'=>true,
					'show_sku'=>true,
					'show_sale_price'=>true,
					'show_original_price'=>true,
					'show_description'=>true, 
					'show_tags'=>true,
					'show_specification'=>true,
					'show_qty_unit'=>true,
					'show_qty_selection'=>true,
					'show_stock_availability'=>false,
					'show_is_enquiry_allow'=>false,
					'show_paginator'=>true,
					'show_personalizedbtn'=>true,
					'show_addtocart'=>true,
					/**/
					'personalized_page_url'=>'',
					'personalized_button_name'=>'Personalize',
					'paginator_set_rows_per_page'=>"4",
					'show_shipping_charge'=>true,
					'shipping_charge_with_item_amount'=>false,
					'show_item_of_category'=>""
				];

	function init(){
		parent::init();

		//Validate Required Options Value
		$message = $this->validateRequiredOptions();
		if($message != 1){
			$this->add('View_Warning')->set($message);
			return;
		}

		$item = $this->add('xepan\commerce\Model_Item_WebsiteDisplay');
		$q = $item->dsql();

		$this->app->stickyGET('xsnb_category_id');
		/**
		
		category wise filter

		*/
		//tool options show only category item
		$selected_category = [];
		if($this->options["show_item_of_category"]){
			$selected_category = explode(",", $this->options["show_item_of_category"]);
		}

		if($_GET['xsnb_category_id'] and is_numeric($_GET['xsnb_category_id'])){
			array_push($selected_category, $_GET['xsnb_category_id']);
		}

		if(count($selected_category)){
			$item_join = $item->Join('category_item_association.item_id');
			$item_join->addField('category_id');
			$item_join->addField('category_assos_item_id','item_id');
			
			$cat_join = $item_join->leftJoin('category.document_id','category_id');
			$cat_join->addField('category_document_id','document_id');

			$document_join = $cat_join->leftJoin('document.id','document_id');
			$document_join->addField('category_status','status');

			$item->addCondition('category_status',"Active");
			$item->addCondition('category_id',$selected_category);
			
			$group_element = $q->expr('[0]',[$item->getElement('category_assos_item_id')]);
		}

		if($_GET['search']){
			$item->addExpression('Relevance')->set('MATCH(search_string) AGAINST ("'.$_GET['search'].'" IN NATURAL LANGUAGE MODE)');
			$item->addCondition('Relevance','>',0);
	 		$item->setOrder('Relevance','Desc');
		}
		
		// //Price Range Search
		if($price_range = $this->app->recall('price_range')){
			$price_array = explode(",", $price_range);
			$item->addCondition('sale_price','>=',$price_array[0]);
			$item->addCondition('sale_price','<=',$price_array[1]);
		}

		
		// //Filter Search
		if($this->options['is_filterable'] and ($filter = $this->app->recall('filter',false))){
			$selected_filter_data_array = json_decode($filter,$filter);

			$item_custom_field_asso_j = $item->Join('customfield_association.item_id','id');
			$item_custom_field_asso_j->addField('customfield_generic_id');
			$item_custom_field_asso_j->addField('specification_item_id','item_id');

			$custom_field_j = $item_custom_field_asso_j->join('customfield_generic.id','customfield_generic_id');
			$custom_field_j->addField('cf_is_filterable','is_filterable');

			$item->addCondition('cf_is_filterable',true);

			$cf_asso_value_j = $item_custom_field_asso_j->join('customfield_value.customfield_association_id','id');
			$cf_asso_value_j->addField('value_name','name');
			$cf_asso_value_j->addField('value_status','status');

			$item->addCondition('value_status','Active');
			
			$cond=[];
			foreach ($selected_filter_data_array as $specification_id => $values_array) {
				if(empty($values_array)) continue;
				
				$or = $q->orExpr();
				foreach ($values_array as $value) {
					$or->where($q->expr('[0] = "[1]"',[$item->getElement('value_name'),$value]));
				}

				$item->addCondition($q->andExpr()
									->where('customfield_generic_id',$specification_id)
									->where($or)
									);
			}


			$group_element = $q->expr('[0]',[$item->getElement('specification_item_id')]);
			$item->_dsql()->group($group_element); // Multiple category association shows multiple times item so .. grouped

			$this->app->forget('filter');
		}


		//load record according to sequence of order 
		$item->setOrder('display_sequence','desc');	
		$cl = $this->add('CompleteLister',null,null,['view/tool/item/'.$this->options['layout']]);
		//not record found
		if(!$item->count()->getOne())
			$cl->template->set('not_found_message','No Record Found');
		else
			$cl->template->del('not_found');


		$cl->setModel($item);
		
		if($this->options['show_paginator']=="true"){
			$paginator = $cl->add('Paginator',['ipp'=>$this->options['paginator_set_rows_per_page']]);
			$paginator->setRowsPerPage($this->options['paginator_set_rows_per_page']);
		}


		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$item]);

		$self = $this;
		$url = $this->app->url($this->options['personalized_page_url']);
		//click in personilize btn redirect to personilize pag
		$cl->on('click','.xepan-commerce-item-personalize',function($js,$data)use($url,$self){
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

	function addToolCondition_show_item($value,$model){

		switch ($value) {
			case 'all':
				// No Need to any condition because on all it' show all item 
				// $model->addCondition(
				// 			$model->dsql()->orExpr()
				// 				->where('is_new',true)
				// 				->where('is_mostviewed',true)
				// 				->where('is_feature',true)
				// 			);
				break;
			case 'new':
				$model->addCondition('is_new',true);
				break;
			case 'mostviewed':
				$model->addCondition('is_mostviewed',true);
				break;
			case 'featured':
				$model->addCondition('is_feature',true);
				break;
		}
	}

	function addToolCondition_row_show_personalizedbtn($value,$l){
		
		if($l->model['is_designable']){
			$btn = $l->add('Button',null,'personalizedbtn')
				->addClass('xepan-commerce-item-personalize btn btn-primary btn-block')
				->setAttr('data-xsnbitemid',$l->model->id)
				;
			$btn->set($this->options['personalized_button_name']?:'Personalize');
			$l->current_row_html['personalizedbtn'] = $btn->getHtml();
		}else{
			$l->current_row_html['personalizedbtn'] = "";
			$l->current_row_html['personalizedbtn_wrapper'] = "";
		}
	}

	function addToolCondition_row_show_image($value, $l){
		if(!$value){
			$l->current_row_html['image_wrapper'] = "";
			return;
		}

		if(!$l->model['first_image'])
			$l->current_row['first_image'] = "vendor/xepan/commerce/templates/view/tool/item/images/xepan_item_list_no_image.jpg";
		
	}

	function addToolCondition_row_show_addtocart($value,$l){
		
		if($value != true){
			$l->current_row_html['addtocart_wrapper'] = "";
			return;
		}

		if($l->model['is_saleable']){
			$options = [
						'show_addtocart_button'=>'true',
						'button_name'=>$this->options['addtocart_name'],
						'show_shipping_charge'=>$this->options['show_shipping_charge'],
						'shipping_charge_with_item_amount'=>$this->options['shipping_charge_with_item_amount']
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

	function addToolCondition_row_show_specification($value,$l){
		
		if(!$value){
			$l->current_row_html['specification']='';
			return;
		}

		$specification = $l->model->specification(null,$highlight_only = true);
		$temp = $l->add('CompleteLister',null,'specification',['view/tool/item/'.$this->options['layout'],'specification']);
		$temp->setModel($specification);

		$l->current_row_html['specification'] = $temp->getHtml();
	}

	function addToolCondition_row_show_shipping_charge($value,$l){
		if(!$value){
			$l->current_row_html['shipping_charge'] = "";
			return;
		}

		if($this->options['shipping_charge_with_item_amount']){
			$l->current_row_html['shipping_charge'] = "";
			$l->current_row_html['shipping_charge_wrapper'] = "";
		}else
			$l->current_row_html['shipping_charge'] = "0";	

	}

	function validateRequiredOptions(){
		return true;
	}
}