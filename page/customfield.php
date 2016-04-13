<?php 
 namespace xepan\commerce;
 class page_customfield extends \xepan\commerce\page_configurationsidebar{

	public $title='Custom Fields';

	function init(){
		parent::init();
		$cf_model = $this->add('xepan\commerce\Model_Item_CustomField');

		$crud=$this->add('xepan\hr\CRUD','null',null,['view/item/customfield']);

		$crud->setModel($cf_model);
		$crud->grid->addPaginator(25);

		$frm=$crud->grid->addQuickSearch(['name']);
		
		$frm_drop=$frm->addField('DropDown','display_type')->setValueList(['Line'=>'Line','DropDown'=>'DropDown','Color'=>'Color'])->setEmptyText('display_type');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['customfield_id'])
				$m->addCondition('customfield_id',$frm['customfield_id']);
		});
	}
} 