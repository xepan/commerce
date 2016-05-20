<?php
namespace xepan\commerce;

class Model_Store_Delivered extends \xepan\commerce\Model_Store_Transaction{
	public $status = ['Shipped','Delivered','Return'];
	public $actions=[
				'Shipped'=>['view','edit','delete','delivered'],
				'Delivered'=>['view','edit','delete','return'],
				'Return'=>['view','edit','delete']
			];
	function init(){
		parent::init();
		
		$this->addCondition('document_type','Deliver');
	}

}