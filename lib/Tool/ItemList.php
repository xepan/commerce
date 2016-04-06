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
					'show_is_mostviewed'=>false,
					'show_is_new'=>true,
					'show_paginator'=>true,
					'layout'=>'grid',

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

		$item = $this->add('xepan\commerce\Model_Item');
		$cl = $this->add('CompleteLister',null,null,['view/tool/item/grid']);
		$item->addExpression('base_url')->set('"http://localhost/xepan2/"');
		$item->addExpression('file')->set(function($m){
			return $m->refSQL('Attachments')->setLimit(1)->fieldQuery('file');
		});
		
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
}