<?php

namespace xepan\commerce;

class Model_Order extends \xepan\commerce\Model_Document{
	public $status = ['Draft','Submitted','Approved','Cancelled','Return','Redesign','OnlinePaid','Processing','Processed','Shipping','Complete'];
	public $actions = [
					'Draft'=>['view','edit','delete','submit'],
					'Submitted'=>['view','edit','delete','approve','redesign'],
					'Redesign'=>['view','edit','delete','approve'],
					'Return'=>['view','edit','delete'],
					'Cancelled'=>['view','edit','delete','return'],
					'OnlinePaid'=>['view','edit','delete'],
					'Approved'=>['view','edit','delete','redesign'],
					'Processing'=>['view','edit','delete','redesign'],
					'Processed'=>['view','edit','delete'],
					'Shipping'=>['view','edit','delete'],
					'Complete'=>['view','edit','delete']
					];

	function init(){
		parent::init();

		$order_j = $this->join('order.document_id');
		$order_j->hasOne('xepan\commerce\Customer','customer_id');
		$order_j->hasOne('xepan\commerce\Supplier','supplier_id');


		//$this->addCondition('epan_id',$this->api->current_website->id);

		// $sorder_j->hasOne('xepan\commerce\Priority','priority_id');
		// $sorder_j->hasOne('xepan\commerce\PaymentGateway','paymentgateway_id');
		// $sorder_j->hasOne('xepan\commerce\Currency','currency_id');
		
		$order_j->hasOne('xepan\commerce\TNC','tnc_id');
		$order_j->addField('name')->caption('Order ID');
	
		$order_j->addField('order_from')->enum(array('online','offline'))->defaultValue('offline');
		
		//$this->addExpression('search_phrase') 'Order No - '

		//$order_j->addField('type');
		
		$order_j->addField('total_amount')->type('money');
		$order_j->addField('tax')->type('money');
		$order_j->addField('discount_voucher_amount');
		$order_j->addField('gross_amount')->type('money');
		$order_j->addField('net_amount')->type('money');

		$order_j->addField('billing_address');
		$order_j->addField('billing_landmark');
		$order_j->addField('billing_city');
		$order_j->addField('billing_state');
		$order_j->addField('billing_country');
		$order_j->addField('billing_zip');
		$order_j->addField('billing_tel');
		$order_j->addField('billing_email');

		$order_j->addField('shipping_address');	
		$order_j->addField('shipping_landmark');
		$order_j->addField('shipping_city');
		$order_j->addField('shipping_state');
		$order_j->addField('shipping_country');
		$order_j->addField('shipping_zip');
		$order_j->addField('shipping_tel');
		$order_j->addField('shipping_email');

		$order_j->addField('narration');
		$order_j->addField('delivery_date');

		// Payment GateWay related Info
		$order_j->addField('transaction_reference');
		$order_j->addField('transaction_response_data')->type('text');


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
