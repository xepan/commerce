<?php

namespace xepan\commerce;

class Model_Store_OrderItemDispatch extends \xepan\commerce\Model_QSP_Detail{

	public $status = ['dispatch'];
	public $actions=[
				'dispatch'=>['view','edit','delete','dispatch','receivedByParty'],
				// 'Dispatch'=>['view','edit','delete','receivedByParty'],
			];
	public $acl_type = "Model_Store_DispatchRequest";		
	public $acl = true;

	function init(){
		parent::init();
		
		// Destroy Unwanted QSP Detail Expression
		$this->getElement('is_shipping_inclusive_tax')->destroy();
		// $this->getElement('qty_unit')->destroy();
		$this->getElement('amount_excluding_tax')->destroy();
		$this->getElement('tax_amount')->destroy();
		$this->getElement('total_amount')->destroy();
		$this->getElement('sub_tax')->destroy();
		
		if($this->hasElement('received_qty'))
			$this->getElement('received_qty')->destroy();
		
		$this->getElement('quantity')->caption('Total Order');
		$this->getElement('qsp_master_id')->sortable(true);

		$this->addExpression('status','"dispatch"');
		$this->addExpression('sale_order_no')->set($this->refSQL('qsp_master_id')->fieldQuery('document_no'));
		$this->addExpression('created_by_id')->set($this->refSQL('qsp_master_id')->fieldQuery('created_by_id'));
		//Dispatch Item Expression
		$this->addExpression('toreceived_quantity')->set(function($m,$q){
			$sql = $m->refSQL('StoreTransactionRows')
							->addCondition('status','ToReceived')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$sql]);
		})->caption('Under Process');

		$this->addExpression('received_quantity')->set(function($m,$q){
			$sql = $m->refSQL('StoreTransactionRows')
							->addCondition('status','Received')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$sql]);
		});

		$this->addExpression('shipped_quantity')->set(function($m,$q){
			$sql = $m->refSQL('StoreTransactionRows')
							->addCondition('status','Shipped')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$sql]);
		});

		$this->addExpression('delivered_quantity')->set(function($m,$q){
			$sql = $m->refSQL('StoreTransactionRows')
							->addCondition('status','Delivered')
							->sum('quantity');
			return $q->expr("IFNULL ([0], 0)",[$sql]);
		});

		$this->addExpression('due_quantity')->set(function($m,$q){
			return $q->expr("IFNULL([received_qty]-([shipped_qty] + [delivered_qty]),0)",
							[
								'received_qty' => $m->getElement('received_quantity'),
								'shipped_qty' => $m->getElement('shipped_quantity'),
								'delivered_qty' => $m->getElement('delivered_quantity')
							]);
		});
	}

	function page_dispatch($page){
								
		$customer = $this->add('xepan\commerce\Model_Customer')->tryLoad($this['customer_id']);
		if(!$customer->loaded()){
			$page->add('View_Error')->set("Customer not found");
			return;
		}
		$dispatch_config = $this->add('xepan\base\Model_ConfigJsonModel',
			[
				'fields'=>[
							'disable_partial_dispatch'=>'checkbox',
						],
					'config_key'=>'PARTIAL_DISPATCH',
					'application'=>'commerce'
			]);
		$dispatch_config->tryLoadAny();
		$disable_partial_dispatch = $dispatch_config['disable_partial_dispatch'];

		
		$order_dispatch_m = $page->add('xepan\commerce\Model_Store_TransactionRow');
		$order_dispatch_m->addCondition('status','Received');
		$order_dispatch_m->addCondition('related_sale_order',$this['qsp_master_id']);
		
		$order_dispatch_m->addExpression('sale_order_id')->set($order_dispatch_m->refSQL('qsp_detail_id')->fieldQuery('qsp_master_id'));
		$order_dispatch_m->addExpression('order_qty')->set($order_dispatch_m->refSql('qsp_detail_id')->fieldQuery('quantity'));
		$order_dispatch_m->addExpression('delivered_from_warehouse_id')->set($order_dispatch_m->refSQL('store_transaction_id')->fieldQuery('to_warehouse_id'));

		$order_dispatch_m->addExpression('received_quantity')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow',['table_alias'=>'received']);
			$model->addCondition('status','Received')
					->addCondition('related_sale_order',$m->getElement('sale_order_id'));
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});
		
		$order_dispatch_m->addExpression('shipped_quantity')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow',['table_alias'=>'shipped']);
			$model->addCondition('status','Shipped')
					->addCondition('related_sale_order',$m->getElement('sale_order_id'));
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$order_dispatch_m->addExpression('delivered_quantity')->set(function($m,$q){
			$model = $m->add('xepan\commerce\Model_Store_TransactionRow',['table_alias'=>'delivered']);
			$model->addCondition('status','Delivered')
					->addCondition('related_sale_order',$m->getElement('sale_order_id'));
			return $q->expr('IFNULL([0],0)',[$model->sum('quantity')]);
		});

		$order_dispatch_m->addExpression('due_quantity')->set(function($m,$q){
			return $q->expr('([0]-([1]+[2]))',[$m->getElement('received_quantity'),$m->getElement('shipped_quantity'),$m->getElement('delivered_quantity')]);
		});

		$order_dispatch_m->_dsql()->group($order_dispatch_m->_dsql()->expr('[0]',[$order_dispatch_m->getElement('from_warehouse_id')]));
		$order_dispatch_m->_dsql()->group('qsp_detail_id');
		
		// $grid = $page->add('Grid');
		// $grid->setModel($order_dispatch_m);
		
		$form = $page->add('Form');
		$form->setLayout(['view/store/form/dispatch-item']);

		$count = 1;
		
		foreach ($order_dispatch_m as $dispatch_item) {

			//row layout
			$serial_item = $this->add('xepan\commerce\Model_Item')->load($dispatch_item['item_id']);
			$field_label_postfix = $count;

			$multiplier = $this->app->getUnitMultiplier($dispatch_item['item_qty_unit_id'],$dispatch_item['order_item_qty_unit_id']);
			
			$row = $form->layout->add('View',null,"item_to_deliver",['view/store/deliver-grid']);
			$dispatchable_view = $row->add('View',null,'present_qty');
			$delivered_view = $row->add('View',null,'delivered');
			$select_view = $row->add('View',null,'selected');
			$serial_view = $row->add('View',null,'serial');

			$select_checkbox_field = $row->addField('checkbox','selected_'.$field_label_postfix,"");
			$name_v = $row->add('View',null,'item_name')->setHtml($dispatch_item['item_name']."<br/> Total Order Qty: ".$dispatch_item['order_qty'] ." ".$dispatch_item['order_item_qty_unit']);
			$row->add('View',null,'item_name')->setElement('small')->set($dispatch_item['from_warehouse'])->addClass('label label-primary')->setAttr('title','From Warehouse');

			$row->add('View',null,'total_qty')->set(($dispatch_item['received_quantity']/$multiplier)." ".$dispatch_item['order_item_qty_unit']);
			$row->add('View',null,'total_delivered')->set((($dispatch_item['shipped_quantity'] + $dispatch_item['delivered_quantity'])/$multiplier) ." ".$dispatch_item['order_item_qty_unit']);

			$dispatchable_view->add('View')->set(($dispatch_item['due_quantity']/$multiplier)." ".$dispatch_item['order_item_qty_unit']);
			
			
			if($disable_partial_dispatch){
				$deliver_field = $delivered_view->addField('hidden','deliver_qty_'.$field_label_postfix,"")->set(($dispatch_item['due_quantity']/$multiplier));
				$delivered_view->add('View')->set(($dispatch_item['due_quantity']/$multiplier));
			}else{
				$deliver_field = $delivered_view->addField('Number','deliver_qty_'.$field_label_postfix,"")->set(($dispatch_item['due_quantity']/$multiplier));
			}

			if($serial_item['is_serializable'])
				$serial_view->addField('text','serial_'.$field_label_postfix,'')->setFieldHint('Enter seperated multiple values');

			//disable check box if no due quantity found
			if($dispatch_item['due_quantity'] == 0){
				$select_checkbox_field->setAttr('disabled','disabled');
				$deliver_field->setAttr('disabled','disabled');
			}


			$count++;
		}

		$customer_address = $customer['shipping_address'].", ".$customer['shipping_city'].", ".$customer['shipping_state'].", ".$customer['shipping_country'].", ".$customer['shipping_pincode'];

		$form->addField('line','delivery_via')->validate('required');
		$form->addField('line','delivery_docket_no','Docket No / Person name / Other Reference')->validate('required');
		$form->addField('text','shipping_address')->set($customer_address);
		$form->addField('text','delivery_narration');
		$form->addField('text','tracking_code');
		$send_invoice_and_challan = $form->addField('xepan\base\DropDown','send_document')->setValueList(array('send_invoice'=>'Generate & Send Invoice','send_challan'=>'Send Challan','all'=>'Send Invoice & Challan'))->setEmptyText('Select Document to Send');

		$form->addField('xepan\base\DropDown','print_document')->setValueList(array('print_challan'=>'Print Challan','print_invoice'=>'Print Invoice','print_all'=>'Print Invoice & Challan'))->setEmptyText('Select Document To Print');
		$form->addField('Checkbox','complete_on_receive')->set(true);
		$form->addField('Checkbox','include_barcode')->set(false);
		
		$from_email=$form->addField('xepan\base\DropDown','from_email')->setEmptyText('Please Select From Email');
		$from_email->setModel('xepan\hr\Model_Post_Email_MyEmails');
		
		$email_setting=$this->add('xepan\communication\Model_Communication_EmailSetting');
		if($_GET['from_email'])
			$email_setting->tryLoad($_GET['from_email']);
		$signature = $form->layout->add('View',null,'signature')->setHTML($email_setting['signature']);
		$from_email->js('change',$signature->js()->reload(['from_email'=>$from_email->js()->val()]));
		
		$form->addField('line','email_to')->set($customer['emails_str']);
		$form->addField('line','subject');
		$form->addField('xepan\base\RichText','message');

		$send_invoice_and_challan->js(true)->univ()->bindConditionalShow([
			'send_invoice'=>['email_to','from_email','subject','message'],
			'send_challan'=>['email_to','from_email','subject','message'],
			'all'=>['email_to','from_email','subject','message'],
		],'div.atk-form-row');
		

		$form->addSubmit('Dispatch the Order');

		if($form->isSubmitted()){
			$dispatch_item_selected = [];
			
			if($form['send_document']){
				
				if(!$form['from_email']){
					$form->displayError('from_email','from email is a mandatory field');
				}

				if(!$form['email_to']){
					$form->displayError('email_to','email to is a mandatory field');
				}

				if(!$form['subject']){
					$form->displayError('subject','subject is a mandatory field');
				}

				if(!$form['message']){
					$form->displayError('message','message is a mandatory field');
				}
			}

			$count = 1;
			
			//check validation
			foreach ($order_dispatch_m as $dispatch_item) {

				$field_label_postfix = $count;
				// continue if not selected
				if(!$form['selected_'.$field_label_postfix]){
					$count++;
					continue;
				}

				$item_model = $this->add('xepan\commerce\Model_Item')->load($dispatch_item['item_id']);
		  		$code = "";

		  		// if item has serializable
		  		if($item_model['is_serializable']){
					$code = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",$form['serial_'.$field_label_postfix])));

			        $serial_no_array = [];
			        if(strlen($code))
			        	$serial_no_array = explode("\n",$code);
			        
			        if($form['deliver_qty_'.$field_label_postfix] != count($serial_no_array))
			            $form->error('serial_'.$field_label_postfix,'count of serial nos must be equal to quantity '.$form['deliver_qty_'.$field_label_postfix]. " = ".count($serial_no_array));
					
					// check all serial no is exist or not RELATED item_id
					$not_found_no = [];
					foreach ($serial_no_array as $key => $value) {
						$serial_model = $this->add('xepan\commerce\Model_Item_Serial');
						$serial_model->addCondition('item_id',$item_model->id);
						$serial_model->addCondition('serial_no',$value);
						$serial_model->addCondition('is_available',true);
						$serial_model->tryLoadAny();
						if(!$serial_model->loaded()){
							// used
							// if(!$serial_model['is_available']){
							// 		$serial_model
							// 			->addCondition('dispatch_row_id',$tr_row->id)
							// 			->addCondition('dispatch_id',$deliver_model->id);
							// 		$serial_model->tryLoadAny();
							// 		if(!$serial_model->loaded())
							// 			$not_found_no[$value] = $value;
							// }
						// }else{
							$not_found_no[$value] = $value;
						}
					}

					if(count($not_found_no))
			            $form->error('serial_'.$field_label_postfix,'some of serial no are not available '. implode(", ", $not_found_no) );

			        // memorizing serial number values 
			     	// $this->app->memorize('serial_no_array',$serial_no_array);
					// }
		  		}



				$multiplier = $this->app->getUnitMultiplier($dispatch_item['item_qty_unit_id'],$dispatch_item['order_item_qty_unit_id']);

				$due_quantity = $dispatch_item['due_quantity'] / $multiplier;


				$dispatch_item_selected = [];
				
				$deliver_qty = $form['deliver_qty_'.$field_label_postfix];
				
				if($deliver_qty == 0 or $deliver_qty == null or $deliver_qty < 0)
					$form->displayError('deliver_qty_'.$field_label_postfix,"cannot deliver ".$deliver_qty." quanity");
				
				//check delivered item not zero or not greater than dispatchable qty
				//check condition based on item is_teller_made_item or allow_negative_stock

				$item_model = $this->add('xepan\commerce\Model_Item')->load($dispatch_item['item_id']);
				
				if(!$item_model['allow_negative_stock']){

					if($item_model['is_teller_made_item']){
						if($deliver_qty > $due_quantity)
							$form->displayError('deliver_qty_'.$field_label_postfix," cannot more than dispatchable quantity ".$due_quantity);
					}else{
						
						$stock_data_array = [];
						$pre_stock_data_array = $item_model->getStockAvalibility($dispatch_item['extra_info'],$dispatch_item['quantity'],$stock_data_array); // to do get from stock avalibility function		

						$cf_key = $item_model->convertCustomFieldToKey($dispatch_item['extra_info'],true);

						$pre_made_qty = 0;
						if(isset($pre_stock_data_array[$item_model['name']])){
							$pre_made_qty = $pre_stock_data_array[$item_model['name']][$cf_key]['available']?:0;
							$pre_made_qty = $pre_made_qty / $multiplier;
						}
						
						if($deliver_qty > $due_quantity)
							$form->displayError('deliver_qty_'.$field_label_postfix," cannot more than dispatchable quantity ".$due_quantity. " or item stock is not available in such qty ");
						if($pre_made_qty < $deliver_qty)
							$form->displayError('deliver_qty_'.$field_label_postfix,'item stock is not available in such qty: '.$due_quantity);
					}
				}
				//end --------------------------------

				$dispatch_item_selected[$dispatch_item['delivered_from_warehouse_id']][] = [
															'qsp_detail_id'=>$dispatch_item['qsp_detail_id'],
															'item_id'=>$dispatch_item['item_id'],
															'quantity'=>$deliver_qty,
															'jobcard_detail_id'=>$dispatch_item['jobcard_detail_id']
														];
				$count++;
			}


			if(!count($dispatch_item_selected))
				$form->js()->univ()->errorMessage("please select at least one item to delivered")->execute();

			// No of transaction = dispatch item unique with order_item_id with from_warehouse_id
			$this->related_transaction_id=null;
			$this->challan_transaction = [];
			foreach ($dispatch_item_selected as $from_warehouse_id => $dispatch_item_array) {

				//create Deliver Type Store Transaction /Challan
				$deliver_model = $this->add('xepan\commerce\Model_Store_Delivered');
				$deliver_model['from_warehouse_id'] = $from_warehouse_id;
				$deliver_model['to_warehouse_id'] = $customer->id;
				$deliver_model['related_document_id'] = $this['qsp_master_id'];
				$deliver_model['delivery_via'] = $form['delivery_via'];
				$deliver_model['delivery_reference'] = $form['delivery_docket_no'];
				$deliver_model['shipping_address'] = $form['shipping_address'];
				$deliver_model['shipping_charge'] = $form['shipping_charge'];
				$deliver_model['narration'] = $form['narration'];
				$deliver_model['tracking_code'] = $form['tracking_code'];
				$deliver_model['status'] = 'Delivered';
				$deliver_model['related_transaction_id'] = $this->related_transaction_id;
				
				if($form['complete_on_receive'])
					$deliver_model['status'] = 'Shipped';
				$deliver_model->save();

				$this->challan_transaction[] = $deliver_model->id;

				if(!$this->related_transaction_id) $this->related_transaction_id = $deliver_model->id;

				// one transaction with multiple rows
				foreach ($dispatch_item_array as $dispatched_item) {
					$deliver_model->addItem(
										$dispatched_item['qsp_detail_id'],
										$dispatched_item['item_id'],
										$dispatched_item['quantity'],
										$dispatched_item['jobcard_detail_id'],
										null,
										"Shipped"
									);

					//remove booked item quantity
					$tr_row = $this->add('xepan\commerce\Model_Store_TransactionRow');
					$tr_row->addCondition('type',"Consumption_Booked");
					$tr_row->addCondition('qsp_detail_id',$dispatched_item['qsp_detail_id']);
					$tr_row->addCondition('item_id',$dispatched_item['item_id']);
					$tr_row->tryLoadAny();

					if($tr_row->loaded()){
						if($dispatched_item['quantity'] >= $tr_row['quantity']){
							$tr_row->delete();
						}else{
							$tr_row['quantity'] = $tr_row['quantity'] - $dispatched_item['quantity'];
							$tr_row->save();
						}
					}
					
				}

			}
			
			if($form['send_document']=='send_invoice'){
				if(!($sale_order = $this->saleOrder()))
					$form->js()->univ()->errorMessage('sale order not found')->execute();
						
				if(!($invoice = $sale_order->invoice())){
					$invoice = $sale_order->createInvoice();
				}
				if($form['include_barcode']){
					$barcode = $this->add('xepan\commerce\Model_BarCode');
					$barcode->addCondition('is_used',null);
					$barcode->tryLoadAny();
					$barcode->markBarCodeUsed($invoice->id,$invoice['type']);
				}
			}

			if($form['send_document']){
				$deliver_model->send(
									$form['send_document'],
									$form['from_email'],
									$form['email_to'],
									$form['subject'],
									$form['message'],
									$this->challan_transaction
								);
			}

			$js = [];
			if($form['print_document']){
				$js = $deliver_model->printDocument(
											$form['print_document'],
											$getPrintUrl=true,
											$this->challan_transaction
										);
			}

			$js[] = $form->js()->reload();
			$js[] = $page->js()->univ()->closeDialog();

			return $form->js(false,$js)->univ()->successMessage('Sale Order Delivered or Shipped');
		}
	}

	function dispatch(){
		throw new \Exception("Error Processing Request", 1);
		
	}
}