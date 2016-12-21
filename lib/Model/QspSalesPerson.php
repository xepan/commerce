<?php

namespace xepan\commerce;


/**
* 
*/
class Model_QspSalesPerson extends \xepan\base\Model_Table{
	public $table = "qsp_sales_person";
	function init(){
		parent::init();

		$this->hasOne('xepan\base\Contact','contact_id');
		$this->hasOne('xepan\commerce\QSP_Master','qsp_master_id');

	}
}