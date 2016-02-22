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
		// $this->addField('is_publis')->type('boolean')->defaultValue(true);
		// $this->addField('is_party_publish')->type('boolean')->defaultValue(false);

		$this->addField('original_price')->type('money')->mandatory(true);
		$this->addField('sales_price')->type('money')->mandatory(true);
		// // $this->addField('short_description')->type('text');
		
		// // $this->addField('rank_weight')->defaultValue(0)->hint('Higher Rank Weight Item Display First')->mandatory(true);
		// // $this->addField('expiry_date')->type('date');
		// // $this->addField('description')->type('text');

		$this->addField('status')->enum(['Active','Inactive']);
		$this->addField('total_sale')->mandatory(true);
	}
}

