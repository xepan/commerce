<?php

namespace xepan\commerce;

class Model_Item extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$item_j = $this->join('item.document_id');

		$item_j->hasone('xepan\commerce\itemdetail');

		$item_j->addField('price');
		$item_j->addField('TotalSale');

		$item_j->addField('name')->mandatory(true);
		$item_j->addField('code')->mandatory(true);
		$item_j->addField('original_price')->type('money')->mandatory(true);
		$item_j->addField('sales_price')->type('money')->mandatory(true);
		$item_j->addField('status')->enum(['Active','Inactive']);
		$item_j->addField('total_sale')->mandatory(true);

	}
} 

	

