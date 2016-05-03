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


		$this->app->side_menu->addItem(['Saleable','badge'=>[$saleable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_saleable']));
		$this->app->side_menu->addItem(['Purchasable','badge'=>[$purchasable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_purchasable']));
		$this->app->side_menu->addItem(['Productionable','badge'=>[$productionable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_productionable']));
		$this->app->side_menu->addItem(['AllowUploadable','badge'=>[$allowuploadable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_allowuploadable']));
		$this->app->side_menu->addItem(['Template','badge'=>[$template_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_template']));
		$this->app->side_menu->addItem(['Designable','badge'=>[$designable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_designable']));
		$this->app->side_menu->addItem(['Dispatchable','badge'=>[$dispatchable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_dispatchable']));
		$this->app->side_menu->addItem(['Maintain Inventory','badge'=>[$maintaininventory_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'maintain_inventory']));
		$this->app->side_menu->addItem(['Allow Negative Stock','badge'=>[$allownewgativestock_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'allow_negative_stock']));

		
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
		
		$frm=$crud->grid->addQuickSearch(['name']);
				
		$s_f=$frm->addField('DropDown','status')->setValueList(['Published'=>'Published','UnPublished'=>'UnPublished'])->setEmptyText('All Status');
		$s_f->js('change',$frm->js()->submit());
	}
}
