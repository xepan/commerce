<?php 
 namespace xepan\commerce;
 class page_item extends \Page{

	public $title='Items';

	function init(){
		parent::init();

		$item=$this->add('xepan\commerce\Model_Item');
		
		$crud=$this->add('xepan\hr\CRUD',
						[
							'action_page'=>'xepan_commerce_itemtemplate',
							'edit_page'=>'xepan_commerce_itemdetail'
						],
						null,
						['view/item/grid']
					);

		$crud->setModel($item);
		$crud->grid->addPaginator(10);

		$frm=$crud->grid->addQuickSearch(['name']);

		$frm_drop=$frm->addField('DropDown','status')->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Reject'=>'Reject','Published'=>'Published'])->setEmptyText('Status');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['category_id'])
				$m->addCondition('item_id',$frm['item_id']);
			
			if($frm['status']='Active'){
				$m->addCondition('status','Active');
			}else{
				$m->addCondition('status','Inactive');

			}

		});
	}

}  