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
		$this->hasOne('xepan\commerce\Taxation','taxation_id');

		$this->addField('price')->caption('Rate');
		$this->addField('quantity');
		$this->addExpression('amount_excluding_tax')->set($this->dsql()->expr('([0]*[1])',[$this->getElement('price'),$this->getElement('quantity')]))->type('money');

		$this->addField('tax_percentage')->defaultvalue(0);
		$this->addExpression('tax_amount')->set($this->dsql()->expr('([0]*[1]/100.00)',[$this->getElement('amount_excluding_tax'),$this->getElement('tax_percentage')]))->type('money');		

		$this->addExpression('total_amount')->set(function($m,$q){
			return $q->expr('([0]+[1])',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);
		})->type('money');

		$this->addField('shipping_charge');
		$this->addField('narration')->type('text');
		$this->addField('extra_info')->type('text')->defaultvalue('{}'); // Custom Fields

		$this->addExpression('customer_id')->set($this->refSQL('qsp_master_id')->fieldQuery('contact_id'));
		$this->addExpression('customer')->set($this->refSQL('qsp_master_id')->fieldQuery('contact'));

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('name');
		});

		$this->addExpression('qsp_status')->set($this->refSQL('qsp_master_id')->fieldQuery('status'));
		$this->addExpression('qsp_type')->set($this->refSQL('qsp_master_id')->fieldQuery('type'));

		
		$this->addHook('beforeSave',$this);
		$this->addHook('afterInsert',$this);
		$this->addHook('beforeDelete',$this);
	}

	function beforeSave(){
		
		//fire only when qspmaster is order
		if($this->loaded() and $this->isDirty('quantity') and $this['qsp_type'] == "SalesOrder")
			$this->app->hook('qsp_detail_qty_changed',[$this]);
	}

	function afterInsert($model,$id){
		$qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail')->load($id);

		if($qsp_detail['qsp_type'] == "SalesOrder")
			$this->app->hook('qsp_detail_insert',[$qsp_detail]);
	}

	function beforeDelete(){
		if($this->loaded() and $this['qsp_type'] == "SalesOrder")
			$this->app->hook('qsp_detail_delete',[$this]);
	}

	function item(){
		if(!$this['item_id'])
			throw $this->exception("can't load the item ")
	                   ->addMoreInfo('item id not found', $type);
			
		return $this->ref('item_id');
	}


	function firstProductionDepartment(){
		return $this->add('xepan\hr\Model_Department')
				->addCondition('id',$this->getProductionDepartment())
				->setOrder('production_level','asc')
				->setLimit(1)
				->tryLoadAny();
	}
	
	function getProductionDepartment(){
		return array_keys(json_decode($this['extra_info'],true));
	}

	function saleInvoice(){
		$m = $this->add('xepan\commerce\Model_SalesInvoice');
		return $m->load($this['qsp_master_id']);
	}

	function invoice($invoice=null){
		if($invoice){
			$this['invoice_id'] = $invoice->id;
			$this->save();
			return $invoice;
		}else{
			if(!$this['invoice_id']) return false;
			return $this->ref('invoice_id');
		}
	}

}