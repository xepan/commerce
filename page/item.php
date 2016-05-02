<?php 
 namespace xepan\commerce;
 class page_item extends \Page{

	public $title='Items';

	function init(){
		parent::init();

		$this->app->side_menu->addItem('Saleable','xepan_commerce_item');
		$this->app->side_menu->addItem('Purchasabele','xepan_commerce_item');
		$this->app->side_menu->addItem('Productionable','xepan_commerce_item');
		$this->app->side_menu->addItem('Allowuploadable','xepan_commerce_item');

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

		$crud->grid->addHook('formatRow',function($g){
			if(!$g->model['first_image']) $g->current_row['first_image']='../vendor/xepan/commerce/templates/view/item/20.jpg';
			if($g->model['original_price'] == $g->model['sale_price']) $g->current_row['original_price']=null;
		});

		

		$crud->grid->addPaginator(50);

		$frm=$crud->grid->addQuickSearch(['name']);

	}

}  