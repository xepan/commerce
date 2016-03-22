<?php

namespace xepan\commerce;

class Model_Item_Template_Design extends xepan\base\Model_Table{
	
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\base\Model_Contact','contcat_id');
	
		$this->addField('name');
		
		$this->addField('last_modified')->type('date')->defaultValue(date('Y-m-d'));
		$this->addField('is_ordered')->type('boolean')->defaultValue(false);
		$this->addField('designs')->type('text');
	}

	function afterSave(){
		$item = $this->ref('item_id');
		$item['is_publish']= false;
		$item['is_party_publish']= false;
		$item->save();
	}
}