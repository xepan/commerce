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

	}

	// function defaultTemplate(){
	// 	return ['view/tool/cart'];
	// }
}