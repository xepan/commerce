<?php
namespace xepan\commerce;

class page_store_deliveryManagment extends \Page{
	public $title="Delivery Managment";

	function init(){
		parent::init();

		/*Item To Dispatch*/
		$transaction_id = $this->app->stickyGET('transaction_id');

		$transaction = $this->add('xepan\commerce\Model_Store_DispatchRequest');
		$transaction->addCondition('id',$transaction_id);
		$transaction->addCondition('status','Received');
		$transaction->tryLoadAny();
		
		$related_sale_order = $transaction['related_document_id'];

		if(!$transaction->loaded()){
			$this->add('View_Warning')->set('No Dispatchable Item Found');
			return;
		}
		$order=$this->add('xepan\commerce\Model_SalesOrder');
		$order->addCondition('id',$transaction['related_document_id']);
		$order->tryLoadAny();
		$customer = $order->ref('contact_id');
		
		$tra_row = $this->add('xepan\commerce\Model_Store_TransactionRow');
		$tra_row->addCondition('status','ToReceived');
		$tra_row->addCondition('related_sale_order',$related_sale_order);
		$tra_row->_dsql()->group('qsp_detail_id');
		
		$tra_row->addExpression('total_qty')->set(function($m,$q){
			return $q->expr("IFNULL(sum([0]),0)",[$m->getElement('quantity')]);
		});

		if(!$tra_row->count()->getOne()){
			$this->add('View_Error')->set("No Dispatchable Item Found");
			return;
		}

		$f = $this->add('Form',null,'form');
		$f->setLayout(['view/store/form/dispatch-item']);

		foreach ($tra_row as $row){

			$deliver_row = $this->add('xepan\commerce\Model_Store_TransactionRow');
			$deliver_row->addCondition('type',"Store_Delivered");
			$deliver_row->addCondition('status','in',array("Shipped","Delivered"));
			$deliver_row->addCondition('related_sale_order',$related_sale_order);
			$deliver_row->addCondition('qsp_detail_id',$row['qsp_detail_id']);
			$delivered_qty = $deliver_row->sum("quantity")->getOne();

			$dispatchable_qty = $row['total_qty'] - $delivered_qty;
			
			//row layout
			$view2 = $f->layout->add('View',null,"item_to_deliver",['view/store/deliver-grid']);

			$dispatchable_view = $view2->add('View',null,'present_qty');
			$delivered_view = $view2->add('View',null,'delivered');
			$select_view = $view2->add('View',null,'selected');

			//actual fields			
			$select_checkbox_field = $view2->addField('checkbox','selected_'.$row['qsp_detail_id'],"");
			$view2->add('View',null,'item_name')->set($row['item_name']);
			$view2->add('View',null,'total_qty')->set($row['total_qty']);
			$view2->add('View',null,'total_delivered')->set($delivered_qty);
			$dispatchable_view->addField('Number','dispatchable_qty_'.$row['qsp_detail_id'],"")->set($dispatchable_qty);
			$delivered_view->addField('Number',	'delivered_qty_'.$row['qsp_detail_id'],"")->set($dispatchable_qty);

			if($dispatchable_qty == 0){
				$select_checkbox_field->setAttr('disabled',true);
			}
		}

		$f->addField('line','delivery_via')->validate('required');
		$f->addField('line','delivery_docket_no','Docket No / Person name / Other Reference')->validate('required');
		$f->addField('text','shipping_address')->set($customer['shipping_address']);
		$f->addField('text','delivery_narration');
		$f->addField('text','tracking_code');
		$send_invoice_and_challan=$f->addField('DropDown','send_document')->setValueList(array('send_invoice'=>'Generate & Send Invoice','send_challan'=>'Send Challan','all'=>'Send Invoice & Challan'))->setEmptyText('Selecet Document to Send');

		$f->addField('DropDown','print_document')->setValueList(array('print_challan'=>'Print Challan','print_invoice'=>'Print Invoice','print_all'=>'Print Invoice & Challan'))->setEmptyText('Select Document To Print');
		// $payment_model_field = $f->addField('DropDown','payment')->setValueList(array('cheque'=>'Bank Account/Cheque','cash'=>'Cash'))->setEmptyText('Select Payment Mode');
		// $f->addField('Money','amount');
		// $f->addField('Money','discount')/*->set($order['discount_amount'])*/;
		// $f->addField('Money','shipping_charge');
		// $f->addField('line','bank_account_detail');
		// $f->addField('line','cheque_no');
		// $f->addField('DatePicker','cheque_date');
		$f->addField('Checkbox','complete_on_receive')->set(true);
		$f->addField('Checkbox','include_barcode')->set(false);
		$from_email=$f->addField('dropdown','from_email')->validate('required')->setEmptyText('Please Select From Email');
		$from_email->setModel('xepan\hr\Model_Post_Email_MyEmails');
		
		$email_setting=$this->add('xepan\communication\Model_Communication_EmailSetting');
		if($_GET['from_email'])
			$email_setting->tryLoad($_GET['from_email']);
		$view=$f->layout->add('View',null,'signature')->setHTML($email_setting['signature']);
		$from_email->js('change',$view->js()->reload(['from_email'=>$from_email->js()->val()]));
		
		$f->addField('line','email_to')->set($customer['emails_str']);
		$f->addField('line','subject');
		$f->addField('xepan\base\RichText','message');

		//bind condition for payment mode
		// $payment_model_field->js(true)->univ()->bindConditionalShow([
		// 	'cash'=>['amount','discount'],
		// 	'cheque'=>['amount','discount','bank_account_detail','cheque_no','cheque_date']
		// ],'div.atk-form-row');

		$send_invoice_and_challan->js(true)->univ()->bindConditionalShow([
			'send_invoice'=>['email_to','from_email'],
			'send_challan'=>['email_to','from_email'],
			'all'=>['email_to','from_email'],
		],'div.atk-form-row');
		

		$f->addSubmit('Dispatch the Order');

		if($f->isSubmitted()){
			
			$orderitems_selected = array();

			//check validation
			foreach ($tra_row as $row) {
				$item_m=$this->add('xepan\commerce\Model_Item')->load($row['item_id']);
				// throw new \Exception($row['item_id'], 1);
				if(!$item_m['allow_negative_stock']){
					if($f['dispatchable_qty_'.$row['qsp_detail_id']] < 0){
						$f->displayError('dispatchable_qty_'.$row['qsp_detail_id'],"Negative Quantity Not Allow");
					}
					$dispatch_qty= $row['total_qty'] - $delivered_qty;
					if($f['dispatchable_qty_'.$row['qsp_detail_id']] > $dispatch_qty){
						$f->displayError('dispatchable_qty_'.$row['qsp_detail_id']," Can Not dispatch quantity more than total quantity ");
					}
				}
				if(!$f['selected_'.$row['qsp_detail_id']])
					continue;

				if($f['delivered_qty_'.$row['qsp_detail_id']] == 0 or $f['delivered_qty_'.$row['qsp_detail_id']] == null or $f['delivered_qty_'.$row['qsp_detail_id']] < 0)
					$f->displayError('delivered_qty_'.$row['qsp_detail_id'],"cannot deliver ".$f['delivered_qty_'.$row['qsp_detail_id']]." quanity");

				//check delivered item not zero or not greater than dispatchable qty
				if($f['delivered_qty_'.$row['qsp_detail_id']] > $f['dispatchable_qty_'.$row['qsp_detail_id']])
					$f->displayError('delivered_qty_'.$row['qsp_detail_id'],"cannot more than dispatchable quantity");

				$orderitems_selected[] = [
										'qsp_detail_id'=>$row['qsp_detail_id']
									];
			}

			if(!count($orderitems_selected))
				$f->js()->univ()->errorMessage("please select at least one item to delivered")->execute();


			//create Deliver Type Store Transaction /Challan
			$deliver_model = $this->add('xepan\commerce\Model_Store_Delivered');
			$deliver_model['from_warehouse_id'] = $transaction['to_warehouse_id'];
			$deliver_model['to_warehouse_id'] = $customer->id;
			$deliver_model['related_document_id'] = $transaction['related_document_id'];	
			$deliver_model['jobcard_id'] = $transaction['jobcard_id'];
			$deliver_model['delivery_via'] = $f['delivery_via'];
			$deliver_model['delivery_reference'] = $f['delivery_docket_no'];
			$deliver_model['shipping_address'] = $f['shipping_address'];
			$deliver_model['shipping_charge'] = $f['shipping_charge'];
			$deliver_model['narration'] = $f['narration'];
			$deliver_model['tracking_code'] = $f['tracking_code'];

			$deliver_model['status'] = 'Delivered';
			if($f['complete_on_receive'])
				$deliver_model['status'] = 'Shipped';
			
			$deliver_model->save();
			
			foreach ($orderitems_selected as  $qsp_detail) {
				
				$qsp_detail_id = $qsp_detail['qsp_detail_id'];
				$qsp_detail_model = $this->add('xepan\commerce\Model_QSP_Detail')->load($qsp_detail_id); 
				$deliver_model->addItem(
									$qsp_detail_id,
									$qsp_detail_model['item_id'],
									$f['delivered_qty_'.$qsp_detail_id],
									null,
									null,
									null,
									"Shipped"
								);					
			}
			
			//generate(if not exist) and send
			if($f['send_document']=='send_invoice'){
				// if($f['payment']){
				// 	switch ($f['payment']) {
				// 		case 'cheque':
				// 			if(trim($f['amount']) == "")
				// 				$f->displayError('amount','Amount Cannot be Null');

				// 			if(trim($f['bank_account_detail']) == "")
				// 				$f->displayError('bank_account_detail','Account Number Cannot  be Null');
					
				// 			if(trim($f['cheque_no']) =="")
				// 				$f->displayError('cheque_no','Cheque Number not valid.');

				// 			if(!$f['cheque_date'])
				// 				$f->displayError('cheque_date','Date Canot be Empty.');

				// 		break;

				// 		case 'cash':
				// 			if(trim($f['amount']) == "")
				// 				$f->displayError('amount','Amount Cannot be Null');
				// 		break;
				// 	}
				// }

				if(!($sale_order = $transaction->saleOrder()))
					$f->js()->univ()->errorMessage('sale order not found')->execute();
						
				if(!($invoice = $sale_order->invoice())){
					$invoice = $sale_order->createInvoice();
				}
				if($f['include_barcode']){
					$barcode = $this->add('xepan\commerce\Model_BarCode');
					$barcode->addCondition('is_used',null);
					$barcode->tryLoadAny();
					$barcode->markBarCodeUsed($invoice->id,$invoice['type']);

				}

				
			}
			
			if($f['send_document'] )
				// $deliver_model->send($f['send_document'],$f['from_email'],$f['email_to'],$f['subject'],$f['message']);

			if($f['print_document']){	
				// $this->js()->univ()->newWindow($this->api->url())->execute();			
				$deliver_model->printChallan($f['print_document']);
			}

			$f->js(false,$f->js()->reload())->univ()->successMessage('Shipped')->execute();				
		}

	}

	function defaultTemplate(){
		return['page/store/delivery-managment'];
	}
}