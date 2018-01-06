<?php
namespace xepan\commerce;

class page_test extends \xepan\base\Page{
	

	function init(){
		parent::init();	

		

		$session_item = $this->add('Model',['table'=>'item']);
		$session_item->setSource('Session');
		$session_item->addField('from_warehouse_id');
	
		$session_item->addField('item')->display(['form'=>'xepan\commerce\Item'])->setModel('xepan\commerce\Model_Item');
		$session_item->addField('item_name');
		$session_item->addHook('afterLoad',function($m){$m['item_name'] = $this->add('xepan\commerce\Model_Item')->load($m['item'])->get('name'); });
	
		$session_item->addField('quantity')->type('number');
		$session_item->addField('extra_info')->type('text');

		// $session_item->addCondition('from_warehouse_id',$this->from_warehouse);

		$crud = $this->add('CRUD');
		$crud->setModel($session_item,['from_warehouse_id','item','quantity','extra_info'],['item_name','from_warehouse_id','quantity','extra_info']);

		if($crud->isEditing()){
			$crud->form->add('Button')->set('Extra Info')->addClass('extra-info');
		}
	}	
}
