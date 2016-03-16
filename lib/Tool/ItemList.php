<?php

namespace xepan\commerce;

class Tool_ItemList extends \xepan\cms\View_Tool{
	public $options = [

					'show_name'=>false,
					'show_sku'=>false,/* true, false*/
			 		'sku'=>"Not",
					'show_sale_price'=>false,/* true, false*/
					'show_original_price'=>false,/* true, false*/
					'show_description'=>false, /*true, false*/ 
					'description'=>"Not Available",
					'show_tags'=>true,/* true, false*/ 
					'tags'=>"Not Taged Yet",
					'show_Specification',
					'show_customfield_type'=>true,
					'show_qty_unit'=>true,
					'show_stock_availability'=>true,
					'show_is_enquiry_allow'=>true,
					'show_is_mostviewed'=>true,
					'show_is_new'=>true,
					'show_is_feature'=>true

					// 'show_template'=>if()

					// 'show_order'=>


				];

	function init(){
		parent::init();

		$item = $this->add('xepan\commerce\Model_Item');
		$item->addExpression('title_image')->set(function($m,$q){
			return $m->refSQL('Attachments')->setOrder('id','desc')->setLimit(1)->fieldQuery('file');
		});

		$cl = $this->add('CompleteLister',null,null,['view/tool/item_grid']);
		$cl->setModel($item);
		$cl->add('xepan\cms\Controller_Tool_Optionhelper',['options'=>$this->options,'model'=>$item]);

		
	}

	function render(){

		$this->js(true)->_load('tool/jquery-elevatezoom')
					->_load('tool/jquery.fancybox');
		parent::render();

	}

	// Tools options 

	function addToolCondition_name($value,$model){
		$model->getElement('name')->destroy();
		$model->addExpression('name')->set($value);
	}

	function addToolCondition_sku($value,$model){
		$model->getElement('sku')->destroy();
		$model->addExpression('sku')->set($value);
	}


	function addToolCondition_original_price($model){
		$model->getElement('original_price')->destroy();
		// $this->addExpression('original_price')->set('"'.$value.'"');
	}

	function addToolCondition_sale_price($model){
		$model->getElement('sale_price')->destroy();
		// $this->addExpression('sku')->set('"'.$value.'"');
	}

	function addToolCondition_is_enquiry_allow($model){
		$model->getElement('is_enquiry_allow')->destroy();
		// $this->addExpression('sku')->set('"'.$value.'"');
	}

	function addToolCondition_is_mostviewed($model){
		$model->getElement('is_mostviewed')->destroy();
		// $this->addExpression('sku')->set('"'.$value.'"');
	}

	function addToolCondition_is_new($model){
		$model->getElement('is_new')->destroy();
		// $this->addExpression('sku')->set('"'.$value.'"');
	}

	function addToolCondition_is_feature($model){
		$model->getElement('is_feature')->destroy();
		// $this->addExpression('sku')->set('"'.$value.'"');
	}

	function addToolCondition_specification($model){
		$model->getElement('Specification')->destroy();
		// $this->addExpression('sku')->set('"'.$value.'"');
	}

	function addToolCondition_tags($value,$model){
		$model->getElement('tags')->destroy();
		$model->addExpression('tags')->set('"'.$value.'"');
	}

	function addToolCondition_description($value,$model){
		$model->getElement('description')->destroy();
		$model->addExpression('description')->set('"'.$value.'"');
	}

}