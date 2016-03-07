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
	
		$this->addField('qty');
		$this->addField('rate')->type('money');
		$this->addField('amount')->type('money');
		$this->addField('narration')->type('text');
		$this->addField('custom_fields')->type('text');
		//$this->addField('apply_tax')->type('boolean');

		// is required?
		// $this->addExpression('name'){refSQL('item_id')->fieldQuery('name');
		
		// $this->addExpression('unit')refSQL('item_id')->fieldQuery('qty_unit');
	
		// $this->addExpression('tax_per_sum')	caption('Total Tax %');

		// $this->addExpression('tax_amount')

		// $this->addExpression('texted_amount')

		
	}
}
