<?php

namespace xepan\commerce;

class Model_QSP_ItemSaleReport extends \xepan\commerce\Model_Item{
	public $from_date=NULL;
	public $to_date=NULL;

	function init(){
		parent ::init();

		// $this->getElement('created_at')->caption('Invoice Date');
		// $this->addExpression('qsp_serial')->set($this->refSQL('qsp_master_id')->fieldQuery('serial'));
		// $this->addExpression('invoice_number')->set(function($m,$q){
		// 	return $q->expr('CONCAT(IFNULL([0],"")," ",IFNULL([1],""))',[$this->getElement('qsp_serial'),$this->getElement('qsp_master')]);
		// });
		// $this->addExpression('is_this_document_cancelled')->set(function($m,$q){
		// 	return $q->expr('IF([0]="Canceled","Yes","No")',[$m->getElement("qsp_status")]);
		// });
		// $this->addExpression('total_transaction_value')->set($this->refSQL('qsp_master_id')->fieldQuery('net_amount'));
		
		$this->getElement('nominal')->sortable(true);
		$this->addExpression('total_sales_invoice')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales_inv']);
			$qsp_details->addCondition('qsp_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			$qsp_details->addCondition('qsp_status',['Due','Paid']);

			if($this->from_date != "null" AND $this->from_date){
				$qsp_details->addCondition('created_at','>=',$this->from_date);
			}
			if($this->to_date != "null" AND $this->to_date)
				$qsp_details->addCondition('created_at','<',$this->app->nextDate($this->to_date));

			return $qsp_details->count();
		});

		$this->addExpression('total_sales_qty')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales_qty']);
			$qsp_details->addCondition('qsp_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			$qsp_details->addCondition('qsp_status',['Due','Paid']);

			if($this->from_date != "null" AND $this->from_date)
				$qsp_details->addCondition('created_at','>=',$this->from_date);
			if($this->to_date != "null" AND $this->to_date)
				$qsp_details->addCondition('created_at','<',$this->app->nextDate($this->to_date));

			return $qsp_details->_dsql()->del('fields')->field($q->expr('SUM([0])',[$qsp_details->getElement('effective_qty')]));
		});

		$this->addExpression('total_sales_amount')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales_amount']);
			$qsp_details->addCondition('qsp_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			$qsp_details->addCondition('qsp_status',['Due','Paid']);

			if($this->from_date != "null" AND $this->from_date)
				$qsp_details->addCondition('created_at','>=',$this->from_date);
			if($this->to_date != "null" AND $this->to_date)
				$qsp_details->addCondition('created_at','<',$this->app->nextDate($this->to_date));

			return $qsp_details->sum('total_amount');
		});
		$this->addExpression('total_sales_amount_excluding_tax')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales_ex_tax_amount']);
			$qsp_details->addCondition('qsp_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			$qsp_details->addCondition('qsp_status',['Due','Paid']);

			if($this->from_date != "null" AND $this->from_date)
				$qsp_details->addCondition('created_at','>=',$this->from_date);
			if($this->to_date != "null" AND $this->to_date)
				$qsp_details->addCondition('created_at','<',$this->app->nextDate($this->to_date));
			return $qsp_details->sum('amount_excluding_tax');
		});
		$this->addExpression('total_tax_amount')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'total_sales_amount']);
			$qsp_details->addCondition('qsp_type','SalesInvoice');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			$qsp_details->addCondition('qsp_status',['Due','Paid']);

			if($this->from_date != "null" AND $this->from_date)
				$qsp_details->addCondition('created_at','>=',$this->from_date);
			if($this->to_date != "null" AND $this->to_date)
				$qsp_details->addCondition('created_at','<',$this->app->nextDate($this->to_date));
			
			return $qsp_details->sum('tax_amount');
		});


	}
}