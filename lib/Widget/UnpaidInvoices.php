<?php

namespace xepan\commerce;

class Widget_UnpaidInvoices extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->view = $this->add('View',null,null,['view\dashboard\smallbox']);
	}

	function recursiveRender(){
		$inv = $this->add('xepan\commerce\Model_SalesInvoice');
		$inv->setOrder('due_date','asc');
		$inv->addCondition('status','Due');
		$inv_count = $inv->count()->getOne();
		
		$this->view->template->trySet('count',$inv_count);
		$this->view->template->trySet('title','Unpaid Invoices');
		$this->view->template->trySet('icon-class','fa fa-shopping-cart');
		
		return Parent::recursiveRender();
	}
}