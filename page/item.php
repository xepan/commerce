<?php 
 namespace xepan\commerce;
 class page_item extends \Page{

	public $title='Items';

	function init(){
		parent::init();

		$item=$this->add('xepan\commerce\Model_Item');

		$crud=$this->add('xepan\base\CRUD',
			[
				'action_page'=>'xepan_commerce_itemdetail',
				'grid_options'=>
					[
						'defaultTemplate'=>['grid/item']
					]
			]
		);

		$crud->setModel($item);
		$crud->grid->addQuickSearch(['name']);
	}

}  