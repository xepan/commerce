<?php 
 namespace xepan\commerce;
 class page_item extends \xepan\base\Page{

	public $title='Items';

	function init(){
		parent::init();

		$item=$this->add('xepan\commerce\Model_Item');
		$item->add('xepan\commerce\Controller_SideBarStatusFilter');
		$condition = $this->app->stickyGet('condition');
		
		if($status = $this->app->stickyGET('status')){
			$item->addCondition('status',$status);			
		}

		if($condition){
			$item->addCondition($condition,true);			
		}
		
		$model_item=$this->add('xepan\commerce\Model_Item');
		$saleable_count = $model_item->addCondition('is_saleable',true)->count();
		
		$model_item1=$this->add('xepan\commerce\Model_Item');
		$purchasable_count = $model_item1->addCondition('is_purchasable',true)->count();
		
		$model_item2=$this->add('xepan\commerce\Model_Item');
		$productionable_count = $model_item2->addCondition('is_productionable',true)->count();
		
		$model_item3=$this->add('xepan\commerce\Model_Item');
		$allowuploadable_count = $model_item3->addCondition('is_purchasable',true)->count();
		
		$model_item4=$this->add('xepan\commerce\Model_Item');
		$template_count = $model_item4->addCondition('is_template',true)->count();
		
		$model_item5=$this->add('xepan\commerce\Model_Item');
		$designable_count = $model_item5->addCondition('is_designable',true)->count();
		
		$model_item6=$this->add('xepan\commerce\Model_Item');
		$dispatchable_count = $model_item6->addCondition('is_dispatchable',true)->count();
		
		$model_item7=$this->add('xepan\commerce\Model_Item');
		$maintaininventory_count = $model_item7->addCondition('maintain_inventory',true)->count();
		
		$model_item8=$this->add('xepan\commerce\Model_Item');
		$allownewgativestock_count = $model_item8->addCondition('allow_negative_stock',true)->count();

		$this->app->side_menu->addItem(['Item Classification','icon'=>'fa fa-arrow-circle-down text-success']);
		$this->app->side_menu->addItem(['Saleable','icon'=>'fa fa-shopping-cart text-success','badge'=>[$saleable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_saleable']))->setAttr(['title'=>'Saleable Item']);
		$this->app->side_menu->addItem(['Purchasable','icon'=>'fa fa-cart-arrow-down text-danger','badge'=>[$purchasable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_purchasable']))->setAttr(['title'=>'Purchasable Item']);
		$this->app->side_menu->addItem(['Productionable','icon'=>'fa fa-industry text-primary','badge'=>[$productionable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_productionable']))->setAttr(['title'=>'Productionable Item']);
		$this->app->side_menu->addItem(['AllowUploadable','icon'=>'fa fa-upload text-warning','badge'=>[$allowuploadable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_allowuploadable']))->setAttr(['title'=>'Uploadable Item']);
		$this->app->side_menu->addItem(['Template','icon'=>'fa fa-file-text-o xepan-effect-yellow','badge'=>[$template_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_template']))->setAttr(['title'=>'Item Template']);
		$this->app->side_menu->addItem(['Designable','icon'=>'fa fa-picture-o','badge'=>[$designable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_designable']))->setAttr(['title'=>'Designable Item']);
		$this->app->side_menu->addItem(['Dispatchable','icon'=>'fa fa-truck','badge'=>[$dispatchable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_dispatchable']))->setAttr(['title'=>'Dispatchable Item']);
		$this->app->side_menu->addItem(['Maintain Inventory','icon'=>'fa fa-cubes','badge'=>[$maintaininventory_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'maintain_inventory']))->setAttr(['title'=>'Maintain Inventory Item']);
		$this->app->side_menu->addItem(['Allow Negative Stock','icon'=>'fa fa-minus-circle text-danger','badge'=>[$allownewgativestock_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'allow_negative_stock']))->setAttr(['title'=>'Allow Negative Stock Item']);

		
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
			if($g->model['original_price'] == $g->model['sale_price'] || $g->model['original_price'] ==0 ) $g->current_row_html['original_price']=null;
		});

		$crud->grid->addPaginator(50);
		
		$frm=$crud->grid->addQuickSearch(['name','sku']);
				
		$crud->grid->js(true)->_load('jquery.sparkline.min')->_selector('.sparkline')->sparkline('html', ['enableTagOptions' => true]);
	}
}
