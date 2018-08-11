<?php 
 namespace xepan\commerce;
 class page_customfield extends \xepan\commerce\page_configurationsidebar{

	public $title='Custom Fields';

	function init(){
		parent::init();
		$cf_model = $this->add('xepan\commerce\Model_Item_CustomField');
		$cf_model->setOrder('id','desc');
		$crud=$this->add('xepan\hr\CRUD');
		if($crud->form){
			$form = $crud->form;
			$form->add('xepan\base\Controller_FLC')
				->showLables(true)
				->addContentSpot()
				->layout([
						'name'=>'Item CustomField~c1~12',
						'display_type'=>'c2~6',
						'sequence_order'=>'c3~6~show in ascending order',
						'value'=>'c4~12~comma separated multiple value',
						'is_filterable~&nbsp;'=>'c5~6',
						'is_system~&nbsp;'=>'c6~6',
						'FormButtons~&nbsp;'=>'c7~6'
					]);
		}
		// if($crud->isEditing()){
		// 	$crud->form->setLayout('view\form\customfield');
		// }

		$crud->setModel($cf_model,['name','display_type','sequence_order','is_filterable','value','is_system']);
		$crud->grid->addPaginator(25);
		$crud->add('xepan\base\Controller_MultiDelete');
		$frm=$crud->grid->addQuickSearch(['name']);
		$frm_drop=$frm->addField('DropDown','display_type')->setValueList(['Line'=>'Line','DropDown'=>'DropDown','Color'=>'Color'])->setEmptyText('Display Type');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('applyFilter',function($frm,$m){
			if($frm['customfield_id'])
				$m->addCondition('customfield_id',$frm['customfield_id']);
		});

		$crud->grid->removeAttachment();
	}
} 