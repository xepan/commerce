<?php

namespace xepan\commerce;

class Widget_OnlineUnpaidOrders extends \xepan\base\Widget{
	function init(){
		parent::init();

		if(!$this->isChart())
			$this->destroy();
		
		$this->view = $this->add('View',null,null,['view\dashboard\smallbox']);
	}

	function recursiveRender(){
		$odr = $this->add('xepan\commerce\Model_SalesOrder');
		$odr->setOrder('due_date','asc');
		$odr->addCondition('status','OnlineUnpaid');
		$odr_count = $odr->count()->getOne();
		
		$this->view->template->trySet('count',$odr_count);
		$this->view->template->trySet('title','Online Unpaid Orders');
		$this->view->template->trySet('icon-class','fa fa-shopping-cart');

		$this->view->js('click')->_selector('#'.$this->view->name)->univ()->frameURL("Unpaid Orders",$this->api->url('xepan_commerce_salesorder',['status'=>'OnlineUnpaid']));
		return Parent::recursiveRender();
	}
}