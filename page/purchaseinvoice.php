<?php 
 namespace xepan\commerce;
 class page_purchaseinvoice extends \xepan\base\Page{

	public $title='Purchase Invoice';

	function init(){
		parent::init();

		$purchaseinvoice = $this->add('xepan\commerce\Model_PurchaseInvoice');
		$purchaseinvoice->add('xepan\commerce\Controller_SideBarStatusFilter');
		
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
		
		$crud->setModel($purchaseinvoice)->setOrder('created_at','desc');
		$frm=$crud->grid->addQuickSearch(['contact','document_no']);
		$crud->grid->addPaginator(50);

		$crud->add('xepan\base\Controller_Avatar',['name_field'=>'contact']);

		if(!$crud->isEditing()){
			$crud->grid->js('click')->_selector('.do-view-frame')->univ()->frameURL('Purchase Order Details',[$this->api->url('xepan_commerce_purchaseinvoicedetail'),'document_id'=>$this->js()->_selectorThis()->closest('[data-purchaseorder-id]')->data('id')]);
			$crud->grid->js('click')->_selector('.do-view-supplier-frame')->univ()->frameURL('Supplier Details',[$this->api->url('xepan_commerce_supplierdetail'),'contact_id'=>$this->js()->_selectorThis()->closest('[data-contact-id]')->data('contact-id')]);
		}
	}
}  