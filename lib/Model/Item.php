<?php

namespace xepan\commerce;

class Model_Item extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$item_j = $this->join('item.document_id');

		$item_j->hasone('xepan\commerce\itemdetail');

		$item_j->addField('price');
		$item_j->addField('TotalSale');

	}
}
