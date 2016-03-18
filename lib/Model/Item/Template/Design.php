<?php

namespace xepan\commerce;

class Model_Item_Template_Design extends \xepan\hr\Model_Document{
	
	function init(){
		parent::init();

		$document_j = $this->join('item_template_design.document_id');
		$document_j->addField('name');

		$document_j->hasOne('xepan\commerce\Item','item_id');
		// $this->hasOne('xepan\base\Model_Customer','customer_id');
	
		$document_j->addField('last_modified')->type('date')->defaultValue(date('Y-m-d'));
		$document_j->addField('is_ordered')->type('boolean')->defaultValue(false);
		$document_j->addField('designs')->type('text');

	}

	function afterSave(){
		$item = $this->ref('item_id');
		$item['is_publish']= false;
		$item['is_party_publish']= false;
		$item->save();
	}
}