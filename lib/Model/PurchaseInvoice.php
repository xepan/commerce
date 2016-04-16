<?php

namespace xepan\commerce;

class Model_PurchaseInvoice extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Due','Paid'];
	public $actions = [

				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','due','manage_attachments'],
				'Due'=>['view','edit','delete','paid','manage_attachments'],
				'Paid'=>['view','edit','delete','manage_attachments']
				];

	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','PurchaseInvoice');

	}

	function submit(){
        $this['status']='Submitted';
        $this->app->employee
            ->addActivity("Draft QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('submit','Draft');
        $this->saveAndUnload();
    }   


    function approve(){
		$this['status']='Approved';
        $this->app->employee
            ->addActivity("Approved QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('reject,due','Submitted');
        $this->saveAndUnload();
    }

    function due(){
		$this['status']='Due';
        $this->app->employee
            ->addActivity("Due QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('redesign,reject,send','Approved');
        $this->saveAndUnload();
    }

    function paid(){
		$this['status']='Paid';
        $this->app->employee
            ->addActivity("Paid QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('send','Due');
        $this->saveAndUnload();
    }
}
