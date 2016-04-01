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

		$order=$this->add('xepan\commerce\Model_SalesOrder');
		$order->addCondition('id',$transaction['related_document_id']);
		$order->tryLoadAny();
		$customer=$order->ref('contact_id');

		// throw new \Exception($customer['shipping_address'], 1);
		// $order=$transaction['related_document_id'];
		
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


		$f=$this->add('Form',null,'form');
		$f->setLayout(['view/store/form/dispatch-item']);
		$f->addField('line','delivery_via')->validateNotNull(true);
		$f->addField('line','delivery_docket_no','Docket No / Person name / Other Reference')->validateNotNull(true);
		$f->addField('text','shipping_address')->set($customer['shipping_address']);
		$f->addField('text','delivery_narration');
		$f->addField('Checkbox','generate_invoice');
		$f->addField('DropDown','include_items')->setValueList(array('Selected'=>'Selected Only','All'=>'All Ordered Items'))->setEmptyText('Select Items Included in Invoice');
		$f->addField('DropDown','payment')->setValueList(array('cheque'=>'Bank Account/Cheque','cash'=>'Cash'))->setEmptyText('Select Payment Mode');
		$f->addField('DropDown','invoice_action')->setValueList(array('Due'=>'Due','Paid'=>'Paid'));//->setEmptyText('Select Invoice Action');
		$f->addField('Money','amount');
		$f->addField('Money','discount')/*->set($order['discount_amount'])*/;
		$f->addField('Money','shipping_charge');
		$f->addField('line','bank_account_detail');
		$f->addField('line','cheque_no');
		$f->addField('DatePicker','cheque_date');
		$f->addField('Checkbox','complete_on_receive');
		$f->addField('Checkbox','send_invoice_via_email');
		$f->addField('line','email_to')->set($customer->ref('Emails')->setLimit(1)->fieldQuery('value'));

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
				$new_item = $m->add('xepan\commerce\Model_Store_TransactionRow');
				$new_item['store_transaction_id'] = $transaction->id;
				$new_item['qsp_detail_id'] = $item['qsp_detail_id'];
				$new_item['qty'] = $item['qty'];
				$new_item['jobcard_detail_id'] = $item['jobcard_detail_id'];
				$new_item['customfield_generic_id'] = $item['custom_fields'];
				$new_item['customfield_value_id'] = $item['customfield_value'] ;
				$new_item->save();
				
			}

			//CHECK FOR GENERATE INVOICE
			if($f['generate_invoice']){
				if(!$f['select_item'])
					$f->displayError('select_item','Select Items tobe Included in Invoice.');

				if($f['include_items'] == "")
					$f->displayError('include_items','Please Select');

				if($f['payment']){
					switch ($f['payment']) {
						case 'cheque':
							if(trim($f['amount']) == "")
								$f->displayError('amount','Amount Cannot be Null');

							if(trim($f['bank_account_detail']) == "")
								$f->displayError('bank_account_detail','Account Number Cannot  be Null');
					
							if(trim($f['cheque_no']) =="")
								$f->displayError('cheque_no','Cheque Number not valid.');

							if(!$f['cheque_date'])
								$f->displayError('cheque_date','Date Canot be Empty.');

						break;

						case 'cash':
							if(trim($f['amount']) == "")
								$f->displayError('amount','Amount Cannot be Null');
						break;
					}
				}
				
				//GENERATE INVOICE FOR SELECTED / ALL ITEMS
				if($f['include_items']=='All'){

					$orderitems_selected=$order->orderItems()->get('id');
				}
				$invoice=$this->add('xepan\commerce\Model_SalesInvoice');
				$invoice->addCondition('related_qsp_master_id',$order->id);
				$invoice->tryLoadAny();
				if($invoice->loaded())
					throw new \Exception("This Order already Create Invoice", 1);
					
				$invoice = $order->createInvoice($status='Approved',$order->id, $orderitems_selected,$f['amount'],$f['discount'],$f['shipping_charge'],$f['delivery_narration'],$f['shipping_address']);


				if($f['payment'] == "cash"){
					$invoice->paid();
					$invoice['status']="Paid";
				}
					
				if($f['payment'] == "cheque"){
					$invoice->Due();
					$invoice['status']="Due";
				}

				if($f['invoice_action']=="Paid"){
					$invoice_id = $invoice->id;
					$invoice['status']="Paid";
					$invoice = $this->add('xepan\commerce/Model_SalesInvoice')->load($invoice_id);
				}
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