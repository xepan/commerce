<?php

 namespace xepan\commerce;

 class Model_RecurringInvoiceItem extends \xepan\commerce\Model_QSP_Detail{
 	
	function init(){
		parent::init();

		$this->addExpression('is_recurring')->set(function($m,$q){
			return $q->expr('IFNULL([0],0)',[$m->refSQL('item_id')->fieldQuery('is_renewable')]);
		})->type('boolean');

		$this->addExpression('renewable_value')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('renewable_value');
		});
		$this->addExpression('renewable_unit')->set(function($m,$q){
			return $m->refSQL('item_id')->fieldQuery('renewable_unit');
		});

		$this->addExpression('created_at')->set(function($m,$q){
			return $m->refSQL('qsp_master_id')->fieldQuery('created_at');
		});

		$this->addExpression('invoice_recurring_date')->set(function($m,$q){
			return $q->expr('DATE_ADD([invoice_created_at],INTERVAL [renewable_value] [renewable_unit])',['invoice_created_at'=>$m->getElement('created_at'),'renewable_value'=>$m->getElement('renewable_value'),'renewable_unit'=>'month']);
		})->type('datetime');

		$this->addExpression('invoice_recurring_date_only')->set(function($m,$q){
			return $q->expr('DATE_FORMAT([0], "%Y-%m-%d")',[$m->getElement('invoice_recurring_date')]);
		});

		$this->addCondition('is_recurring',true);
		$this->addCondition([['recurring_qsp_detail_id',0],['recurring_qsp_detail_id',null]]);
		$this->addCondition('invoice_recurring_date_only','<=',$this->app->today);
	}
}
 
    