<?php

namespace xepan\commerce;

class Model_QSP_Detail extends \xepan\base\Model_Table{
	public $table="qsp_detail";
	public $status = [];
	public $actions = [];
	public $acl = false;

	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\QSP_Master','qsp_master_id');
		$this->hasOne('xepan\commerce\Item','item_id')->display(array('form'=>'xepan\commerce\Item'));

		$this->addField('price')->caption('Rate');
		$this->addField('quantity');
		$this->addExpression('amount_excluding_tax')->set($this->dsql()->expr('([0]*[1])',[$this->getElement('price'),$this->getElement('quantity')]))->type('money');

		$this->addField('tax_percentage');
		$this->addExpression('tax_amount')->set($this->dsql()->expr('([0]*[1]/100.00)',[$this->getElement('amount_excluding_tax'),$this->getElement('tax_percentage')]))->type('money');

		$this->addExpression('total_amount')->set(function($m,$q){
			return $q->expr('([0]+[1])',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);
		})->type('money');

		$this->addField('shipping_charge');
		$this->addField('narration');
		$this->addField('extra_info')->type('text'); // Custom Fields

	}
}