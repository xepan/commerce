<?php

namespace xepan\commerce;

class Widget_UnpaidOrders extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->view = $this->add('View',null,null,['view\dashboard\smallbox']);
	}

	function recursiveRender(){
		$odr = $this->add('xepan\commerce\Model_SalesOrder');
		$odr->setOrder('due_date','asc');
		$odr->addCondition('status','OnlineUnpaid');
		$odr_count = $odr->count()->getOne();
		
		$this->view->template->trySet('count',$odr_count);
		$this->view->template->trySet('title','Unpaid Orders');
		$this->view->template->trySet('icon-class','fa fa-shopping-cart');

		return Parent::recursiveRender();
	}
}