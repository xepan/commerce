<?php 
 namespace xepan\commerce;
 class page_item extends \Page{

	public $title='Items';

	function init(){
		parent::init();

		$item=$this->add('xepan\commerce\Model_Item');
		$condition = $this->app->stickyGet('condition');
		

		if($condition){
			$item->addCondition($condition,true);			
		}
		
		// $model_item=$this->add('xepan\commerce\Model_Item');
		// $saleable_count = $model_item->addCondition('is_saleable',true);


		$this->app->side_menu->addItem(['Saleable','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_saleable']));
		$this->app->side_menu->addItem(['Purchasable','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_purchasable']));
		$this->app->side_menu->addItem(['Productionable','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_productionable']));
		$this->app->side_menu->addItem(['AllowUploadable','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_allowuploadable']));
		$this->app->side_menu->addItem(['Template','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_template']));
		$this->app->side_menu->addItem(['Designable','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_designable']));
		$this->app->side_menu->addItem(['Dispatchable','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_dispatchable']));
		$this->app->side_menu->addItem(['Maintain Inventory','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'maintain_inventory']));
		$this->app->side_menu->addItem(['Allow Negative Stock','badge'=>[123,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'allow_negative_stock']));

		
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