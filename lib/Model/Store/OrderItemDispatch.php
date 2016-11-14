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
		$this->getElement('qty_unit')->destroy();
		$this->getElement('amount_excluding_tax')->destroy();
		$this->getElement('tax_amount')->destroy();
		$this->getElement('total_amount')->destroy();
		$this->getElement('sub_tax')->destroy();
		$this->getElement('received_qty')->destroy();
		
		$this->getElement('quantity')->caption('Total Order');

		$this->addExpression('status','"dispatch"');
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

		$order_dispatch_m = $page->add('xepan\commerce\Model_Store_OrderItemDispatch');
		$order_dispatch_m->addCondition('qsp_master_id',$this['qsp_master_id']);
		$order_dispatch_m->setOrder('id','desc');

		$form = $page->add('Form');
		$form->setLayout(['view/store/form/dispatch-item']);

		foreach ($order_dispatch_m as $dispatch_item) {
			//row layout
			$row = $form->layout->add('View',null,"item_to_deliver",['view/store/deliver-grid']);
			$dispatchable_view = $row->add('View',null,'present_qty');
			$delivered_view = $row->add('View',null,'delivered');
			$select_view = $row->add('View',null,'selected');

			$select_checkbox_field = $row->addField('checkbox','selected_'.$dispatch_item['id'],"");
			$row->add('View',null,'item_name')->setHtml($dispatch_item['item']."<br/> Total Order Qty: ".$dispatch_item['quantity']);

			$row->add('View',null,'total_qty')->set($dispatch_item['received_quantity']);
			$row->add('View',null,'total_delivered')->set($dispatch_item['shipped_quantity'] + $dispatch_item['delivered_quantity']);

			$dispatchable_view->add('View')->set($dispatch_item['due_quantity']);
			$delivered_view->addField('Number',	'deliver_qty_'.$dispatch_item['id'],"")->set($dispatch_item['due_quantity']);

			//disable check box if no due quantity found
			if($dispatch_item['due_quantity'] == 0){
				$select_checkbox_field->setAttr('disabled','disabled');
			}
		}

		$form->addField('line','delivery_via')->validate('required');
		$form->addField('line','delivery_docket_no','Docket No / Person name / Other Reference')->validate('required');
		$form->addField('text','shipping_address')->set("customer address");
		$form->addField('text','delivery_narration');
		$form->addField('text','tracking_code');
		$send_invoice_and_challan = $form->addField('DropDown','send_document')->setValueList(array('send_invoice'=>'Generate & Send Invoice','send_challan'=>'Send Challan','all'=>'Send Invoice & Challan'))->setEmptyText('Select Document to Send');

		$form->addField('DropDown','print_document')->setValueList(array('print_challan'=>'Print Challan','print_invoice'=>'Print Invoice','print_all'=>'Print Invoice & Challan'))->setEmptyText('Select Document To Print');
		$form->addField('Checkbox','complete_on_receive')->set(true);
		$form->addField('Checkbox','include_barcode')->set(false);
		
		$from_email=$form->addField('dropdown','from_email')->setEmptyText('Please Select From Email');
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
			'all'=>['email_to','from_email'],
		],'div.atk-form-row');
		

		$form->addSubmit('Dispatch the Order');

		if($form->isSubmitted()){
			$orderitems_selected = array();
			
			//check validation
			foreach ($order_dispatch_m as $dispatch_item) {	
				// continue if not selected
				if(!$form['selected_'.$dispatch_item['id']])
					continue;

				//  check for quantity to deliver
				$delivered_qty = $form['deliver_qty_'.$dispatch_item['id']];
				if($delivered_qty == 0 or $delivered_qty == null or $delivered_qty < 0)
					$form->displayError('deliver_qty_'.$dispatch_item['id'],"cannot deliver ".$form['delivered_qty_'.$dispatch_item['qsp_detail_id']]." quanity");

				//check delivered item not zero or not greater than dispatchable qty
				if($delivered_qty > $dispatch_item['due_quantity'])
					$form->displayError('deliver_qty_'.$dispatch_item['id']," cannot more than dispatchable quantity ".$dispatch_item['due_quantity']);
				//end --------------------------------

				$orderitems_selected[] = [
										'qsp_detail_id'=>$dispatch_item['id']
									];
			}

			if(!count($orderitems_selected))
				$form->js()->univ()->errorMessage("please select at least one item to delivered")->execute();

			
		}
	}

	function dispatch(){
		throw new \Exception("Error Processing Request", 1);
		
	}
}