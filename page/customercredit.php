<?php 
 namespace xepan\commerce;

 class page_customercredit extends \xepan\commerce\page_configurationsidebar{

	public $title='Customer Credit';

	function init(){
		parent::init();

		$credit_model = $this->add('xepan\commerce\Model_Credit');
		$crud=$this->add('xepan\hr\CRUD',null,
						null,
						['view\customer\credit']
					);
		$crud->setModel($credit_model,['customer_id','name','amount','type'],['customer','amount','name','type']);
		$crud->add('xepan\base\Controller_MultiDelete');
	}
} 