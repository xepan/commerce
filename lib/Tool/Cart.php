<?php

namespace xepan\commerce;

class Tool_Cart extends \xepan\cms\View_Tool{
	public $options = [
					'show_name'=>true				
				];

	function init(){
		parent::init();

		$cart = $this->add('xepan\commerce\Model_Cart');
		
		$lister=$this->add('CompleteLister',null,null,['view/tool/cart']);
		$lister->setModel($cart);

		$sum_amount_excluding_tax=0;
		$sum_tax_amount=0;

		foreach ($cart as $item) {
			$sum_amount_excluding_tax += $item['amount_excluding_tax'];
			$sum_tax_amount += $item['tax_amount'];
		}
		
		$lister->template->set('sum_amount_excluding_tax',$sum_amount_excluding_tax);
		$lister->template->set('tax_amount',$sum_tax_amount);

	}

	// function defaultTemplate(){
	// 	return ['view/tool/cart'];
	// }
}