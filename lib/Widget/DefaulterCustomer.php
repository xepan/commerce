<?php

namespace xepan\commerce;

class Widget_DefaulterCustomer extends \xepan\base\Widget{
	function init(){
		parent::init();

		$this->view = $this->add('View',null,null,['view\dashboard\smallbox']);
	}

	function recursiveRender(){
		$master = $this->add('xepan\commerce\Model_QSP_Master');
		$master->addCondition('type',['SalesOrder','SalesInvoice']);
		$master->addCondition('status',['Due','OnlineUnpaid']);
		$master->_dsql()->group('contact_id');		
		$count = $master->count()->getOne();
				
		$this->view->template->trySet('count',$count);
		$this->view->template->trySet('title','Defaulter Customers');
		$this->view->template->trySet('icon-class','fa fa-user');

		return Parent::recursiveRender();
	}
}