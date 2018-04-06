<?php
 
namespace xepan\commerce;

class page_mystocks extends \xepan\base\Page {
	public $title='My Stocks';

	function init(){
		parent::init();

		$tabs = $this->add('Tabs');
		$stock_tab = $tabs->addtab('My Stocks');
		$to_receive_tab = $tabs->addtab('My To Receive');
		$receive_tab = $tabs->addtab('Received');

		$stock_model=$this->add('xepan\commerce\Model_Item_Stock',['warehouse_id'=>$this->app->employee->id]);
		$stock_model->addCondition('maintain_inventory',true);
		$grid= $stock_tab->add('xepan\base\Grid',['fixed_header'=>false]);
		$grid->setModel($stock_model,['name','net_stock','qty_unit']);
		$grid->addPaginator(10);

		$to_rec_model = $to_receive_tab->add('xepan\commerce\Model_Store_Transaction');
		$to_rec_model->addCondition('to_warehouse_id',$this->app->employee->id);
		$to_rec_model->addCondition('status','ToReceived');
		$to_rec_model->addCondition('item_quantity','>',0);

		$grid =$to_receive_tab->add('xepan\hr\CRUD',['allow_add'=>false, 'allow_del'=>false ,'allow_edit'=>false,'actionsWithoutACL'=>true,['grid_options'=>['fixed_header'=>false]]]);
		$grid->setModel($to_rec_model,['from_warehouse','created_by','type','status','item_quantity','toreceived','received']);
		$grid->removeAttachment();

		$to_rec_model = $receive_tab->add('xepan\commerce\Model_Store_TransactionRow');
		$to_rec_model->addCondition('to_warehouse_id',$this->app->employee->id);
		$to_rec_model->addCondition('status','Received');
		
		$grid = $receive_tab->add('xepan\hr\CRUD',['allow_add'=>false, 'allow_del'=>false ,'allow_edit'=>false,'actionsWithoutACL'=>true,['grid_options'=>['fixed_header'=>false]]]);
		$grid->setModel($to_rec_model,['item_name','quantity','extra_info','serial_nos','narration','from_warehouse','created_at']);
		$grid->removeAttachment();
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