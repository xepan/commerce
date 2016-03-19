<?php

namespace xepan\commerce;

class Model_Quotation extends \xepan\commerce\Model_QSP_Master{
	
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','redesign','reject','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','redesign','reject','convert','manage_attachments'],
				'Redesign'=>['view','edit','delete','submit','reject','manage_attachments'],
				'Rejected'=>['view','edit','delete','redesign','manage_attachments'],
				'Converted'=>['view','edit','delete','send','manage_attachments']
				];

	function init(){
		parent::init();

		$this->addCondition('type','Quotation');

	}

	function convert(){
		$this['status']='Converted';
        $this->app->employee
            ->addActivity("Converted QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('send','Approved');
        $this->saveAndUnload();
    }
}
