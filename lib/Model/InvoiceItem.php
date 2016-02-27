<?php

namespace xepan\commerce;

class Model_InvoiceItem extends \xepan\base\Model_Document{
	
	function init(){
		parent::init();

		$q = $this->dsql();

		$item_j = $this->join('invoice_item.document_id');
		$item_j->hasOne('xepan\commere\Invoice','invoice_id');
		$item_j->hasOne('xepan\commerce\Item','item_id');
		$item_j->hasOne('xepan\commerce\OrderItem','orderitem_id');
		
		$item_j->addField('user_choice');

		$qty = $item_j->addField('qty');
		$item_j->addField('unit');
		$price = $item_j->addField('price')->type('money');
		$gross_amount = $item_j->addExpression('gross_amount')->set($q->expr('[0]*[1]',[$qty,$price]))->type('money');

		$discount = $item_j->addField('discount')->type('money');
		$grand_total = $item_j->addExpression('grand_total')->set($q->expr('[0]-[1]',[$gross_amount,$discount]))->type('money');

		$item_j->addField('tax_name');
		$tax_percentage = $item_j->addField('tax_percentage')->type('money');
		$tax_amount = $item_j->addExpression('tax_amount')->set($q->expr('[0]*[1]/100.00',[$grand_total,$tax_percentage]))->type('money');
		$shipping = $item_j->addField('shipping_charge')->type('money');

		$item_j->addField('net_amount')->set($q->expr('[1]+[2]+[3]',[$grand_total,$tax_amount,$shipping]))->type('money');

		$item_j->addField('narration')->type('text');

	}
}
 