<?php 
 namespace xepan\commerce;
 class page_purchaseinvoice extends \xepan\commerce\page_qspstatus{

	public $title='Purchase Invoice';

	function init(){
		parent::init();

		$purchaseinvoice = $this->add('xepan\commerce\Model_PurchaseInvoice');

		$purchaseinvoice->add('misc/Field_Callback','net_amount_client_currency')->set(function($m){
			return $m['exchange_rate'] == '1'? "": ($m['net_amount'].' '. $m['currency']);
		});


		$purchaseinvoice->addExpression('contact_type',$purchaseinvoice->refSQL('contact_id')->fieldQuery('type'));

		$crud=$this->add('xepan\hr\CRUD',
						['action_page'=>'xepan_commerce_purchaseinvoicedetail']
						,null,
						['view/invoice/purchase/grid']);

		$crud->grid->addHook('formatRow',function($g){
			$g->current_row['contact_url']= $g->model['contact_type'];
		});
		
		$crud->setModel($purchaseinvoice);
		$frm=$crud->grid->addQuickSearch(['name']);
		$crud->grid->addPaginator(10);

		$frm=$crud->grid->addQuickSearch(['name']);
		
		$frm_drop=$frm->addField('DropDown','Actions')->setValueList(['Draft'=>'Draft','Submitted'=>'Submitted','Approved'=>'Approved','Redesign'=>'Redesign','Rejected'=>'Rejected','Converted'=>'Converted'])->setEmptyText('Actions');
		$frm_drop->js('change',$frm->js()->submit());

		$frm->addHook('appyFilter',function($frm,$m){
			if($frm['purchaseinvoice_id'])
				$m->addCondition('purchaseinvoice_id',$frm['purchaseinvoice_id']);
		});

		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);
	}
}  