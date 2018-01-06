<?php
namespace xepan\commerce;

class Model_Store_Item extends \xepan\commerce\Model_Item{

	public $title_field = 'name_with_detail';

	function init(){
		parent::init();

		$this->addExpression('name_with_detail')->set(function($m,$q){
			return $q->expr('CONCAT([0]," :: ",[1]," :: ",IFNULL([2]," "))',[$m->getElement('name'),$m->getElement('sku'),$m->getElement('hsn_sac')]);
		});
		
		$this->addCondition('maintain_inventory',true);
	}	
}