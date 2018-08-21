<?php

namespace xepan\commerce;

class Model_QSP_Detail extends \xepan\base\Model_Table{

	public $table="qsp_detail";
	public $status = [];
	public $actions = [];
	public $acl = false;

	function init(){
		parent::init();
		
		$qsp_config = $this->add('xepan\commerce\Model_Config_QSPConfig');
		$qsp_config->tryLoadAny();

		$this->hasOne('xepan\commerce\QSP_Master','qsp_master_id');
		$this->hasOne('xepan\commerce\Item','item_id')->display(array('form'=>'xepan\commerce\Item'));
		$this->hasOne('xepan\commerce\Taxation','taxation_id');
		$this->hasOne('xepan\commerce\Model_Item_Template_Design','item_template_design_id');
		$this->hasOne('xepan\commerce\Model_Unit','qty_unit_id');

		$this->addField('price')->caption('Rate')->type('money');
		$this->addField('quantity')->defaultValue(1);
		$this->addField('discount')->type('money')->defaultValue(0);
		$this->addField('treat_sale_price_as_amount')->type('boolean')->defaultValue(0);
		// $this->addField('sale_amount'); // not included tax always
		// $this->addField('original_amount'); //not included tax always
		$this->addField('shipping_charge')->defaultValue(0); // not included tax always
		$this->addField('shipping_duration');
		$this->addField('express_shipping_charge')->defaultValue(0); //not included tax always
		$this->addField('express_shipping_duration');
		$this->addField('tax_percentage')->defaultValue(0)->type('money');
		// $this->addExpression('qty_unit')->set($this->refSQL('item_id')->fieldQuery('qty_unit'));		
		$this->addExpression('is_shipping_inclusive_tax')->set($this->refSQL('qsp_master_id')->fieldQuery('is_shipping_inclusive_tax'))->type('boolean');

		// in case for treat_sale_price_as_amount;
		$this->addExpression('effective_qty')->set(function($m,$q){
			return $q->expr('IF([treat_sale_price_as_amount]=1,1,[qty])',['treat_sale_price_as_amount'=>$m->getElement('treat_sale_price_as_amount'),'qty'=>$m->getElement('quantity')]);
		});


		$this->addExpression('amount_excluding_tax')
				->set(function($m,$q)use($qsp_config){
					$tax_on_discounted_amount = ($qsp_config['discount_per_item']?1:0);

					return $q->expr('
						round((([price]*[effective_qty])+[shipping_charges]-IF([tax_on_discounted_amount],IFNULL([discount],0),0)),2)',
						[
							"price"=>$m->getElement('price'),
							"effective_qty"=>$m->getElement('effective_qty'),
							"shipping_charges" => $m->getElement("shipping_charge"),
							"tax_on_discounted_amount"=>$tax_on_discounted_amount,
							"discount"=>$m->getElement('discount')
						]);

				})->type('money');
		
		$this->addExpression('amount_excluding_tax_and_shipping')
				->set($this->dsql()->expr('
					round((([price]*[effective_qty])),2)',
					[
						"price"=>$this->getElement('price'),
						"effective_qty"=>$this->getElement('effective_qty')
					]))->type('money');

		// $this->addField('discount')->type('money')->defaultValue(0) ;// if reversed due to tax on discounted or direct
		// effective amount = -discount(if tax on discounted) + shipping (if shipping taxable)

		$this->addExpression('shipping_amount')->set(function($m,$q){
			return $q->expr('IF([is_express],IFNULL([express_charge],0),IFNULL([shipping_charge],0))',[
								'is_express'=>$m->refSQL('qsp_master_id')->fieldQuery('is_express_shipping'),
								'express_charge'=>$m->getElement('express_shipping_charge'),
								'shipping_charge'=>$m->getElement('shipping_charge')
							]);
		});

		$this->addExpression('tax_amount')
			->set(function($m,$q)use($qsp_config){

				$tax_on_discounted_amount = (($qsp_config['discount_per_item']?1:0) * ($qsp_config['tax_on_discounted_amount']?1:0));
				return $q->expr('
					round((([price]*[effective_qty]+IF([is_shipping_inclusive_tax],[shipping_charges],0)-IF([tax_on_discounted_amount],[discount],0) )*[tax_percentage]/100.00),2)',
						[
							"price"=>$this->getElement('price'),
							"effective_qty"=>$this->getElement('effective_qty'),
							"is_shipping_inclusive_tax" => $this->getElement('is_shipping_inclusive_tax'),
							"shipping_charges" => $this->getElement("shipping_charge"),
							"amount_excluding_tax"=>$this->getElement('amount_excluding_tax'),
							"tax_percentage" => $this->getElement('tax_percentage'),
							"tax_on_discounted_amount"=>$tax_on_discounted_amount,
							"discount"=>$this->getElement('discount')
						]);
			})->type('money');

		$this->addExpression('item_designer_id')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('designer_id');
		});

		$this->addExpression('item_nominal_id')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('nominal_id');
		});

		$this->addExpression('item_purchase_nominal_id')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('purchase_nominal_id');
		});

		// total_amount = effective_amount+tax+discount(if not tax on discounted) + shipping (if shipping not taxable)
		$this->addExpression('total_amount')->set(function($m,$q){
			return $q->expr('([0]+[1])',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount')]);
			// return $q->expr('([0]+[1]-IFNULL([2],0))',[$m->getElement('amount_excluding_tax'),$m->getElement('tax_amount'),$m->getElement('discount')]);
		})->type('money');
		
		$this->addField('narration')->type('text')->display(['form'=>'xepan\base\RichText'])->defaultValue(null);
		$this->addField('extra_info')->type('text')->defaultvalue('{}'); // Custom Fields
		$this->addField('recurring_qsp_detail_id')->defaultvalue(0);
		$this->addExpression('customer_id')->set($this->refSQL('qsp_master_id')->fieldQuery('contact_id'));
		$this->addExpression('customer')->set($this->refSQL('qsp_master_id')->fieldQuery('contact'));

		$this->addExpression('name')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('name');
		});

		$this->addExpression('description')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('description');
		});

		$this->addExpression('item_qty_unit_id')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('qty_unit_id')]);
		});

		$this->addExpression('item_qty_unit')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('qty_unit')]);
		});

		$this->addExpression('item_sku')->set(function($m,$q){
			return $q->expr('[0]',[$m->refSQL('item_id')->fieldQuery('sku')]);
		});

		$this->addExpression('qsp_status')->set($this->refSQL('qsp_master_id')->fieldQuery('status'));
		$this->addExpression('qsp_type')->set($this->refSQL('qsp_master_id')->fieldQuery('type'));
		$this->addExpression('sub_tax')->set($this->refSQL('taxation_id')->fieldQuery('sub_tax'));
		$this->addExpression('hsn_sac')->set($this->refSQL('item_id')->fieldQuery('hsn_sac'));
		$this->addExpression('created_at')->set($this->refSQL('qsp_master_id')->fieldQuery('created_at'));

		$this->is([
				'price|to_trim|required',
				// 'quantity|gt|0',
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
			$this->consumeSerialNumber();
		}
	}

	function consumeSerialNumber(){

		$item = $this->add('xepan\commerce\Model_Item')->load($this['item_id']);
		if(!$item['is_serializable'])
			return;

		$serial_nos = $this->app->recall('serial_no_array')?$this->app->recall('serial_no_array'):[];

		$added_serial_no = $this->getSerialNos();
		$field_name = $this->getSerialFieldName();

		// echo "<pre>";
		// print_r($serial_nos);
		// print_r($added_serial_no);
		// print_r($field_name);
		// echo "</pre>";
		foreach ($serial_nos as $key => $no) {
			
			if(in_array($no, $added_serial_no)){
				unset($added_serial_no[$key]);
				// echo "old ".$no."<br/>";
				continue;
			}
				
			$serial_no_model = $this->add('xepan\commerce\Model_Item_Serial');
			$serial_no_model
					->addCondition('serial_no',$no)
					->addCondition('item_id',$this['item_id'])
					;
			$serial_no_model->tryLoadAny();
			if(!$serial_no_model->loaded() && !$serial_no_model['is_available']){
				throw new \Exception("Serial no ".$no." already used or not available");
			}

			$serial_no_model[$field_name['master']] = $this['qsp_master_id'];
			$serial_no_model[$field_name['detail']] = $this->id;
			$serial_no_model['is_available'] = false;
			$serial_no_model->save();

			// echo "new ".$serial_no_model['serial_no']."<br/>";
			// throw new \Exception("Error Processing Request", 1);
		}

		//unset from qsp_detail and sale invoice id unused serial no and make it available
		if(count($added_serial_no)){
			$s_no_ids = $this->getIdsOfSerialNos($added_serial_no);

			foreach ($s_no_ids as $key => $id) {
				$temp = $this->add('xepan\commerce\Model_Item_Serial')->tryLoad($id);
				$temp[$field_name['master']] = 0;
				$temp[$field_name['detail']] = 0; 
				$temp['is_available'] = true;
				$temp->save(); 
			}
		}
		$this->app->forget('serial_no_array');
	}

	function getIdsOfSerialNos($serial_no = []){
		$field_name = $this->getSerialFieldName();

		$serial_no_model = $this->add('xepan\commerce\Model_Item_Serial')
						->addCondition($field_name['detail'],$this->id)
						->addCondition($field_name['master'],$this['qsp_master_id'])
						;
		if(count($serial_no))
			$serial_no_model->addCondition('serial_no',$serial_no);
			
		$serial_no_model = $serial_no_model->_dsql()->del('fields')->field('id')->getAll();

		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($serial_no_model)),false);
	}

	function getSerialFieldName(){
		$data = [
				'SalesInvoice'=>['master'=>'sale_invoice_id','detail'=>'sale_invoice_detail_id'],
				'SalesOrder'=>['master'=>'sale_order_id','detail'=>'sale_invoice_detail_id'],
				'PurchaseOrder'=>['master'=>'purchase_order_detail_id','detail'=>'purchase_order_detail_id'],
				'PurchaseInvoice'=>['master'=>'purchase_invoice_id','detail'=>'purchase_invoice_detail_id'],
				'Store_Transaction'=>['master'=>'transaction_id','detail'=>'transaction_row_id'],
				'Store_DispatchRequest'=>['master'=>'dispatch_id','detail'=>'dispatch_row_id']
			];

		return $data[$this['qsp_type']];
	}

	function getSerialNos(){

		$field_name = $this->getSerialFieldName();

		$serial_no_model = $this->add('xepan\commerce\Model_Item_Serial')
						->addCondition($field_name['master'],$this['qsp_master_id'])
						->addCondition($field_name['detail'],$this->id)
						->_dsql()->del('fields')->field('serial_no')->getAll();

		return iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($serial_no_model)),false);
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

	// $qsp_master_type : purchaseOrder, purchaseInvoice, saleOrder, saleInvoice
	
	function purchaseOrderSerialItemCount(){
		if(!$this->loaded()) throw new \Exception("qsp detail model must loaded");

		$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
		return $serial_model
				->addCondition('item_id',$this['item_id'])
				->addCondition('purchase_order_id',$this['qsp_master_id'])
				->count()->getOne();

	}

	function purchaseInvoiceSerialItemCount(){
		if(!$this->loaded()) throw new \Exception("qsp detail model must loaded");
		
		$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
		return $serial_model
				->addCondition('item_id',$this['item_id'])
				->addCondition('purchase_invoice_id',$this['qsp_master_id'])
				->count()->getOne();

	}

	function saleOrderSerialItemCount(){
		if(!$this->loaded()) throw new \Exception("qsp detail model must loaded");

		$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
		return $serial_model
				->addCondition('item_id',$this['item_id'])
				->addCondition('sale_order_id',$this['qsp_master_id'])
				->count()->getOne();

	}

	function saleInvoiceSerialItemCount(){
		if(!$this->loaded()) throw new \Exception("qsp detail model must loaded");
		
		$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
		return $serial_model
				->addCondition('item_id',$this['item_id'])
				->addCondition('sale_invoice_id',$this['qsp_master_id'])
				->count()->getOne();

	}
}