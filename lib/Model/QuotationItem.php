<?php

namespace xepan\commerce;

class Model_QuotationItem extends \xepan\commerce\Model_Document{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
					'Draft'=>['view','edit','delete','submit'],
					'Submitted'=>['view','edit','delete','approve','redesign','reject'],
					'Approved'=>['view','edit','delete','redesign','reject','send'],
					'Redesign'=>['view','edit','delete','submit','reject'],
					'Rejected'=>['view','edit','delete'],
					'Converted'=>['view','edit','delete','send']
					];

	function init(){
		parent::init();

		$qitem_j = $this->join('document.document_id');

		$qitem_j->hasOne('xepan\base\Contact','contact_id');

		$qitem_j->hasOne('xepan\commerce\Quotation','quotation_id');
		$qitem_j->hasOne('xepan\commerce\Item_Saleable','item_id');
	
		$qitem_j->addField('qty');
		$qitem_j->addField('rate')->type('money');
		$qitem_j->addField('amount')->type('money');
		$qitem_j->addField('narration')->type('text');
		$qitem_j->addField('custom_fields')->type('text');
		//$qitem_j->addField('apply_tax')->type('boolean');

		// is required?
		// $this->addExpression('name'){refSQL('item_id')->fieldQuery('name');
		
		// $this->addExpression('unit')refSQL('item_id')->fieldQuery('qty_unit');
	
		// $this->addExpression('tax_per_sum')	caption('Total Tax %');

		// $this->addExpression('tax_amount')

		// $this->addExpression('texted_amount')

		
		$this->addCondtion('type','quotation');

	}
}
