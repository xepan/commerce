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
		$this->hasOne('xepan\commerce\Model_Item_Template_Design','item_template_design_id');
		$this->hasOne('xepan\commerce\Model_Unit','qty_unit_id','name_with_group');

		$this->addField('price')->caption('Rate')->type('money');
		$this->addField('quantity')->defaultValue(1);

		// $this->addField('sale_amount'); // not included tax always
		// $this->addField('original_amount'); //not included tax always
		$this->addField('shipping_charge'); // not included tax always
		$this->addField('shipping_duration');
		$this->addField('express_shipping_charge'); //not included tax always
		$this->addField('express_shipping_duration');
		$this->addField('tax_percentage')->defaultvalue(0)->type('money');
		// $this->addExpression('qty_unit')->set($this->refSQL('item_id')->fieldQuery('qty_unit'));		
		$this->addExpression('is_shipping_inclusive_tax')->set($this->refSQL('qsp_master_id')->fieldQuery('is_shipping_inclusive_tax'))->type('boolean');
		$this->addExpression('amount_excluding_tax')
				->set($this->dsql()->expr('
					round((([price]*[quantity])+[shipping_charges]),2)',
					[
						"price"=>$this->getElement('price'),
						"quantity"=>$this->getElement('quantity'),
						"shipping_charges" => $this->getElement("shipping_charge")
					]))->type('money');
		
		$this->addExpression('amount_excluding_tax_and_shipping')
				->set($this->dsql()->expr('
					round((([price]*[quantity])),2)',
					[
						"price"=>$this->getElement('price'),
						"quantity"=>$this->getElement('quantity')
					]))->type('money');

		// $this->addField('discount')->type('money')->defaultValue(0) ;// if reversed due to tax on discounted or direct
		// effective amount = -discount(if tax on discounted) + shipping (if shipping taxable)

		$this->addExpression('tax_amount')
			->set($this->dsql()->expr('
				round((([price]*[quantity]+IF([is_shipping_inclusive_tax],[shipping_charges],0))*[tax_percentage]/100.00),2)',
					[
						"price"=>$this->getElement('price'),
						"quantity"=>$this->getElement('quantity'),
						"is_shipping_inclusive_tax" => $this->getElement('is_shipping_inclusive_tax'),
						"shipping_charges" => $this->getElement("shipping_charge"),
						"amount_excluding_tax"=>$this->getElement('amount_excluding_tax'),
						"tax_percentage" => $this->getElement('tax_percentage')
					]
				))->type('money');

		$this->addExpression('item_designer_id')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('designer_id');
		});	

		// total_amount = effective_amount+tax+discount(if not tax on discounted) + shipping (if shipping not taxable)
		$this->addExpression('total_amount')->set(function($m,$q){
			return $q->expr('([0]+[1])',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);
		})->type('money');
		
		$this->addField('narration')->type('text')->display(['form'=>'xepan\base\RichText'])->defaultValue(null);
		$this->addField('extra_info')->type('text')->defaultvalue('{}'); // Custom Fields

		$this->addExpression('customer_id')->set($this->refSQL('qsp_master_id')->fieldQuery('contact_id'));
		$this->addExpression('customer')->set($this->refSQL('qsp_master_id')->fieldQuery('contact'));

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('name');
		});

		$this->addExpression('item_qty_unit_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('qty_unit_id')]);
		});

		$this->addExpression('item_qty_unit')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('qty_unit')]);
		});

		$this->addExpression('qsp_status')->set($this->refSQL('qsp_master_id')->fieldQuery('status'));
		$this->addExpression('qsp_type')->set($this->refSQL('qsp_master_id')->fieldQuery('type'));
		$this->addExpression('sub_tax')->set($this->refSQL('taxation_id')->fieldQuery('sub_tax'));

		$this->is([
				'price|to_trim|required',
				'quantity|gt|0',
				'item_id|required'
			]);

		$this->addHook('beforeSave',$this);
		$this->addHook('afterInsert',$this);
		$this->addHook('afterSave',$this);
		$this->addHook('beforeDelete',$this);
		$this->addHook('afterDelete',$this);

		$this->hasMany("xepan\commerce\QSP_DetailAttachment",'qsp_detail_id',null,'Attachments');
		$this->hasMany("xepan\commerce\Store_TransactionRow",'qsp_detail_id',null,'StoreTransactionRows');
	}
	
	function afterSave(){

		$master = $this->add('xepan\commerce\Model_QSP_Master')
					->addCondition('type',$this['qsp_type'])
					->load($this['qsp_master_id']);
		$master->updateRoundAmount();
		$master->updateTnCTextifChanged();		
		if($this['qsp_type'] === "SalesInvoice"){
			$this->add('xepan\commerce\Model_SalesInvoice')->load($master->id)->updateTransaction();
		}
	}

	function beforeSave(){
		
		//fire only when qspmaster is order
		if($this->loaded() and $this->isDirty('quantity') and $this['qsp_type'] == "SalesOrder")
			$this->app->hook('qsp_detail_qty_changed',[$this]);
		
		if(!$this['extra_info']){
			$this['extra_info'] = "{}";
		}

		if($this['qty_unit_id']){
			$qsp_unit_model = $this->add('xepan\commerce\Model_Unit')->load($this['qty_unit_id']);
			$item_model = $this->add('xepan\commerce\Model_Item')->load($this['item_id']);
			if($item_model['qty_unit_group_id'] != $qsp_unit_model['unit_group_id'])
				throw $this->exception('unit must belong to item unit group','ValidityCheck')->setField('qty_unit_id');
		}else{
			throw $this->exception('quantity unit must not be empty','ValidityCheck')->setField('qty_unit_id');
		}
	}

	function afterInsert($model,$id){
		$qsp_detail = $this->add('xepan\commerce\Model_QSP_Detail')->load($id);

		if($qsp_detail['qsp_type'] == "SalesOrder")
			$this->app->hook('qsp_detail_insert',[$qsp_detail]);

		$master = $this->add('xepan\commerce\Model_QSP_Master')
					->addCondition('type',$qsp_detail['qsp_type'])
					->load($qsp_detail['qsp_master_id']);
		$master->updateTnCTextifChanged();
	}

	function beforeDelete(){
		if($this->loaded() and $this['qsp_type'] == "SalesOrder")
			$this->app->hook('qsp_detail_delete',[$this]);

		// $master = $this->add('xepan\commerce\Model_QSP_Master')
		// 			->addCondition('type',$this['qsp_type'])
		// 			->load($this['qsp_master_id']);
		// $master->updateTnCTextifChanged();
		// $master->save();
	}

	function afterDelete($m){
		$master = $this->add('xepan\commerce\Model_QSP_Master')
					->addCondition('type',$m['qsp_type'])
					->tryLoad($m['qsp_master_id']);
		if($master->loaded()){
			$master->updateTnCTextifChanged();
			$master->save();
		}
	}

	function item(){
		if(!$this['item_id'])
			throw $this->exception("can't load the item ")
	                   ->addMoreInfo('item id not found');
			
		return $this->ref('item_id');
	}

	function renewableService(){
		// $detail = $this->add('xepan\commerce\Model_QSP_Detail');

		// $detail->addExpression('is_renewable')->set(function($m,$q){
		// 	return $m->ref('item_id')
		// 	  		 ->addCondition('is_renewable',true);
		// });

		// $detail->addExpression('remind')->set(function($m,$q){
		// 	$extra_info = $m['extra_info'];
		// });
	}

	function firstProductionDepartment(){
		$production_department = $this->getProductionDepartment();

		if(!count($production_department))
			return false;
		
		$model =  $this->add('xepan\hr\Model_Department')
				->addCondition('id',$production_department)
				->setOrder('production_level','asc')
				->setLimit(1)
				->tryLoadAny();

		return $model;
	}
	
	function lastProductionDepartment($return_loaded=true){
		$dept = $this->add('xepan\hr\Model_Department')
				->addCondition('id',$this->getProductionDepartment())
				->setOrder('production_level','desc')
				->setLimit(1);

		if($return_loaded)
			return $dept->tryLoadAny();
		else
			return $dept;
	}

	function getProductionDepartment(){
		$array = [];
		if($this['extra_info'])
			$array = json_decode($this['extra_info'],true);
		return array_keys($array);
	}

	function saleOrder(){
		$m = $this->add('xepan\commerce\Model_SalesOrder');
		return $m->load($this['qsp_master_id']);
	}

	function saleInvoice(){
		$m = $this->add('xepan\commerce\Model_SalesInvoice');
		return $m->load($this['qsp_master_id']);
	}
	function purchaseInvoice(){
		$m = $this->add('xepan\commerce\Model_PurchaseInvoice');
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

	function convertCustomFieldToKey($custom_field,$use_only_stock_effect_cf=false){
		if(!$this->loaded())
			throw $this->exception('item model must loaded');
			
		if(!is_array($custom_field))
			throw new \Exception("must pass array of custom field");
		$item_model = $this->add('xepan\commerce\Model_Item')->load($this['item_id']);

		ksort($custom_field);
		$key = "";
		foreach ($custom_field as $dept_id => $cf_array) {
			if(isset($cf_array['department_name'])){
				unset($cf_array['department_name']);
			}

			ksort($cf_array);
			foreach ($cf_array as $cf_key => $data) {
				// get stock_effect_custom_field
				$dept_stock_effect_cf_array = $item_model->getAssociatedCustomFields($dept_id,true);
				
				if(!in_array($cf_key, $dept_stock_effect_cf_array))
					continue;

				$key .= $cf_key."~".trim($data['custom_field_name'])."<=>".$data['custom_field_value_id']."~".trim($data['custom_field_value_name'])."||";
			}
		}

		return $key?:0;
	}
}