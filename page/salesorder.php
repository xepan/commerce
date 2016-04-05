<?php 
 namespace xepan\commerce;
 class page_salesorder extends \Page{

	public $title='Sale Order';

	function init(){
		parent::init();

		$saleorder = $this->add('xepan\commerce\Model_SalesOrder');
		if($status=$this->api->stickyGET('status')){
			$saleorder->addCondition('status',$status);
		}

		$this->app->side_menu->addItem('Draft',$this->api->url('xepan_commerce_salesorder',['status'=>'Draft']));
		$this->app->side_menu->addItem('Submitted',$this->api->url('xepan_commerce_salesorder',['status'=>'Submitted']));
		$this->app->side_menu->addItem('Approved',$this->api->url('xepan_commerce_salesorder',['status'=>'Approved']));
		$this->app->side_menu->addItem('InProgress',$this->api->url('xepan_commerce_salesorder',['status'=>'InProgress']));
		$this->app->side_menu->addItem('Canceled',$this->api->url('xepan_commerce_salesorder',['status'=>'Canceled']));
		$this->app->side_menu->addItem('Completed',$this->api->url('xepan_commerce_salesorder',['status'=>'Completed']));
		

		$saleorder->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});

		$saleorder->addExpression('contact_type',$saleorder->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_salesorderdetail']
						,null,
						['view/order/sale/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});

		$crud->setModel($saleorder);
		$crud->grid->addPaginator(10);
		$frm=$crud->grid->addQuickSearch(['name']);
		
		$frm_drop=$frm->addField('DropDown','Actions')->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Approved'=>'Approved','Redesign'=>'Redesign','Rejected'=>'Rejected','Converted'=>'Converted'])->setEmptyText('Actions');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['salesorder_id'])
				$m->addCondition('salesorder_id',$frm['salesorder_id']);
		});
		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
	}

}  