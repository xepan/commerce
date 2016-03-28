<?php

namespace xepan\commerce;

class Tool_ItemList extends \xepan\cms\View_Tool{
	public $options = [

					'show_name'=>true,
					'show_image'=>true,
					'show_sku'=>true,/* true, false*/
			 		'sku'=>"%",
					'show_sale_price'=>true,/* true, false*/
					'show_original_price'=>true,/* true, false*/
					'show_description'=>true, /*true, false*/ 
					'show_tags'=>true,/* true, false*/ 
					'show_Specification'=>true,
					'show_customfield_type'=>true,
					'show_qty_unit'=>true,
					'show_stock_availability'=>false,
					'show_is_enquiry_allow'=>false,
					'show_is_mostviewed'=>false


					// 'show_item_layout'=>'item_grid'
					// 'show_how_many_item in a row in grid'
					// 'zoom image'
					// 'detail page clicked on image'
					// 'detail page clicked on side anywhere'
					// 'total items on a one page website & paginator'
					// item detail sub page
					// open details in frame (Enquiry form)
					// sorting of item by date /asscending on their name/ code wise
					// personalize page on button click
					// add to cart page on button click
					// redmore.... link on description 


				];

	function init(){
		parent::init();

		$item = $this->add('xepan\commerce\Model_Item');
		// $item->addExpression('title_image')->set(function($m,$q){
		// 	return $m->refSQL('Attachments')->setOrder('id','asc')->setLimit(1)->fieldQuery('file_id');
		// });

		// $cl = $this->add('CompleteLister',null,null,['view/tool/'.$this->options['show_item_layout']]);
		$cl = $this->add('CompleteLister',null,null,['view/tool/item/grid']);
		$item->addExpression('file')->set(function($m){
			return $m->refSQL('Attachments')->setLimit(1)->fieldQuery('file');
		});
		$cl->setModel($item);

		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$item]);


		// echo "<pre>";
		// print_r($this->options);
		// exit;
		

		
	}

	function render(){

		$this->js(true)
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery-elevatezoom.js')
				->_load($this->api->url()->absolute()->getBaseURL().'vendor/xepan/commerce/templates/js/tool/jquery.fancybox.js');
		parent::render();

	}

	function defaultTemplate(){
		return ['view\tool\item\/'.$this->options['layout']]
	}


	function addToolCondition_is_new($model){
		$model->getElement('is_new')->destroy();
	}

	function addToolCondition_is_feature($model){
		$model->getElement('is_feature')->destroy();
	}

	function addToolCondition_is_mostviewed($model){
		$model->getElement('is_mostviewed')->destroy();
	}

	function addToolCondition_specification($model){
		$model->getElement('Specification')->destroy();
	}

	function addToolCondition_name($model){
		$model->getElement('name')->destroy();
	}
	
	function addToolCondition_sale_price($model){
		$model->getElement('sale_price')->destroy();
	}

	function addToolCondition_original_price($model){
		$model->getElement('original_price')->destroy();
	}

	function addToolCondition_image($model){
		$model->getElement('image')->destroy();
	}
}