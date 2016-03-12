<?php
 
namespace xepan\commerce;

class page_category extends \Page {
	public $title='Customer';

	function init(){
		parent::init();

		$category_model = $this->add('xepan\commerce\Model_Category');

		$crud = $this->add('xepan\hr\CRUD',
							null,
							null,
							['view/item/category']
						);

		$crud->setModel($category_model);
		$crud->grid->addPaginator(10);
		$crud->add('xepan\base\Controller_Avatar');

		$frm=$crud->grid->addQuickSearch(['name']);
		$frm_drop=$frm->addField('DropDown','status')->setValueList(['Active'=>'Active','Inactive'=>'Inactive'])->setEmptyText('Status');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['category_id'])
				$m->addCondition('category_id',$frm['category_id']);
			
			if($frm['status']='Active'){
				$m->addCondition('status','Active');
			}else{
				$m->addCondition('status','Inactive');

			}

		});

	}
}



























// <?php
//  namespace xepan\commerce;
//  class page_customerprofile extends \Page{
//  	public $title='Customer';

// 	function init(){
// 		parent::init();
// 	}

// 	function defaultTemplate(){

// 		return['page/customerprofile'];
// 	}
// }