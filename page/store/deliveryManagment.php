<?php
namespace xepan\commerce;

class page_store_deliveryManagment extends \Page{
	public $title="Delivery Managment";

	function init(){
		parent::init();
		/*Item To Dispatch*/
		$transaction_id=$this->app->stickyGET('transaction_id');

		$transaction=$this->add('xepan\commerce\Model_Store_DispatchRequest')->tryLoadBy('id',$transaction_id);
		$transaction->addCondition('status','Received');
		
		$order=$transaction['related_document_id'];
		
		$tra_row=$transaction->ref('StoreTransactionRows');
		
		$tra_j=$tra_row->join('store_transaction.id');
		$tra_j->addField('related_document_id');
		$tra_j->addField('jobcard_id');
		// $tra_j->addField('status');
		$tra_row->_dsql()->group('related_document_id');

		// $this->add('H3')->set('Items to Deliver');

		$grid = $this->add('xepan\hr\Grid',null,'send',['view/store/deliver-grid']);
		$grid->setModel($tra_row/*->addCondition('status','Received')*/);
		$grid->template->tryDel('Pannel');


		$f=$this->add('Form',null,'send');

		$select_item = $f->addField('hidden','select_item');

		$grid->addSelectable($select_item);

		$f->addSubmit('Dispatch the Order');
		// throw new \Exception($transaction['related_document_id'], 1);
		
		if($f->isSubmitted()){
			if(!$f['select_item'])
				throw $f->displayError($f['select_item'],'No Item Selected');

			$orderitems_selected = array();
			$items_selected = json_decode($f['select_item'],true);

			$m = $this->add('xepan\commerce\Model_Store_Transaction');
			$m['type'] = $transaction['type'];
			$m['from_warehouse_id'] = $transaction['$related_doc_contact_id'];
			$m['to_warehouse_id'] = $transaction['to_warehouse_id'];
			$m['related_document_id']=$transaction['related_document_id'];	
			$m['jobcard_id']=$transaction['jobcard_id'];	
			$m['status']='Dispatch';	
			$m->save();

			foreach ($items_selected as  $value) {
				$item = $this->add('xepan\commerce\Model_Store_TransactionRow')->load($value);
				$orderitems_selected[] = $item['qsp_detail_id'];
				
				$new_item = $m->ref('StoreTransactionRows');
				// $new_item['store_transaction_id'] = $->id;
				$new_item['qsp_detail_id'] = $item['qsp_detail_id'];
				$new_item['qty'] = $item['qty'];
				$new_item['jobcard_detail_id'] = $item['jobcard_detail_id'];
				$new_item['customfield_generic_id'] = $item['custom_fields'];
				$new_item['customfield_value_id'] = $item['customfield_value'] ;
				$new_item->save();
				
			}

			$f->js(null,$f->js()->univ()->successMessage('SuccessFully Dispatch'))->reload()->execute();	
		}


		/*Dispatched Item */

		$transaction=$this->add('xepan\commerce\Model_Store_DispatchRequest')->tryLoadBy('id',$transaction_id);
		$transaction->addCondition('status','Dispatch');
		$tra_dispatch_row=$transaction->ref('StoreTransactionRows');
		
		$tra_d_j=$tra_dispatch_row->join('store_transaction.id');
		$tra_d_j->addField('related_document_id');
		$tra_d_j->addField('jobcard_id');
		// $tra_j->addField('status');
		$tra_row->_dsql()->group('related_document_id');

		// $this->add('H3')->set('Items to Deliver');

		$grid = $this->add('xepan\hr\Grid',null,'delivered',['view/store/deliver-grid']);
		$grid->setModel($tra_row);
		$grid->template->tryDel('Pannel');

	}

	function defaultTemplate(){
		return['page/store/delivery-managment'];
	}
}