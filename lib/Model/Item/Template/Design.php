<?php

namespace xepan\commerce;

class Model_Item_Template_Design extends \xepan\base\Model_Table{
	public $table = "item_template_design";
	function init(){
		parent::init();

		$this->hasOne('xepan\commerce\Item','item_id');
		$this->hasOne('xepan\base\Contact','contact_id');
	
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

	function duplicate($designer_id, $item_id, $item_design){
		$model_design = $this->add('xepan\commerce\Model_Item_Template_Design');
		
		$model_design['contact_id'] = $designer_id;
		$model_design['item_id'] = $item_id;
		$model_design['designs'] = $item_design;
		$model_design->save();
	}
}