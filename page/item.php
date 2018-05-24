<?php 
 namespace xepan\commerce;
 class page_item extends \xepan\base\Page{

	public $title='Items';

	function init(){
		parent::init();

		$item=$this->add('xepan\commerce\Model_Item');
		$item->addExpression('weakly_sales')->set(function($m,$q){
			$qsp_details = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'weeklysales']);
			$qsp_details->addExpression('document_type')->set($qsp_details->refSQL('qsp_master_id')->fieldQuery('type'));
			$qsp_details->addCondition('document_type','SalesOrder');
			$qsp_details->addCondition('item_id',$q->getField('id'));
			$qsp_details->addExpression('week')->set('WEEK(created_at)');
			$qsp_details->addExpression('sum_qty')->set(function($m,$q){
				$qsp_details_sum = $m->add('xepan\commerce\Model_QSP_Detail',['table_alias'=>'weeklysales_qty']);
				$qsp_details_sum->addExpression('week')->set('WEEK(created_at)');
				$qsp_details_sum->addCondition('id',$q->getField('id'));
				return $qsp_details_sum->sum('quantity')->order('created_at','asc')->group($q->expr('[0]',[$qsp_details_sum->getElement('week')]));
			});


			return $qsp_details->_dsql()->del('fields')
						->field($q->expr('substring_index(GROUP_CONCAT([0]),",",8)',[$qsp_details->getElement('sum_qty')]))
						->order('created_at','asc')
						->group($q->expr('([0])',[$qsp_details->getElement('week')]))
						;
		});

		$item->add('xepan\base\Controller_TopBarStatusFilter');
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

		$party_publish=$this->add('xepan\commerce\Model_Item');
		$party_publish_count = $party_publish->addCondition('is_party_publish',1)->addCondition('status','UnPublished')->count();

		$this->app->side_menu->addItem(['Party Publish','icon'=>'fa fa-shopping-cart text-success','badge'=>[$party_publish_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_party_publish']),['status','condition'])->setAttr(['title'=>'Party Publish Item']);
		$this->app->side_menu->addItem(['Item Classification','icon'=>'fa fa-arrow-circle-down text-success']);
		$this->app->side_menu->addItem(['Saleable','icon'=>'fa fa-shopping-cart text-success','badge'=>[$saleable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_saleable']),['status','condition'])->setAttr(['title'=>'Saleable Item']);
		$this->app->side_menu->addItem(['Purchasable','icon'=>'fa fa-cart-arrow-down text-danger','badge'=>[$purchasable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_purchasable']),['status','condition'])->setAttr(['title'=>'Purchasable Item']);
		$this->app->side_menu->addItem(['Productionable','icon'=>'fa fa-industry text-primary','badge'=>[$productionable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_productionable']),['status','condition'])->setAttr(['title'=>'Productionable Item']);
		$this->app->side_menu->addItem(['AllowUploadable','icon'=>'fa fa-upload text-warning','badge'=>[$allowuploadable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_allowuploadable']),['status','condition'])->setAttr(['title'=>'Uploadable Item']);
		$this->app->side_menu->addItem(['Template','icon'=>'fa fa-file-text-o xepan-effect-yellow','badge'=>[$template_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_template']),['status','condition'])->setAttr(['title'=>'Item Template']);
		$this->app->side_menu->addItem(['Designable','icon'=>'fa fa-picture-o','badge'=>[$designable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_designable']),['status','condition'])->setAttr(['title'=>'Designable Item']);
		$this->app->side_menu->addItem(['Dispatchable','icon'=>'fa fa-truck','badge'=>[$dispatchable_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'is_dispatchable']),['status','condition'])->setAttr(['title'=>'Dispatchable Item']);
		$this->app->side_menu->addItem(['Maintain Inventory','icon'=>'fa fa-cubes','badge'=>[$maintaininventory_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'maintain_inventory']),['status','condition'])->setAttr(['title'=>'Maintain Inventory Item']);
		$this->app->side_menu->addItem(['Allow Negative Stock','icon'=>'fa fa-minus-circle text-danger','badge'=>[$allownewgativestock_count,'swatch'=>' label label-primary label-circle pull-right']],$this->app->url('xepan_commerce_item',['condition'=>'allow_negative_stock']),['status','condition'])->setAttr(['title'=>'Allow Negative Stock Item']);

		
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
		$crud->add('xepan\base\Controller_MultiDelete');
		$frm =$crud->grid->addQuickSearch(['name','sku']);
				
		$crud->grid->js(true)->_load('jquery.sparkline.min')->_selector('.sparkline')->sparkline('html', ['enableTagOptions' => true]);
	
		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-item-detail')->univ()->frameURL('Item Details',[$this->api->url('xepan_commerce_itemdetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-item-id]')->data('id')]);
		}

		$itm_frm = $frm->addField('DropDown','duplicate_from_item_id','Template')->setEmptyText("Select Template");
		$itm_frm->setModel('xepan\commerce\Item')->addCondition('is_template',true);
		$itm_frm->js('change',$frm->js()->submit());
		
	}
}
