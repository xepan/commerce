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
		
		$nominal_field = $this->getField('nominal_id');
		$nominal_field->mandatory(true);

		$sale_group = $this->add('xepan\accounts\Model_Group')->loadSalesAccount();
		$model = $nominal_field->getModel();
		
		$model->addCondition(
							$model->dsql()->orExpr()
								->where('root_group_id',$sale_group->id)
								->where('parent_group_id',$sale_group->id)
								->where('id',$sale_group->id)
						);
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


    function PayViaOnline($transaction_reference,$transaction_reference_data){
		$this['transaction_reference'] =  $transaction_reference;
	    $this['transaction_response_data'] = json_encode($transaction_reference_data);
	    $this->save();
	}
}
