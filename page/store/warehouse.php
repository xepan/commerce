<?php

namespace xepan\commerce;

/**
* 
*/
class page_store_warehouse extends \xepan\base\Page{
	public $title = "Warehouse";
	function init(){
		parent::init();
		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');

		foreach ($warehouse as $w) {
			$this->app->side_menu->addItem(([$w['name'],'icon'=>'fa fa-empire']),$this->app->url('xepan_commerce_store_warehouse',['warehouse_id'=>$w->id]),['warehouse_id']);
		}
		// return ;

		$warehouse_id = $this->api->stickyGET('warehouse_id');
		// $department = $this->add('xepan\hr\Model_Department')->tryLoad($department_id);
		// if(!$department->loaded()){
		// 	$this->add('View_Error')->set("department not loaded");
		// 	return;
		// }

		// $this->title = $department['name']." Material Request / Stock Management";
		// check department has warehouse or not
		$warehouse = $this->add('xepan\commerce\Model_Store_Warehouse');
		if($_GET['warehouse_id'])
			$warehouse->addCondition('id',$_GET['warehouse_id']);
		// $warehouse->addCondition('related_id',$department->id);
		// $warehouse->addCondition('related_with',"xepan\hr\Model_Department");
		$warehouse->tryLoadAny();
		if(!$warehouse->loaded()){
			$warehouse->save();
		}

		
		$tab = $this->add('Tabs');
		$mr_send_tab = $tab->addTab('Material Request Send');
		$mr_received_tab = $tab->addTab('Material Request Received');
		$mr_receiveddispatch_tab = $tab->addTab('Material Request Dispatch Received');
		$dept_stock_tab = $tab->addTab('Department Stock');


		$mr_model = $mr_send_tab->add('xepan\production\Model_MaterialRequest');
		// $mr_model->addCondition('department_id',$department_id);
		$mr_model->addCondition('from_warehouse_id',$warehouse->id);
		$mr_model->setOrder('created_at','desc');

		$crud = $mr_send_tab->add('xepan\hr\CRUD');
		$crud->setModel($mr_model,['to_warehouse_id','narration'],['to_warehouse','narration','status']);
		$crud->grid->addPaginator(50);

		if($crud->isEditing('add') or $crud->isEditing('edit')){
			$form = $crud->form;
			$form->getElement('to_warehouse_id')->getModel()->addCondition('id','<>',$warehouse->id);
		}

		if($crud->grid){
			$grid = $crud->grid;

			$grid->add('VirtualPage')
	       		->addColumn('Request_Item')
	       		->set(function($page){
		           $id = $_GET[$page->short_name.'_id'];

		           $row_model = $page->add('xepan\commerce\Model_Store_TransactionRow');
		           $row_model->addCondition('store_transaction_id',$id);

		           $row_crud = $page->add('xepan\hr\CRUD',['entity_name'=>"Request Item"]);
		           $row_crud->setModel($row_model,['item_id','item','quantity','extra_info'],['item','quantity']);

		           if($row_crud->isEditing()){
		           		$form = $row_crud->form;
		           		$item_field = $form->getElement('item_id');
		           		$form->add('Button')->set('Extra-Info')->setClass('btn btn-primary extra-info');
		           }

		       });
		}
		// mr received 
		$mr_received_model = $mr_received_tab->add('xepan\production\Model_MaterialRequest');
		$mr_received_model
			->addCondition('to_warehouse_id',$warehouse->id)
			->setOrder('created_at','desc')
			;
		$crud = $mr_received_tab->add('xepan\hr\CRUD',['allow_add'=>false]);
		$crud->setModel($mr_received_model,['from_warehouse','from_warehouse_id','status','related_transaction_id','ToReceived','Received']);

		// mr received Dispatch 
		$mr_receiveddispatch_model = $mr_receiveddispatch_tab->add('xepan\production\Model_MaterialRequestDispatch');
		$mr_receiveddispatch_model
			->addCondition('to_warehouse_id',$warehouse->id)
			->setOrder('created_at','desc')
			;
		$crud = $mr_receiveddispatch_tab->add('xepan\hr\CRUD',['allow_add'=>false]);
		$crud->setModel($mr_receiveddispatch_model,['from_warehouse','from_warehouse_id','status','related_transaction_id','ToReceived','Received']);

		/*Department Stock*/
		
		$grid= $dept_stock_tab->add('xepan\hr\Grid');
		$department_stock = $this->add('xepan\commerce\Model_Item_Stock',['warehouse_id'=>$warehouse->id]);
		$department_stock->addCondition('net_stock','<>',0);
		$grid->setModel($department_stock,['name','opening','purchase','purchase_return','consumed','consumption_booked','received','adjustment_add','adjustment_removed','issue','issue_submitted','sales_return','movement_in','movement_out','net_stock']);	
	}
}