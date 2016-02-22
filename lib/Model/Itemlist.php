<?php

 namespace xepan\commerce;
 class Model_Itemlist extends \Model_Table{
 	public $table='item';

	function init(){
		parent::init();

		$this->hasOne('xepan\base\Epan','epan_id');
		
		// Basic Field
		$this->addField('name')->mandatory(true);
		$this->addField('code')->mandatory(true);
		$this->addField('original_price')->type('money')->mandatory(true);
		$this->addField('sales_price')->type('money')->mandatory(true);
		$this->addField('status')->enum(['Active','Inactive']);
		$this->addField('total_sale')->mandatory(true);
	}
}

