<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_Document{
	public $status = ['Draft','Submitted','Approved','Processing','Processed','Shipping','Complete'];
	public $actions = [
					'Draft'=>['view','edit','delete','submit'],
					'Submitted'=>['view','edit','delete','approve','redesign','cancel/return'],
					'Approved'=>['view','edit','delete','redesign','cancel'],
					'Processing'=>['view','edit','delete','redesign','cancel'],
					'Processed'=>['view','edit','delete','cancel'],
					'Shipping'=>['view','edit','delete','cancel'],
					'Complete'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		$sorder_j = $this->join('salesorder.document_id');
		$sorder_j->hasOne('xepan\commerce\Customer','customer_id');

		//$this->addCondition('epan_id',$this->api->current_website->id);

		// $sorder_j->hasOne('xepan\commerce\Priority','priority_id');
		// $sorder_j->hasOne('xepan\commerce\PaymentGateway','paymentgateway_id');
		// $sorder_j->hasOne('xepan\commerce\Currency','currency_id');
		
		$sorder_j->hasOne('xepan\commerce\TNC','tnc_id');
		$sorder_j->addField('name')->caption('Order ID');
	
		$sorder_j->addField('order_from')->enum(array('online','offline'))->defaultValue('offline');
		
		//$this->addExpression('search_phrase') 'Order No - '

		$sorder_j->addField('total_amount')->type('money');
		$sorder_j->addField('tax')->type('money');
		$sorder_j->addField('discount_voucher_amount');
		$sorder_j->addField('gross_amount')->type('money');
		$sorder_j->addField('net_amount')->type('money');

		$sorder_j->addField('billing_address');
		$sorder_j->addField('billing_landmark');
		$sorder_j->addField('billing_city');
		$sorder_j->addField('billing_state');
		$sorder_j->addField('billing_country');
		$sorder_j->addField('billing_zip');
		$sorder_j->addField('billing_tel');
		$sorder_j->addField('billing_email');

		$sorder_j->addField('shipping_address');	
		$sorder_j->addField('shipping_landmark');
		$sorder_j->addField('shipping_city');
		$sorder_j->addField('shipping_state');
		$sorder_j->addField('shipping_country');
		$sorder_j->addField('shipping_zip');
		$sorder_j->addField('shipping_tel');
		$sorder_j->addField('shipping_email');

		$sorder_j->addField('narration');
		$sorder_j->addField('delivery_date');

		// Payment GateWay related Info
		$sorder_j->addField('transaction_reference');
		$sorder_j->addField('transaction_response_data')->type('text');

		$this->addCondition('type','SalesOrder');

		// Last OrderItem Status
		// $dept_status = $this->add('xShop/Model_OrderItemDepartmentalStatus',array('table_alias'=>'ds'));
		// $oi_j = $dept_status->join('xshop_orderdetails','orderitem_id');
		// $oi_j->addField('order_id');
		// $dept_status->addCondition($dept_status->getElement('order_id'),$this->getElement('id'));
		// $dept_status->_dsql()->limit(1)->order($dept_status->getElement('id'),'desc')->where('status','<>','Waiting');
		
		// $this->hasMany('xShop/OrderDetails','order_id');
		// $this->hasMany('xShop/SalesOrderAttachment','related_document_id',null,'Attachments');
		// $this->hasMany('xShop/SalesInvoice','sales_order_id');
		// $this->hasMany('xDispatch/DeliveryNote','order_id');
		
		//$this->addExpression('orderitem_count')->set($this->refSQL('xShop/OrderDetails')->count());
		
		// $member = $this->add('xShop/Model_MemberDetails');
		// $member->loadLoggedIn();
	}
}
