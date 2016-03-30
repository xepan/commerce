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
		$tra_j->addField('status');
		$tra_row->_dsql()->group('related_document_id');

		// $this->add('H3')->set('Items to Deliver');

		$grid = $this->add('xepan\hr\Grid',null,'send',['view/store/deliver-grid']);
		$grid->setModel($tra_row);
		$grid->template->tryDel('Pannel');


		$f=$this->add('Form',null,'send');

		$select_item = $f->addField('hidden','select_item');

		$grid->addSelectable($select_item);

		$f->addSubmit('Dispatch the Order');

		if($f->isSubmitted()){
			if(!$f['select_item'])
				throw $f->displayError($f['select_item'],'No Item Selected');

			$orderitems_selected = array();
			$items_selected = json_decode($f['select_item'],true);

			foreach ($items_selected as  $value) {
				$item = $this->add('xepan\commerce\Model_Store_TransactionRow')->load($value);
				$orderitems_selected[] = $item['qsp_detail_id'];
			}

			$this['status']="Dispatch";
			$this->save();		

			$f->js(null,$f->js()->univ()->successMessage('SuccessFully Dispatch'))->reload()->execute();	
		}
	}

	function defaultTemplate(){
		return['page/store/delivery-managment'];
	}
}