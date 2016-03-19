<?php

namespace xepan\commerce;

class Model_SalesInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Redesign','Due','Paid','Canceled'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','due','manage_attachments'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Due'=>['view','edit','delete','redesign','reject','paid','send','manage_attachments'],
				'Paid'=>['view','edit','delete','send','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments']
				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','SalesInvoice');

	}

	function draft(){
		$this['status']='Draft';
        $this->app->employee
            ->addActivity("Draft QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('submit','Submitted');
        $this->saveAndUnload();
    }

    function due(){
		$this['status']='Due';
        $this->app->employee
            ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('redesign,reject,send','Submitted');
        $this->saveAndUnload();
    }

    function paid(){
		$this['status']='Paid';
        $this->app->employee
            ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('send','Due');
        $this->saveAndUnload();
    }
}
