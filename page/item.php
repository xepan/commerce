<?php 
 namespace xepan\commerce;
 class page_item extends \Page{

	public $title='Items';

	function init(){
		parent::init();

		$model_item=$this->add('xepan\commerce\Model_Item');
		
		// $saleable_count = $model_item->addCondition('is_saleable',true)->count()->getOne();
		
		// $this->app->side_menu->addItem(['Saleable','badge'=>$saleable_count,],$this->app->url('xepan_commerce_item',['condition'=>'saleable']));
		// $this->app->side_menu->addItem(['Purchasable','badge'=>$purchasable_count],$this->app->url('xepan_commerce_item',['condition'=>'purchasable']));
		// $this->app->side_menu->addItem(['Productionable','badge'=>$productionable_count],$this->app->url('xepan_commerce_item',['condition'=>'productionable']));
		// $this->app->side_menu->addItem(['AllowUploadable','badge'=>$allowuploadable_count],$this->app->url('xepan_commerce_item',['condition'=>'allowuploadable']));

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