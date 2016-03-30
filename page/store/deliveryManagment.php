<?php
namespace xepan\commerce;

class page_store_deliveryManagment extends \Page{
	public $title="Delivery Managment";

	function init(){
		parent::init();

		$transaction_id=$this->app->stickyGET('transaction_id');

		$transaction=$this->add('xepan\commerce\Model_Store_DispatchRequest')->tryLoadBy('id',$transaction_id);
		$transaction->addCondition('status','Received');
		
		$order=$transaction['related_document_id'];
		
		$tra_row=$transaction->ref('StoreTransactionRows');
		
		$tra_j=$tra_row->join('store_transaction.id');
		$tra_j->addField('related_document_id');
		$tra_j->addField('jobcard_id');
		$tra_row->_dsql()->group('related_document_id');

		$this->add('H3')->set('Items to Deliver');

		$grid = $this->add('xepan\hr\Grid',null,null,['view/store/deliver-grid']);
		$grid->setModel($tra_row);

		$f=$this->add('Form');

		$select_item = $f->addField('hidden','select_item');

		$grid->addSelectable($select_item);
		$f->add('View')->setElement('br');

		$f->addSubmit('Dispatch the Order');

	}
}