<?php 
 namespace xepan\commerce;
 class page_salesorder extends \xepan\commerce\page_qspstatus{

	public $title='Sale Order';

	function init(){
		parent::init();

		$saleorder = $this->add('xepan\commerce\Model_SalesOrder');

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