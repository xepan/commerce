<?php

namespace xepan\commerce;

class Widget_OnlineUnpaidCustomer extends \xepan\base\Widget{
	function init(){
		parent::init();

		if(!$this->isChart())
			$this->destroy();

		$this->view = $this->add('View',null,null,['view\dashboard\smallbox']);
		$this->view->setStyle('cursor','pointer');

		$this->customer_v_page = $this->add('VirtualPage');
		$this->customer_v_page->set(function($vp){
			$customer = $vp->add('xepan\commerce\Model_Customer');
			$customer->addExpression('online_unpaid_order')->set($customer->refSQL('QSPMaster')->addCondition('status','OnlineUnpaid')->count());
			$customer->addCondition('online_unpaid_order','>',0);

			$crud = $vp->add('xepan\hr\CRUD',
								['action_page'=>'xepan_commerce_customerdetail','allow_add'=>false],
								null,
								['view/customer/grid']
							);
			$crud->setModel($customer)->setOrder('created_at','desc');
			$crud->grid->addPaginator(20);
		});


	}

	function recursiveRender(){
		$customer = $this->add('xepan\commerce\Model_Customer');
		$customer->addExpression('online_unpaid_order')->set($customer->refSQL('QSPMaster')->addCondition('status','OnlineUnpaid')->count());
		$customer->addCondition('online_unpaid_order','>',0);
		$count = $customer->count()->getOne();

		$this->view->template->trySet('count',$count);
		$this->view->template->trySet('title','Online Unpaid Customers');
		$this->view->template->trySet('icon-class','fa fa-user');


		$this->view->js('click')->_selector('#'.$this->view->name)->univ()->frameURL("Online Unpaid Customers",$this->api->url($this->customer_v_page->getUrl()));
		return Parent::recursiveRender();
	}
}