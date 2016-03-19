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

	// function approve_page($page){

	// 	$form = $page->add('Form_Stacked');
	// 	$form->addField('text','comments');
	// 	$form->addSubmit('Approve & Create Jobcards');

	// 	$page->add('HtmlElement')->setElement('H3')->setHTML('<small>Approving Job Card will move this order to approved status and create JobCards to receive in respective FIRST Departments for EACH Item</small>');
	// 	if($form->isSubmitted()){
	// 		$this->approve($form['comments']);
	// 		// $this->send_via_email_page($this);
	// 		return true;
	// 	}
	// 	return false;
	// }

	function approve($message){
		// check conditions
		foreach ($qis=$this->qspItems() as $qi) {
			$qis->createDepartmentalAssociations();
			if($department_association = $qi->nextDeptStatus()){
				$department_association->createJobCardFromOrder();
			}
		}

		$this->setStatus('approved',$message);
		return $this;
	}

	function qspItems(){
		return $this->ref('xepan/commerce/QSP_Detail');
	}

	// function itemrows(){
	// 	return $this->qspItems();
	// }

	// function unCompletedQSPItems(){
	// 	$qi=$this->qspItems();
	// 	$qi->addExpression('open_departments')->set($qi->refSQL('xShop/OrderItemDepartmentalStatus')->addCondition('is_open',true)->count());
	// 	$qi->addCondition('open_departments',true);

	// 	return $qi;
	// }

}
