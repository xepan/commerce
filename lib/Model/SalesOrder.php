<?php

namespace xepan\commerce;

class Model_SalesOrder extends \xepan\commerce\Model_QSP_Master{
	public $status = ['Draft','Submitted','Approved','Redesign','Rejected','Converted'];
	public $actions = [
				'Draft'=>['view','edit','delete','submit','manage_attachments'],
				'Submitted'=>['view','edit','delete','approve','manage_attachments'],
				'Approved'=>['view','edit','delete','inprogress','manage_attachments'],
				'InProgress'=>['view','edit','delete','cancel','complete','manage_attachments'],
				'Canceled'=>['view','edit','delete','manage_attachments'],
				'Completed'=>['view','edit','delete','manage_attachments'],
				// 'Returned'=>['view','edit','delete','manage_attachments']
				];

	// public $notification_rules = array(
	// 		// 'activity NOT STATUS' => array (....)
	// 						),
	// 		'approved' => array('xepan/commerce/SalesOrder_Approved/creator' => ['title'=>'Sales Order Approved','message'=>'Sales Order {$document_name} is approved by {$contact_id}']),
			
	// 	);


	// public $acl = false;

	function init(){
		parent::init();

		$this->addCondition('type','SalesOrder');

	}

	function inprogress(){
		$this['status']='InProgress';
        $this->app->employee
            ->addActivity("InProgress QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('cancel','Approved');
        $this->saveAndUnload();
    }

    function cancel(){
		$this['status']='Canceled';
        $this->app->employee
            ->addActivity("Canceled QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();
    }

    function complete(){
		$this['status']='Completed';
        $this->app->employee
            ->addActivity("Completed QSP", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            ->notifyWhoCan('','InProgress');
        $this->saveAndUnload();
    }

	function page_approve($page){

		$page->add('View_Info')->setElement('H2')->setHTML('Approving Job Card will move this order to approved status and create JobCards to receive in respective FIRST Departments for EACH Item');

		$form = $page->add('Form_Stacked');
		$form->addField('text','comments');
		$form->addSubmit('Approve & Create Jobcards');

		if($form->isSubmitted()){
			$this->approve($form['comments']);
		
			$this['status']='InProgress';
        	$this->app->employee
            	->addActivity("SaleOrder Jobcard created", $this->id/* Related Document ID*/, $this['contact_id'] /*Related Contact ID*/)
            	->notifyWhoCan('','InProgress');
            $this->saveAndUnload();
            return true;
		}
		return false;
	}

	function approve($message){

		foreach ($ois=$this->orderItems() as $oi) {
			$oi->createDepartmentalAssociations();
			if($department_association = $oi->nextDeptStatus()){
				$department_association->createJobCardFromOrder();
			}
		}
		return $this;
	}

	function orderItems(){
		return $this->items();
	}

	function customer(){
		return $this->ref('contact_id');
	}

	function invoice(){
		if(!$this->loaded());
			throw new \Exception("Model Must Loaded, SaleOrder");
			
		$inv = $this->add('xepan\commerce\Model_SalesInvoice')
					->addCondition('related_qsp_master_id',$this->id);

		$inv->tryLoadAny();
		if($inv->loaded()) return $inv;
		return false;
	}
}
