<?php

namespace xepan\commerce;

class Model_QuotationItem extends \xepan\base\Model_Table{

	public $table ="quotation_item";

	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
					'Draft'=>['view','edit','delete','submit'],
					'Submitted'=>['view','edit','delete','approve','redesign','reject'],
					'Approved'=>['view','edit','delete','redesign','reject','send'],
					'Redesign'=>['view','edit','delete','submit','reject'],
					'Rejected'=>['view','edit','delete'],
					'Converted'=>['view','edit','delete','send']
					];
	public $acl = false;

	function init(){
		parent::init();


		$this->hasOne('xepan\commerce\Quotation','quotation_id');
		$this->hasOne('xepan\commerce\Item_Saleable','item_id');
	
		
		$this->addField('price')->type('money');
		$this->addField('qty');
		$this->addExpression('amount_excluding_tax')->set('ROUND(price*qty,2)');
		$this->addField('tax_per')->type('money');
		$this->addExpression('tax_amount')->set('ROUND(price*qty*tax_per/100.00,2)');

		$this->addExpression('amount')->set(function($m,$q){
			return $q->expr('[0]+[1]',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);
		});


		$this->addField('custom_fields')->type('text');
		$this->addField('narration')->type('text');
		
	}
}
